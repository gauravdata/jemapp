package nl.thewebmen.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class VideoPlayerCuePointEvent extends Event 
	{
		
		public static const CUEPOINT:String = "cuepoint";
		
		public var cuetime:Number;
		public var cuename:String;
		public var cuetype:String;
		
		public function VideoPlayerCuePointEvent(type:String, cuetime:Number, cuename:String, cuetype:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			this.cuetime = cuetime;
			this.cuename = cuename;
			this.cuetype = cuetype;
			super(type, bubbles, cancelable);
		} 
		
		public override function clone():Event 
		{ 
			return new VideoPlayerCuePointEvent(type, cuetime, cuename, cuetype, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("VideoPlayerCuePointEvent", "cuetime", "cuename", "cuetype", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}