package nl.thewebmen.drawing 
{
	import flash.display.Sprite;
	/**
	 * ...
	 * @author Michel van der Steege
	 */
	public class Wedge extends Sprite
	{
		
		public function Wedge() 
		{
			
		}
		
		public function draw(startAngle:Number, arc:Number, xradius:Number, yRadius:Number):void
		{
			if (Math.abs(arc) > 360) {
				arc = 360;
			}
			var angleMid:Number;
			var segs:Number = Math.ceil(Math.abs(arc) / 45);
			var segAngle:Number = arc / segs;
			var theta:Number = -(segAngle / 180) * Math.PI;
			var angle:Number = -(startAngle / 180) * Math.PI;
			var ax:Number, ay:Number, bx:Number, by:Number, cx:Number, cy:Number;
			var x:Number = 0;
			var y:Number = 0;
			
			this.graphics.moveTo(x, y);
			if (segs>0) {
				ax = x + Math.cos(startAngle / 180 * Math.PI) * xradius;
				ay = y + Math.sin( -startAngle / 180 * Math.PI) * yRadius;
				this.graphics.lineTo(ax, ay);
				for (var i:int = 0; i < segs; i++) {
					angle -= theta;
					angleMid = angle + (theta / 2);
					bx = x + Math.cos(angle) * xradius;
					by = y + Math.sin(angle) * yRadius;
					cx = x + Math.cos(angleMid) * (xradius / Math.cos(theta / 2));
					cy = y + Math.sin(angleMid) * (yRadius / Math.cos(theta / 2));
					this.graphics.curveTo(cx, cy, bx, by);
				}
				this.graphics.lineTo(x, y);
			}
		}
		
	}

}