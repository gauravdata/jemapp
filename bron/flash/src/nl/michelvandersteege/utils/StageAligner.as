package nl.michelvandersteege.utils 
{
	import flash.display.Stage;
	import flash.display.StageAlign;
	import flash.geom.Rectangle;
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class StageAligner
	{
		
		private static var _stage:Stage;
		private static var _initwidth:Number;
		private static var _initheight:Number;
		private static var _alignmode:String;
		
		public static function init(stage:Stage, initwidth:Number, initheight:Number):void
		{
			_stage = stage;
			_initwidth = initwidth;
			_initheight = initheight;
			_alignmode = stage.align;
		}
		
		public static function getRect():Rectangle
		{
			if (_stage == null) {
				throw(new Error("StageAligner: Init first!"));
			}
			var r:Rectangle = new Rectangle(0, 0, _stage.stageWidth, _stage.stageHeight);
			switch (_alignmode) {
				case StageAlign.BOTTOM:
					r.x = (_initwidth / 2) - (_stage.stageWidth / 2);
					r.y = (-_stage.stageHeight) + _initheight;
				break;
				case StageAlign.BOTTOM_LEFT:
					r.y = (-_stage.stageHeight) + _initheight;
				break;
				case StageAlign.BOTTOM_RIGHT:
					r.x = (-_stage.stageWidth) + _initwidth;
					r.y = (-_stage.stageHeight) + _initheight;
				break;
				case StageAlign.LEFT:
					r.y = (_initheight / 2) - (_stage.stageHeight / 2);
				break;
				case StageAlign.RIGHT:
					r.x = (-_stage.stageWidth) + _initwidth;
					r.y = (_initheight / 2) - (_stage.stageHeight / 2);
				break;
				case StageAlign.TOP:
					r.x = (_initwidth / 2) - (_stage.stageWidth / 2);
				break;
				case StageAlign.TOP_RIGHT:
					r.x = (-_stage.stageWidth) + _initwidth;
				break;
				case StageAlign.TOP_LEFT:
				break;
				default:
					r.x = (_initwidth / 2) - (_stage.stageWidth / 2);
					r.y = (_initheight / 2) - (_stage.stageHeight / 2);
			}
			return r;
		}
		
	}

}