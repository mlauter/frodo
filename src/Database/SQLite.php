<?php declare(strict_types=1);

namespace Frodo\Database;

use Frodo\Exception\DatabaseException;

class SQLite
{

    /**
     * Add consts for error codes as needed
     * @see https://www.sqlite.org/c3ref/constlist.html
     */
    public const SQLITE_CONSTRAINT = 19;

    private const PARAM_STR = "?";

    /** @var string SQLite database file*/
    private $datafile;

    /** @var \SQLite3 A singleton SQLite3 client */
    private $client;

    public function __construct(string $datafile)
    {
        $this->datafile = $datafile;
        // Initialize the db
    }

    /**
     * @param string $sql Sql statement with placeholders (e.g. :param)
     * @param array<int,array{0:mixed,1:int}> $params Array of value, type tuples
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function executeSingle(string $sql, array $params = []): array
    {

        // No multi-statement passages, can't ensure they're parameterized
        if (preg_match('/;\s*\S/', $sql)) {
            throw new \InvalidArgumentException("executeSingle requires exactly one statement");
        }

        // Any unbound parameters will silently be set to NULL
        if (substr_count($sql, self::PARAM_STR) !== count($params)) {
            $msg = sprintf(
                "Query expects %d params, got %d",
                substr_count($sql, '?'),
                count($params)
            );
            throw new \InvalidArgumentException($msg);
        }

        $client = $this->getClient();
        if (count($params) > 0) {
            $stmt = $client->prepare($sql);

            if (!$stmt) {
                $this->throwLastError();
            }

            foreach ($params as $idx => [$val, $type]) {
                if (is_string($val)) {
                    $val = \SQLite3::escapeString($val);
                }
                // Positional parameters start with 1
                $stmt->bindValue($idx + 1, $val, $type);
            }

            // To improve: error handling
            $result = @$stmt->execute();
        } else {
            $result = $client->query($sql);
        }

        $rows = [];
        if ($result) {
            if ($result->numColumns()) {
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $rows[] = $row;
                }
            }
            $result->finalize();
        } else {
            $this->throwLastError();
        }

        return $rows;
    }

    /**
     * Execute multiple parameterized statements in a transaction
     * @param string[] $sqls A list of parameterized sql statements
     * @param array<int,array<int,array{0:mixed,1:int}>> $params An array of param arrays
     *
     * @throws \Exception
     * @return array
     */
    public function executeMulti(array $sqls, array $params): array
    {

        if (count($sqls) !== count($params)) {
            throw new \InvalidArgumentException("executeMulti requires a param array per sql statement");
        }

        $results = [];
        foreach ($sqls as $idx => $sql) {
            $results[] = $this->executeSingle($sql, $params[$idx]);
        }

        return $results;
    }

    public function executeMultiNoParams(array $sqls): array
    {
        return $this->executeMulti($sqls, array_fill(0, count($sqls), []));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function runInTransaction(callable $fn, array $args = [])
    {
        $client = $this->getClient();

        if (!$client->exec('BEGIN;')) {
            throw new \RuntimeException("Unable to begin transaction");
        };

        try {
            $results = call_user_func_array($fn, $args);
            if (!$client->exec('COMMIT;')) {
                $this->throwLastError();
            }
        } catch (\Exception $e) {
            // To improve: rollback could fail too
            $client->exec('ROLLBACK;');
            throw $e;
        }

        return $results;
    }

    public function lastInsertRowID(): int
    {
        return $this->getClient()->lastInsertRowID();
    }

    private function getClient() : \SQLite3
    {
        if (!isset($this->client)) {
            $this->client = new \SQLite3($this->datafile);
        }

        return $this->client;
    }

    /**
     * @throws \RuntimeException
     */
    private function throwLastError(): void
    {
        $code = $this->getClient()->lastErrorCode();
        $msg = $this->getClient()->lastErrorMsg();
        throw new DatabaseException("Database error: {$msg} ({$code})", $code);
    }
}
