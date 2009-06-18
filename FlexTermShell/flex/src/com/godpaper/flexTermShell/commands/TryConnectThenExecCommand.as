package com.godpaper.flexTermShell.commands
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	import com.godpaper.flexTermShell.bussiness.delegates.ClientDelegate;
	import com.godpaper.flexTermShell.events.TryConnectThenExecEvent;
	import com.godpaper.flexTermShell.model.ClientModelLocator;
	
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
			Alert.show(data.result);
			if(data.result!="Failure")
			{
				clientModel.execResult = data.result;
			}
		}
		
		
	}
}