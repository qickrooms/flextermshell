package com.godpaper.flexTermShell.controller
{
	import com.adobe.cairngorm.control.FrontController;
	import com.godpaper.flexTermShell.commands.TryConnectThenExecCommand;

	public class ClientController extends FrontController
	{
		public static const TRY_CONNECT_THEN_EXEC:String = "tryConnectThenExec";
		
		public function ClientController()
		{
			//TODO: implement function
			super();
			
			addCommand(ClientController.TRY_CONNECT_THEN_EXEC,TryConnectThenExecCommand);
		}
		
	}
}