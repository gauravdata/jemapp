package nl.thewebmen.net 
{
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.net.URLRequestMethod;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.events.Event;
	import flash.utils.Dictionary;
	
	import com.adobe.serialization.json.JSON;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class DataPost
	{
		
		public static const RETURN_JSON:String = 'json';
		public static const RETURN_XML:String = 'xml';
		
		private static var _callbacks:Dictionary = new Dictionary(true);
		
		/**
		 * Post variables
		 * @param	url			url
		 * @param	params		params object
		 * @param	callback	callback function that gets the results
		 * @param	returntype	the return format: xml, json or leave blank to get the raw return
		 * @return	URLLoader
		 */
		public static function post(url:String, params:Object, callback:Function, returntype:String = ""):URLLoader
		{
			var r:URLRequest = new URLRequest(url);
			r.method = URLRequestMethod.POST;
			
			var v:URLVariables = new URLVariables();
			for(var key:String in params){
				v[key] = params[key];
			}
			r.data = v;
			
			var l:URLLoader = new URLLoader();
			if (callback != null) {
				_callbacks[l] = {callback:callback, returntype:returntype};
				l.addEventListener(Event.COMPLETE, onSendComplete);
			}
			l.load(r);
			return l;
		}
		
		private static function onSendComplete(e:Event):void 
		{
			var rt:String = _callbacks[e.target].returntype;
			if (rt == 'json') {
				_callbacks[e.target].callback.apply(null, [JSON.decode(e.target.data)]);
			}else if (rt == 'xml') {
				_callbacks[e.target].callback.apply(null, [new XML(e.target.data)]);
			}else {
				_callbacks[e.target].callback.apply(null, [e.target.data]);
			}
			delete _callbacks[e.target];
		}
		
	}

}