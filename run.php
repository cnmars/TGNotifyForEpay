#!/usr/bin/env php
<?php
require_once "./config.php";
require_once "./src/Medoo.php";
require_once "./src/Tcurl.php";

use Medoo\Medoo;

//加载数据库和redis
try{
	$database = new Medoo([
    	'database_type' => 'mysql',
    	'server' => $_ENV["servername"],
    	'port' => $_ENV["port"],
    	'database_name' => $_ENV["formname"],
    	'username' => $_ENV["username"],
    	'password' => $_ENV["passwd"]
	]);

	$redis = new Redis();
	$redis->connect($_ENV["redis_host"], $_ENV["redis_port"]);  
	$redis->ping();

}catch (Exception $e){
        echo $e->getMessage();
        die("Fail to connect database or redis!");
}

//设置机器人密钥
Tcurl::SetBotToken($_ENV["bottoken"]);

for($orderNo = $redis->rPop('orderno');$_ENV["number"] && $orderNo;$_ENV["number"]--){
	$curOrder = $database->select("pay_order",["uid","out_trade_no","type","name","getmoney","endtime","domain"],["trade_no"=>$orderNo]);
	$tgid = $redis->get($curOrder[0]['uid']);
    var_dump($curOrder);
	if($tgid){
		$messsage = "收款通知！\n尊敬的用户{$curOrder[0]['uid']}您好！\n内部订单号{$curOrder[0]['trade_no']}\n外部订单号{$curOrder[0]['out_trade_no']}\n到账金额{$curOrder[0]['getmoney']}\n付款时间{$curOrder[0]['endtime']}\n网站{$curOrder[0]['domain']}";
		Tcurl::Tpost("sendMessage",["chat_id"=>$tgid,"text"=>$messsage]);
		unset($messsage);
	}
	
	unset($tgid);
	unset($curOrder);
	$orderNo = $redis->rPop('orderno');
}
$redis->close();
