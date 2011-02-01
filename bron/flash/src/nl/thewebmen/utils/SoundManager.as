package nl.thewebmen.utils 
{
	import flash.media.Sound;
	import flash.media.SoundChannel;
	import flash.media.SoundMixer;
	import flash.media.SoundTransform;
	import flash.net.URLRequest;
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class SoundManager
	{
		
		private static var _sounds:Object = { };
		
		private static function createExternalSound(file:String, volume:Number):void
		{
			var r:URLRequest = new URLRequest(file);
			var s:Sound = new Sound(r);
			var c:SoundChannel = new SoundChannel();
			_sounds[file] = { sound:s, channel:c };
		}
		private static function createLibrarySound(cls:Class, volume:Number):void
		{
			var s:Sound = cls();
			var c:SoundChannel = new SoundChannel();
			_sounds[String(cls)] = { sound:s, channel:c };
		}
		
		/**
		 * Play a external sound
		 * @param	file	The sound url
		 * @param	loop	Loop the sound?
		 * @param	volume	The sound volume
		 */
		public static function playExternalSound(file:String, loop:Boolean = false, volume:Number = 1):SoundChannel
		{
			if (_sounds[file] == undefined) {
				createExternalSound(file, volume);
			}
			SoundManager.stopExternalSound(file);
			if(loop){
				_sounds[file].channel = _sounds[file].sound.play(0, int.MAX_VALUE);
			}else {
				_sounds[file].channel = _sounds[file].sound.play();
			}
			_sounds[file].channel.soundTransform = new SoundTransform(volume);
			return _sounds[file].channel;
		}
		
		/**
		 * Play a library sound
		 * @param	cls		The sound class
		 * @param	loop	Loop the sound?
		 * @param	volume	The sound volume
		 */
		public static function playLibrarySound(cls:Class, loop:Boolean = false, volume:Number = 1):SoundChannel
		{
			if (_sounds[String(cls)] == undefined) {
				createLibrarySound(cls, volume);
			}
			SoundManager.stopLibrarySound(cls);
			if(loop){
				_sounds[String(cls)].channel = _sounds[String(cls)].sound.play(0, int.MAX_VALUE);
			}else {
				_sounds[String(cls)].channel = _sounds[String(cls)].sound.play();
			}
			_sounds[String(cls)].channel.soundTransform = new SoundTransform(volume);
			return _sounds[String(cls)].channel;
		}
		
		/**
		 * Stop a external sound
		 * @param	file	The sound url
		 */
		public static function stopExternalSound(file:String):void
		{
			if (_sounds[file] != undefined) {
				_sounds[file].channel.stop();
			}
		}
		
		/**
		 * Stop a library sound
		 * @param	cls		The sound class
		 */
		public static function stopLibrarySound(cls:Class):void
		{
			if (_sounds[String(cls)] != undefined) {
				_sounds[String(cls)].channel.stop();
			}
		}
		
		/**
		 * Stop all the sounds
		 */
		public static function stopAllSounds():void
		{
			for(var key:String in _sounds){
				_sounds[key].channel.stop();
			}
		}
		
		/**
		 * Get a soundchannel
		 * @param	reference	The reference (class or path)
		 * @return				SoundChannel
		 */
		public static function getSoundChannel(reference:*):SoundChannel
		{
			return _sounds[String(reference)].channel;
		}
		
		/**
		 * Set the global volume
		 * @param	volume	The volume (0-1)
		 */
		public static function setGlobalVolume(volume:int = 1):void
		{
			SoundMixer.soundTransform = new SoundTransform(volume);
		}
		
	}

}