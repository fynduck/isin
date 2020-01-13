<?php

namespace Fynduck\ISIN;

use Fynduck\ISIN\Exception\InvalidISINException;

class ISIN
{
    const VALIDATION_LENGTH = 12;
    const VALIDATION_PATTERN = '/[A-Z]{2}[A-Z0-9]{9}[0-9]{1}/';

    /**
     * @var string
     */
    private $isin;

    /**
     * ISIN constructor.
     * @param string $input
     */
    public function __construct($input)
    {
        $this->isin = $input;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->isin;
    }

    /**
     * Returns only the check digit of the ISIN
     * @return int
     */
    public function getCheckDigit()
    {
        return (int)substr($this->isin, -1);
    }

    /**
     * Return the ISIN value as a string, for echoing
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * @param string $input
     * @return bool
     */
    public static function isValid($input)
    {
        try {
            self::validate($input);
        } catch (InvalidISINException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $input
     * @return string
     * @throws InvalidISINException
     */
    public static function validate($input)
    {
        $validator = new Validator();

        return $validator->validate($input);
    }

    /**
     * @param $input
     * @return null|int
     */
    public static function generateDigit($input)
    {
        $validator = new Validator();

        try {
            return $validator->generateDigit($input);
        } catch (InvalidISINException $e) {
            return null;
        }
    }
}
