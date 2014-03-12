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

		$data = array('keyword'=>'1111');
//		$data[] = array('keyword'=>'1111');
//		$data[] = array('keyword'=>'2222');
//		$data[] = array('keyword'=>'3333');
//		$data[] = array('keyword'=>'4444');

		$result = $mysql->insert('keywords2',$data);

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