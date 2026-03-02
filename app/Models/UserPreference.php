<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme_mode',
        'colorize_accounts',
        'dense_interface',
        'reduced_motion',
        'report_sales_settings',
    ];

    protected $casts = [
        'colorize_accounts' => 'boolean',
        'dense_interface' => 'boolean',
        'reduced_motion' => 'boolean',
        'report_sales_settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
