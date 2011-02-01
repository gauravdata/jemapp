package nl.thewebmen.media 
{
	import flash.display.Sprite;
	
	import flash.events.Event;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	
	import flash.media.Video;
	
	import flash.net.NetConnection;
	import flash.net.NetStream;
	
	import nl.thewebmen.events.VideoPlayerCuePointEvent;
	import nl.thewebmen.events.VideoPlayerEvent;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class VideoPlayer extends Sprite
	{
		
		private static const BUFFERTIME:int = 10;
		
		private var _ns:NetStream;
		private var _nc:NetConnection;
		private var _vid:Video;
		private var _ispause:Boolean = false;
		private var _duration:Number = 0;
		private var _framerate:Number = 0;
		private var _videodatarate:Number = 0;
		private var _file:String = "";
		private var _smoothing:Boolean = false;
		private var _loop:Boolean = false;
		
		/**
		 * Basic videoplayer
		 * @param	width		player width
		 * @param	height		player height
		 * @param	file		file to play
		 */
		public function VideoPlayer(width:Number, height:Number, file:String = "") 
		{
			_nc = new NetConnection();
			_nc.addEventListener(NetStatusEvent.NET_STATUS, onNetStatus);
			_nc.addEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError);
			_nc.connect(null);
			
			_ns = new NetStream(_nc);
			_ns.addEventListener(NetStatusEvent.NET_STATUS, onNetStatus);
			_ns.client = {onMetaData:onMetaData, onCuePoint:onCuePoint};
			_ns.bufferTime = BUFFERTIME;
			
			_vid = new Video(width, height);
			_vid.attachNetStream(_ns);
			this.addChild(_vid);
			
			this.graphics.beginFill(0);
			this.graphics.drawRect(0, 0, width, height);
			this.graphics.endFill();
			
			if (file != "") {
				this.play(file);
			}
			this.addEventListener(Event.REMOVED_FROM_STAGE, onRemovedFromStage);
		}
		
		private function onRemovedFromStage(e:Event):void 
		{
			this.removeEventListener(Event.REMOVED_FROM_STAGE, onRemovedFromStage);
			_ns.removeEventListener(NetStatusEvent.NET_STATUS, onNetStatus);
			_nc.removeEventListener(NetStatusEvent.NET_STATUS, onNetStatus);
			_nc.removeEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError);
			_ns.close();
			_nc.close();
			_vid.clear();
			this.removeChild(_vid);
		}
		
		/**
		 * Privates-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
		 */
		
		private function onMetaData(o:Object):void
		{
			_duration = o.duration;
			_framerate = o.framerate;
			_videodatarate = o.videodatarate;
			this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.META_DATA, this));
		}
		private function onCuePoint(o:Object):void
		{
			this.dispatchEvent(new VideoPlayerCuePointEvent(VideoPlayerCuePointEvent.CUEPOINT, o.time, o.name, o.type));
		}
		
		private function onNetStatus(e:NetStatusEvent):void 
		{
			switch (e.info.code) {
                case "NetConnection.Connect.Success":
					//connection succes
                    break;
                case "NetStream.Play.StreamNotFound":
                   this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.STREAM_NOT_FOUND, this));
                    break;
				case "NetStream.Buffer.Flush":
					this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.FLUSH, this));
					break;
				case "NetStream.Play.Start":
					this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.START, this));
					break;
				case "NetStream.Play.Stop":
					this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.END, this));
					if (_loop) {
						this.seekto(0);
						this.play();
					}
					break;
				case "NetStream.Buffer.Full":
					this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.BUFFER_FULL, this));
					break;
            }
		}
		
		private function onSecurityError(e:SecurityErrorEvent):void 
		{
			this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.SECURITY_ERROR, this));
		}
		
		/**
		 * Publics-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
		 */
		
		/**
		 * Play a file
		 * @param	file	the video url (leave empty to resume the current video)
		 */
		public function play(file:String = ""):void
		{
			if (_file == file || (file == "" && _file != "")) {
				this.resume();
			}else {
				_file = file;
				_duration = 0;
				_framerate = 0;
				_videodatarate = 0;
				_ispause = false;
				_ns.play(_file);
			}
		}
		/**
		 * Toggle pause
		 */
		public function togglePause():void
		{
			if (this.ispause) {
				this.resume();
			}else {
				this.pause();
			}
		}
		/**
		 * Pause the player
		 */
		public function pause():void
		{
			_ns.pause();
			_ispause = true;
			this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.PAUSE, this));
		}
		/**
		 * Resume the player
		 */
		public function resume():void
		{
			_ns.resume();
			_ispause = false;
			this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.RESUME, this));
		}
		/**
		 * Stop the player (seek to 0 and pause)
		 */
		public function stop():void
		{
			this.seekto(0);
			this.pause();
			this.dispatchEvent(new VideoPlayerEvent(VideoPlayerEvent.STOP, this));
		}
		/**
		 * Seek to a percentage of the video
		 * @param	proc	the percentage(0-1)
		 */
		public function seekto(proc:Number):void
		{
			proc = proc > 1 ? 1 : proc;
			proc = proc < 0 ? 0 : proc;
			_ns.seek(this.duration * proc);
		}
		/**
		 * Rewind to start
		 */
		public function rewind():void
		{
			this.seekto(0);
		}
		
		/**
		 * Getters/Setters-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
		 */
		
		/**
		 * [Read only] if the player is paused
		 */
		public function get ispause():Boolean { return _ispause; }
		/**
		 * [Read only] the video duration
		 */
		public function get duration():Number { return _duration; }
		/**
		 * [Read only] the video data rate
		 */
		public function get videodatarate():Number { return _videodatarate; }
		/**
		 * [Read only] the current file playing
		 */
		public function get file():String { return _file; }
		/**
		 * [Read only] the video playtime
		 */
		public function get playtime():Number { return _ns.time }
		/**
		 * smoothing of the video
		 */
		public function get smoothing():Boolean { return _vid.smoothing; }
		public function set smoothing(value:Boolean):void 
		{
			_vid.smoothing = value;
		}
		/**
		 * The playing percentage(0-1)
		 */
		public function get playperc():Number { return this.playtime / _duration; }
		public function set playperc(value:Number):void 
		{
			this.seekto(value);
		}
		/**
		 * Is this a looping video? (when the end even is dispatched it automatically restarts the video)
		 */
		public function get loop():Boolean { return _loop; }
		public function set loop(value:Boolean):void 
		{
			_loop = value;
		}
		
	}

}