<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Hands out gap-free, monotonically increasing document numbers per tenant and
 * document type, safely under concurrency.
 *
 * The old approach — COUNT(*) + 1 — could hand the same number to two tills that
 * checked out at the same instant, because neither sees the other's uncommitted
 * row. Here we serialise on a single counter row: insertOrIgnore guarantees the
 * row exists (racing inserts collapse to one), then lockForUpdate makes every
 * caller queue behind whoever holds the row until they commit.
 *
 * Must be called inside a surrounding DB::transaction() (SaleService /
 * PurchaseService already open one) so the lock is held until the parent row is
 * persisted — otherwise the lock releases early and the guarantee is lost.
 */
class NumberSequenceService
{
    /**
     * Reserve and return the next integer in the (pharmacy, type, period) series.
     *
     * @param  string  $period  Bucket the counter resets on. Pass a constant like
     *                          'all' for a never-resetting series, or e.g. a
     *                          'Ym' string for monthly reset.
     */
    public function next(?int $pharmacyId, string $type, string $period = 'all'): int
    {
        $key = [
            'pharmacy_id' => $pharmacyId,
            'type' => $type,
            'period' => $period,
        ];

        // Ensure the counter row exists without racing: concurrent inserts of the
        // same key collapse to a single row instead of throwing.
        DB::table('document_sequences')->insertOrIgnore($key + [
            'next_value' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Serialise: the first caller locks the row; others block here until it
        // commits, so no two callers ever read the same next_value.
        $row = DB::table('document_sequences')
            ->where($key)
            ->lockForUpdate()
            ->first();

        $current = (int) $row->next_value;

        DB::table('document_sequences')
            ->where('id', $row->id)
            ->update([
                'next_value' => $current + 1,
                'updated_at' => now(),
            ]);

        return $current;
    }
}
