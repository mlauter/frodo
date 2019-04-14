<?php declare(strict_types=1);

namespace Frodo;

use Frodo\Validator\LongUrl;
use Frodo\Validator\ShortUrl;
use Frodo\Database;
use Frodo\Encoder;

class Shortener
{

    /** @var Database */
    private $db;

    public function __construct(string $datafile)
    {

        $this->db = new Database($datafile, new Encoder());
    }

    public function shorten(string $long_url, string $short_url): string
    {

        // Validate input
        (new LongUrl($long_url))->validate();

        // Short url is optional
        if (!empty($short_url)) {
            (new ShortUrl($short_url))->validateCustom();
        }

        if (!empty($short_url)) {
            return $this->db->createCustomShortUrl($long_url, $short_url);
        } else {
            return $this->db->findOrCreateAutoShortUrl($long_url);
        }
    }

    public function lengthen(string $short_url): string
    {

        (new ShortUrl($short_url))->validate();

        return $this->db->findByShortUrl($short_url);
    }
}
