<?php
require_once 'chat.php';

class server {

	private $server   = null;
	private $host     = '0.0.0.0';
	private $port     = '9501';
	private $taskName = 'swooleUploader';
	private $pathName = 'upload/';
	private $chatObj  = null;
	private $options  = [
		'work_num'  => 4,     //worker进程数,一般设置为CPU数的1-4倍
		'daemonize' => false, //是否启用守护进程
	];

	public function __construct(){
		$this->chatObj = new chat();
		$this->server  = new swoole_websocket_server($this->host, $this->port);
		$this->server->set($this->options);
		$this->server->on('open', [$this, 'onOpen']);
		$this->server->on('message', [$this, 'onMessage']);
		$this->server->on('close', [$this, 'onClose']);
		$this->server->start();
	}

	public function onOpen($server, $request){
		echo "connect success {$request->fd}\n";
		$server->push($request->fd, $this->returnMsg('test', [], 100));
	}

	public function onMessage($server, $frame){
		$this->chatObj->onMessage($server, $frame);
	}	

	public function onClose($server, $fd){
		$this->chatObj->onClose($server, $fd);
		echo "connect closed\n";
	}

	public function returnMsg($msg, $data, $type){
		$msg = [
			'msg'  => $msg,
			'data' => $data,
			'type' => $type
		];
		return json_encode($msg);
	}
}

new server();
