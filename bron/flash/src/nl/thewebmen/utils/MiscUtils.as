package nl.thewebmen.utils 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class MiscUtils
	{
		
		/**
		 * Remove all children from a DisplayObjectContainer
		 * @param	target		The DisplayObjectContainer
		 */
		public static function removeAllChildren(target:DisplayObjectContainer):void
		{
			while (target.numChildren != 0) {
				target.removeChildAt(0);
			}
		}
		
		/**
		 * Duplicate a displayobject
		 * @param	target	The target to duplicate
		 * @return			A copy of the target
		 */
		public static function duplicateDisplayobject(target:DisplayObject):*
		{
			var c:Class = Object(target).constructor;
			return new c();
		}
		
		/**
		 * Shuffle an array
		 * @param	arr		The target array
		 * @return			Shuffled array
		 */
		public static function shuffleArray(arr:Array):Array
		{
			var len:int = arr.length;
			var arr2:Array = new Array(len);
			for(var i:int = 0; i<len; i++)
			{
				arr2[i] = arr.splice(int(Math.random() * (len - i)), 1)[0];
			}
			return arr2;
		}
		
	}

}