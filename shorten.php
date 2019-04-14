<?php declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

use Frodo\Validator\LongUrl;
use Frodo\Validator\ShortUrl;
use Frodo\Exception\ValidationException;
use Frodo\Exception\InUseException;
use Frodo\Exception\HTTPException;
use Frodo\HTTPResponse;

$long_url = isset($_REQUEST['longurl']) ? trim($_REQUEST['longurl']) : "";
$short_url = isset($_REQUEST['shorturl']) ? trim($_REQUEST['shorturl']) : "";

$datafile = $GLOBALS['server_config']['db']['file'];

try {
    $short_url = (new Frodo\Shortener($datafile))->shorten($long_url, $short_url);
} catch (ValidationException $e) {
    throw new HTTPException($e->getErrorMessage(), HTTPResponse::HTTP_STATUS_BAD_REQUEST);
} catch (InUseException $e) {
    throw new HTTPException("This short url is taken", HTTPResponse::HTTP_STATUS_BAD_REQUEST);
} catch (\Exception $e) {
    throw new HTTPException("Something went wrong - please try again");
}

HTTPResponse::getInstance()->sendJSON(
    [
        'status' => 200,
        'response' => BASE_HREF . $short_url,
    ]
);
