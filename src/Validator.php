<?php

namespace Fynduck\isin;

use Fynduck\isin\Exception\InvalidISINException;

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

        if (!$this->isCorrectPattern($input, true)) {
            throw new InvalidISINException(
                'Input contained invalid characters. Must be A-Z and 0-9 with AAXXXXXXXXX'
            );
        }

        $characters = $this->generateSum($input);

        return $this->getCheckDigit(implode('', $characters));
    }

    private function isCorrectLength($input, $number = 0)
    {
        return strlen($input) == Isin::VALIDATION_LENGTH - $number;
    }

    private function isCorrectPattern($input, $generate = false)
    {
        if ($generate)
            $response = preg_match(Isin::VALIDATION_PATTERN_FOR_GENERATE, $input);
        else
            $response = preg_match(Isin::VALIDATION_PATTERN, $input);

        return $response;
    }

    private function isCorrectChecksum($input)
    {
        $characters = $this->generateSum($input);

        $checkDigit = array_pop($characters);

        $expectedCheckDigit = $this->getCheckDigit(implode('', $characters));

        return ($checkDigit === $expectedCheckDigit);
    }

    private function generateSum($input)
    {
        $characters = str_split($input);
        foreach ($characters as $i => $char)
            $characters[$i] = intval($char, 36);

        return $characters;
    }

    private function getCheckDigit($input)
    {
        // this method performs the luhn algorithm
        // to obtain a check digit

        $input = (string)$input;

        // first split up the string
        $numbers = str_split($input);

        // calculate the positional value.
        // when there is an even number of digits the second group will be multiplied, so p starts on 0
        // when there is an odd number of digits the first group will be multiplied, so p starts on 1
        $p = count($numbers) % 2;
        // run through each number
        foreach ($numbers as $i => $num) {
            $num = (int)$num;
            // every positional number needs to be multiplied by 2
            if ($p % 2) {
                $num = $num * 2;
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
