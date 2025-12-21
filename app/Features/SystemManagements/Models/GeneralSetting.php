<?php

namespace App\Features\SystemManagements\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'caption',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function setValue(string $key, $value): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get base price setting
     */
    public static function getBasePrice(): float
    {
        return (float) self::getValue('base_price', 0);
    }

    /**
     * Set base price setting
     */
    public static function setBasePrice(float $price): self
    {
        return self::setValue('base_price', $price);
    }
}
