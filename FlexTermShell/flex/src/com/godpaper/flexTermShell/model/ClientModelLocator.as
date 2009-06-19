package com.godpaper.flexTermShell.model
{
	import com.adobe.cairngorm.CairngormError;
	import com.adobe.cairngorm.CairngormMessageCodes;
	import com.adobe.cairngorm.model.IModelLocator;
	import com.godpaper.flexTermShell.vo.SSH2ResultVO;
	import com.godpaper.flexTermShell.vo.SSH2VO;

	public class ClientModelLocator implements IModelLocator
	{
		// Singleton instance of ModelLocator
		private static var instance:ClientModelLocator;
		
		[Bindable]public var resultInvokedByPWDorCD:String = "";
		[Bindable]public var resultInvokedByOthers:String = "";
		[Bindable]public var resultAsMuchAsEver:SSH2ResultVO = new SSH2ResultVO();
		
		[Bindable]public var status:String = "DISCONNECT";
		[Bindable]public var successCommands:Array = [];
		[Bindable]public var userLoginInfo:SSH2VO = new SSH2VO();
		
		public function ClientModelLocator(access:Private)
		{
			if (access != null){
				if (instance == null)
				{				
					instance = this;
				}
			}
			else{
				throw new CairngormError( CairngormMessageCodes.SINGLETON_EXCEPTION, "ClientModelLocator" );
			}
		}
		/**
		 * 
		 * @return the Singleton instance of RSSReaderModelLocator
		 * 
		 */		
		public static function getInstance():ClientModelLocator
		{
			if(instance == null)
			{
				instance = new ClientModelLocator(new Private());
			}
			return instance;
		}

	}
}
/**
 * Inner class which restricts contructor access to Private
 */
class Private 
{
	
}