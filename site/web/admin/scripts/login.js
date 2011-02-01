Ext.onReady(function() {
	
    Ext.BLANK_IMAGE_URL = __BASE_URL + '/admin/ext/resources/images/default/s.gif';
	
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	
    var win = new Ext.Window({
        border:false,
        title:'Login',
        layout:'fit',
        height:160,
        width:350,
        resizable :false,
        items : {
            xtype:'admin.loginpanel'
        }
    });
    win.show();
});