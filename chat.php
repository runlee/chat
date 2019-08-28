<?php
require_once 'redis.php';
class chat {

	private $redis = null;
	private $redisUserList = 'redisUserList';

	public function __construct(){
		$this->redis = myRedis::getInstance()->getRedis();
	}

	public function onMessage($server, $frame){
		$data = json_decode($frame->data, true);

		// 1 设置登陆状态，并返回登陆用户列表
		if ($data['type'] == 1) {
			$this->setUserOnline($data['data'], $frame->fd);
			$list      = $this->getUserList($data['data'], $frame->fd);
			$returnMsg = $this->returnMsg('login success', ['user_list' => $list], $data['type']);
		}elseif ($data['type'] == 2) {// 2 用户聊天发送信息
			$this->sendMsgToUser($server, $data['data'], $data['type']);
			$returnMsg = $this->returnMsg('send success', [''], $data['type']);
		}

		$server->push($frame->fd, $returnMsg);
	}

	public function onClose($server, $fd){
		$this->setUserOffline($fd);
	}

	private function returnMsg($msg, $data, $type){
		$msg = [
			'msg'  => $msg,
			'data' => $data,
			'type' => $type
		];
		return json_encode($msg);
	}

	/**
	 * 发送信息
	 * @author Kevinlee 2019-08-28T15:55:00+0800
	 * @param  [type] $server [description]
	 * @param  [type] $data   [description]
	 * @return [type]         [description]
	 */
	public function sendMsgToUser($server, $data, $type){
		$to = $data['to'];
		$toFd = $this->redis->ZSCORE($this->redisUserList, $to);
		$msg = $this->returnMsg('send success', $data, 2);
		$server->push($toFd, $msg);
	}

	/**
	 * 获取剔除自己的在线用户列表
	 * @author Kevinlee 2019-08-27T14:19:11+0800
	 * @return [type] [description]
	 */
	public function getUserList($userName, $fd){
		$userList = $this->redis->ZRANGE($this->redisUserList, 0, -1, 'WITHSCORES');

		if (empty($userList)) {
			return [];
		}

		$list = [];
		foreach ($userList as $key => $value) {
			if ($key != $userName && $value != $fd) {
				$list[] = $key;
			}
		}

		return $list;
	}

	/**
	 * 设置用户在线
	 * @author Kevinlee 2019-08-27T14:47:16+0800
	 * @param  [type] $userName [description]
	 * @param  [type] $fd       [description]
	 */
	public function setUserOnline($userName, $fd){
		$this->redis->zadd($this->redisUserList, $fd, $userName);
	}

	/**
	 * 设置用户下线
	 * @author Kevinlee 2019-08-27T14:55:12+0800
	 * @param  [type] $fd [description]
	 */
	public function setUserOffline($fd){
		$a = $this->redis->ZREMRANGEBYSCORE($this->redisUserList, $fd, $fd);
		echo "close string: {$fd}\n";
	}
}
