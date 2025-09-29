<?php

namespace Shahkochaki\TaraService;

/**
 * Tara service constants and enums
 */
class TaraConstants
{
    /**
     * واحدهای کالا
     */
    const UNITS = [
        1 => 'کیلوگرم',
        2 => 'متر',
        3 => 'لیتر',
        4 => 'مترمربع',
        5 => 'عدد',
        6 => 'قطعه',
        7 => 'دستگاه',
        8 => 'دست',
        9 => 'بسته',
        10 => 'جعبه',
        11 => 'ست',
        12 => 'جفت'
    ];

    /**
     * منشأ کالا
     */
    const MADE_IN = [
        0 => 'نامشخص',
        1 => 'ایرانی',
        2 => 'خارجی'
    ];

    // واحد کیلوگرم
    const UNIT_KILOGRAM = 1;

    // واحد متر
    const UNIT_METER = 2;

    // واحد لیتر
    const UNIT_LITER = 3;

    // واحد مترمربع
    const UNIT_SQUARE_METER = 4;

    // واحد عدد
    const UNIT_PIECE = 5;

    // واحد قطعه
    const UNIT_PART = 6;

    // واحد دستگاه
    const UNIT_DEVICE = 7;

    // واحد دست
    const UNIT_HAND = 8;

    // واحد بسته
    const UNIT_PACKAGE = 9;

    // واحد جعبه
    const UNIT_BOX = 10;

    // واحد ست
    const UNIT_SET = 11;

    // واحد جفت
    const UNIT_PAIR = 12;

    // منشأ نامشخص
    const MADE_UNKNOWN = 0;

    // منشأ ایرانی
    const MADE_IRANIAN = 1;

    // منشأ خارجی
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
