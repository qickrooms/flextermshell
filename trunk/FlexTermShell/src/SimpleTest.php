<?php
	require_once (dirname(__FILE__).'/SSH2ClientBase.php');
	
	$MySSH2ClientBase = new SSH2ClientBase();
	echo $MySSH2ClientBase->tryConnectThenExec("yourDomain.com",22,10,"username","password","ls");
	
	class SimpleTest
	{
		
	}
?>