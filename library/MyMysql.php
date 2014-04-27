<?php

/**
 * mysql PDO 操作类
 * Created by PhpStorm.
 * User: sumiaowen
 * Date: 14-3-12
 * Time: 下午4:57
 * To change this template use File | Settings | File Templates.
 */
class MyMysql
{
	//pdo 链接 mysql dns
	public $dns = NULL;

	//mysql 用户名
	public $username = NULL;

	//mysql 密码
	public $password = NULL;

	//pdo
	protected $pdo = NULL;

	//对象实例化
	static $instance = array();

	//数据库
	public $database = NULL;

	//调试
	public $debug = NULL;

	//开始事务
	private $_begin_transaction = FALSE;

	/**
	 * @param bool   $debug    是否开启调试，错误信息输出
	 * @param string $database 数据库类别
	 */
	public function __construct($debug = TRUE, $database = 'default')
	{
		$this->debug    = $debug;
		$this->database = $database;
		$this->dns      = Yaf_Registry::get('config')->db->$database->dns;
		$this->username = Yaf_Registry::get('config')->db->$database->username;
		$this->password = Yaf_Registry::get('config')->db->$database->password;
	}

	/**
	 * @param $database
	 * @param $dns
	 * @param $username
	 * @param $password
	 * @return mixed
	 */
	static function instance($database, $dns, $username, $password)
	{
		if(empty(self::$instance[$database]))
		{
			try
			{
				self::$instance[$database] = new PDO($dns, $username, $password);
				self::$instance[$database]->query('set names utf8');
			}
			catch(PDOException $e)
			{
				exit('PDOException: ' . $e->getMessage());
			}
		}

		return self::$instance[$database];
	}

	/**
	 * 预处理执行 select sql语句
	 * @param string $sql
	 * @param array  $parameters
	 * @param int    $option
	 * @return array
	 */
	public function query($sql, $parameters = array(), $option = PDO::FETCH_ASSOC)
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($parameters);

		$tmp = array();
		while($row = $stmt->fetch($option))
		{
			$tmp[] = $row;
		}

		if($this->debug)
		{
			$this->error($stmt);
		}

		return $tmp;
	}

	/**
	 * 预处理执行 update、delete、insert SQL语句
	 * @param sting $sql
	 * @param array $parameters
	 * @return int 返回影响行数
	 */
	public function execute($sql, $parameters = array())
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($parameters);

		if($this->debug)
		{
			$this->error($stmt);
		}

		return $stmt->rowCount();
	}

	/**
	 * 执行一条SQL语句
	 * @param string $sql
	 * @return int 返回影响行数
	 */
	public function exec($sql)
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		$rows = $this->pdo->exec($sql);

		if($this->debug)
		{
			$this->error();
		}

		return $rows;
	}

	/**
	 * 添加一条记录
	 * @param string $tableName 数据库表名
	 * @param array  $data      需要添加的数据，一个 key|value 对应的数组，其中key为表字段名称,value为插入的值，如：$data = array('keyword'=>'关键词')
	 * @return int 返回插入行的ID
	 */
	public function insert($tableName, $data)
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		$fields = '`' . implode('`,`', array_keys($data)) . '`';

		$values = implode(',', array_fill(0, count($data), '?'));

		$sql = "INSERT INTO `{$tableName}`({$fields}) VALUES ({$values})";

		$this->execute($sql, array_values($data));

		if($this->debug)
		{
			$this->error();
		}

		return $this->getLastInsertId();
	}

	/**
	 * 添加多条数据
	 * @param string $tableName 数据库表名
	 * @param array  $data      需要添加的数据，为一个二维数组，如：$data = array(array('fileld1'=>'value1','fileld2'=>'value2'),array('fileld1'=>'value1','fileld2'=>'value2'))
	 * @return int 返回影响行数
	 */
	public function insertBatch($tableName, $data)
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		$fields = '`' . implode('`,`', array_keys($data[0])) . '`';

		$tmp  = array();
		$tmp2 = array();
		foreach($data as $value)
		{
			$tmp[] = implode(',', array_fill(0, count($value), '?'));

			foreach($value as $v)
			{
				$tmp2[] = $v;
			}
		}

		$values = "(" . implode("),(", $tmp) . ")";

		$sql = "INSERT INTO `{$tableName}`({$fields}) VALUES {$values}";

		$rows = $this->execute($sql, $tmp2);

		if($this->debug)
		{
			$this->error();
		}

		return $rows;
	}

	/**
	 * 根据主键更新数据
	 * @param string $tableName 数据库表名
	 * @param array  $where     更新条件，为 key|value 对应的数组，如：array('id'=>233)
	 * @param array  $data      更新数据，为 key|value 对应的数组，如：array('field1'=>'value1','field12'=>'value2')
	 * @return int 成功返回影响行数，失败返回错误信息
	 */
	public function updateByPrimaryKey($tableName, $where, $data)
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		//条件
		$whereId    = array_keys($where);
		$whereValue = array_values($where);

		$tmp = array();
		foreach($data as $key => $value)
		{
			$tmp[] = "`{$key}`= ?";
		}

		$set = implode(',', $tmp);

		$sql = "UPDATE `{$tableName}` SET {$set} WHERE `{$whereId[0]}`='{$whereValue[0]}'";

		$rows = $this->execute($sql, array_values($data));

		if($this->debug)
		{
			$this->error();
		}

		return $rows;
	}

	/**
	 * 根据主键删除数据
	 * @param string $tableName 数据库表名
	 * @param array  $where     删除条件，为 key|value 对应的数组，如：array('id'=>233)
	 * @return int 成功返回影响行数，失败返回错误信息
	 */
	public function deleteByPrimaryKey($tableName, $where)
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		//条件
		$whereId    = array_keys($where);
		$whereValue = array_values($where);

		$sql = "DELETE FROM `{$tableName}` WHERE `{$whereId[0]}`='{$whereValue[0]}'";

		$rows = $this->pdo->exec($sql);

		if($this->debug)
		{
			$this->error();
		}

		return $rows;
	}

	/**
	 * 返回最后插入行的ID或序列值
	 * @return int
	 */
	public function getLastInsertId()
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		return $this->pdo->lastInsertId();
	}

	/**
	 * 设置错误信息
	 */
	public function error($stmt = '')
	{
		$error = $stmt ? $stmt->errorInfo() : $this->pdo->errorInfo();

		$msg = "SQLSTATE:{$error[0]}";

		if($error[1])
		{
			$msg .= " - ERRORCODE:{$error[1]}";
		}

		if($error[2])
		{
			$msg .= " - ERROR:{$error[2]}";
		}

		if($error[1] || $error[2])
		{
			exit($msg);
		}
	}

	/**
	 * 事务开始
	 * @return bool
	 */
	public function begin()
	{
		$this->pdo || $this->pdo = self::instance($this->database, $this->dns, $this->username, $this->password);

		//已经有事务，退出事务
		$this->rollback();

		if(!$this->pdo->beginTransaction())
		{
			return FALSE;
		}

		return $this->_begin_transaction = TRUE;
	}

	/**
	 * 事务提交
	 * @return bool
	 */
	public function commit()
	{
		if($this->_begin_transaction)
		{
			$this->_begin_transaction = FALSE;
			$this->pdo->commit();
		}

		return TRUE;
	}

	/**
	 * 事务回滚
	 * @return bool
	 */
	public function rollback()
	{
		if($this->_begin_transaction)
		{
			$this->_begin_transaction = FALSE;
			$this->pdo->rollback();
		}

		return FALSE;
	}

	/**
	 * 关闭链接
	 */
	public function close()
	{
		$this->pdo = NULL;
	}
}