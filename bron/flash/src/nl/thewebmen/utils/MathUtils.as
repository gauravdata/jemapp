package nl.thewebmen.utils 
{
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class MathUtils
	{
		
		/**
		 * Convert radians to degrees
		 * @param	value	radians
		 * @return			degrees
		 */
		public static function toDegrees(value:Number):Number
		{
			return value * 180 / Math.PI;	
		}

		/**
		 * Conver degrees to radians
		 * @param	value	degrees
		 * @return			radians
		 */
		public static function toRadians(value:Number):Number
		{
			return value * Math.PI / 180;	
		}
		
		/**
		 * Get a percentage (0 - 1)
		 * @param	value	The value
		 * @param	min		The minimum value
		 * @param	max		The maximum value
		 * @return			The percentage (0 - 1)
		 */
		public static function getPercentage(value:Number, min:Number, max:Number):Number 
		{
			return (value - min) / (max - min);
		}
		
		/**
		 * Get the angle
		 * @param	xa		X position a
		 * @param	ya		Y position a
		 * @param	xb		X position b
		 * @param	yb		Y position b
		 * @param	useDegrees	Degrees or radians?
		 * @return				The angle
		 */
		public static function getAngle(xa:Number, ya:Number, xb:Number, yb:Number, useDegrees:Boolean = true):Number 
		{
			var dx:Number = xa - xb;
			var dy:Number = ya - yb;
			var r:Number = Math.atan2(dx, dy);
			if (useDegrees)
			{
				return MathUtils.toDegrees(r);
			}
			return r;
		}
		
		/**
		 * Check if a number is between two values
		 * @param	value	The number
		 * @param	min		Minimum value
		 * @param	max		Maximum value
		 * @return			true or false
		 */
		public static function between(value:Number, min:Number, max:Number):Boolean
		{
			return value > min && value < max;
		}
		
		/**
		 * Get the distance between two points
		 * @param	xa		X position a
		 * @param	ya		Y position a
		 * @param	xb		X position b
		 * @param	yb		Y position b
		 * @return			The distance
		 */
		public static function distance(xa:Number, ya:Number, xb:Number, yb:Number):Number
		{
			var dx:Number = xa - xb;
			var dy:Number = ya - yb;
			return Math.sqrt(dx * dx + dy * dy);
		}
		
	}

}