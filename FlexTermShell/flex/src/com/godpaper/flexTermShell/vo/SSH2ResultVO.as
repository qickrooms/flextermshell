package com.godpaper.flexTermShell.vo
{
	import com.adobe.cairngorm.vo.IValueObject;
	[RemoteClass(alias="FlexTermShell.dao.SSH2ResultDAO")]
	[Bindable]
	public class SSH2ResultVO implements IValueObject
	{
		public var execResult:String;
		public var asMuchAsEver:SSH2ResulDetailVO;
	}
}