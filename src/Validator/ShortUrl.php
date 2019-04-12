<?php declare(strict_types=1);

namespace Frodo\Validator;

use Frodo\Exception\ValidationException;

class ShortUrl
{

    const PARAM_NAME = 'longurl';

    const PATTERN = '/[a-zA-Z0-9\-]*/';

    /** @var string */
    private $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /** @throws ValidationException */
    public function validate()
    {
        if (strlen($this->raw) <= 6) {
            throw new ValidationException(
                self::PARAM_NAME,
                ValidationException::ERROR_TOO_SHORT,
                'must be great than 6 characters long'
            );
        }

        if (strlen($this->raw) > 20) {
            throw new ValidationException(
                self::PARAM_NAME,
                ValidationException::ERROR_TOO_LONG,
                'must be 20 characters or less'
            );
        }

        if (preg_match(self::PATTERN, $this->raw) !== 1) {
            throw new ValidationException(
                self::PARAM_NAME,
                ValidationException::ERROR_ILLEGAL_CHARS,
                "must contain only the characters a-z, A-Z, 0-9, and '-'"
            );
        };
    }
}
