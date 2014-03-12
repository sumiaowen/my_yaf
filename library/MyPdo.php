<?php
/**
 * 简单 PDO mysql 操作类
 * Created by PhpStorm.
 * User: sumiaowen
 * Contact: http://www.php230.com/
 * Date: 13-11-30
 * Time: 下午10:33
 * To change this template use File | Settings | File Templates.
 */
class MyPdo
{
	private $dns = null;
	private $username = null;
	private $password = null;
	private $conn = null;

	public function __construct($database = 'default')
	{
		$this->dns      = Yaf_Registry::get('config')->db->$database->dns;
		$this->username = Yaf_Registry::get('config')->db->$database->username;
		$this->password = Yaf_Registry::get('config')->db->$database->password;

		$this->_connect();
	}

	private function _connect()
	{
		try
		{
			$this->conn = new PDO($this->dns, $this->username, $this->password);
			$this->conn->query('set names utf8');
		}
		catch(PDOException $e)
		{
			exit('PDOException: ' . $e->getMessage());
		}
	}

	/**
	 * 查询
	 * @param string $sql
	 * @param array  $parameters 需要绑定的参数
	 * @param int    $option
	 * @return array
	 */
	public function query($sql, $parameters = array(), $option = PDO::FETCH_ASSOC)
	{
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($parameters);

		$tmp = array();
		while($row = $stmt->fetch($option))
		{
			$tmp[] = $row;
		}

		return $tmp;
	}

	/**
	 * 增、删、改
	 * @param string $sql
	 * @param array  $parameters
	 * @return int  返回影响行数
	 */
	public function execution($sql, $parameters = array())
	{
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($parameters);

		return $stmt->rowCount();
	}

	/**
	 * 返回最后插入行的ID
	 * @return mixed
	 */
	public function getLastInsertId()
	{
		return $this->conn->lastInsertId();
	}
}