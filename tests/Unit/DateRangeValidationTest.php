<?php

namespace Tests\Unit;

use App\Support\DateRangeValidation;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class DateRangeValidationTest extends TestCase
{
    public function test_it_accepts_an_equal_or_later_to_date(): void
    {
        $rules = DateRangeValidation::rules('from_date', 'to_date');

        $this->assertTrue(Validator::make([
            'from_date' => '2026-07-05',
            'to_date' => '2026-07-05',
        ], $rules)->passes());

        $this->assertTrue(Validator::make([
            'from_date' => '2026-07-05',
            'to_date' => '2026-07-20',
        ], $rules)->passes());
    }

    public function test_it_rejects_a_to_date_before_the_from_date(): void
    {
        $validator = Validator::make([
            'from_date' => '2026-07-05',
            'to_date' => '2026-06-29',
        ], DateRangeValidation::rules('from_date', 'to_date'));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('to_date', $validator->errors()->toArray());
    }
}