Ext.ns('Admin');
Admin.LoginPanel = Ext.extend(Ext.FormPanel, {
    labelAlign : 'right',
    frame : true,
    width : 350,
    border :false,
    resizeable:false,
 
    initComponent : function() {
        // provide feedback for any errors
        Ext.QuickTips.init();
        Ext.Direct.addProvider(Admin.Remoting.REMOTING_API);

        var config = {
            api : {
                submit: Admin.Remoting.Base.login
            },
            paramsAsHash: true,
            items : [ {
                xtype : 'textfield',
                name : 'username',
                allowBlank : false,
                msgTarget: 'side',
                fieldLabel : 'Username',
                anchor : '93%',
                value : Ext.state.Manager.get('username')
            }, {
                xtype : 'textfield',
                name : 'password',
                allowBlank : false,
                msgTarget: 'side',
                fieldLabel : 'Password',
                inputType : 'password',
                anchor : '93%',
                value : Ext.state.Manager.get('password')
            }, {
                xtype : 'checkbox',
                name : 'remember',
                labelSeparator : '',
                inputValue : 'on',
                boxLabel : 'Remember me.',
                value : Ext.state.Manager.get('remember')
            } ],
            buttons : [ {
                text : 'Aanmelden',
                id : 'submitbutton',
                handler: function(){
                    this.getForm().submit({
                        waitTitle : 'Request in progress',
                        waitMsg : 'Loggin on...',
                        success:function(form, action) {
                            window.location = __BASE_URL + '/admin/';
                        }
                    });
                }.createDelegate(this)
            }]
        };
	Ext.apply(this, Ext.apply(this.initialConfig, config));

        Admin.LoginPanel.superclass.initComponent.apply(this, arguments);
    }
});
Ext.reg('admin.loginpanel', Admin.LoginPanel);