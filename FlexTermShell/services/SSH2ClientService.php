<?php

require_once (dirname(dirname(__FILE__)).'/interfaces/ISSH2ClientService.php');
require_once (dirname(dirname(__FILE__)).'/libs/Net/SSH2.php');
require_once (dirname(dirname(__FILE__)).'/dao/SSH2DAO.php');

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/WebOrb/Util/Logging/Log.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/WebOrb/Util/Logging/LoggingConstants.php');

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
		
		$MySSH2 = new Net_SSH2($host,$port,$timeout);
		Log::log(LoggingConstants::INFO,$host);
		if (!$MySSH2->login($username,$password)) 
		{			
			return "Failure";
		}else 
		{
			return $MySSH2->exec($command);
		}		
	}
	
}

?>