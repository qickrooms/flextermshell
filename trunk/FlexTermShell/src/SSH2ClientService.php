<?php

require_once (dirname(dirname(__FILE__)).'/interfaces/ISSH2ClientService.php');
require_once (dirname(dirname(__FILE__)).'/libs/Net/SSH2.php');
require_once (dirname(dirname(__FILE__)).'/dao/SSH2DAO.php');
require_once (dirname(dirname(__FILE__)).'/dao/SSH2ResultDAO.php');
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
		$asMuchAsEver = $ssh2dao->asMuchAsEver;
		
		UserData::$SSH2Data = $ssh2dao;
		
		UserData::$SSH2Conn = new Net_SSH2($host,$port,$timeout);
//		Log::log(LoggingConstants::INFO,$host);
		if (!UserData::$SSH2Conn->login($username,$password)) 
		{			
			return "Failure";
		}else 
		{
			$execResult = UserData::$SSH2Conn->exec($command);
			
			if($asMuchAsEver)
			{
				$mySSH2ResultDAO = new SSH2ResultDao();
				$mySSH2ResultDAO->CompressionAlgorithmsClient2Server = UserData::$SSH2Conn->getCompressionAlgorithmsClient2Server();
				$mySSH2ResultDAO->CompressionAlgorithmsServer2Client = UserData::$SSH2Conn->getCompressionAlgorithmsServer2Client();
				$mySSH2ResultDAO->DebugInfo = UserData::$SSH2Conn->getDebugInfo();
				$mySSH2ResultDAO->EncryptionAlgorithmsClient2Server = UserData::$SSH2Conn->getEncryptionAlgorithmsClient2Server();
				$mySSH2ResultDAO->EncryptionAlgorithmsServer2Client = UserData::$SSH2Conn->getEncryptionAlgorithmsServer2Client();
				$mySSH2ResultDAO->KexAlgorithms = UserData::$SSH2Conn->getKexAlgorithms();
				$mySSH2ResultDAO->LanguagesClient2Server = UserData::$SSH2Conn->getLanguagesClient2Server();
				$mySSH2ResultDAO->LanguagesServer2Client = UserData::$SSH2Conn->getLanguagesServer2Client();
				$mySSH2ResultDAO->Log = UserData::$SSH2Conn->getLog();
				$mySSH2ResultDAO->MACAlgorithmsClient2Server = UserData::$SSH2Conn->getMACAlgorithmsClient2Server();
				$mySSH2ResultDAO->MACAlgorithmsServer2Client = UserData::$SSH2Conn->getMACAlgorithmsServer2Client();
				$mySSH2ResultDAO->ServerHostKeyAlgorithms = UserData::$SSH2Conn->getServerHostKeyAlgorithms();
				$mySSH2ResultDAO->ServerIdentification = UserData::$SSH2Conn->getServerIdentification();
				$mySSH2ResultDAO->ServerPublicHostKey = UserData::$SSH2Conn->getServerPublicHostKey();
			}
			
			return array( "execResult"=>$execResult,
						  "asMuchAsEver"=>$mySSH2ResultDAO);
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