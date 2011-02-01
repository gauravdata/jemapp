package nl.thewebmen.utils 
{
	
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import flash.net.navigateToURL;
	import flash.net.URLRequest;
	import flash.utils.Dictionary;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class URLUtils
	{
		
		public static const SELF:String = "_self";
		public static const BLANK:String = "_blank";
		public static const PARENT:String = "_parent";
		public static const TOP:String = "_top";
		
		private static var _urldata:Dictionary = new Dictionary(true);
		
		/**
		 * Open a url
		 * @param	url			The url to open
		 * @param	target		The target, URLUtils.SELF, URLUtils.BLANK, URLUtils.PARENT, URLUtils.TOP
		 */
		public static function openURL(url:String, target:String = "_blank"):void
		{
			navigateToURL(new URLRequest(url), target);
		}
		
		/**
		 * Add a click event to a target with a navigateToURL on click
		 * @param	target		The target
		 * @param	url			The url
		 * @param	urltarget	The urltarget, URLUtils.SELF, URLUtils.BLANK, URLUtils.PARENT, URLUtils.TOP
		 * @param	weak		Is it a weak event or not
		 */
		public static function openURLOnClick(target:EventDispatcher, url:String, urltarget:String = "_blank", weak:Boolean = true):void
		{
			_urldata[target] = { url:url, target:urltarget };
			target.addEventListener(MouseEvent.CLICK, handleClick, false, 0, weak);
		}
		/**
		 * Remove the click event from a target
		 * @param	target	The target
		 */
		public static function removeURLOnClick(target:EventDispatcher):void
		{
			target.removeEventListener(MouseEvent.CLICK, handleClick, false);
			delete _urldata[target];
		}
		private static function handleClick(e:MouseEvent):void
		{
			var data:Object = _urldata[e.currentTarget];
			URLUtils.openURL(data.url, data.target);
		}
		
		/**
		 * Add a nocache param to the url
		 * @param	url		The url
		 * @return			The url with the nocache param
		 */
		public static function noCacheURL(url:String):String
		{
			return url + "?nocache=" + (Math.round(Math.random() * 500));
		}
		
	}

}