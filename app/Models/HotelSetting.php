<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelSetting extends Model
{
    protected $table = 'hotel_settings';

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, mixed $value, ?string $description = null): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value) ? (int) $value : $value,
                'description' => $description,
            ]
        );
    }
}
