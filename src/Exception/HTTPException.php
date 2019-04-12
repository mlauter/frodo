<?php declare(strict_types=1);

namespace Frodo\Exception;

use Frodo\HTTPResponse;

class HTTPException extends \Exception
{

    /** @var string */
    private $status_msg;

    /** @var int */
    private $status_code;

    public function __construct(string $status_msg, int $status_code = HTTPResponse::HTTP_STATUS_SERVER_ERROR)
    {
        $this->status_msg = $status_msg;
        $this->status_code = $status_code;

        parent::__construct();
    }

    public function getStatusMessage(): string
    {
        return $this->status_msg;
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }
}
