<?php
namespace Fynduck\isin\Test;

use Fynduck\isin\Validator;
use PHPUnit_Framework_TestCase;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Validator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new Validator();
    }

    /**
     * @dataProvider okProvider
     */
    public function testOK($input)
    {
        $this->assertSame($input, $this->validator->validate($input));
    }

    public function okProvider()
    {
        return [
            ['AA0000000006'], // manual checksum check (not real isin)
            ['ZZ9876543215'], // manual checksum check (not real isin)
            ['US0378331005'],
            ['GB00B3W23161'],
            ['XS1103310618'],
            ['USG4634UAV47'],
            ['US404280AF65'],
            ['AU0000XVGZA3'],
            ['AU0000VXGZA3'], // this is a flaw in the ISIN system (V&X are transposed), but this code should handle it
        ];
    }

    public function testIsConvertedToUpperCase()
    {
        $original = 'gb00b3w23161';
        $expected = 'GB00B3W23161';

        $this->assertSame($expected, $this->validator->validate($original));
    }

    /**
     * @expectedException \Fynduck\ISIN\Exception\InvalidISINException
     * @expectedExceptionMessage ISIN Input was not a string
     * @dataProvider nonStringProvider
     */
    public function testNotAString($input)
    {
        $this->validator->validate($input);
    }

    public function nonStringProvider()
    {
        return [
            [0],
            [1.2],
            [new \StdClass],
            [[]],
        ];
    }

    /**
     * @expectedException \Fynduck\ISIN\Exception\InvalidISINException
     * @expectedExceptionMessage ISIN Input was not the correct length. Must be 12 characters
     * @dataProvider wrongLengthProvider
     */
    public function testWrongLength($input)
    {
        $this->validator->validate($input);
    }

    public function wrongLengthProvider()
    {
        return [
            ['GB00B3W231610'],
            ['GB00B3W2316'],
            [''],
        ];
    }

    /**
     * @expectedException \Fynduck\ISIN\Exception\InvalidISINException
     * @expectedExceptionMessage ISIN Input contained invalid characters. Must be A-Z and 0-9 with AAXXXXXXXXX#
     * @dataProvider invalidCharacterProvider
     */
    public function testInvalidCharacter($input)
    {
        $this->validator->validate($input);
    }

    public function invalidCharacterProvider()
    {
        return [
            ['GB00B3W2316!'],
            ['GB00B3W2316#'],
            ['GB00B3 W2316'],
            ['G000B3W23161'], // doesn't match pattern
            ['GB00B3W2316A'], // doesn't match pattern
            ['0A00B3W23161'], // doesn't match pattern
        ];
    }

    /**
     * @expectedException \Fynduck\ISIN\Exception\InvalidISINException
     * @expectedExceptionMessage ISIN Input failed checksum validation
     * @dataProvider invalidChecksumProvider
     */
    public function testInvalidChecksum($input)
    {
        $this->validator->validate($input);
    }

    public function invalidChecksumProvider()
    {
        return [
            ['GB00B3W23162'],
        ];
    }
}
