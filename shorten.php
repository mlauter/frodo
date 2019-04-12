<?php declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

use Frodo\Validator\LongUrl;
use Frodo\Validator\ShortUrl;
use Frodo\Exception\ValidationException;
use Frodo\Exception\HTTPException;
use Frodo\HTTPResponse;

$long_url = isset($_REQUEST['longurl']) ? trim($_REQUEST['longurl']) : "";
$short_url = isset($_REQUEST['shorturl']) ? trim($_REQUEST['shorturl']) : "";

// Validate input
try {
    (new LongUrl($long_url))->validate();

    // Short url is optional
    if (!empty($short_url)) {
        (new ShortUrl($short_url))->validate();
    }

} catch (ValidationException $e) {
    throw new HTTPException($e->getErrorMessage(), HTTPResponse::HTTP_STATUS_BAD_REQUEST);
}

$shortener = new Frodo\Shorten();
$short_url = $shortener->getShortUrl($long_url, $short_url);

HTTPResponse::getInstance()->sendJSON(
    [
        'status' => 200,
        'response' => $short_url,
    ]
);
