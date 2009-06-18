<?php
	require_once (dirname(__FILE__).'/SSH2ClientService.php');
	
	$MySSH2DAO = new SSH2DAO();
	$MySSH2DAO->host = "72.167.232.228";
	$MySSH2DAO->port = 22;
	$MySSH2DAO->timeout = 10;
	$MySSH2DAO->username = "godpaper";
	$MySSH2DAO->password = "KiT7740321";
	$MySSH2DAO->command = "ls";
	
	$MySSH2ClientService = new SSH2ClientService();
	echo $MySSH2ClientService->tryConnectThenExec($MySSH2DAO);
	
	class SimpleTest
	{
		
	}
?>