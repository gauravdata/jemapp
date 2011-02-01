package nl.thewebmen.events 
{
	import flash.events.Event;
	import nl.thewebmen.media.VideoPlayer;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class VideoPlayerEvent extends Event 
	{
		
		public static const META_DATA:String = "metaData";
		public static const STREAM_NOT_FOUND:String = "StreamNotFound";
		public static const FLUSH:String = "flush";
		public static const BUFFER_FULL:String = "bufferFull";
		public static const SECURITY_ERROR:String = "securityError";
		public static const PAUSE:String = "pause";
		public static const RESUME:String = "resume";
		public static const STOP:String = "stop";
		public static const END:String = "end";
		public static const START:String = "start";
		
		/**
		 * The video player
		 */
		public var player:VideoPlayer;
		
		public function VideoPlayerEvent(type:String, player:VideoPlayer, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			this.player = player;
			super(type, bubbles, cancelable);
		} 
		
		public override function clone():Event 
		{ 
			return new VideoPlayerEvent(type, player, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("VideoPlayerEvent", "player", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}