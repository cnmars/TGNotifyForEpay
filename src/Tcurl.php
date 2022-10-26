
<?php
class Tcurl{
	private static $TeleUrl = "https://api.telegram.org/bot";
	private static $BotToken = "";

	public static function Tpost($Method,$Params=null){
		if(is_null(self::$BotToken)){
			throw new \Exception("cURL error no BotToken", 700);
		}
		if(is_null($Method)){
			throw new \Exception("cURL error no Method", 700);
		}
		$turl = self::$TeleUrl . self::$BotToken . '/' . $Method;
        $Params = json_encode($Params);
        $C = curl_init();
        curl_setopt($C, CURLOPT_URL, $turl);
        curl_setopt($C, CURLOPT_POST, 1);
        curl_setopt($C, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf-8']);
        curl_setopt($C, CURLOPT_POSTFIELDS, $Params);
        curl_setopt($C, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($C, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($C, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3314.0 Safari/537.36 SE 2.X MetaSr 1.0');
    	curl_setopt($C, CURLOPT_ENCODING, "gzip");
    	curl_setopt($C, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($C, CURLOPT_TIMEOUT, 5);
        curl_setopt($C, CURLOPT_RETURNTRANSFER, 1);
        if($errno = curl_errno($C)) {
    		$error_message = curl_strerror($errno);
    		curl_close($C);
    		throw new \Exception("cURL error ({$errno}):\n {$error_message}", 701);
		}
        $R = curl_exec($C);
        curl_close($C);
        return $R;
	}

	public static function SetBotToken($bottoken){
		self::$BotToken = $bottoken;
	}
}
