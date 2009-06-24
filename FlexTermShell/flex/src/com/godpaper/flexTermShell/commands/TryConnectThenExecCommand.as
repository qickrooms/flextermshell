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
	
	import mx.controls.Alert;
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
			if(data.result.execResult!="Failure")
			{
				//when pwd or cd ,change current user directory;
				if(tryConnectThenExecEvent.ssh2VO.command.indexOf("pwd")!=-1 || tryConnectThenExecEvent.ssh2VO.command.indexOf("cd")!=-1)
				{
					clientModel.resultInvokedByPWDorCD = data.result.execResult;
				}else
				{
					clientModel.resultInvokedByOthers = data.result.execResult;
				}
				
				//store as much as ever ssh2result 
				if(tryConnectThenExecEvent.ssh2VO.asMuchAsEver)
				{
					try
					{
						clientModel.resultAsMuchAsEver.CompressionAlgorithmsClient2Server = data.result.asMuchAsEver.CompressionAlgorithmsClient2Server;
						clientModel.resultAsMuchAsEver.CompressionAlgorithmsServer2Client = data.result.asMuchAsEver.CompressionAlgorithmsServer2Client;
						clientModel.resultAsMuchAsEver.DebugInfo = data.result.asMuchAsEver.DebugInfo;
						clientModel.resultAsMuchAsEver.EncryptionAlgorithmsClient2Server = data.result.asMuchAsEver.EncryptionAlgorithmsClient2Server;
						clientModel.resultAsMuchAsEver.EncryptionAlgorithmsServer2Client = data.result.asMuchAsEver.EncryptionAlgorithmsServer2Client;
						clientModel.resultAsMuchAsEver.KexAlgorithms = data.result.asMuchAsEver.KexAlgorithms;
						clientModel.resultAsMuchAsEver.LanguagesClient2Server = data.result.asMuchAsEver.LanguagesClient2Server;
						clientModel.resultAsMuchAsEver.LanguagesServer2Client = data.result.asMuchAsEver.LanguagesServer2Client;
						clientModel.resultAsMuchAsEver.Log = data.result.asMuchAsEver.Log;
						clientModel.resultAsMuchAsEver.MACAlgorithmsClient2Server = data.result.asMuchAsEver.MACAlgorithmsClient2Server;
						clientModel.resultAsMuchAsEver.MACAlgorithmsServer2Client = data.result.asMuchAsEver.MACAlgorithmsServer2Client;
						clientModel.resultAsMuchAsEver.ServerHostKeyAlgorithms = data.result.asMuchAsEver.ServerHostKeyAlgorithms;
						clientModel.resultAsMuchAsEver.ServerIdentification = data.result.asMuchAsEver.ServerIdentification;
						clientModel.resultAsMuchAsEver.ServerPublicHostKey = data.result.asMuchAsEver.ServerPublicHostKey;
						
						trace(clientModel.resultAsMuchAsEver);
					}catch(e:Error)
					{
						//
					}	
				}
				//update command textarea;
				try
				{
					(ViewLocator.getInstance().getViewHelper("mainPanelHelper") as MainPanelHelper).updateTextArea(data.result.execResult);
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
					(ViewLocator.getInstance().getViewHelper("mainPanelHelper") as MainPanelHelper).textAreaSetFocus();
				}catch(error01:Error)
				{
					//	
				}
				
			}else
			{
				Alert.show(data.toString());
			}
		}
		
		
	}
}