<?php declare(strict_types=1);

namespace Frodo\Validator;

use Frodo\Exception\ValidationException;

class LongUrl
{

    const PARAM_NAME = 'longurl';

    /** @var string */
    private $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @throws ValidationException
     */
    public function validate()
    {
        if (empty($this->raw)) {
            throw new ValidationException(
                self::PARAM_NAME,
                ValidationException::ERROR_EMPTY
            );
        }

        if (!preg_match('|^https?://|', $this->raw) ||
            !filter_var($this->raw, FILTER_VALIDATE_URL) !== false) {
            throw new ValidationException(
                self::PARAM_NAME,
                ValidationException::ERROR_INVALID,
                'not a valid url'
            );
        }
    }
}
