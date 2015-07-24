<?php

/**
 * Created by PhpStorm.
 * User: sumiaowen
 * Date: 13-12-18
 * Time: 22:40
 * To change this template use File | Settings | File Templates.
 */
class Test_TestController extends Yaf_Controller_Abstract
{

	//访问URL：http://www.yaf.com/test_test/index
	public function IndexAction()
	{
		echo 'test_test_controller<br>';

		//测试test_test_model
		$test_test_model = new Test_TestModel();
		echo $test_test_model->index();

		return false;
	}
}