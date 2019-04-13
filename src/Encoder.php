<?php declare(strict_types=1);

namespace Frodo;

class Encoder
{

    // Custom url safe and readable charset
    const CHARSET = "_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";

    public function encode(int $id): string
    {
        if ($id <= 0) {
            throw new \RuntimeException("encode expects a positive integer, got {$id}");
        }

        $n = strlen(self::CHARSET);

        $encoded = '';
        while ($id !== 0) {
            $remainder = $id % $n;
            $quotient = intdiv($id, $n);
            $encoded = self::CHARSET[$remainder] . $encoded;
            $id = $quotient;
        }

        return $encoded;
    }

    /** @throws \RuntimeException */
    public function decode(string $str): int
    {
        $n = strlen(self::CHARSET);

        $decoded = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $c = $str[-($i+1)];

            $pos = strpos(self::CHARSET, $c); // @phan-suppress-current-line PhanParamSuspiciousOrder
            if ($pos === false) {
                throw new \RuntimeException("decode encountered invalid character {$c}");
            }

            // to improve, would be more time efficient
            // to have a bidirectional character map
            $decoded += $pos * pow($n, $i);
        }

        return $decoded;
    }
}
