<?php
require_once "./config.php";
require_once "./src/Medoo.php";

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

$pid = $_REQUEST['pid'];
$key = $_REQUEST['key'];
$tgid = $_REQUEST['tgid'];
$type = $_REQUEST['type'];
if(preg_match('/[a-zA-Z0-9_]/', $pid) && preg_match('/[a-zA-Z0-9_]/', $key) && preg_match('/[a-zA-Z0-9_]/', $tgid )){
	$userinfo = $database->has("pay_user",['uid'=>$pid,'key'=>$key]);
	if($userinfo){
	    switch($type){
	        case 'add':
                $redis->set($pid,$tgid);
                echo "设置成功";
	            break;
	        case 'del':
                $redis->delete($pid);
                echo "删除成功";
	            break;
	    }
	}else{
		echo "密钥或者商户号不正确";
	}
}else{
	echo "非法输入";
}
$redis->close();
