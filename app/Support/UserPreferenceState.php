<?php

namespace App\Support;

use App\Models\User;
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

    public static function forUser(?User $user): array
    {
        $defaults = static::defaults();

        if (! $user) {
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
