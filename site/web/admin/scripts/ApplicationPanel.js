Ext.ns('Admin');
Admin.ApplicationPanel = Ext.extend(Ext.Panel, {

    border:false,
    layout: 'anchor',
    style: {
	margin:'10px'
    },

    initComponent : function()
    {
	var config = {
	    items: [{
		anchor:'-20',
		itemId : 'header',
		bodyCssClass : 'application-header',
		border:false,
		height:100,
		layout:'fit',
		html : '<img src="../admin/images/logo_thewebmen.png" alt="logo ssc" style="float:left; vertical-align:middle;" /> <h1>Beheer</h1>',
		xtype:'panel',
		bbar : [ {
		    text: 'Users',
		    handler : function() {
			this.getComponent('cardcontainer').layout.setActiveItem('users');
		    }.createDelegate(this)
		},{
		    text: 'Panel 2',
		    handler : function() {
			this.getComponent('cardcontainer').layout.setActiveItem('panel2');
		    }.createDelegate(this)
		},{
		    text: 'Panel 3',
		    handler : function() {
			this.getComponent('cardcontainer').layout.setActiveItem('panel3');
		    }.createDelegate(this)
		},'->',{
		    text:'logout',
		    iconCls : 'icon-logout',
		    handler : function(){
			window.location = __BASE_URL + '/admin/auth/logout';
		    }.createDelegate(this)
		}]
	    },{
		html:'&nbsp;',
		height:20,
		border:false
	    },{
		anchor:'-20, -140',
		itemId : 'cardcontainer',
		layout : 'card',
		activeItem : 'users',
		border : false,
		defaults : {
		    frame:true,
		    bodyStyle:'padding: 5px;'
		},
		items : [ {
		    itemId : 'users',
		    title : 'Accounts',
		    xtype:'admin.accountgridpanel'
		},{
		    itemId : 'panel2',
		    title : 'panel 2'
		},{
		    itemId : 'panel3',
		    title : 'panel 3'
		} ]
	    }]
	};
	
	Ext.apply(this, Ext.apply(this.initialConfig, config));

	Admin.ApplicationPanel.superclass.initComponent.apply(this, arguments);
    }
});
Ext.reg('admin.applicationpanel', Admin.ApplicationPanel);