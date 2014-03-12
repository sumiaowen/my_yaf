<?php

/**
 * 简易 memcache 操作类
 * Created by PhpStorm.
 * User: sumiaowen
 * Contact: http://www.php230.com/
 * Date: 13-12-1
 * Time: 上午12:05
 * To change this template use File | Settings | File Templates.
 */
class MyMemcache
{
	//memcache服务器地址
	static $host = null;

	//memcache服务器端口
	static $port = null;

	//memcahce实例
	static $memcache = null;

	public function __construct()
	{
		self::$host = Yaf_Registry::get('config')->cache->memcache->host;
		self::$port = Yaf_Registry::get('config')->cache->memcache->port;
	}

	/**
	 * 实例化 memcache 对象 & 连接memcache服务器
	 * @return object
	 */
	static function instance()
	{
		if(is_null(self::$memcache))
		{
			self::$memcache = new memcache;

			self::$memcache->connect(self::$host, self::$port);
		}

		return self::$memcache;
	}

	/**
	 * set 保存数据，如果 key 不存在，则新增； 如果 key 存在，则更新替换
	 * @param string $key    要设置值的key
	 * @param mixed  $var    要存储的值，字符串和数值直接存储，其他类型序列化后存储
	 * @param int    $expire 缓存有效期，单位为秒(0：表示永久有效)
	 * @return bool 成功时返回true,失败时返回false
	 */
	public function set($key, $var, $expire = 0)
	{
		self::$memcache || self::instance();

		return self::$memcache->set($key, $var, 0, $expire);
	}

	/**
	 * add 保存数据，如果 key 不存在，则新增； 如果 key 存在，返回false
	 * @param string $key    要设置值的key
	 * @param mixed  $var    要存储的值，字符串和数值直接存储，其他类型序列化后存储
	 * @param int    $expire 缓存有效期，单位为秒(0：表示永久有效)
	 * @return bool 成功时返回true; 失败时或者key已经存在时返回false
	 */
	public function add($key, $var, $expire = 0)
	{
		self::$memcache || self::instance();

		return self::$memcache->add($key, $var, 0, $expire);
	}

	/**
	 * replace 替换更新数据
	 * @param string $key    要设置值的key
	 * @param mixed  $var    要存储的值，字符串和数值直接存储，其他类型序列化后存储
	 * @param int    $expire 缓存有效期，单位为秒(0：表示永久有效)
	 * @return bool 成功时返回 true; 失败时 或者 key不存在时返回 false
	 */
	public function replace($key, $var, $expire = 0)
	{
		self::$memcache || self::instance();

		return self::$memcache->replace($key, $var, 0, $expire);
	}

	/**
	 * get 获取数据
	 * @param mixed $key
	 * @return mixed 返回key对应的存储元素的字符串值 或者 在失败或key未找到的时候返回FALSE。
	 */
	public function get($key)
	{
		self::$memcache || self::instance();

		return self::$memcache->get($key);
	}

	/**
	 * delete 删除一个元素
	 * @param mixed $key     要删除的元素的key
	 * @param int   $timeOut 删除该元素的执行时间。如果值为0,则该元素立即删除，如果值为30,元素会在30秒内被删除
	 * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE
	 */
	public function delete($key, $timeOut = 0)
	{
		self::$memcache || self::instance();

		return self::$memcache->delete($key, $timeOut);
	}

	/**
	 * flush 清洗（删除）已经存储的所有的元素
	 * 并不会真正的释放任何资源，而是仅仅标记所有元素都失效了，因此已经被使用的内存会被新的元素复写
	 * @return bool
	 */
	public function flush()
	{
		self::$memcache || self::instance();

		return self::$memcache->flush();
	}

	/**
	 * close 关闭memcache连接
	 * 该函数不会关闭持久化连接， 持久化连接仅仅会在web服务器关机/重启时关闭
	 * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE
	 */
	public function close()
	{
		self::$memcache || self::instance();

		return self::$memcache->close();
	}
}