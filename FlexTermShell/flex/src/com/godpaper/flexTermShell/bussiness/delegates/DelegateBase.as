package com.godpaper.flexTermShell.bussiness.delegates
{
	import mx.managers.CursorManager;
	import mx.rpc.AsyncToken;
	import mx.rpc.IResponder;
	import mx.rpc.remoting.RemoteObject;

	public class DelegateBase implements IResponder
	{
		protected var responder:IResponder;
		protected var service:RemoteObject;
		
		public function DelegateBase(responder:IResponder)
		{
			this.responder = responder;
		}
		//result or fault
		public virtual function result(data:Object):void
		{
			responder.result(data);
			CursorManager.removeBusyCursor();
		}
		
		final public function fault(info:Object):void
		{
			responder.fault(info);
			CursorManager.removeBusyCursor();
		}
		
		protected function setProgressStatus(setBusyCursor:Boolean):void
		{
			if(setBusyCursor)
			{
				CursorManager.setBusyCursor();
			}			
		}
	}
}