<?php
class myRedis {
	private static $instance = null;
	private $redis = null;
	private $host = '127.0.0.1';
	private $port = 6379;

	private function __construct(){
		try {
			$this->redis = new Redis();
			$isCon       = $this->redis->connect($this->host, $this->port);
			if (empty($isCon)) {
				throw new Exception("Redis connect error");
			}
			if (!empty($this->auth)) {
				$this->redis->auth($this->auth);
			}

		} catch (Exception $e) {
			echo $e->errorMessage();
		}
	}

	public static function getInstance(){
		if (!(self::$instance instanceof self)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 获取reids对象
	 * @author kevinlee 2018-08-14T10:59:14+0800
	 * @return object
	 */
	public function getRedis(){
		return $this->redis;
	}

	private function __clone(){
		self::$instance->redis->close();
		self::$instance = null;
	}
}

