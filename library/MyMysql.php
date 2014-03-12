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

	public function __construct($database = 'default')
	{
		self::$dns      = Yaf_Registry::get('config')->db->$database->dns;
		self::$username = Yaf_Registry::get('config')->db->$database->username;
		self::$password = Yaf_Registry::get('config')->db->$database->password;
	}

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

		return $tmp;
	}

	/**
	 * 预处理执行 update、delete、insert SQL语句
	 * @param sting $sql
	 * @param array $parameters
	 * @return int 影响函数
	 */
	public function execution($sql, $parameters = array())
	{
		self::$pdo || self::instance();

		$stmt = self::$pdo->prepare($sql);
		$stmt->execute($parameters);

		return $stmt->rowCount();
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

		if(self::$pdo->exec($sql))
		{
			return $this->getLastInsertId();
		}

		return 0;
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
	 * 关闭链接
	 */
	public function close()
	{
		self::$pdo = null;
	}
}