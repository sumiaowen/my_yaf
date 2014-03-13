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

		$k = mt_rand(10,99);
		try
		{
			$mysql->begin();

			$mysql->exec("update test set k={$k} where id =1");
			$mysql->exec("update test set k={$k}2 where id =2");

			$mysql->commit();
		}
		catch(Exception $e)
		{
			$mysql->rollback();
			echo $e->getMessage();
		}

		echo '<pre>';
//		print_r($result);
		echo '</pre>';

		return false;
	}

	public function nameAction()
	{

		echo 'sdfsdfsdf';

		return false;
	}

}