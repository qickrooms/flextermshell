<?php

/**
 *
 */
interface ISSH2ClientBase 
{
	function tryConnectThenExec($host, $port = 22, $timeout = 10, $username, $password,$command);
}

?>