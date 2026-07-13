<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * A platform broadcast shown to every tenant. "Live" means active and within its
 * optional start/end window.
 */
class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'level', 'is_active', 'starts_at', 'ends_at', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The live banner set, cached for 5 minutes and busted on any admin write
     * (see AnnouncementController). Safe before the table exists (early boot).
     *
     * @return Collection<int,Announcement>
     */
    public static function cachedLive(): Collection
    {
        if (! Schema::hasTable('announcements')) {
            return new Collection();
        }

        return Cache::remember(
            \App\Http\Controllers\SuperAdmin\AnnouncementController::CACHE_KEY,
            now()->addMinutes(5),
            fn () => static::live()->latest()->get()
        );
    }

    /** Active and within its scheduled window (nulls = open-ended). */
    public function scopeLive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }
}
