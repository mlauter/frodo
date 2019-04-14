<?php declare(strict_types=1);

namespace Frodo\Tests;

use PHPUnit\Framework\TestCase;
use Frodo\Shortener;

class ShortenerTest extends TestCase
{

    /** @var string */
    private $datafile;

    public function setUp()
    {
        $this->datafile = $GLOBALS['server_config']['db']['file'];
    }
    public function tearDown()
    {
        $rm_cmd = 'rm ' . escapeshellarg($this->datafile);
        exec($rm_cmd);
    }

    public function testShortenAuto()
    {
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            ''
        );
        $this->assertEquals('b', $s);
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.example.com?foo=bar',
            ''
        );
        $this->assertEquals('c', $s);
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.google.com',
            ''
        );
        $this->assertEquals('d', $s);
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            ''
        );
        $this->assertEquals('b', $s);
    }

    public function testShortenCustom()
    {
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            'example'
        );
        $this->assertEquals('example', $s);
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            'foooooo'
        );
        $this->assertEquals('foooooo', $s);
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            ''
        );
        $this->assertEquals('d', $s);
        $s = (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            'barrrrr'
        );
        $this->assertEquals('barrrrr', $s);
    }

    public function testLengthen()
    {
        (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            ''
        );
        (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            'example'
        );
        $this->assertEquals(
            'http://www.example.com',
            (new Shortener($this->datafile))->lengthen('b')
        );
        $this->assertEquals(
            'http://www.example.com',
            (new Shortener($this->datafile))->lengthen('example')
        );
    }

    /** @expectedException Frodo\Exception\InUseException */
    public function testShortenThrowsOnDupeShortUrl()
    {
        (new Shortener($this->datafile))->shorten(
            'http://www.example.com',
            'example'
        );
        (new Shortener($this->datafile))->shorten(
            'http://www.google.com',
            'example'
        );
    }

    /** @expectedException Frodo\Exception\NotFoundException */
    public function testLengthenThrowsOnNotFound()
    {
        (new Shortener($this->datafile))->lengthen('b');
    }
}
