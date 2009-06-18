<?php

require_once (dirname(dirname(__FILE__)).'/interfaces/ISSH2ClientService.php');
require_once (dirname(dirname(__FILE__)).'/libs/Net/SSH2.php');
require_once (dirname(dirname(__FILE__)).'/dao/SSH2DAO.php');
require_once (dirname(dirname(__FILE__)).'/util/UserData.php');

//require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/WebOrb/Util/Logging/Log.php');
//require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/WebOrb/Util/Logging/LoggingConstants.php');

class SSH2ClientService implements ISSH2ClientService
{	
	public function SSH2ClientService()
	{
	}	
	
	public function tryConnectThenExec(SSH2DAO $ssh2dao) 
	{
		$host = trim($ssh2dao->host);
		$port = $ssh2dao->port;
//		echo is_numeric($port);
		$timeout = $ssh2dao->timeout;
//		echo is_numeric($timeout);
		$username = $ssh2dao->username;
		$password = $ssh2dao->password;
		$command = $ssh2dao->command;
		
		UserData::$SSH2Data = $ssh2dao;
		
		UserData::$SSH2Conn = new Net_SSH2($host,$port,$timeout);
//		Log::log(LoggingConstants::INFO,$host);
		if (!UserData::$SSH2Conn->login($username,$password)) 
		{			
			return "Failure";
		}else 
		{
//			print_r(UserData::$SSH2Conn->getLog());
			return UserData::$SSH2Conn->exec($command);
		}		
	}
	
	public function tryConnect(SSH2DAO $ssh2dao)
	{
		UserData::$SSH2Data = $ssh2dao;
		
		$host = trim($ssh2dao->host);
		$port = $ssh2dao->port;
		$timeout = $ssh2dao->timeout;
		
		UserData::$SSH2Conn = new Net_SSH2( $host,$port,$timeout );
	}
	
	public function tryLogin(SSH2DAO $ssh2dao)
	{
		$username = $ssh2dao->username;
		$password = $ssh2dao->password;
		
		if (!UserData::$SSH2Conn->login( $username,$password )) 
		{			
			return "Failure";
		}else 
		{
			return $this->tryExec("ls");
		}	
	}
	
	public function tryExec(SSH2DAO $ssh2dao)
	{
		$command = $ssh2dao->command;
		return UserData::$SSH2Conn->exec( $command );
	}
	
}

?>