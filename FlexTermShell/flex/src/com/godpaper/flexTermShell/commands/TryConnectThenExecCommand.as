package com.godpaper.flexTermShell.commands
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	import com.adobe.cairngorm.view.ViewLocator;
	import com.godpaper.flexTermShell.bussiness.delegates.ClientDelegate;
	import com.godpaper.flexTermShell.events.TryConnectThenExecEvent;
	import com.godpaper.flexTermShell.model.ClientModelLocator;
	import com.godpaper.flexTermShell.view.components.LoginTitleWindowHelper;
	import com.godpaper.flexTermShell.view.components.MainPanelHelper;
	
	import mx.rpc.IResponder;

	public class TryConnectThenExecCommand extends CommandBase implements IResponder,ICommand
	{
		private var delegate:ClientDelegate;
		private var tryConnectThenExecEvent:TryConnectThenExecEvent;
		private var clientModel:ClientModelLocator = ClientModelLocator.getInstance();
		
		public function TryConnectThenExecCommand()
		{
			//TODO: implement function
			super();
			delegate = new ClientDelegate(this);
		}
		
		override public function execute(event:CairngormEvent):void
		{
			tryConnectThenExecEvent = TryConnectThenExecEvent(event);
			delegate.tryConnectThenExec(tryConnectThenExecEvent.ssh2VO);
		}
		
		override public function result(data:Object):void
		{
//			Alert.show(data.result);
			if(data.result!="Failure")
			{
				//when pwd or cd ,change current user directory;
				if(tryConnectThenExecEvent.ssh2VO.command.indexOf("pwd")!=-1 || tryConnectThenExecEvent.ssh2VO.command.indexOf("cd")!=-1)
				{
					clientModel.resultInvokedByPWDorCD = data.result;
				}else
				{
					clientModel.resultInvokedByOthers = data.result;
				}
				//update command textarea;
				try
				{
					(ViewLocator.getInstance().getViewHelper("mainPanelHelper") as MainPanelHelper).updateTextArea(data.result);
				}catch(error00:Error)
				{
					//
				}
				//display connection status;
				clientModel.status = "CONNECTED";
				//store executed command;
				clientModel.successCommands.push(tryConnectThenExecEvent.ssh2VO.command);
				//store user login info;
				clientModel.userLoginInfo = tryConnectThenExecEvent.ssh2VO;
				//Close the popup window;
				try
				{
					(ViewLocator.getInstance().getViewHelper("loginTitleWindowHelper") as LoginTitleWindowHelper).closeWindow();
				}catch(error01:Error)
				{
					//	
				}
				
			}
		}
		
		
	}
}