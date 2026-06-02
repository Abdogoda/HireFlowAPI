<?php

use App\Enums\Users\Gender;

it('returns keys and values and labels correctly', function () {
    $keys = Gender::keys();
    expect($keys)->toBeArray()->toContain('MALE');

    $values = Gender::values();
    expect($values)->toBeArray()->toContain('male');

    // getLabel
    expect(Gender::getLabel('MALE'))->toBe('Male');

    // getKey by value
    expect(Gender::getKey('male'))->toBe('MALE');

    // toBeautifyStructure exists and returns expected shape
    $list = Gender::toBeautifyStructure();
    expect($list)->toBeArray();
    expect($list[0])->toHaveKeys(['key', 'value', 'label']);
});