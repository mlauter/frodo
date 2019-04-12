<?php declare(strict_types=1);

namespace Frodo;

use Frodo\Database\SQLite;

class Database {

    // Long url is a pretty large field to index
    // I don't think sqlite allows indexing a prefix of a text field
    // but if scalng the application, switch to a database that can,
    // or store a hash of the long url and index that
    private const INIT_SQL = [
        'CREATE TABLE IF NOT EXISTS `urls` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `long_url` text NOT NULL,
            `short_url` text NOT NULL,
            `create_date` unsigned integer NOT NULL,
            `hits` unsigned integer NOT NULL DEFAULT 0
        );',
        'CREATE UNIQUE INDEX IF NOT EXISTS `short_url_idx` ON `urls`(`short_url`);',
        'CREATE INDEX IF NOT EXISTS `hits_idx` ON `urls`(`hits`);',
    ];

    /** @var SQLite */
    public $sqlite;

    public function __construct(string $datafile) {
        $this->sqlite = new SQLite($datafile);

        // Initialize the database
        $this->sqlite->executeMultiInTxnNoParams(self::INIT_SQL);
    }

}
