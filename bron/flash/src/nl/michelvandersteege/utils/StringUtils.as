package nl.michelvandersteege.utils 
{
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class StringUtils
	{
		
		public static function camelCaseSplit(input:String, delimiter:String = '_'):String
		{
			var reg:RegExp = new RegExp('[A-Z]', 'g');
			var rep:String = input.replace(reg, delimiter + '$&');
			if (rep.substr(0, delimiter.length) == delimiter) {
				rep = rep.substr(delimiter.length);
			}
			return rep;
		}
		
	}

}