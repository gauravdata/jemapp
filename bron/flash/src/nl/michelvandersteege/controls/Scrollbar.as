package nl.michelvandersteege.controls 
{
	import flash.filters.BlurFilter;
	
	import flash.display.DisplayObjectContainer;
	import flash.display.Shape;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	
	import flash.geom.Rectangle;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class Scrollbar extends EventDispatcher
	{
		
		//const
		public static const VERTICAL:String = "vertical";
		public static const HORIZONTAL:String = "horizontal";
		
		//privates fro getters and setters
		private var _uparrow:DisplayObjectContainer;
		private var _downarrow:DisplayObjectContainer;
		private var _dragger:DisplayObjectContainer;
		private var _path:DisplayObjectContainer;
		private var _mousewheel:DisplayObjectContainer;
		private var _ease:int = 1;
		private var _motionblur:Boolean;
		private var _enabled:Boolean = true;
		private var _visible:Boolean;
		private var _autodisable:Boolean = true;
		private var _autohide:Boolean;
		private var _arrowspeed:Number = 1;
		//privates
		private var _axis:String;
		private var _controlaxis:String;
		private var _directionprop:String;
		private var _controldirectionprop:String;
		private var _target:DisplayObjectContainer;
		private var _mask:Shape;
		private var _scrollrect:Rectangle;
		private var _mousescroll:Boolean;
		private var _perc:Number = 0;
		private var _dragclick:Number;
		private var _arrowvalue:Number = 0;
		private var _isdragging:Boolean;
		private var _debugmask:Shape;
		
		/**
		 * Scrollbar class
		 * @param	target			scroll target
		 * @param	scrollrect		scroll rectangle
		 * @param	direction		direction (vertical/horizontal) use 2 scrollbars to scroll both
		 * @param	mousescroll		mouse scroll?
		 * @param	initobj			a init object can contain the following properties: (uparrow, downarrow, dragger, mousewheel, enabled, path, ease, motionblur, createmask, autostart, autodisable(default true), autohide, controldirection, autodestroy(default true))
		 */
		public function Scrollbar(target:DisplayObjectContainer, scrollrect:Rectangle, direction:String = "vertical", mousescroll:Boolean = false, initobj:Object = null) 
		{
			_target = target;
			_scrollrect = scrollrect;
			if (direction == "vertical") {
				_axis = "y";
				_controlaxis = "y";
				_directionprop = "height";
				_controldirectionprop = "height";
			}else {
				_axis = "x";
				_controlaxis = "x";
				_directionprop = "width";
				_controldirectionprop = "width";
			}
			_mousescroll = mousescroll;
			
			if (initobj == null) {
				initobj = { };
			}
			if (initobj.uparrow != undefined) this.uparrow = initobj.uparrow;
			if (initobj.downarrow != undefined) this.downarrow = initobj.downarrow;
			if (initobj.path != undefined) this.path = initobj.path;
			if (initobj.dragger != undefined) this.dragger = initobj.dragger;
			if (initobj.mousewheel != undefined) this.mousewheel = initobj.mousewheel;
			if (initobj.enabled != undefined) this.enabled = initobj.enabled;
			if (initobj.ease != undefined) this.ease = initobj.ease;
			if (initobj.motionblur != undefined) this.motionblur = initobj.motionblur;
			if (initobj.controldirection != undefined) this.controldirection = initobj.controldirection;
			if (initobj.createmask == true) this.createMask();
			if (initobj.autostart == true) this.startRender();
			if (initobj.autodisable != undefined) this.autodisable = initobj.autodisable;
			if (initobj.autohide != undefined) this.autohide = initobj.autohide;
			if (initobj.autodestroy != false) {
				target.addEventListener(Event.REMOVED_FROM_STAGE, onRemovedFromStage);
			}
		}
		
		/**
		 * PRIVATES-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
		 */
		private function onRemovedFromStage(e:Event):void 
		{
			this.destroy();
		}
		
		private function onMouseWheel(e:MouseEvent):void
		{
			if (!_enabled) {
				return;
			}
			this.move( -e.delta );
		}
		private function onArrowUpDown(e:MouseEvent):void
		{
			if (!_enabled) {
				return;
			}
			_arrowvalue = -_arrowspeed;
			_target.stage.addEventListener(MouseEvent.MOUSE_UP, onArrowUp);
		}
		private function onArrowDownDown(e:MouseEvent):void
		{
			if (!_enabled) {
				return;
			}
			_arrowvalue = _arrowspeed;
			_target.stage.addEventListener(MouseEvent.MOUSE_UP, onArrowUp);
		}
		private function onArrowUp(e:MouseEvent):void
		{
			_target.stage.removeEventListener(MouseEvent.MOUSE_UP, onArrowUp);
			_arrowvalue = 0;
		}
		private function onPathClick(e:MouseEvent):void
		{
			if (!_enabled) {
				return;
			}
			this.moveTo( _path["mouse" + _controlaxis.toUpperCase()] / _path[_controldirectionprop] );
		}
		private function dragDragger(e:MouseEvent):void
		{
			if (!_enabled) {
				return;
			}
			_isdragging = true;
			_dragclick = _dragger["mouse" + _controlaxis.toUpperCase()];
			_dragger.stage.addEventListener(MouseEvent.MOUSE_MOVE, moveDragger);
			_dragger.stage.addEventListener(MouseEvent.MOUSE_UP, dropDragger);
			moveDragger(null);
		}
		private function dropDragger(e:MouseEvent):void
		{
			_isdragging = false;
			_dragger.stage.removeEventListener(MouseEvent.MOUSE_MOVE, moveDragger);
			_dragger.stage.removeEventListener(MouseEvent.MOUSE_UP, dropDragger);
			moveDragger(null);
		}
		private function moveDragger(e:MouseEvent):void
		{
			_dragger[_controlaxis] = _dragger.parent["mouse" + _controlaxis.toUpperCase()] - _dragclick;
			var max:Number = _path[_controlaxis] + _path[_controldirectionprop] - _dragger[_controldirectionprop];
			if (_dragger[_controlaxis] < _path[_controlaxis]) {
				_dragger[_controlaxis] = _path[_controlaxis];
			}else if (_dragger[_controlaxis] > max) {
				_dragger[_controlaxis] = max;
			}
			this.moveTo( (_dragger[_controlaxis] - _path[_controlaxis]) / (max - _path[_controlaxis]) );
		}
		
		/**
		 * PUBLICS-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
		 */
		
		 /**
		  * Debug only! draw a debug mask
		  */
		public function debugDrawMask():void
		{
			if (_debugmask != null) {
				_debugmask.parent.removeChild(_debugmask);
			}
			var r:Rectangle = _scrollrect;
			_debugmask = new Shape();
			_debugmask.graphics.beginFill(0xFF0000, .5);
			_debugmask.graphics.drawRect(0, 0, r.width, r.height);
			_mask.parent.addChild(_debugmask);
			_debugmask.x = r.x;
			_debugmask.y = r.y;
		}
		/**
		 * Create a mask for the scrollarea
		 */
		public function createMask():void
		{
			this.removeMask();
			_mask = new Shape();
			_mask.graphics.beginFill(0);
			_mask.graphics.drawRect(0, 0, _scrollrect.width, _scrollrect.height);
			_mask.x = _scrollrect.x;
			_mask.y = _scrollrect.y;
			_target.parent.addChild(_mask);
			_target.mask = _mask;
			_target.cacheAsBitmap = _mask.cacheAsBitmap = true;
		}
		/**
		 * Remove the mask that is made using createMask()
		 */
		public function removeMask():void
		{
			if (_mask != null) {
				_target.mask = null;
				_mask.parent.removeChild(_mask);
				_mask = null;
			}
		}
		
		/**
		 * Move to a percentage(0-1) of the scollarea (does not work when using mouse scoll)
		 * @param	perc		the percentage
		 * @param	animate		animate or not
		 */
		public function moveTo(perc:Number, animate:Boolean = true):void
		{
			_perc = perc;
			_perc = _perc > 1 ? 1 : _perc;
			_perc = _perc < 0 ? 0 : _perc;
			if (!animate) {
				var min:Number = _target[_directionprop] - _scrollrect[_directionprop];
				var t:Number = -(min * _perc) + _scrollrect[_axis];
				_target[_axis] = t;
			}
		}
		/**
		 * Move the scroll with a speed
		 * @param	speed	the speed
		 */
		public function move(speed:Number):void
		{
			this.moveTo(_perc + (speed / 100));
		}
		/**
		 * Move to the start position (does not work when using mouse scoll)
		 * @param	animate		animate or not
		 */
		public function moveToStart(animate:Boolean = true):void
		{
			this.moveTo(0, animate);
		}
		/**
		 * Move to the end position (does not work when using mouse scoll)
		 * @param	animate		animate or not
		 */
		public function moveToEnd(animate:Boolean = true):void
		{
			this.moveTo(1, animate);
		}
		
		/**
		 * Start render
		 */
		public function startRender():void
		{
			_target.addEventListener(Event.ENTER_FRAME, render);
		}
		/**
		 * Stop render
		 */
		public function stopRender():void
		{
			_target.removeEventListener(Event.ENTER_FRAME, render);
		}
		/**
		 * Render (loop) can be called manual but also via startRender()
		 * @param	e	Event defaults to null
		 */
		public function render(e:Event = null):void
		{
			//autohide and autodisable
			var issmaller:Boolean = _target[_directionprop] < _scrollrect[_directionprop];
			if (_autodisable && issmaller) {
				this.enabled = false;
			}else if(_autodisable) {
				this.enabled = true;
			}
			if (_autohide && issmaller) {
				this.visible = false;
			}else if(_autohide) {
				this.visible = true;
			}
			
			//mouse and arrows
			if (_mousescroll) {
				if (!this.enabled) {
					return;
				}
				var m:Number = _target.stage["mouse" + _axis.toUpperCase()];
				this.moveTo( (m - _scrollrect[_axis]) / _scrollrect[_directionprop] );
			}else if (_arrowvalue != 0) {
				this.move(_arrowvalue);
			}
			
			//calculate
			var min:Number = _target[_directionprop] - _scrollrect[_directionprop];
			var t:Number = -(min * _perc) + _scrollrect[_axis];
			
			//blur
			if (_motionblur) {
				var ba:Number = Math.abs((_target[_axis] - t) / _ease);
				if(ba > 2){
					var blur:BlurFilter = new BlurFilter(0, 0);
					blur["blur" + _axis.toUpperCase()] = ba;
					_target.filters = [blur];
				}else {
					_target.filters = [];
				}
			}
			
			//move
			_target[_axis] -= (_target[_axis] - t) / _ease;
			
			//dragger update
			if (!_isdragging && _dragger != null) {
				_dragger[_controlaxis] = (_perc * (_path[_controldirectionprop] - _dragger[_controldirectionprop])) + _path[_controlaxis];
			}
		}
		/**
		 * Destroy the scrollbar
		 */
		public function destroy():void
		{
			_target.removeEventListener(Event.REMOVED_FROM_STAGE, onRemovedFromStage);
			this.stopRender();
			this.uparrow = null;
			this.downarrow = null;
			this.dragger = null;
			this.path = null;
			this.mousewheel = null;
			this.removeMask();
		}
		
		/**
		 * GETTERS/SETTERS-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
		 */
		
		/**
		 * The up(or left) arrow control (is ignored when using mouse scoll)
		 */
		public function get uparrow():DisplayObjectContainer { return _uparrow; }
		public function set uparrow(value:DisplayObjectContainer):void 
		{
			if (_uparrow != null) {
				_uparrow.removeEventListener(MouseEvent.MOUSE_DOWN, onArrowUpDown);
			}
			_uparrow = value;
			if (_uparrow != null) {
				_uparrow.addEventListener(MouseEvent.MOUSE_DOWN, onArrowUpDown);
			}
		}
		
		/**
		 * The down(or right) arrow control (is ignored when using mouse scoll)
		 */
		public function get downarrow():DisplayObjectContainer { return _downarrow; }
		public function set downarrow(value:DisplayObjectContainer):void 
		{
			if (_downarrow != null) {
				_downarrow.removeEventListener(MouseEvent.MOUSE_DOWN, onArrowDownDown);
			}
			_downarrow = value;
			if (_downarrow != null) {
				_downarrow.addEventListener(MouseEvent.MOUSE_DOWN, onArrowDownDown);
			}
		}
		
		/**
		 * The dragger control (is ignored when using mouse scoll) registration point needs to be top left, cant exist without a path
		 */
		public function get dragger():DisplayObjectContainer { return _dragger; }
		public function set dragger(value:DisplayObjectContainer):void 
		{
			if (_path == null && value != null) {
				throw(new Error("Add a path first!"));
			}
			if (_dragger != null) {
				_dragger.removeEventListener(MouseEvent.MOUSE_DOWN, dragDragger);
			}
			_dragger = value;
			if (_dragger != null) {
				_dragger.addEventListener(MouseEvent.MOUSE_DOWN, dragDragger);
			}
		}
		
		/**
		 * The path control (is ignored when using mouse scoll) registration point needs to be top left
		 */
		public function get path():DisplayObjectContainer { return _path; }
		public function set path(value:DisplayObjectContainer):void 
		{
			if (_path != null) {
				_path.removeEventListener(MouseEvent.CLICK, onPathClick);
			}
			_path = value;
			if (_path != null) {
				_path.addEventListener(MouseEvent.CLICK, onPathClick);
			}else if(_dragger != null && value == null) {
				throw(new Error("Remove the dragger first!"));
			}
		}
		
		/**
		 * The scrolling ease (1 or higher)
		 */
		public function get ease():int { return _ease; }
		public function set ease(value:int):void 
		{
			_ease = value < 1 ? 1 : value;
		}
		
		/**
		 * Use motion blur
		 */
		public function get motionblur():Boolean { return _motionblur; }
		public function set motionblur(value:Boolean):void 
		{
			_motionblur = value;
		}
		
		/**
		 * The speed the scoll moves when you click a arrow
		 */
		public function get arrowspeed():Number { return _arrowspeed; }
		public function set arrowspeed(value:Number):void 
		{
			_arrowspeed = value;
		}
		
		/**
		 * The mouse wheel target, this gets the MouseEvent.MOUSE_WHEEL event
		 */
		public function get mousewheel():DisplayObjectContainer { return _mousewheel; }
		public function set mousewheel(value:DisplayObjectContainer):void 
		{
			if (_mousewheel != null) {
				_mousewheel.removeEventListener(MouseEvent.MOUSE_WHEEL, onMouseWheel);
			}
			_mousewheel = value;
			if (_mousewheel != null) {
				_mousewheel.addEventListener(MouseEvent.MOUSE_WHEEL, onMouseWheel);
			}
		}
		
		/**
		 * The control direction default is the same a s the scroll direction but can be set to the other (horizontal/vertical)
		 */
		public function get controldirection():String { return _controlaxis == "x" ? "horizontal" : "vertical" }
		public function set controldirection(value:String):void
		{
			if (value == "vertical") {
				_controlaxis = "y";
				_controldirectionprop = "height";
			}else {
				_controlaxis = "x";
				_controldirectionprop = "width";
			}
		}
		
		/**
		 * Enable or disable the scrollbar
		 */
		public function get enabled():Boolean { return _enabled; }
		public function set enabled(value:Boolean):void 
		{
			_enabled = value;
		}
		
		/**
		 * Auto disbale the scrollbar if the target is smaller then the scroll area
		 */
		public function get autodisable():Boolean { return _autodisable; }
		public function set autodisable(value:Boolean):void 
		{
			_autodisable = value;
		}
		
		/**
		 * Auto hide the scrollbar if the target is smaller then the scroll area (only for controls)
		 */
		public function get autohide():Boolean { return _autohide; }
		public function set autohide(value:Boolean):void 
		{
			_autohide = value;
		}
		
		/**
		 * Whether or not the controls are visible.
		 */
		public function get visible():Boolean { return _visible; }
		public function set visible(value:Boolean):void 
		{
			_visible = value;
			if (_dragger != null) {
				_dragger.visible = value;
			}
			if (_path != null) {
				_path.visible = value;
			}
			if (_uparrow != null) {
				_uparrow.visible = value;
			}
			if (_downarrow != null) {
				_downarrow.visible = value;
			}
		}
		
		/**
		 * The scroll percentage (0-1)
		 */
		public function get percentage():Number { return _perc; }
		public function set percentage(value:Number):void 
		{
			this.moveTo(value);
		}
		
		/**
		 * the scrollrect x
		 */
		public function get x():Number { return _scrollrect.x }
		public function set x(value:Number):void
		{
			_scrollrect.x = value;
			if (_debugmask != null) {
				this.debugDrawMask();
			}
			if (_mask != null) {
				this.createMask();
			}
		}
		/**
		 * the scrollrect y
		 */
		public function get y():Number { return _scrollrect.y }
		public function set y(value:Number):void
		{
			_scrollrect.y = value;
			if (_debugmask != null) {
				this.debugDrawMask();
			}
			if (_mask != null) {
				this.createMask();
			}
		}
		/**
		 * the scrollrect width
		 */
		public function get width():Number { return _scrollrect.width }
		public function set width(value:Number):void
		{
			_scrollrect.width = value;
			if (_debugmask != null) {
				this.debugDrawMask();
			}
			if (_mask != null) {
				this.createMask();
			}
		}
		/**
		 * the scrollrect height
		 */
		public function get height():Number { return _scrollrect.height }
		public function set height(value:Number):void
		{
			_scrollrect.height = value;
			if (_debugmask != null) {
				this.debugDrawMask();
			}
			if (_mask != null) {
				this.createMask();
			}
		}
		
	}

}