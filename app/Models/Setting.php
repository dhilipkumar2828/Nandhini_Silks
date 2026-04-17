<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get the effective admin notification email based on environment settings.
     */
    public static function getAdminEmail()
    {
        $settings = self::all()->pluck('value', 'key');
        $mode = $settings['mail_mode'] ?? 'local';
        $fallback = $settings['order_notification_email'] ?? 'nandhinisilks.arni@gmail.com';

        if ($mode === 'live') {
            return $settings['live_notification_email'] ?? $fallback;
        }

        return $settings['local_notification_email'] ?? $fallback;
    }
}
