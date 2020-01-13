<?php

namespace Fynduck\ISIN;

use Fynduck\ISIN\Exception\InvalidISINException;

class Validator
{
    /**
     * @param $input
     * @return string
     * @throws InvalidISINException
     */
    public function validate($input)
    {
        if (!is_string($input)) {
            throw new InvalidISINException(
                'ISIN Input was not a string'
            );
        }
        $input = strtoupper($input);
        $input = trim($input);

        if (!$this->isCorrectLength($input)) {
            throw new InvalidISINException(
                'ISIN Input was not the correct length. Must be 12 characters'
            );
        }

        if (!$this->isCorrectPattern($input)) {
            throw new InvalidISINException(
                'ISIN Input contained invalid characters. Must be A-Z and 0-9 with AAXXXXXXXXX#'
            );
        }

        if (!$this->isCorrectChecksum($input)) {
            throw new InvalidISINException(
                'ISIN Input failed checksum validation'
            );
        }

        return $input;
    }

    public function generateDigit($input)
    {
        if (!$this->isCorrectLength($input, 1)) {
            throw new InvalidISINException(
                'Input was not the correct length. Must be 11 characters'
            );
        }

        if (!$this->isCorrectPattern($input)) {
            throw new InvalidISINException(
                'Input contained invalid characters. Must be A-Z and 0-9 with AAXXXXXXXXX#'
            );
        }

        return $this->getCheckDigit($input);
    }

    private function isCorrectLength($input, $number = 0)
    {
        return strlen($input) == ISIN::VALIDATION_LENGTH - $number;
    }

    private function isCorrectPattern($input)
    {
        return preg_match(ISIN::VALIDATION_PATTERN, $input);
    }

    private function isCorrectChecksum($input)
    {
        $characters = str_split($input);
        // convert all characters to numbers (ints)
        foreach ($characters as $i => $char) {
            // cast to int, by using intval at base 36 we also convert letters to numbers
            $characters[$i] = intval($char, 36);
        }

        // pull out the checkDigit
        $checkDigit = array_pop($characters);

        // put the string back together
        $number = implode('', $characters);
        $expectedCheckDigit = $this->getCheckDigit($number);

        return ($checkDigit === $expectedCheckDigit);
    }

    private function getCheckDigit($input)
    {
        // this method performs the luhn algorithm
        // to obtain a check digit

        $input = (string) $input;

        // first split up the string
        $numbers = str_split($input);

        // calculate the positional value.
        // when there is an even number of digits the second group will be multiplied, so p starts on 0
        // when there is an odd number of digits the first group will be multiplied, so p starts on 1
        $p = count($numbers) % 2;
        // run through each number
        foreach ($numbers as $i => $num) {
            $num = (int) $num;
            // every positional number needs to be multiplied by 2
            if ($p % 2) {
                $num = $num*2;
                // if the result was more than 9
                // add the individual digits
                $num = array_sum(str_split($num));
            }
            $numbers[$i] = $num;
            $p++;
        }

        // get the total value of all the digits
        $sum = array_sum($numbers);

        // get the remainder when dividing by 10
        $mod = $sum % 10;

        // subtract from 10
        $rem = 10 - $mod;

        // mod from 10 to catch if the result was 0
        $digit = $rem % 10;

        return $digit;
    }
}
