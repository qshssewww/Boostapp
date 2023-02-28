<?php namespace Hazzard\Database;

use App\Utils\DebugBar;
use PDO;

class Connection {

	/**
	 * The active PDO connection.
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * The default fetch mode of the connection.
	 *
	 * @var int
	 */
	protected $fetchMode = PDO::FETCH_CLASS;

	/**
	 * The table prefix for the connection.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * Create a new connection instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct(array $config)
	{
		$this->pdo = $this->createConnection($config);

		$this->tablePrefix = $config['prefix'];

		/*$this->cache = new \stdClass(); // use in select line 123
		$this->debugSql = false;
		if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
			$this->debugSql = (!empty($_REQUEST['boostappSQL']) && $_REQUEST['boostappSQL']=='true' || !empty($_SERVER['HTTP_BOOSTAPPSQL']) && $_SERVER['HTTP_BOOSTAPPSQL'] =='true');
		}*/

	}

	/**
	 * Create a new PDO connection.
	 *
	 * @param  array   $config
	 * @return PDO
	 */
	public function createConnection(array $config)
	{
		extract($config);

		$dsn = "$driver:host={$hostname};dbname={$database}";

		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "set names {$charset} collate {$collation}"
		);

		return new PDO($dsn, $username, $password, $options);
	}

	/**
	 * Begin a fluent query against a database table.
	 *
	 * @param  string  $table
	 * @return \Hazzard\Database\Query
	 */
	public function table($table)
	{
		$query = new Query($this);

		return $query->from($table);
	}

	/**
	 * Get a new raw query expression.
	 *
	 * @param  mixed  $value
	 * @return \Hazzard\Database\Expression
	 */
	public function raw($value)
	{
		return new Expression($value);
	}

	/**
	 * Run a select statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return array
	 */
	public function select($query, $bindings = array())
	{
        $start = microtime(true);

        $statement = $this->getPdo()->prepare($query);
        $statement->execute($bindings);

        $executionTime = microtime(true) - $start;

        $this->collectDebugInfo($query, $bindings, $executionTime);

		return $statement->fetchAll($this->getFetchMode());
	}

	/**
	 * Run an insert statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return bool
	 */
	public function insert($query, $bindings = array())
	{
        $start = microtime(true);

        $statement = $this->statement($query, $bindings);

        $executionTime = microtime(true) - $start;

        $this->collectDebugInfo($query, $bindings, $executionTime);

        return $statement;
	}

	/**
	 * Run an update statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function update($query, $bindings = array())
	{
		return $this->affectingStatement($query, $bindings);
	}

	/**
	 * Run a delete statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function delete($query, $bindings = array())
	{
		return $this->affectingStatement($query, $bindings);
	}

	/**
	 * Execute an SQL statement and return the boolean result.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return bool
	 */
	public function statement($query, $bindings = array())
	{
		return $this->getPdo()->prepare($query)->execute($bindings);
	}

	/**
	 * Run an SQL statement and get the number of rows affected.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function affectingStatement($query, $bindings = array())
	{
        $start = microtime(true);

        $statement = $this->getPdo()->prepare($query);
        $statement->execute($bindings);

        $executionTime = microtime(true) - $start;

        $this->collectDebugInfo($query, $bindings, $executionTime);

        return $statement->rowCount();
	}

	/**
	 * Get the table prefix for the connection.
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Set the table prefix in use by the connection.
	 *
	 * @param  string  $prefix
	 * @return void
	 */
	public function setTablePrefix($prefix)
	{
		$this->tablePrefix = $prefix;
	}

	/**
	 * Get the PDO instance.
	 *
	 * @return PDO
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Get the default fetch mode for the connection.
	 *
	 * @return int
	 */
	public function getFetchMode()
	{
		return $this->fetchMode;
	}

	/**
	 * Set the default fetch mode for the connection.
	 *
	 * @param  int  $fetchMode
	 * @return int
	 */
	public function setFetchMode($fetchMode)
	{
		$this->fetchMode = $fetchMode;
	}

    /**
     * @param $query
     * @param $bindings
     * @param $executionTime
     * @return false|void
     */
    private function collectDebugInfo($query, $bindings, $executionTime) {
        if (!DebugBar::isEnabled()) {
            return false;
        }

        $keys = [];

        # build a regular expression for each parameter
        foreach ($bindings as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }

            if (!is_int($value) && !is_float($value)) {
                $bindings[$key] = $this->pdo->quote($value);
            }
        }

        $bt = debug_backtrace();
        $trace = [];
        foreach ($bt as $value) {
            // skip internal files from the backtrace
            if (isset($value['file']) && strpos($value['file'], 'Hazzard') === false && strpos($value['file'], 'Illuminate') === false) {
                $trace[] = $value['file'] . ':' . $value['line'];
            }

            // fix loop while we get options from DB
            if (isset($value['file']) && strpos($value['file'], 'Hazzard\Config\Repository') !== false) {
                return;
            }
        }

        $queryForLog = preg_replace($keys, $bindings, $query, 1);

        $data = [
            'time' => $executionTime,
            'type' => 'SELECT',
            'query' => $queryForLog,
            'trace' => $trace,
            'url' => $_SERVER['REQUEST_URI'] ?: null,
        ];

        DebugBar::put($data);
    }
}
