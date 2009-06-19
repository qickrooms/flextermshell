package com.godpaper.flexTermShell.view.components
{
	import com.adobe.cairngorm.view.ViewHelper;
	
	import mx.core.Application;

	public class MainPanelHelper extends ViewHelper
	{
		public function MainPanelHelper()
		{
			super();
		}
		
		public function updateTextArea(value:String):void
		{
			mainPanel.MyTextArea.text += value;
//			mainPanel.MyTextArea.text += "\n";
			mainPanel.MyTextArea.text += "$ ";
		}
		
		public function textAreaSetFocus():void
		{
			mainPanel.MyTextArea.setFocus();
		}
		
		protected function get mainPanel():MainPanel
		{
			return view as MainPanel;
		}
	}
}