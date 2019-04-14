<?php declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

use Frodo\Validator\ShortUrl;
use Frodo\Exception\ValidationException;
use Frodo\Exception\HTTPException;
use Frodo\Exception\NotFoundException;
use Frodo\HTTPResponse;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    throw new HTTPException("Not found", HTTPResponse::HTTP_STATUS_NOT_FOUND);
}

$path = $_SERVER['PATH_INFO'];
$exploded = explode("/", $path);
if (count($exploded) > 2) {
    throw new HTTPException("Not found", HTTPResponse::HTTP_STATUS_NOT_FOUND);
}

// to improve, allow passing through query params using short url
$short_url = $exploded[1];

$datafile = $GLOBALS['server_config']['db']['file'];

try {
    $long_url = (new Frodo\Shortener($datafile))->lengthen($short_url);
} catch (ValidationException $e) {
    throw new HTTPException("Invalid short url", HTTPResponse::HTTP_STATUS_BAD_REQUEST);
} catch (NotFoundException $e) {
    throw new HTTPException("Not found", HTTPResponse::HTTP_STATUS_NOT_FOUND);
} catch (\Exception $e) {
    throw new HTTPException("Something went wrong - please try again");
}

// do redirect
HTTPResponse::getInstance()->redirect($long_url);
exit(0);
