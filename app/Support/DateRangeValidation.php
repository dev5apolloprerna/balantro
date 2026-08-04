<?php

namespace App\Support;

use Illuminate\Http\Request;

final class DateRangeValidation
{
    public static function validate(Request $request, string $fromField, string $toField): void
    {
        $request->validate(self::rules($fromField, $toField), self::messages($fromField, $toField));
    }

    public static function rules(string $fromField, string $toField): array
    {
        return [
            $fromField => ['nullable', 'date_format:Y-m-d'],
            $toField => ['nullable', 'date_format:Y-m-d', "after_or_equal:{$fromField}"],
        ];
    }

    public static function messages(string $fromField, string $toField): array
    {
        return [
            "{$toField}.after_or_equal" => 'The to date must be the same as or later than the from date.',
        ];
    }
}
