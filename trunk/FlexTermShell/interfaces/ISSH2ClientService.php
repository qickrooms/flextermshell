<?php
require_once (dirname(dirname(__FILE__)).'/dao/SSH2DAO.php');
/**
 *
 */
interface ISSH2ClientService 
{
	function tryConnectThenExec(SSH2DAO $ssh2dao);
	
	function tryConnect(SSH2DAO $ssh2dao);
	function tryLogin(SSH2DAO $ssh2dao);
	function tryExec(SSH2DAO $ssh2dao);
}

?>