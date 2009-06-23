package com.godpaper.flexTermShell.commands
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import mx.controls.Alert;
	import mx.rpc.IResponder;

	public class CommandBase implements ICommand, IResponder
	{
		public function CommandBase()
		{
			//TODO: implement function
		}

		public function execute(event:CairngormEvent):void
		{
			//TODO: implement function
		}
		
		public function result(data:Object):void
		{
			Alert.show(data.toString());
		}
		
		public function fault(info:Object):void
		{
			Alert.show(info.toString());
		}
		
	}
}