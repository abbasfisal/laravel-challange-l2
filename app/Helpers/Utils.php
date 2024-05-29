<?php

if (!function_exists('convertPersianToEnglish')) {
    function convertPersianToEnglish($number)
    {
        $conversion_map = array(
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        );

        return strtr($number, $conversion_map);
    }
}

if (!function_exists('validCardBank')) {
    function validCardBank(): string
    {
        $number = generateBankCardNumber();
        if (!isValidIranianCardNumber($number)) {
            validCardBank();
        }
        return $number;
    }
}

if (!function_exists('generateBankCardNumber')) {
    function generateBankCardNumber(): string
    {

        // Generate the first 15 digits randomly
        $number = mt_rand(100000000000000, 999999999999999);
        // Perform the Luhn algorithm to calculate the last digit (checksum)
        $checksum = 0;
        $isSecondDigit = false;
        for ($i = 15; $i >= 0; $i--) {
            $digit = (int)substr($number, $i, 1);

            if ($isSecondDigit) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $checksum += $digit;
            $isSecondDigit = !$isSecondDigit;
        }

        // Calculate the checksum digit and append it to the number
        $checksum %= 10;
        if ($checksum !== 0) {
            $checksum = 10 - $checksum;
        }

        return $number . $checksum;
    }
}
if (!function_exists('isValidIranianCardNumber')) {
    function isValidIranianCardNumber($cardNumber)
    {
        // Convert the card number to a string to ensure consistency
        $cardNumber = (string)$cardNumber;

        // Check if the card number has exactly 16 digits
        if (!preg_match('/^\d{16}$/', $cardNumber)) {
            return false;
        }

        // Perform the Luhn algorithm to validate the card number
        $checksum = 0;
        for ($i = 0; $i < 16; $i++) {
            $digit = (int)$cardNumber[$i]; // Access each character as a string
            if ($i % 2 == 0) { // Even index (0-based)
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $checksum += $digit;
        }

        return ($checksum % 10) === 0;
    }

}
