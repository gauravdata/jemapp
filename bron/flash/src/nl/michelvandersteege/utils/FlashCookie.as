package nl.michelvandersteege.utils 
{
	import flash.net.SharedObject;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class FlashCookie
	{
		//private
		private var _so:SharedObject;
		
		/**
		 * @param	name	Cookie name
		 */
		public function FlashCookie(name:String) 
		{
			_so = SharedObject.getLocal(name);
		}
		
		/**
		 * Save a value into this cookie
		 * @param	name		Value name
		 * @param	value		Value
		 */
		public function save(name:String, value:Object):void
		{
			_so.data[name] = value;
		}
		
		/**
		 * Get a value from this cookie
		 * @param	name	Value name
		 * @return			Value or null
		 */
		public function getValue(name:String):Object
		{
			return _so.data[name];
		}
		
		/**
		 * Remove a value from this cookie
		 * @param	name		Value name
		 */
		public function removeValue(name:String):void
		{
			_so.data[name] = undefined;
		}
		
		/**
		 * Remove all data from this cookie
		 */
		public function clear():void
		{
			_so.clear();
		}
		
	}

}