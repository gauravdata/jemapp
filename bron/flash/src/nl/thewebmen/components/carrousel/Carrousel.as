package nl.thewebmen.components.carrousel 
{
	import flash.display.Sprite;
	import flash.events.Event;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class Carrousel extends Sprite
	{
		
		private var _items:Array = [];
		private var _index:int;
		private var _previndex:int;
		private var _distance:int = 75;
		
		public function Carrousel() 
		{
			
		}
		
		private function reposition():void
		{
			var dist:int = 0;
			for (var i:int = _index - 1; i >= 0; i--) {
				dist++;
				_items[i].moveTo(1 - (dist * .1), -(dist * _distance), 0);
			}
			
			_items[_index].moveTo(1, 0, 0);
			
			dist = 0;
			for (i = _index + 1; i < this.numitems; i++) {
				dist++;
				_items[i].moveTo(1 - (dist * .1), dist * _distance, 0);
			}
			
			var a:Array = _items.concat();
			a.sortOn('targzoom');
			var index:int = 0;
			for each(var item:CarrouselItem in a) {
				this.setChildIndex(item, index);
				index++;
			}
			
			_items[_previndex].unsetFront();
			_items[_index].setFront();
			_previndex = _index;
			
			this.dispatchEvent( new Event(Event.CHANGE) );
		}
		
		private function onItemClick(e:Event):void
		{
			var itm:CarrouselItem = e.target as CarrouselItem;
			this.gotoIndex( itm.index );
		}
		
		public function addItem(item:CarrouselItem):int
		{
			this.addChild(item);
			item.unsetFront();
			item.addEventListener(CarrouselItem.NON_FRONT_CLICK, onItemClick, false, 0, true);
			
			var index:int = _items.length;
			_items[index] = item;
			item.index = index;
			return index;
		}
		
		public function refresh(center:Boolean):void
		{
			if (center) {
				_index = _items.length / 2;
			}
			reposition();
		}
		
		public function next():void
		{
			_index++;
			if (_index >= _items.length) {
				_index = _items.length - 1;
			}
			reposition();
		}
		
		public function previous():void
		{
			_index--;
			if (_index < 0) {
				_index = 0;
			}
			reposition();
		}
		
		public function gotoIndex(index:int):void
		{
			if (index < 0) {
				index = 0;
			}
			if (index >= _items.length) {
				index = _items.length - 1;
			}
			_index = index;
			reposition();
		}
		
		public function getItemAt(index:int):CarrouselItem
		{
			return _items[index];
		}
		
		public function get numitems():int
		{
			return _items.length;
		}
		
		public function get frontItem():CarrouselItem
		{
			return _items[_index];
		}
		
		public function get index():int { return _index; }
		public function set index(value:int):void 
		{
			gotoIndex(value);
		}
		
		public function get distance():int { return _distance; }
		public function set distance(value:int):void 
		{
			_distance = value;
		}
		
	}

}