<?php declare(strict_types=1);

namespace Frodo;

use Frodo\Exception\HTTPException;
use Frodo\HTTPResponse;

class ErrorHandler
{

    private static $initialized = false;

    public static function init()
    {
        if (self::$initialized) {
            return;
        }

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        self::$initialized = true;
    }

    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ) {

        if (!error_reporting()) {
            return;
        }

        $is_error = $errno & (E_ERROR | E_USER_ERROR);
        $is_warning = $errno & (E_WARNING | E_RECOVERABLE_ERROR);

        $backtrace = ($is_error || $is_warning) ? debug_backtrace() : null;
        $formatted = self::getFormattedError($errstr, $errfile, $errline, $backtrace);

        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
                \Monolog\Registry::logger()->error($formatted);
                break;
            case E_WARNING:
            case E_RECOVERABLE_ERROR:
                \Monolog\Registry::logger()->warning($formatted);
                break;
            case E_ALL:
                \Monolog\Registry::logger()->info($formatted);
                break;
            default:
                \Monolog\Registry::logger()->debug($formatted);
        }

        if ($is_error) {
            header('HTTP/1.1 500 Internal Server Error');
            exit(0);
        }
    }

    public static function handleException(\Throwable $e)
    {
        \Monolog\Registry::logger()->error(
            self::getFormattedError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace())
        );

        $response = HTTPResponse::getInstance();

        if ($e instanceof HTTPException) {
            $response->setStatus($e->getStatusCode());
            try {
                $response->sendJSON(
                    [
                        'status' => $e->getStatusCode(),
                        'reason' => $e->getStatusMessage(),
                    ]
                );
            } catch (\RuntimeException $e) {
                echo 'Unknown error';
            }
            exit(0);
        }

        $response->setStatus(HTTPResponse::HTTP_STATUS_SERVER_ERROR);
        echo 'Server error';
        exit(0);
    }

    public static function getFormattedError(
        string $msg,
        string $errfile = null,
        int $errline = null,
        array $backtrace = null
    ): string {

        if ($errfile && $errline) {
            $msg .= ' at ' . $errfile . ' line ' . $errline . '.';
        }

        if ($backtrace) {
            $msg .= ' Stack trace: ' . print_r($backtrace, true);
        }

        return $msg;
    }
}
