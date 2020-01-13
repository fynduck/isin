<?php
namespace Fynduck\ISIN\Test;

use Fynduck\ISIN\ISIN;
use PHPUnit_Framework_TestCase;

class ISINTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $isin = 'AA0000000006';
        $isinObj = new ISIN($isin);

        $this->assertSame($isin, $isinObj->getValue());
        $this->assertSame($isin, (string) $isinObj);
        $this->assertSame(6, $isinObj->getCheckDigit());
    }

    /**
     * @expectedException \Fynduck\ISIN\Exception\InvalidISINException
     * @expectedExceptionMessage ISIN Input was not a string
     */
    public function testConstructThrowsInvalid()
    {
        new ISIN(0);
    }

    public function testValidateReturns()
    {
        $input = 'AA0000000006';
        $output = ISIN::validate($input);
        $this->assertSame($input, $output);

        $output = ISIN::validate('aa0000000006');
        $this->assertSame($input, $output);
    }

    /**
     * @expectedException \Fynduck\ISIN\Exception\InvalidISINException
     * @expectedExceptionMessage ISIN Input was not a string
     */
    public function testValidateThrows()
    {
        ISIN::validate(0);
    }

    /**
     * @dataProvider isValidProvider
     */
    public function testIsValid($input, $expected)
    {
        $this->assertEquals($expected, ISIN::isValid($input));
    }

    public function isValidProvider()
    {
        return [
            ['AA0000000006', true],
            ['aa0000000006', true],
            ['AA0000000001', false],
            ['', false],
            [0, false],
        ];
    }
}
