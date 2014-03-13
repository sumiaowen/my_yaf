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
	static $dns = null;

	//mysql 用户名
	static $username = null;

	//mysql 密码
	static $password = null;

	//pdo 链接实例
	static $pdo = null;

	//调试
	public $debug = null;

	/**
	 * @param bool   $debug    是否开启调试，错误信息输出
	 * @param string $database 数据库类别
	 */
	public function __construct($debug = true, $database = 'default')
	{
		$this->debug    = $debug;
		self::$dns      = Yaf_Registry::get('config')->db->$database->dns;
		self::$username = Yaf_Registry::get('config')->db->$database->username;
		self::$password = Yaf_Registry::get('config')->db->$database->password;
	}

	/**
	 * PDO对象实例化
	 * @return null|PDO
	 */
	static function instance()
	{
		if(is_null(self::$pdo))
		{
			try
			{
				self::$pdo = new PDO(self::$dns, self::$username, self::$password);
				self::$pdo->query('set names utf8');
			}
			catch(PDOException $e)
			{
				exit('PDOException: ' . $e->getMessage());
			}
		}

		return self::$pdo;
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
		self::$pdo || self::instance();

		$stmt = self::$pdo->prepare($sql);
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
		self::$pdo || self::instance();

		$stmt = self::$pdo->prepare($sql);
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
		self::$pdo || self::instance();

		$rows = self::$pdo->exec($sql);

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
		self::$pdo || self::instance();

		$fields = '`' . implode('`,`', array_keys($data)) . '`';

		$values = "'" . implode("','", $data) . "'";

		$sql = "INSERT INTO `{$tableName}`({$fields}) VALUES ({$values})";

		self::$pdo->exec($sql);

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
		self::$pdo || self::instance();

		$fields = '`' . implode('`,`', array_keys($data[0])) . '`';

		$tmp = array();
		foreach($data as $value)
		{
			$tmp[] = "'" . implode("','", $value) . "'";
		}

		$values = "(" . implode("),(", $tmp) . ")";

		$sql = "INSERT INTO `{$tableName}`({$fields}) VALUES {$values}";

		$rows = self::$pdo->exec($sql);

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
		self::$pdo || self::instance();

		//条件
		$whereId    = array_keys($where);
		$whereValue = array_values($where);

		$tmp = array();
		foreach($data as $key => $value)
		{
			$tmp[] = "`{$key}`='{$value}'";
		}

		$data = implode(',', $tmp);

		$sql = "UPDATE `{$tableName}` SET {$data} WHERE `{$whereId[0]}`='{$whereValue[0]}'";

		$rows = self::$pdo->exec($sql);

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
		self::$pdo || self::instance();

		//条件
		$whereId    = array_keys($where);
		$whereValue = array_values($where);

		$sql = "DELETE FROM `{$tableName}` WHERE `{$whereId[0]}`='{$whereValue[0]}'";

		$rows = self::$pdo->exec($sql);

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
		self::$pdo || self::instance();

		return self::$pdo->lastInsertId();
	}

	/**
	 * 设置错误信息
	 */
	public function error($stmt = '')
	{
		$error = $stmt ? $stmt->errorInfo() : self::$pdo->errorInfo();

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
	 * 关闭链接
	 */
	public function close()
	{
		self::$pdo = null;
	}
}