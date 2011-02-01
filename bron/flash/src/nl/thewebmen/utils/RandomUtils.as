package nl.thewebmen.utils 
{
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class RandomUtils
	{
		
		/**
		 * Get random a true or a false
		 * @return		true or false
		 */
		public static function randomBoolean():Boolean
		{
			return Math.random() > .5;
		}
		
		/**
		 * Get a random number in a range
		 * @param	min		The minimum value
		 * @param	max		The maximum value
		 * @return			random number
		 */
		public static function randomNumber(min:Number, max:Number):Number 
		{
			var s:Number = max - min;
			return min + (s - Math.random() * s);
		}
		
		/**
		 * Get a random character
		 * @return	Random character
		 */
		public static function getRandomCharacter():String
		{
			var char:String = String.fromCharCode( RandomUtils.randomNumber(65, 90) );
			return char;
		}
		
		/**
		 * Get a random item from a array
		 * @param	array	The array
		 * @return			random item
		 */
		public static function getRandomFromArray(array:Array):*
		{
			return array[ RandomUtils.randomNumber(0, array.length) ];
		}
		
	}

}