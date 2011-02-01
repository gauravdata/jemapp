package nl.michelvandersteege.controls 
{
	import flash.display.Sprite;
	import flash.display.StageDisplayState;
	import flash.events.Event;
	import flash.events.MouseEvent;
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class FullScreenButton extends Sprite
	{
		
		private var _graphics:Sprite;
		
		public function FullScreenButton(linecolor:uint = 0, linealpha:Number = 1, fillcolor:uint = 0xFFFFFF, fillalpha:Number = 0) 
		{
			_graphics = new Sprite();
			_graphics.graphics.beginFill(fillcolor, fillalpha);
			_graphics.graphics.lineStyle(3, linecolor, linealpha);
			_graphics.graphics.moveTo( -10, -5);
			_graphics.graphics.lineTo(10, -5);
			_graphics.graphics.lineTo(10, 5);
			_graphics.graphics.lineTo(-10, 5);
			_graphics.graphics.lineTo(-10, -5);
			_graphics.x = _graphics.width / 2;
			_graphics.y = _graphics.height / 2;
			this.addChild(_graphics);
			
			this.buttonMode = true;
			this.addEventListener(MouseEvent.CLICK, onClick);
			this.addEventListener(MouseEvent.ROLL_OVER, onOver);
			this.addEventListener(MouseEvent.ROLL_OUT, onOut);
			
			this.addEventListener(Event.REMOVED_FROM_STAGE, onRemovedFromStage);
		}
		
		private function onRemovedFromStage(e:Event):void 
		{
			this.removeEventListener(Event.REMOVED_FROM_STAGE, onRemovedFromStage);
			this.removeEventListener(MouseEvent.CLICK, onClick);
			this.removeEventListener(MouseEvent.ROLL_OVER, onOver);
			this.removeEventListener(MouseEvent.ROLL_OUT, onOut);
		}
		
		private function onClick(e:MouseEvent):void
		{
			if (stage.displayState == StageDisplayState.NORMAL) {
				this.setFullScreen();
			}else {
				this.setNormalScreen();
			}
		}
		private function onOver(e:MouseEvent):void
		{
			_graphics.scaleX = _graphics.scaleY = 1.3;
		}
		private function onOut(e:MouseEvent):void
		{
			_graphics.scaleX = _graphics.scaleY = 1;
		}
		
		public function setFullScreen():void
		{
			stage.displayState = StageDisplayState.FULL_SCREEN;
		}
		
		public function setNormalScreen():void
		{
			stage.displayState = StageDisplayState.NORMAL;
		}
		
	}

}