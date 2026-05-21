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
        $data = [];

        foreach (self::toArray() as $key => $value) {
            $data[] = self::getButifyStructureByKey($key);
        }

        return $data;
    }

    public static function getButifyStructureByKey($key): array
    {
        return [
            'key'   => $key,
            'value' => self::getValue($key),
            'label' => self::getLabel($key),
        ];
    }

    public static function getButifyStructure($value): array
    {
        $key = self::getKey($value);
        return self::getButifyStructureByKey($key);
    }

    public static function getKey($value): string
    {
        return array_search($value, self::toArray());
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
