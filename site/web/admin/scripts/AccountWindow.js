Ext.ns('Admin');
Admin.AccountWindow = Ext.extend(Admin.Window, {

    autoHeight:true,
    
    initComponent : function() {

	this.addEvents('datachange');

	Ext.apply(this, {
	    title : 'Add account',
	    tbar : {
		xtype : 'admin.toolbar.saveclosetoolbar',
		listeners : {
		    save : this.onSave.createDelegate(this),
		    saveclose : this.onSaveClose.createDelegate(this),
		    close : this.onClose.createDelegate(this)
		}
	    },
	    items : [{
		itemId : 'accountpanel',
		xtype : 'admin.accountpanel'
	    }]
	});

	Admin.AccountWindow.superclass.initComponent.apply(this, arguments);
    },

    init : function (account_id)
    {
	if (account_id)
	{
	    this.getComponent('accountpanel').load({
		params : {
		    account_id:account_id
		}
	    });
	    this.setTitle('Edit account ('+account_id+')');
	}
    },
	
    onSave : function()
    {
	this.getComponent('accountpanel').getForm().submit({
	    success : function(form, action){
		var data = action.result.data;
		this.getComponent('accountpanel').setData(action.result.data);
		this.fireEvent('datachange',this);
		this.setTitle('Edit account ('+data.account_id+')');
	    }.createDelegate(this)
	});
    },
	
    onSaveClose : function()
    {
	this.getComponent('accountpanel').getForm().submit({
	    success : function(form, action){
		this.getComponent('accountpanel').setData(action.result.data);
		this.fireEvent('datachange',this);
		this.close();
	    }.createDelegate(this)
	});
    },
	
    onClose : function()
    {
	this.close();
    }	
});
Ext.reg('admin.accountwindow', Admin.AccountWindow);