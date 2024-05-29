<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BankUnitTest extends TestCase
{
    public function test_convert_Persian_To_English()
    {
        $converted = convertPersianToEnglish("۱۲۳۴۵۶۷۸۹");
        $convertedMix = convertPersianToEnglish("۱۲3۴۵۶۷89");

        //
        $this->assertEquals("123456789", $converted);
        $this->assertEquals("123456789", $convertedMix);
    }
}
