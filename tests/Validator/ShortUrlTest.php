<?php declare(strict_types=1);

namespace Frodo\Tests;

use PHPUnit\Framework\TestCase;
use Frodo\Validator\ShortUrl;

class ShortUrlTest extends TestCase
{

    /** @dataProvider provide_testValidateGood */
    public function testValidateGood(string $input)
    {
        $this->assertTrue((new ShortUrl($input))->validate());
    }

    /**
     * @dataProvider provide_testValidateCustomBad
     * @expectedException Frodo\Exception\ValidationException
     */
    public function testValidateCustomBad(string $input)
    {
        (new ShortUrl($input))->validateCustom();
    }

    /**
     * @dataProvider provide_testValidateBad
     * @expectedException Frodo\Exception\ValidationException
     */
    public function testValidateBad(string $input)
    {
        (new ShortUrl($input))->validate();
    }

    function provide_testValidateGood(): array
    {
        return [
            ['a'],
            ['abcdef'],
            ['-_BZ0'],
            ['1234-'],
        ];
    }

    function provide_testValidateBad(): array
    {
        return [
            ['~'],
            ['@.@'],
            ['<html>foo</html>'],
            ['foo=bar'],
            ['foo=bar&baz=zap'],
            ['a/b'],
            ['a.com'],
        ];
    }

    function provide_testValidateCustomBad(): array
    {
        return [
            ['a'],
            ['B'],
            ['abcdefghijklmnopqrstu'],
            ['123456'],
        ];
    }
}
