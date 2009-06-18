package com.godpaper.flexTermShell.model
{
	import com.adobe.cairngorm.CairngormError;
	import com.adobe.cairngorm.CairngormMessageCodes;
	import com.adobe.cairngorm.model.IModelLocator;
	
	import mx.collections.ArrayCollection;

	public class ClientModelLocator implements IModelLocator
	{
		// Singleton instance of ModelLocator
		private static var instance:ClientModelLocator;
		
		[Bindable]public var execResult:String = "";
		
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