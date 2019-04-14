<?php declare(strict_types=1);

namespace Frodo;

class HTTPResponse
{

    // Some greatest hits of http statuses
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_REDIRECT = 302;
    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_SERVER_ERROR = 500;

    const CONTENT_TYPE_JSON = 'application/json';

    private static $response;

    private function __construct()
    {
    }

    public static function getInstance(): HTTPResponse
    {
        if (!isset(self::$response)) {
            self::$response = new self();
        }

        return self::$response;
    }

    public function setStatus(int $code)
    {
        http_response_code($code);
    }

    /**
     * Set headers
     * Note: probably will want to implement removing header too
     *
     * @throws \RuntimeException
     */
    public function setHeader(string $key, string $val, int $code = 200)
    {
        if (headers_sent()) {
            throw new \RuntimeException("Headers already sent");
        }

        header("$key: $val", true, $code);
    }

    /** @throws \RuntimeException */
    public function setContentType(string $type)
    {
        if (empty($type)) {
            throw new \RuntimeException("Cannot set empty content type");
        }

        $this->setHeader('Content-type', self::CONTENT_TYPE_JSON);
    }

    /** @throws \RuntimeException */
    public function sendJSON(array $data)
    {
        $json = json_encode($data);

        if ($json === false) {
            $msg = 'Failed to encode json';
            \Monolog\Registry::logger()->error($msg . ' ' . print_r($data, true));
            throw new \RuntimeException($msg);
        }

        echo $json;
    }

    public function redirect(string $loc)
    {
        $this->setHeader('Location', $loc, self::HTTP_STATUS_REDIRECT);
    }
}
