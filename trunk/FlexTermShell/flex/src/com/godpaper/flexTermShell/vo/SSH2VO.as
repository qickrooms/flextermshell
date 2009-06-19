package com.godpaper.flexTermShell.vo
{
	import com.adobe.cairngorm.vo.IValueObject;
	[RemoteClass(alias="FlexTermShell.dao.SSH2DAO")]
	[Bindable]
	public class SSH2VO implements IValueObject
	{
		public var host:String;
		public var port:int;
		public var timeout:int;
		public var username:String;
		public var password:String;
		public var command:String="pwd";
		public var asMuchAsEver:Boolean;
	}
}