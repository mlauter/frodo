<?php declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

use Frodo\Validator\ShortUrl;
use Frodo\Stats;
use Frodo\Exception\ValidationException;
use Frodo\Exception\NotFoundException;
use Frodo\Exception\HTTPException;
use Frodo\HTTPResponse;

$short_url = isset($_REQUEST['shorturl']) ? trim($_REQUEST['shorturl']) : "";


$datafile = $GLOBALS['server_config']['db']['file'];

try {
    $stats = [];
    if (empty($short_url)) {
        $stats = (new Frodo\Stats($datafile))->getGlobalStats();
    } else {
        $stats = (new Frodo\Stats($datafile))->getStats($short_url);
    }
} catch (ValidationException $e) {
    throw new HTTPException($e->getErrorMessage(), HTTPResponse::HTTP_STATUS_BAD_REQUEST);
} catch (NotFoundException $e) {
    throw new HTTPException("Not found", HTTPResponse::HTTP_STATUS_NOT_FOUND);
} catch (\Exception $e) {
    throw new HTTPException("Something went wrong - please try again");
}

HTTPResponse::getInstance()->sendJSON(
    [
        'status' => 200,
        'response' => $stats,
    ]
);
