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
			mainPanel.MyTextArea.selectionBeginIndex = mainPanel.MyTextArea.text.length;
			mainPanel.MyTextArea.verticalScrollPosition = mainPanel.MyTextArea.maxVerticalScrollPosition;
			
			mainPanel.MyTextArea.setFocus();
			if(Application.application.stage.focus == null)
			{
				Application.application.stage.focus = this.view;
				Application.application.focusManager.setFocus(mainPanel.MyTextArea);
			}
		}
		
		protected function get mainPanel():MainPanel
		{
			return view as MainPanel;
		}
	}
}