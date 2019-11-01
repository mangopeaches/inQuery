<?php
namespace InQuery\QueryBuilders;

use InQuery\QueryBuilder;
use InQuery\Queries\FindQuery;

/**
 * Translates input values into mongo format.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MongoQueryBuilder implements QueryBuilder
{
    /**
     * Prepares a find command for execution.
     * @param string $dataSet data set against which to perform the query
     * @param array $conditions (optional) array of query conditions
     * @param array $fields (optional) fields to return, all if omittied
     * @param array $order (optional) fields to order by
     * @param array $options (optional) options to be passed through as query params
     * @return Command
     */
    public function find($dataSet, array $conditions = [], array $fields = [], array $order = [], array $options = [])
    {
        return (new Query())->find($dataSet, $conditions, $fields, $order, $options);
    }

    /**
     * Builds the condition set into query format.
     * @param array $conditions (optional)
     * @return array
     */
    private static function buildConditions(array $conditions = [])
    {

    }

    /**
     * Builds the fields set into query format.
     * @param array $fields (optional)
     * @return array
     */
    private static function buildFields(array $fields = [])
    {
        
    }

    /**
     * Builds set of order fields into order by format.
     * @param array $order (optional)
     * @return array
     */
    private static function buildOrder(array $order = [])
    {

    }

    /**
     * Builds options set into options format.
     * @param array $options (optional)
     * @return array
     */
    private static function buildOptions(array $options = [])
    {

    }

    /**
     * Builds query object into mongo query commands.
     * @param Query $query
     * @return Command
     */
    public function build(Query $query)
    {
        // process the query set and build it into an execution string
        $queryValues = [];
        while ($querySet = $query->next()) {
            switch ($querySet['type']) {

            }
        }
        return new Command($queryValues);
    }
}
