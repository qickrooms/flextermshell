package com.godpaper.flexTermShell.bussiness.delegates
{
	import com.adobe.cairngorm.business.ServiceLocator;
	import com.godpaper.flexTermShell.constants.ServiceInfoList;
	import com.godpaper.flexTermShell.vo.SSH2VO;
	
	import mx.rpc.AsyncToken;
	import mx.rpc.IResponder;

	public class ClientDelegate extends DelegateBase implements IResponder
	{
		public function ClientDelegate(responder:IResponder)
		{
			//TODO: implement function
			super(responder);
			this.service = ServiceLocator.getInstance().getRemoteObject(ServiceInfoList.SERVICE_SSH2_CLIENT);
		}
		
		public function tryConnectThenExec(arguments:SSH2VO,setBusyCursor:Boolean=true,showProgressBar:Boolean=true):void
		{
			var token:AsyncToken = service.getOperation(ServiceInfoList.OPERATION_TRY_CONNECT_THEN_EXEC).send( arguments.host,
																											   arguments.port,
																											   arguments.timeout,
																											   arguments.username,
																											   arguments.password,
																											   arguments.command);
																											   
			token.addResponder(this);
			
			setProgressStatus(setBusyCursor);
		}
		
	}
}