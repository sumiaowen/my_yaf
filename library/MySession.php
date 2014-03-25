<?php

/**
 * 简单 session 操作类
 * Created by PhpStorm.
 * User: sumiaowen
 * Contact: http://www.php230.com/
 * Date: 13-12-8
 * Time: 下午8:22
 * To change this template use File | Settings | File Templates.
 */
class MySession
{
	public $sessionStart = FALSE;

	public function start()
	{
		if(!$this->sessionStart)
		{
			session_start();
			$this->sessionStart = TRUE;
		}

		return $this->sessionStart;
	}

	public function set($key, $value, $lifeTime, $session_name = 'PHPSESSID')
	{
		ini_set('session.gc_maxlifetime', $lifeTime);
//		ini_set('session.save_path', '/tmp/test');

		session_name(md5($session_name));
		session_set_cookie_params($lifeTime);

		$this->start();

		$_SESSION[$key] = $value;
	}

	public function get($key)
	{
		$this->start();

		return isset($_SESSION[$key]) ? $_SESSION[$key] : FALSE;
	}

	public function destory()
	{
		$this->start();
		session_destroy();
	}
}