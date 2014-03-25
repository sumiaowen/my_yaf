<?php

/**
 * redis 简单操作类
 * Created by PhpStorm.
 * User: sumiaowen
 * Date: 13-12-20
 * Time: 下午2:54
 * To change this template use File | Settings | File Templates.
 */
class MyRedis
{
	public $host = NULL;
	public $port = NULL;
	static $redis = NULL;


	public function __construct()
	{
		$this->host = Yaf_Registry::get('config')->redis->host;
		$this->port = Yaf_Registry::get('config')->redis->port;
	}

	public function instance()
	{
		if(!self::$redis)
		{
			self::$redis = new redis();
			self::$redis->connect($this->host, $this->port);
		}

		return self::$redis;
	}


}