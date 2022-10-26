#!/usr/bin/env php
<?php
define ('VERIONS', '1.0.2');
define ('APP_PATH', __DIR__); 
define ('APP_URL', rtrim (dirname ($_SERVER['SCRIPT_NAME']), DIRECTORY_SEPARATOR));
define ('SRC', APP_PATH . '/src');
require_once APP_PATH."/config.php";
require_once SRC."/Medoo.php";
require_once SRC."/Tcurl.php";

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
date_default_timezone_set("Asia/Shanghai");
$delay=strtotime("-".$_ENV["dtime"]." minutes");


$curOrder = $database->select('pay_order',["uid","out_trade_no","type","name","getmoney","endtime","domain"],['endtime[>=]'=>date("Y-m-d h:i:s",$delay),"param" =>NULL,'LIMIT' => $_ENV["number"]]);

for($count = 0;(!empty($curOrder)) && $count < $_ENV["number"];$count++){
	$tgid = $redis->get($curOrder[$count]['uid']);
	if($tgid){
	    $database->update('pay_order',["param" => 1],["out_trade_no"=>$curOrder[$count]['out_trade_no']]);
	    var_dump($curOrder[$count]);

		switch ($curOrder[$count]['type']) {
			case '1':
				$type = "支付宝";
				break;
			case '2':
				$type = "微信";
				break;			
			default:
				$type = "未知通道";
				break;
		}
		$message = "💰 收款通知 💰\n-----------------------\n到账金额: {$curOrder[$count]['getmoney']}\n支付方式: {$type}\n订单编号: {$curOrder[$count]['out_trade_no']}";
		Tcurl::Tpost("sendMessage",["chat_id"=>$tgid,"text"=>$message]);
		unset($message);

	}
	unset($curOrder[$count]);		
	unset($tgid);
}
$redis->close();