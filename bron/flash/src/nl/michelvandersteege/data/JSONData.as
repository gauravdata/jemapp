package nl.michelvandersteege.data 
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	
	import com.adobe.serialization.json.JSON;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public dynamic class JSONData extends EventDispatcher
	{
		
		public static const ON_LOADED:String = 'on_loaded';
		
		private static var _references:Object = { };
		
		private var _reference:String;
		private var _url:String;
		private var _rawjson:Object;
		
		public function JSONData(reference:String, url:String) 
		{
			if (_references[reference] != undefined) {
				throw(new Error('JSONData: reference already taken'));
			}
			
			_reference = reference;
			_url = url;
			_references[_reference] = this;
			loadJSON();
		}
		
		private function loadJSON():void
		{
			var r:URLRequest = new URLRequest(_url);
			var l:URLLoader = new URLLoader();
			l.addEventListener(Event.COMPLETE, onJSONLoaded);
			l.load(r);
		}
		
		private function onJSONLoaded(e:Event):void
		{	
			_rawjson = JSON.decode(e.target.data);
			for (var prop in _rawjson) {
				this[prop] = _rawjson[prop];
			}
			this.dispatchEvent(new Event(JSONData.ON_LOADED));
		}
		
		/**
		 * Get a JSONData by reference
		 * @param	reference
		 * @return	JSONData object
		 */
		public static function getByReference(reference:String):JSONData
		{
			return _references[reference];
		}
		
		/**
		 * Create a new json data
		 * @param	reference
		 * @param	url
		 * @return	JSONData object
		 */
		public static function addNew(reference:String, url:String):JSONData
		{
			return new JSONData(reference, url);
		}
		
		/**
		 * Get the raw JSON
		 * @return	JSON object
		 */
		public function getRawJSON():Object
		{
			return _rawjson;
		}
		
		/**
		 * Get the JSON url
		 * @return	JSON url
		 */
		public function getUrl():String
		{
			return _url;
		}
		
		/**
		 * Get the reference
		 * @return	reference
		 */
		public function getReference():String
		{
			return _reference;
		}
		
	}

}