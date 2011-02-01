Ext.ns('Admin');
Admin.AdminPanel = Ext.extend(Ext.Panel, {
    labelAlign : 'right',
    border :false,
    layout:'fit',
 
    initComponent : function() {

	Ext.apply(this, {
	    items:{
		xtype:'admin.accountgridpanel'
	    }
	});

	Admin.AdminPanel.superclass.initComponent.apply(this, arguments);
    }
});
Ext.reg('admin.adminpanel', Admin.AdminPanel);