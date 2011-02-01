package nl.thewebmen.utils 
{
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class Validation
	{
		
		/**
		 * Validate a email address
		 * @param	email	The email address
		 * @return			Valid (true) or not (false)
		 */
		public static function email(email:String):Boolean
		{
			if (email.indexOf(' ') != -1) {
				return false;
			}
			return (email.match(/(\w|[_.\-])+@((\w|-)+\.)+\w{2,4}+/) != null);
		}
		
		/**
		 * Validate the length of a string
		 * @param	value		The string
		 * @param	minlength	The minimum length
		 * @param	maxlength	The maximum length (-1 for no maximum length)
		 * @return				Valid (true) or not (false)
		 */
		public static function length(value:String, minlength:int = 1, maxlength:int = -1):Boolean
		{
			if (maxlength != -1) {
				return value.length >= minlength && value.length <= maxlength;
			}
			return value.length >= minlength;
		}
		
		/**
		 * Check if a string is a valid number
		 * @param	value	The string
		 * @return			Valid (true) or not (false)
		 */
		public static function isNumber(value:String):Boolean
		{
			if (value == '') {
				return false;
			}
			return !isNaN(Number(value));
		}
		
		/**
		 * Check if a variable is set (not empty not null and not undefined)
		 * @param	variable	The variable to check
		 * @return				Valid (true) or not (false)
		 */
		public static function isset(variable:*):Boolean
		{
			return variable != null && variable != undefined && variable != "";
		}
		
		/**
		 * Validate a dutch postalcode (0000XX)
		 * @param	value	The postalcode
		 * @return			Valid (true) or not (false)
		 */
		public static function postalcodeNL(value:String):Boolean
		{
			if (value.length != 6) {
				return false;
			}
			return (value.match(/[0-9]{4}[a-zA-Z]{2}/) != null);
		}
		
	}

}