package com.godpaper.flexTermShell.vo
{
	import com.adobe.cairngorm.vo.IValueObject;
	[RemoteClass(alias="FlexTermShell.dao.SSH2ResultDAO")]
	[Bindable]
	public class SSH2ResultVO implements IValueObject
	{

		public var Log:*;//String
		public var DebugInfo:*;//String
		public var ServerIdentification:*="FlexTermShell";//String
		public var KexAlgorithms:*;//String
		public var ServerHostKeyAlgorithms:*;//Array
		public var EncryptionAlgorithmsClient2Server:*;//Array
		public var EncryptionAlgorithmsServer2Client:*;//Array
		public var MACAlgorithmsClient2Server:*;//Array
		public var MACAlgorithmsServer2Client:*;//Array
		public var CompressionAlgorithmsClient2Server:*;//Array
		public var CompressionAlgorithmsServer2Client:*;//Array
		public var LanguagesServer2Client:*;//Array
		public var LanguagesClient2Server:*;//Array
		public var ServerPublicHostKey:*;//Array

	}
}