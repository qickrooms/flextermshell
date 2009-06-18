package com.godpaper.flexTermShell.events
{
	import com.adobe.cairngorm.control.CairngormEvent;
	import com.godpaper.flexTermShell.controller.ClientController;
	import com.godpaper.flexTermShell.vo.SSH2VO;

	public class TryConnectThenExecEvent extends CairngormEvent
	{
		public var ssh2VO:SSH2VO;
		public function TryConnectThenExecEvent(customSSH2VO:SSH2VO)
		{
			//TODO: implement function
			super(ClientController.TRY_CONNECT_THEN_EXEC);
			this.ssh2VO = customSSH2VO;
		}
		
	}
}