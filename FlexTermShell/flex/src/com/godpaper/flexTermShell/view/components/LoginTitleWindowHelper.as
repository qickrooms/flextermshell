package com.godpaper.flexTermShell.view.components
{
	import com.adobe.cairngorm.view.ViewHelper;
	
	import mx.core.IFlexDisplayObject;
	import mx.managers.PopUpManager;

	public class LoginTitleWindowHelper extends ViewHelper
	{
		public function LoginTitleWindowHelper()
		{
			super();
		}
		public function closeWindow():void
		{
			if(this.view)
			{
				PopUpManager.removePopUp(this.view as IFlexDisplayObject);
			}
		}
	}
}