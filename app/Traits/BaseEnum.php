<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait BaseEnum
{
    public static function keys(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_values(self::toArray());
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function toCollection(): Collection
    {
        return collect(self::toArray());
    }

    public static function toString(): string
    {
        return implode(',', self::values());
    }

    public static function toButifyStructure(): array
    {
        // Deprecated: use `toBeautifyStructure()` instead
        @trigger_error('Method ' . __METHOD__ . ' is deprecated, use toBeautifyStructure() instead', E_USER_DEPRECATED);

        $data = [];

        foreach (self::toArray() as $key => $value) {
            $data[] = self::getBeautifyStructureByKey($key);
        }

        return $data;
    }

    /**
     * Correctly-spelled wrapper for legacy `toButifyStructure()`.
     * @return array
     */
    public static function toBeautifyStructure(): array
    {
        return self::toButifyStructure();
    }

    public static function getButifyStructureByKey($key): array
    {
        return [
            'key'   => $key,
            'value' => self::getValue($key),
            'label' => self::getLabel($key),
        ];
    }

    /**
     * Correctly-spelled wrapper for legacy `getButifyStructureByKey()`.
     */
    public static function getBeautifyStructureByKey($key): array
    {
        return self::getButifyStructureByKey($key);
    }

    public static function getButifyStructure($value): array
    {
        @trigger_error('Method ' . __METHOD__ . ' is deprecated, use getBeautifyStructure() instead', E_USER_DEPRECATED);

        return self::getBeautifyStructure($value);
    }

    /**
     * Correctly-spelled wrapper for legacy `getButifyStructure()`.
     */
    public static function getBeautifyStructure($value): array
    {
        return self::getButifyStructure($value);
    }

    public static function getKey($value): ?string
    {
        $key = array_search($value, self::toArray(), true);
        return $key === false ? null : (string) $key;
    }

    public static function getValue(self|string $key): mixed
    {
        if ($key instanceof self) {
            return $key->value;
        }

        $key = Str::upper($key);
        return Arr::first(self::toArray(), fn ($_value, $_key) => $key === Str::upper($_key));
    }

    public static function getLabel($key): string
    {
        return Str::title(str_replace('_', ' ', $key));
    }

    public static function hasValue($value): bool
    {
        $validValues = self::values();
        return in_array($value, $validValues, true);
    }

    public static function getRandomValue()
    {
        $array = self::values();
        $key   = array_rand($array);
        return $array[$key];
    }
}
