/**
 * Ext JS Library 3.0 rc2 Copyright(c) 2006-2007, Ext JS, LLC. licensing@extjs.com
 * http://extjs.com/license
 * 
 * @author Jeroen Olthof
 */
// reference local blank image
Ext.BLANK_IMAGE_URL = 'ext/resources/images/default/s.gif';
Ext.ns('Admin');
Ext.onReady( function() {
    Ext.QuickTips.init();
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
    Ext.Direct.addProvider(Admin.Remoting.REMOTING_API);

    new Ext.Viewport({
	layout:'fit',
	items : {
	    xtype:'admin.applicationpanel'
	}
    });
});

