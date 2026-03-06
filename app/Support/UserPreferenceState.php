<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

class UserPreferenceState
{
    public static function defaults(): array
    {
        return [
            'theme_mode' => 'system',
            'colorize_accounts' => true,
            'dense_interface' => false,
            'reduced_motion' => false,
        ];
    }

    public static function forUser(?Authenticatable $user): array
    {
        $defaults = static::defaults();

        if (! $user) {
            return $defaults;
        }

        if (! method_exists($user, 'preference')) {
            return $defaults;
        }

        $preference = $user->preference()->first();

        if (! $preference) {
            return $defaults;
        }

        return array_merge(
            $defaults,
            Arr::only($preference->toArray(), array_keys($defaults)),
        );
    }
}
