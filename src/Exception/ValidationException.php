<?php declare(strict_types=1);

namespace Frodo\Exception;

class ValidationException extends \Exception
{

    const ERROR_EMPTY         = 0;
    const ERROR_INVALID       = 1;
    const ERROR_TOO_SHORT     = 2;
    const ERROR_TOO_LONG      = 3;
    const ERROR_ILLEGAL_CHARS = 4;

    const ERROR_MESSAGES = [
        self::ERROR_EMPTY => "cannot be empty",
        self::ERROR_INVALID => "is not valid",
        self::ERROR_TOO_SHORT => "is too short",
        self::ERROR_TOO_LONG => "is too long",
        self::ERROR_ILLEGAL_CHARS => "contains illegal chars",
    ];

    const DEFAULT_ERROR_MESSAGE = "had unknown validation error";

    /** @var int */
    private $error_code;

    /** @var string */
    private $param_name;

    /** @var ?string */
    private $extra_msg;

    /**
     * Messages should be safe for returning to the client
     */
    public function __construct(string $param_name, int $error_code, string $extra_msg = null)
    {
        $this->error_code = $error_code;
        $this->param_name = $param_name;
        $this->extra_msg  = $extra_msg;

        parent::__construct();
    }

    public function getErrorCode(): int
    {
        return $this->error_code;
    }

    public function getErrorMessage(): string
    {
        $base_msg = self::ERROR_MESSAGES[$this->error_code] ?? self::DEFAULT_ERROR_MESSAGE;
        if (isset($this->extra_msg)) {
            $base_msg .= ": {$this->extra_msg}";
        }

        return $this->param_name . ' ' . $base_msg;
    }
}
