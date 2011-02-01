package nl.michelvandersteege.utils 
{
	import flash.display.BitmapData;
	import flash.utils.getTimer;
	
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class Benchmark
	{
		
		public static const FAST:String = 'fast';
		public static const MEDIUM:String = 'medium';
		public static const SLOW:String = 'slow';
		
		public static var speed:String = Benchmark.FAST;
		
		public static function run(w:int = 100, h:int = 100, n:int = 100):void
		{
			var st:int = getTimer();
			for(var i:int = 0; i < n; i++){
				var b:BitmapData = new BitmapData(w, h);
				for(var bx:int = 0; bx < w; bx++){
					for(var by:int = 0; by < h; by++){
						b.setPixel(bx, by, Math.random() * 0xFFFFFF);
					}
				}
			}
			var tt:int = getTimer() - st;
			if(tt > 100 && tt < 200){
				Benchmark.speed = Benchmark.MEDIUM;
			}else if(tt > 200){
				Benchmark.speed = Benchmark.SLOW;
			}
		}
		
	}

}