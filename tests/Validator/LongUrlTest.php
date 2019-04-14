<?php declare(strict_types=1);

namespace Frodo\Tests;

use PHPUnit\Framework\TestCase;
use Frodo\Validator\LongUrl;

class LongUrlTest extends TestCase
{

    /** @dataProvider provide_testValidateGood */
    public function testValidateGood(string $input)
    {
        $this->assertTrue((new LongUrl($input))->validate());
    }

    /**
     * @dataProvider provide_testValidateBad
     * @expectedException Frodo\Exception\ValidationException
     */
    public function testValidateBad(string $input)
    {
        (new LongUrl($input))->validate();
    }

    function provide_testValidateGood(): array
    {
        return [
            ['https://example.com/././foo'],
            ['http://testsite.test/<script>alert("TEST");</script>'], // validation does not strip out potentially malicious things
            ['http://example.com/?foo=bar&baz=zap'],
        ];
    }

    function provide_testValidateBad(): array
    {
        return [
            ['https:example.org'],
            ['https://////example.com///'],
            ['http:example.org'],
            ['https://ex ample.org/'],
            ['file:///C|/foo'],
            ['example'],
        ];
    }
}
