<?php

require_once (dirname(dirname(__FILE__)).'/interfaces/ISSH2ClientService.php');
require_once (dirname(dirname(__FILE__)).'/libs/Net/SSH2.php');
require_once (dirname(dirname(__FILE__)).'/dao/SSH2DAO.php');
require_once (dirname(dirname(__FILE__)).'/dao/SSH2ResultDAO.php');
require_once (dirname(dirname(__FILE__)).'/dao/SSH2ResultDetailDAO.php');
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
		
		$mySSH2ResultDAO = new SSH2ResultDAO();
		$mySSH2ResultDAO->execResult =  "Failure";
		$mySSH2ResultDAO->asMuchAsEver = new SSH2ResultDetailDAO();
		
		UserData::$SSH2Data = $ssh2dao;
		
		UserData::$SSH2Conn = new Net_SSH2($host,$port,$timeout);
		
//		Log::log(LoggingConstants::INFO,$host);
		if (!UserData::$SSH2Conn->login($username,$password)) 
		{			
			return $mySSH2ResultDAO;
		}else 
		{
			$execResult = UserData::$SSH2Conn->exec($command);
			$mySSH2ResultDAO->execResult =  $execResult;	
			
			if($asMuchAsEver)
			{	

				$mySSH2ResultDAO->asMuchAsEver->DebugInfo = UserData::$SSH2Conn->getDebugInfo();
				$mySSH2ResultDAO->asMuchAsEver->Log = UserData::$SSH2Conn->getLog();
				$mySSH2ResultDAO->asMuchAsEver->ServerIdentification = UserData::$SSH2Conn->getServerIdentification();
				
				$mySSH2ResultDAO->asMuchAsEver->KexAlgorithms = implode(";",UserData::$SSH2Conn->getKexAlgorithms());
				$mySSH2ResultDAO->asMuchAsEver->CompressionAlgorithmsClient2Server = implode(";",UserData::$SSH2Conn->getCompressionAlgorithmsClient2Server());
				$mySSH2ResultDAO->asMuchAsEver->CompressionAlgorithmsServer2Client = implode(";",UserData::$SSH2Conn->getCompressionAlgorithmsServer2Client());
				
				$mySSH2ResultDAO->asMuchAsEver->EncryptionAlgorithmsClient2Server = implode(";",UserData::$SSH2Conn->getEncryptionAlgorithmsClient2Server());
				$mySSH2ResultDAO->asMuchAsEver->EncryptionAlgorithmsServer2Client = implode(";",UserData::$SSH2Conn->getEncryptionAlgorithmsServer2Client());
				
				$mySSH2ResultDAO->asMuchAsEver->LanguagesClient2Server = implode(";",UserData::$SSH2Conn->getLanguagesClient2Server());
				$mySSH2ResultDAO->asMuchAsEver->LanguagesServer2Client = implode(";",UserData::$SSH2Conn->getLanguagesServer2Client());
				
				$mySSH2ResultDAO->asMuchAsEver->MACAlgorithmsClient2Server = implode(";",UserData::$SSH2Conn->getMACAlgorithmsClient2Server());
				$mySSH2ResultDAO->asMuchAsEver->MACAlgorithmsServer2Client = implode(";",UserData::$SSH2Conn->getMACAlgorithmsServer2Client());
				$mySSH2ResultDAO->asMuchAsEver->ServerHostKeyAlgorithms = implode(";",UserData::$SSH2Conn->getServerHostKeyAlgorithms());
				
				$mySSH2ResultDAO->asMuchAsEver->ServerPublicHostKey = implode(";",UserData::$SSH2Conn->getServerPublicHostKey());
				
				return $mySSH2ResultDAO;
			}		
			return $mySSH2ResultDAO;
		}			
	}	
}

?>