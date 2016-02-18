<?php
/**
 * PUSH MVC Framework.
 * @package PUSH MVC Framework
 * @version See PUSH.json
 * @author See PUSH.json
 * @copyright See PUSH.json
 * @license See PUSH.json
 * PUSH MVC Framework is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See PUSH.json for copyright notices and details.
 */

namespace Push;

abstract Class Model {
	protected static $init, $table;
	protected $db, $tb, $columns, $inputs;

	final public function __construct($database){
		$this->db = $database;
		$this->tb_prefix = \Push\Application::init()->config('db')['db_prefix'];
		// $this->pagination = $app->get('pagination');
		// $this->app = $app;
	}

	final public function __call($method, $args){
		if(stripos($method, 'set') === 0){
			$col = strtolower(ltrim($method, 'set'));
			return call_user_func_array([$this, 'set'], [$col, (isset($args[0]) ? $args[0] : NULL), (isset($args[1]) ? $args[1] : NULL)]);
		}
	}

	final protected function tb($table = null){
		return $this->tb_prefix.($table === null ? static::$table : $table);
	}

	final protected function col($column, $table = null){
		return ($table ? $table : static::$table).'_'.$column;
	}

	final public function set($column, $value, $table = null){
		$this->columns[($table ? $table : static::$table).'_'.$column] = $this->inputs[$column] = $value;
		// $this->inputs[$column] = $value;
		return $this;
	}
	final public function get($column, $table = null){
		return $this->columns[($table ? $table : static::$table).'_'.$column];
	}
	final public function columns(){
		return $this->columns;
	}
	final public function inputs(){
		return $this->inputs;
	}


	/* trigger an event */
	public function create($params = null){
		return $this->db->insert($this->tb(), $this->columns());
	}
	/* trigger an event */
	public function update(){}

	/* trigger an event */
	public function delete($conditions = []){
		if(is_array($conditions)){
			foreach ($conditions as $k => $v) {
				$this->set($k, $v);
			}
		}
		return $this->db->delete($this->tb(), $this->columns(), 1);
	}

	/* trigger an event */
	/**
	 * @todo first param should by columns to fetch
	 */
	public function fetch($offset = 1, $limit = 20){
		return $this->db
		->select($this->tb(),'*',' ORDER BY '.$this->col('id').' DESC'.($limit?' LIMIT '.($offset? ($offset-1).', '.$limit:$limit):''))
		->fetchAll(FETCH_OBJECT);
	}

	public function fetchAs($columns, $offset = 1, $limit = 20){
		if(is_array($columns)){
			foreach ($columns as $k => $v) {
				$this->set($k.' '.$v, null);
			}
		}
		return $this->db
		->select($this->tb(),array_keys($this->columns()),' ORDER BY '.$this->col('id').' DESC'.($limit?' LIMIT '.($offset? ($offset-1).', '.$limit:$limit):''))
		->fetchAll(FETCH_OBJECT);
	}

	/* trigger an event */
	/**
	 * Create new row in Database
	 * @param  boolean $inputParams If set to true, returns array of `id` and input parameter names and values
	 * @param  boolean $inputParams If set to false, returns array of `id` and input column names and values
	 * @return array
	 */
	/**
	 * @todo Make this method return INSERT;SELECT last add row
	 */
	public function save($inputParams = false){
		// alert($this->inputs(), $this->columns());
		// alert(array('id'=>$this->create()) + $this->columns());
		// alert(array('id'=>$this->create()) + ($inputParams === true ? $this->inputs() : $this->columns()));
		return ['id'=>$this->create()] + ($inputParams === true ? $this->inputs() : $this->columns());
		// $this->create();
		// return $this;
	}
}
