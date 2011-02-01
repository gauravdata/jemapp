package nl.thewebmen.media 
{
	import flash.display.Bitmap;
	import flash.display.Loader;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.net.URLRequest;
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class Image extends Sprite
	{
		
		public static const LOADED:String = 'loaded';
		public static const FIT:String = 'fit';
		
		public static const TOP:String = 'top';
		public static const BOTTOM:String = 'bottom';
		public static const LEFT:String = 'left';
		public static const RIGHT:String = 'right';
		public static const CENTER:String = 'center';
		
		private var _bitmap:Bitmap;
		private var _loader:Loader;
		private var _width:Number;
		private var _height:Number;
		private var _mask:Sprite;
		private var _proportionscale:Boolean;
		private var _horizontalsnap:String = 'left';
		private var _verticalsnap:String = 'top';
		private var _url:String;
		
		public function Image(url:String, proportionscale:Boolean = true) 
		{
			_proportionscale = proportionscale;
			
			//vanaf boven, onder, links, rechts scalen
			
			_mask = new Sprite();
			_mask.graphics.beginFill(0);
			_mask.graphics.drawRect(0, 0, 100, 100);
			this.addChild(_mask);
			this.mask = _mask;
			
			this.load(url);
		}
		
		public function load(url:String):void
		{
			_url = url;
			if (_loader != null) {
				_loader.close();
			}
			_loader = new Loader();
			_loader.contentLoaderInfo.addEventListener(Event.COMPLETE, onImageLoaded, false, 0, true);
			_loader.load(new URLRequest(url));
		}
		
		private function fit():void
		{
			if (_bitmap == null) {
				return;
			}
			if (isNaN(_width)) {
				_width = _bitmap.width;
			}
			if (isNaN(_height)) {
				_height = _bitmap.height;
			}
			_mask.width = _width;
			_mask.height = _height;
			if (!_proportionscale) {
				_bitmap.width = _width;
				_bitmap.height = _height;
				return;
			}
			_bitmap.width = _width;
			_bitmap.scaleY = _bitmap.scaleX;
			if (_bitmap.height < _height) {
				_bitmap.height = _height;
				_bitmap.scaleX = _bitmap.scaleY;
			}
			
			if (_verticalsnap == Image.TOP) {
				_bitmap.y = 0;
			}else if(_verticalsnap == Image.BOTTOM) {
				_bitmap.y = -(_bitmap.height - _mask.height);
			}else {
				_bitmap.y = -((_bitmap.height - _mask.height) / 2);
			}
			
			if (_horizontalsnap == Image.LEFT) {
				_bitmap.x = 0;
			}else if(_verticalsnap == Image.RIGHT) {
				_bitmap.x = -(_bitmap.width - _mask.width);
			}else {
				_bitmap.x = -((_bitmap.width - _mask.width) / 2);
			}
			
			this.dispatchEvent(new Event(Image.FIT));
		}
		
		private function onImageLoaded(e:Event):void
		{
			_loader.removeEventListener(Event.COMPLETE, onImageLoaded, false);
			_bitmap = e.target.content as Bitmap;
			_bitmap.smoothing = true;
			this.addChild(_bitmap);
			fit();
			this.dispatchEvent(new Event(Image.LOADED));
			_loader = null;
		}
		
		override public function get width():Number { return _width; }
		override public function set width(value:Number):void 
		{
			_width = value;
			fit();
		}
		
		override public function get height():Number { return _height; }
		override public function set height(value:Number):void 
		{
			_height = value;
			fit();
		}
		
		public function get proportionscale():Boolean { return _proportionscale; }
		public function set proportionscale(value:Boolean):void 
		{
			_proportionscale = value;
			fit();
		}
		
		public function get horizontalsnap():String { return _horizontalsnap; }
		public function set horizontalsnap(value:String):void 
		{
			_horizontalsnap = value;
			fit();
		}
		
		public function get verticalsnap():String { return _verticalsnap; }
		public function set verticalsnap(value:String):void 
		{
			_verticalsnap = value;
			fit();
		}
		
		public function get url():String { return _url; }
		public function set url(value:String):void 
		{
			this.load(value);
		}
		
	}

}