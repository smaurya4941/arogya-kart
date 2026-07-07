<?php

namespace Tests\Unit;

use App\Services\NumberSequenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NumberSequenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private NumberSequenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NumberSequenceService();
    }

    public function test_it_returns_a_gap_free_increasing_series_per_type(): void
    {
        $first = DB::transaction(fn () => $this->service->next(1, 'sale'));
        $second = DB::transaction(fn () => $this->service->next(1, 'sale'));
        $third = DB::transaction(fn () => $this->service->next(1, 'sale'));

        $this->assertSame([1, 2, 3], [$first, $second, $third]);
    }

    public function test_counters_are_isolated_per_tenant(): void
    {
        DB::transaction(fn () => $this->service->next(1, 'sale')); // pharmacy 1 -> 1
        DB::transaction(fn () => $this->service->next(1, 'sale')); // pharmacy 1 -> 2

        // A different pharmacy starts its own series at 1.
        $this->assertSame(1, DB::transaction(fn () => $this->service->next(2, 'sale')));
    }

    public function test_counters_are_isolated_per_type(): void
    {
        DB::transaction(fn () => $this->service->next(1, 'sale'));   // sale -> 1
        DB::transaction(fn () => $this->service->next(1, 'sale'));   // sale -> 2

        // Purchases keep an independent counter for the same pharmacy.
        $this->assertSame(1, DB::transaction(fn () => $this->service->next(1, 'purchase')));
    }

    public function test_users_without_a_pharmacy_get_their_own_series(): void
    {
        $first = DB::transaction(fn () => $this->service->next(null, 'sale'));
        $second = DB::transaction(fn () => $this->service->next(null, 'sale'));

        $this->assertSame([1, 2], [$first, $second]);
    }
}
