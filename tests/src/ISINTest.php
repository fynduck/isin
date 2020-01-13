<?php
namespace Fynduck\isin\Test;

use Fynduck\isin\Isin;
use PHPUnit_Framework_TestCase;

class ISINTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $isin = 'AA0000000006';
        $isinObj = new Isin($isin);

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
        new Isin(0);
    }

    public function testValidateReturns()
    {
        $input = 'AA0000000006';
        $output = Isin::validate($input);
        $this->assertSame($input, $output);

        $output = Isin::validate('aa0000000006');
        $this->assertSame($input, $output);
    }

    /**
     * @expectedException \Fynduck\ISIN\Exception\InvalidISINException
     * @expectedExceptionMessage ISIN Input was not a string
     */
    public function testValidateThrows()
    {
        Isin::validate(0);
    }

    /**
     * @dataProvider isValidProvider
     */
    public function testIsValid($input, $expected)
    {
        $this->assertEquals($expected, Isin::isValid($input));
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
