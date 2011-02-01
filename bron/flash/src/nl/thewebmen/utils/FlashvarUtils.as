package nl.thewebmen.utils 
{
	import flash.display.DisplayObject;
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class FlashvarUtils
	{
		
		public static function getVar(root:DisplayObject, name:String, defaultvalue:String):String
		{
			if (root.loaderInfo.parameters[name] != undefined) {
				return root.loaderInfo.parameters[name];
			}
			return defaultvalue;
		}
		
	}

}