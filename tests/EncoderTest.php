<?php declare(strict_types=1);

namespace Frodo\Tests;

use PHPUnit\Framework\TestCase;
use Frodo\Encoder;

class EncoderTest extends TestCase
{

    /** @dataProvider provide_testEncode */
    public function testEncode(int $input, string $expected)
    {
        $this->assertEquals($expected, (new Encoder())->encode($input));
    }

    /** @dataProvider provide_testEncode */
    public function testDecode(int $input, string $ignore)
    {
        $e = new Encoder();
        $this->assertEquals($input, $e->decode($e->encode($input)));
    }

    /**
     * @dataProvider provide_testEncodeThrowsOnNonPosInteger
     * @expectedException \RuntimeException
     */
    public function testEncodeThrowsOnNonPosInteger(int $input)
    {
        (new Encoder())->encode($input);
    }

    function provide_testEncode(): array
    {
        return [
            [1, 'b'],
            [64, 'ba'],
            [65, 'bb'],
            [75, 'bl'],
            [1000, 'pO'],
            [10000, 'cCq'],
            [9223372036854775807, "h__________"],
        ];
    }

    function provide_testEncodeThrowsOnNonPosInteger(): array
    {
        return [
            [0],
            [-1],
            [-10],
        ];
    }
}
