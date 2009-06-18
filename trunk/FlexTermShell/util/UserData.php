<?php
require_once (dirname(dirname(__FILE__)).'/dao/SSH2DAO.php');
class UserData 
{
	public static $SSH2Data;
	public static $SSH2Conn;
	public function UserData()
	{
		self::$SSH2Data = new SSH2DAO();
	}
}

?>