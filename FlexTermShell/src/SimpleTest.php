<?php
	require_once (dirname(__FILE__).'/SSH2ClientService.php');
	require_once (dirname(dirname(__FILE__)).'/util/UserData.php');
	
	$MySSH2DAO = new SSH2DAO();
	$MySSH2DAO->host = "x4100.unix-center.net";
	$MySSH2DAO->port = 22;
	$MySSH2DAO->timeout = 10;
	$MySSH2DAO->username = "godpaper";
	$MySSH2DAO->password = "KiT7740321";
	$MySSH2DAO->command = "pwd";
	
	$MySSH2ClientService = new SSH2ClientService();
	echo $MySSH2ClientService->tryConnectThenExec($MySSH2DAO);
	echo "\t";
	echo UserData::$SSH2Conn->login($MySSH2DAO->username,$MySSH2DAO->password);
	echo "\t";
	echo UserData::$SSH2Conn->exec($MySSH2DAO->command);
	class SimpleTest
	{
		
	}
?>