package nl.thewebmen.utils 
{
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class DateUtils
	{
		public static const MORNING:String = 'morning';
		public static const AFTERNOON:String = 'afternoon';
		public static const EVENING:String = 'evening';
		public static const NIGHT:String = 'night';
		
		/**
		 * converts a timestamp in the ISO format yyyy-mm-dd hh:mm:ss to a date object.
		 * Mostly used to get a date from a database delivered timestamp in string format.
		 * @param 	timeStamp	a string in the ISO format yyyy-mm-dd hh:mm:ss
		 * @return				The date
		 */
		public static function timeStampToDate(timeStamp:String):Date
		{
			var year:int = DateUtils.getYearFromTimeStamp(timeStamp);
			var month:int = DateUtils.getMonthFromTimeStamp(timeStamp) - 1;
			var day:int = DateUtils.getDayFromTimeStamp(timeStamp);
			var hour:int = DateUtils.getHourFromTimeStamp(timeStamp);
			var minute:int = DateUtils.getMinutesFromTimeStamp(timeStamp);
			var second:int = DateUtils.getSecondsFromTimeStamp(timeStamp);
			return new Date(year, month, day, hour, minute, second);
		}
		
		/**
		 * Get the seconds from a timestamp
		 * @param	timeStamp	a string in the ISO format yyyy-mm-dd hh:mm:ss
		 * @return				The seconds
		 */
		public static function getSecondsFromTimeStamp(timeStamp:String):int
		{
			return parseInt(timeStamp.substr(17, 2));
		}
		
		/**
		 * Get the minute from a timestamp
		 * @param	timeStamp	a string in the ISO format yyyy-mm-dd hh:mm:ss
		 * @return				The minute
		 */
		public static function getMinutesFromTimeStamp(timeStamp:String):int
		{
			return parseInt(timeStamp.substr(14, 2));
		}
		
		/**
		 * Get the hour from a timestamp
		 * @param	timeStamp	a string in the ISO format yyyy-mm-dd hh:mm:ss
		 * @return				The hour
		 */
		public static function getHourFromTimeStamp(timeStamp:String):int
		{
			return parseInt(timeStamp.substr(11, 2));
		}
		
		/**
		 * Get the day from a timestamp
		 * @param	timeStamp	a string in the ISO format yyyy-mm-dd hh:mm:ss
		 * @return				The day
		 */
		public static function getDayFromTimeStamp(timeStamp:String):int
		{
			return parseInt(timeStamp.substr(8, 2));
		}
		
		/**
		 * Get the month from a timestamp
		 * @param	timeStamp	a string in the ISO format yyyy-mm-dd hh:mm:ss
		 * @return				The month
		 */
		public static function getMonthFromTimeStamp(timeStamp:String):int
		{
			return parseInt(timeStamp.substr(5, 2));
		}
		
		/**
		 * Get the year from a timestamp
		 * @param	timeStamp	a string in the ISO format yyyy-mm-dd hh:mm:ss
		 * @return				The year
		 */
		public static function getYearFromTimeStamp(timeStamp:String):int
		{
			return parseInt(timeStamp.substr(0, 4));
		}
		
		/**
		 * Format milliseconds to minutes and seconds (mag 59:59)
		 * @param	miliseconds		The miliseconds
		 * @return					The formated time
		 */
		public static function formatTimeMinSec(miliseconds:int):String
		{
			var t:int = new Date().getTime();
			var d:Date = new Date( new Date(t + miliseconds).getTime() - t);
			return d.toString().split(' ')[3].substr( -5, 5);
		}
		
		/**
		 * Format a data like this: 13-12-2010
		 * @param	data	The date to use
		 * @return	string
		 */
		public static function dayMonthYear(data:Date):String
		{
			var day:int = data.date;
			var month:int = data.month + 1;
			var year:int = data.fullYear;
			return String( day + '-' + month + '-' + year );
		}
		
		/**
		 * Get the part of the day
		 * @param	data	The date to use
		 * @return	string	morning, afternoon, evening, night
		 */
		public static function dayPart(data:Date):String
		{
			var t:int = data.hours; //0 - 23
			trace(t);
			if (t >= 6 && t < 12) {
				return DateUtils.MORNING;
			}else if (t >= 12 && t < 17) {
				return DateUtils.AFTERNOON;
			}else if (t >= 17 && t <= 23) {
				return DateUtils.EVENING;
			}
			return DateUtils.NIGHT;
		}
		
	}

}