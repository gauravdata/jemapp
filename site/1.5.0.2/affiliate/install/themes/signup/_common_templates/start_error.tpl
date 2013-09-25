<!-- start_error -->
<style media="all" type="text/css">
{literal}
	*,html,body {margin:0px; padding:0px;}
	.clear {clear:both;}
	.MainContainer {background:#FFFFFF url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/login.background.png') repeat-x; position:relative;}
	#Container {width:900px; text-align:left; margin:0 auto; position:relative;}
	#Content {min-height:500px; _height:500px;}
	#Header {position:relative; height:75px;}
	#Footer {background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/footer.background.jpg') repeat-x; height:46px; clear:both; color:#606060;}
	#FooterMenuItems {margin:8px 0px 0px 0px; float:left;}
	#FooterMenuItems li{display:inline; float:left;}
	.FooterMenuItem {color:#606060; margin-left:20px;}
	.FooterMenuItem:hover {color:#305478; text-decoration:none;}
	.FooterLink {color:#606060;}
	.FooterLink:hover {color:#305478; text-decoration:none;}
	#Copyright {color:#606060; font-size:10px; margin-left:20px; padding-top:5px;}
	
	.ErrorMessageTop,
	.ErrorMessageTopLeft,
	.ErrorMessageTopRight,
	.ErrorMessageBottom,
	.ErrorMessageBottomLeft,
	.ErrorMessageBottomRight {padding:0px; line-height:1px; font-size:1px; margin:0px; position:relative;}	
	
	.ErrorMessageTop {height:5px; margin:0px 5px; background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.t.png') repeat-x;}
	.ErrorMessageTopLeft {height:5px; background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.tl.png') no-repeat top left;}
	.ErrorMessageTopRight {height:5px;  background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.tr.png') no-repeat top right;}
	.ErrorMessageBottom {height:5px; margin:0px 5px;  background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.b.png') repeat-x;}
	.ErrorMessageBottomLeft {height:5px; background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.bl.png') no-repeat bottom left;}
	.ErrorMessageBottomRight {height:5px;  background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.br.png') no-repeat bottom right;}
	.ErrorMessageLeft {background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.l.png') repeat-y top left;}
	.ErrorMessageRight {background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/error.message.r.png') repeat-y top right;}
	
	.ErrorMessageContainer {width:535px; margin:30px auto;}
	.ErrorMessageBox {margin:0px 5px; border:5px solid #c0c0c0; padding:20px 20px 20px 80px; font-size: 11px; font-family:Arial,Tahoma;
			background:url('../../../../GwtPhpFramework/trunk/server/install/themes/default/img/icon.error.message.png') no-repeat 20px 20px; min-height:60px; _height:60px;}
		
{/literal}
</style>
<div class="MainContainer">
	<div id="Container">
    	<div id="Content">
    		<div class="ErrorMessageContainer">
    			<div class="ErrorMessageTopLeft"><div class="ErrorMessageTopRight"><div class="ErrorMessageTop"></div></div></div>
				<div class="ErrorMessageLeft"><div class="ErrorMessageRight"><div class="ErrorMessageCenter">
					<div class="ErrorMessageBox">
						{$errorMessage}<br/>
					</div>
				</div></div></div>
				<div class="ErrorMessageBottomLeft"><div class="ErrorMessageBottomRight"><div class="ErrorMessageBottom"></div></div></div>
			</div>
    	</div>  
	</div>
</div>
