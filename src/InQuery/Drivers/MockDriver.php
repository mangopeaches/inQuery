<?php
namespace InQuery\Drivers;

use InQuery\Driver;
use InQuery\{QueryResult};
use InQuery\Drivers\BaseDriver;
use InQuery\Exceptions\DatabaseConnectionException;
use InQuery\Exceptions\DatabaseException;
use InQuery\Exceptions\DependencyException;
use InQuery\Queries\FindQuery;
use InQuery\QueryResults\MockQueryResult;
use InQuery\QueryBuilders\MockQueryBuilder;
use InQuery\Command;

/**
 * Mock database driver for tests.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MockDriver extends BaseDriver implements Driver
{
    /**
     * Define constants.
     */
    const NAME = 'mock';

    /**
     * Establishes a connection to the database.
     * @return void
     * @throws DependencyException
     * @throws DatabaseConnectionException
     */
    public function connect(): void
    {
        return;
    }

    /**
     * Executes query and returns result.
     * @param Command $command
     * @param array $params
     * @param int $offset
     * @param int $limit
     * @return QueryResult
     */
    public function exec(
        Command $command,
        array $params = [],
        int $offset = self::OFFSET_DEFAULT,
        int $limit = self::RETURNED_ROW_DEFAULT
    ): QueryResult {
        return new MockQueryResult();
    }
}
