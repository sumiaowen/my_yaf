<?php
/**
 * Created by PhpStorm.
 * User: sumiaowen
 * Date: 15/7/24
 * Time: 23:25
 */
class Test_TestModel
{
	private static $db;

	public function __construct()
	{
		self::$db = new MyMysql(FALSE, 'default');
	}

	public function __destruct()
	{
		self::$db->close();
	}

	public function index()
	{
		$sql = "select * from test";
		$result = self::$db->query($sql);

		echo '<pre>';
		var_dump($result);

		return 'test_test_model';
	}
}