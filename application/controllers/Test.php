<?php

/**
 * Created by PhpStorm.
 * User: sumiaowen
 * Date: 13-12-16
 * Time: 21:24
 * To change this template use File | Settings | File Templates.
 */
class TestController extends Yaf_Controller_Abstract
{


	public function indexAction()
	{
		$mysql = new MyMysql();

		$sql = "insert into keywords2(keyword) values('232323')";

		$result = $mysql->exec($sql);

		echo '<pre>';
		print_r($result);
		echo '</pre>';

		return false;
	}

	public function nameAction()
	{

		echo 'sdfsdfsdf';

		return false;
	}

}