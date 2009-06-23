<?php
require_once (dirname(dirname(__FILE__)).'/dao/SSH2DAO.php');
/**
 *
 */
interface ISSH2ClientService 
{
	function tryConnectThenExec(SSH2DAO $ssh2dao);
}

?>