<?php

namespace Shahkochaki\TaraService;

/**
 * Tara service constants and enums
 */
class TaraConstants
{
    /**
     * Product units
     */
    const UNITS = [
        1 => 'Kilogram',
        2 => 'Meter',
        3 => 'Liter',
        4 => 'Square Meter',
        5 => 'Piece',
        6 => 'Item',
        7 => 'Device',
        8 => 'Pair',
        9 => 'Package',
        10 => 'Box',
        11 => 'Set',
        12 => 'Couple'
    ];

    /**
     * Product origin
     */
    const MADE_IN = [
        0 => 'Unknown',
        1 => 'Iranian',
        2 => 'Foreign'
    ];

    // Kilogram unit
    const UNIT_KILOGRAM = 1;

    // Meter unit
    const UNIT_METER = 2;

    // Liter unit
    const UNIT_LITER = 3;

    // Square meter unit
    const UNIT_SQUARE_METER = 4;

    // Piece unit
    const UNIT_PIECE = 5;

    // Item unit
    const UNIT_PART = 6;

    // Device unit
    const UNIT_DEVICE = 7;

    // Pair unit (hand)
    const UNIT_HAND = 8;

    // Package unit
    const UNIT_PACKAGE = 9;

    // Box unit
    const UNIT_BOX = 10;

    // Set unit
    const UNIT_SET = 11;

    // Couple/Pair unit
    const UNIT_PAIR = 12;

    // Unknown origin
    const MADE_UNKNOWN = 0;

    // Iranian origin
    const MADE_IRANIAN = 1;

    // Foreign origin
    const MADE_FOREIGN = 2;

    /**
     * دریافت نام واحد بر اساس کد
     */
    public static function getUnitName(int $unitCode): string
    {
        return self::UNITS[$unitCode] ?? 'نامشخص';
    }

    /**
     * دریافت نام منشأ بر اساس کد
     */
    public static function getMadeName(int $madeCode): string
    {
        return self::MADE_IN[$madeCode] ?? 'نامشخص';
    }

    /**
     * دریافت تمام واحدها
     */
    public static function getAllUnits(): array
    {
        return self::UNITS;
    }

    /**
     * دریافت تمام منشأها
     */
    public static function getAllMadeIn(): array
    {
        return self::MADE_IN;
    }
}
