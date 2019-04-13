<?php
namespace InQuery;

use InQuery\Command;
use InQuery\Driver;
use InQuery\QueryBuilder;

use InQuery\Exceptions\InvalidConditionalException;
use InQuery\Exceptions\InvalidOrderException;
use InQuery\Exceptions\InvalidJoinException;

/**
 * Represents a single query.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Query
{
    /**
     * Define join types.
     */
    const JOIN_INNER = 'inner';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';
    const JOIN_OUTER = 'outer';

    /**
     * Define order types.
     */
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * Define query set fields.
     */
    const QUERY_SET_TABLE = 'table';
    const QUERY_SET_FIELDS = 'fields';
    const QUERY_SET_WHERE = 'where';
    const QUERY_SET_JOIN = 'join';
    const QUERY_SET_ORDER = 'order';

    /**
     * Define conditionals.
     */
    const EQ = '=';
    const NOT_EQ = '!=';
    const IN = 'in';
    const NOT_IN = 'not in';
    const GT = '>';
    const GT_EQ = '>=';
    const LT = '<';
    const LT_EQ = '<=';
    const IS_NULL = 'is null';

    /**
     * Define valid conditionals.
     * @var array
     */
    protected $conditionals = [
        self::EQUAL,
        self::NOT_EQ,
        self::IN,
        self::NOT_IN,
        self::GT,
        self::GT_EQ,
        self::LT,
        self::LT_EQ,
        self::IS_NULL
    ];

    /**
     * Define valid joins.
     * @var array
     */
    protected $joins = [
        self::JOIN_INNER,
        self::JOIN_LEFT,
        self::JOIN_RIGHT,
        self::JOIN_OUTER
    ];

    /**
     * Query execution data per-table.
     * @var array
     */
    protected $queryData = [];

    /**
     * Current query set.
     * @var int
     */
    protected $querySet = 0;

    /**
     * Instance of db driver.
     * @var Driver
     */
    protected $driver;

    /**
     * Instance of db query builder.
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * Instantiate a new instance.
     * @param Driver $driver
     * @param QueryBuilder $builder
     */
    public function __construct(Driver $driver, QueryBuilder $builder)
    {
        $this->driver = $driver;
        $this->builder = $builder;
    }

    /**
     * Sets the table we're querying against.
     * @param string $table
     * @return $this
     */
    public function table($table)
    {
        $this->queryData[$this->querySet][self::QUERY_SET_TABLE] = $table;
        $this->queryData[$this->querySet][self::QUERY_SET_FIELDS] = [];
        $this->queryData[$this->querySet][self::QUERY_SET_WHERE] = [];
        $this->queryData[$this->querySet][self::QUERY_SET_JOIN] = [];
        $this->queryData[$this->querySet][self::QUERY_SET_ORDER] = [];
        return $this;
    }

    /**
     * Sets query fields.
     * @param string $fields
     * @return $this
     */
    public function select(...$fields)
    {
        foreach ($fields as $field) {
            if (!in_array($field, $this->queryData[$this->querySet][self::QUERY_SET_FIELDS])) {
                $this->queryData[$this->querySet][self::QUERY_SET_FIELDS][] = $field;
            }
        }
        return $this;
    }

    /**
     * Sets singular where condition.
     * @param string $field
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @throws InvalidConditionalException
     */
    public function where($field, $value, $condition = null)
    {
        if ($condition !== null && !$this->validConditional($condition)) {
            throw new InvalidConditionalException("{$condition} is not a valid conditional.");
        }
        $this->queryData[$this->querySet][self::QUERY_SET_WHERE][] = [$field, $value, $condition];
        return $this;
    }

    /**
     * Sets sort order.
     * @param string $field
     * @param string $order
     * @return $this
     * @throws InvalidOrderException
     */
    public function order($field, $order)
    {
        $order = strtolower($order);
        if ($order !== self::ORDER_ASC && $order !== self::ORDER_DESC) {
            throw new InvalidOrderException("{$order} is not a valid order.");
        }
        $this->queryData[$this->querySet][self::QUERY_SET_ORDER][] = [$field, $order];
        return $this;
    }

    /**
     * Defines a join.
     * @param string $table
     * @param array $on
     * @param string $type
     * @return $this
     * @throws InvalidJoinException
     */
    public function join($table, array $on, $type = self::JOIN_INNER)
    {
        if (!$this->validJoin($type)) {
            throw new InvalidJoinException("${type} is not a valid join type.");
        }
        $this->queryData[$this->querySet][self::QUERY_SET_JOIN] = [$table, $on, $type];
        $this->querySet++;
        return $this->table($table);
    }

    /**
     * Returns query data.
     * @return array
     */
    public function getQueryData()
    {
        return $this->queryData;
    }

    /**
     * Returns whether or not a conditional is valid.
     * @param string $conditional
     * @return bool
     */
    protected function validConditional($conditional)
    {
        return in_array(strtolower($conditional), $this->conditionals);
    }

    /**
     * Returns whether or not a join is valid.
     * @param string $type
     * @return bool
     */
    protected function validJoin($type) {
        return in_array(strtolower($type, $this->joins));
    }

    /**
     * Execute the select query.
     * @param array $params
     * @param int $offset
     * @param int $limit
     * @return QueryResult
     * @throws 
     */
    public function get(array $params = [], $offset = Driver::OFFSET_DEFAULT, $limit = Driver::RETURNED_ROW_DEFAULT)
    {
        $command = $this->builder->selectQuery($this);
        return $this->driver->exec($command, $params, $limit, $offset);
    }
}
