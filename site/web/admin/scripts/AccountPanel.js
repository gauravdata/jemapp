Ext.ns('Admin');
Admin.AccountPanel = Ext.extend(Ext.FormPanel, {
    labelAlign : 'right',
    border : false,
    bodyStyle: 'padding:5px; background-color:#DFE8F6',
    width : 350,
    resizeable:false,
 
    initComponent : function() {
	// provide feedback for any errors

	this.addEvents('cancel');
	var config = {
	    defaults: {
		labelWidth:100
	    },
	    border:false,
	    api : {
		submit: Admin.Remoting.AccountPanel.submit,
		load: Admin.Remoting.AccountPanel.load
	    },
	    paramsAsHash: true,
	    items : [ {
		xtype : 'hidden',
		name : 'account_id'
	    }, {
		xtype : 'textfield',
		name : 'realname',
		allowBlank : false,
		msgTarget: 'side',
		fieldLabel : 'Real name',
		anchor : '93%'
	    }, {
		xtype : 'textfield',
		name : 'username',
		allowBlank : false,
		msgTarget: 'side',
		fieldLabel : 'Username',
		anchor : '93%'
	    }, {
		id : 'password_field',
		xtype : 'textfield',
		name : 'password',
		allowBlank : true,
		msgTarget: 'side',
		fieldLabel : 'Password',
		inputType : 'password',
		anchor : '93%'
	    }, {
		xtype : 'textfield',
		name : 'password_match',
		allowBlank : true,
		msgTarget: 'side',
		fieldLabel : 'Confirm password',
		inputType : 'password',
		vtype: 'password',
		initialPassField: 'password_field', // id of the initial password field
		anchor : '93%'
	    } ]
	};

	Ext.apply(this, Ext.apply(this.initialConfig, config));

	Admin.AccountPanel.superclass.initComponent.apply(this, arguments);
    },

    setData : function(data) {
	this.getForm().setValues(data);
    }
});
Ext.reg('admin.accountpanel', Admin.AccountPanel);