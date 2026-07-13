<?php

namespace App\Services;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

/**
 * Read/write access to platform-wide configuration, backed by the
 * platform_settings table with a forever-cache in front (busted on write).
 *
 * Secret values (gateway credentials) are encrypted at rest; callers always see
 * plaintext through get(). The whole store degrades gracefully to defaults when
 * the table doesn't exist yet (e.g. during the very first migration).
 */
class PlatformSettings
{
    private const CACHE_KEY = 'platform_settings.all';

    /** Keys whose values are encrypted at rest. */
    public const SECRET_KEYS = ['razorpay_secret', 'razorpay_webhook_secret'];

    /** In-request memoisation on top of the cache. */
    private ?array $loaded = null;

    /**
     * @return array<string,?string> Raw (still-encrypted) key/value map.
     */
    public function raw(): array
    {
        if ($this->loaded !== null) {
            return $this->loaded;
        }

        if (! Schema::hasTable('platform_settings')) {
            return $this->loaded = [];
        }

        return $this->loaded = Cache::rememberForever(
            self::CACHE_KEY,
            fn () => PlatformSetting::pluck('value', 'key')->toArray()
        );
    }

    /** Fetch a setting (secrets decrypted), falling back to $default when unset. */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->raw()[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        if (in_array($key, self::SECRET_KEYS, true)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Throwable) {
                return $default;
            }
        }

        return $value;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /** True when a secret is present without exposing its value (for UI hints). */
    public function hasSecret(string $key): bool
    {
        return ! empty($this->raw()[$key] ?? null);
    }

    /** Persist one setting. Null removes it; secrets are encrypted. */
    public function set(string $key, mixed $value): void
    {
        if ($value === null || $value === '') {
            PlatformSetting::where('key', $key)->delete();
        } else {
            if (in_array($key, self::SECRET_KEYS, true)) {
                $value = Crypt::encryptString((string) $value);
            }

            PlatformSetting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        $this->flush();
    }

    /**
     * Persist several settings at once (single cache bust).
     *
     * @param  array<string,mixed>  $pairs
     */
    public function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            if ($value === null || $value === '') {
                PlatformSetting::where('key', $key)->delete();
                continue;
            }

            $stored = in_array($key, self::SECRET_KEYS, true)
                ? Crypt::encryptString((string) $value)
                : (string) $value;

            PlatformSetting::updateOrCreate(['key' => $key], ['value' => $stored]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        $this->loaded = null;
        Cache::forget(self::CACHE_KEY);
    }
}
