package nl.thewebmen.components.carrousel 
{
	import flash.display.Sprite;
	import com.greensock.TweenMax;
	import flash.events.Event;
	import flash.events.MouseEvent;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class CarrouselItem extends Sprite
	{
		
		public static const NON_FRONT_CLICK:String = 'non_front_click';
		public static const FRONT_CLICK:String = 'front_click';
		
		public var index:int;
		public var targzoom:Number;
		public var isfront:Boolean;
		
		public function CarrouselItem() 
		{
			this.buttonMode = true;
			this.mouseChildren = false;
			this.addEventListener(MouseEvent.CLICK, onClick, false, 0, true);
		}
		
		private function onClick(e:MouseEvent):void 
		{
			if (this.isfront) {
				handleClick();
				this.dispatchEvent(new Event(CarrouselItem.FRONT_CLICK));
			}else {
				this.dispatchEvent(new Event(CarrouselItem.NON_FRONT_CLICK));
			}
		}
		
		protected function handleClick():void { }
		
		public function setFront():void
		{
			isfront = true;
		}
		
		public function unsetFront():void
		{
			isfront = false;
		}
		
		public function moveTo(zoom:Number, x:Number, y:Number):void
		{
			if (zoom < .2) {
				zoom = .2;
			}
			this.targzoom = zoom;
			if (this.x != x || this.y != y || this.scaleX != zoom || this.scaleY != zoom) {
				animate(zoom, x, y);
			}
		}
		
		protected function animate(zoom:Number, x:Number, y:Number):void
		{
			TweenMax.to(this, .5, { x:x, y:y, scaleX:zoom, scaleY:zoom } );
		}
		
	}

}