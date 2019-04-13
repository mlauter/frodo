<?php declare(strict_types=1);

namespace Frodo\Exception;

class DatabaseException extends \RuntimeException
{
    /** @var int  */
    private $db_error_code;

    public function __construct(string $msg, int $db_error_code)
    {
        $this->db_error_code = $db_error_code;
        parent::__construct($msg);
    }

    public function getDbErrorCode(): int
    {
        return $this->db_error_code;
    }
}
