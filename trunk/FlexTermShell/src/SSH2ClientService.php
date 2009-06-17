<?php

require_once (dirname(dirname(__FILE__)).'/interfaces/ISSH2ClientBase.php');
require_once (dirname(dirname(__FILE__)).'/libs/Net/SSH2.php');

class SSH2ClientBase implements ISSH2ClientBase 
{	
	public function SSH2ClientBase()
	{
	}	
	
	public function tryConnectThenExec($host, $port = 22, $timeout = 10, $username, $password,$command) 
	{
		$MySSH2 = new Net_SSH2($host, $port, $timeout);
		if (!$MySSH2->login($username,$password)) {
			return "Failure";
		}else 
		{
			return $MySSH2->exec($command);
		}		
	}
}

?>