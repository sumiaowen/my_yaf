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
		$session = new MySession();

		$data = array('one' => 111, 'two' => 222);

//		$session->set('test',$data,60);

		var_dump($session->get('test'));

		return FALSE;
	}

	public function nameAction()
	{
		session_start();
		var_dump($_SESSION);

		return FALSE;
	}

	public function destroyAction()
	{
		session_start();
		session_destroy();

		return FALSE;
	}

}