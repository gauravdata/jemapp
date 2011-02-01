Ext.ns('Admin');
Admin.Window = Ext.extend(Ext.Window, {
	title : 'Untitled window',
	autoHeight:true,
	border:true,

	initComponent : function() {

		Admin.Window.superclass.initComponent.apply(this, arguments);
	}
});
Ext.reg('admin.window', Admin.Window);