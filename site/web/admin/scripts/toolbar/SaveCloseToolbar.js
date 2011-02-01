Ext.ns('Admin.Toolbar');
Admin.Toolbar.SaveCloseToolbar = Ext.extend(Ext.Toolbar, {
	
    initComponent : function() {
	
	this.addEvents('save', 'saveclose', 'close');

	Ext.apply(this, {
	    items:[{
		text: 'Save & Close',
		iconCls: 'icon-saveclose',
		handler : this.fireEvent.createDelegate(this,['saveclose'])
	    },{
		text: 'Save',
		iconCls: 'icon-save',
		handler : this.fireEvent.createDelegate(this,['save'])
	    },'->',{
		text: 'Close',
		iconCls: 'icon-close',
		handler : this.fireEvent.createDelegate(this,['close'])
	    }]
	});
		
	Admin.Toolbar.SaveCloseToolbar.superclass.initComponent.apply(this, arguments);
    }
	
});
Ext.reg('admin.toolbar.saveclosetoolbar', Admin.Toolbar.SaveCloseToolbar);