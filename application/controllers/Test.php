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
		$data = array('one' => 1111, 'two' => 222);

		$session = new MySession();

//		$session->destory();

		$session->set('one','111',60);

		echo $session->get('one');

		echo '<pre>';
//		print_r($result);
		echo '</pre>';

		return false;
	}

	public function nameAction()
	{
		session_start();
		var_dump($_SESSION);

		return false;
	}

	public function destroyAction()
	{
		session_start();
		session_destroy();

		return false;
	}

}