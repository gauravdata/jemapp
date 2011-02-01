Ext.ns('Admin');
Admin.AccountGridPanel = Ext.extend(Ext.grid.GridPanel, {

    border:true,

    initComponent : function() {

	this.store = new Ext.data.DirectStore({
	    api : {
		read : Admin.Remoting.AccountGridPanel.read
	    },
	    autoLoad:true,
	    paramsAsHash: true,
	    encode:true,
	    idProperty: 'account_id',
	    root: 'data',
	    remoteSort: true,
	    fields: [{
		name: 'account_id'
	    },
	    {
		name: 'realname'
	    },
	    {
		name: 'username'
	    }]
	});

	this.pagingBar = new Ext.PagingToolbar( {
	    pageSize : 50,
	    store : this.store,
	    displayInfo : true,
	    displayMsg : 'Item {0} - {1} of {2}',
	    emptyMsg : "Geen items om weer te geven"
	});

	Ext.apply(this, {
	    columns : [ {
		id : 'realname',
		header : "Name",
		dataIndex : 'realname',
		sortable : true
	    }, {
		header : "Username",
		dataIndex : 'username',
		hidden : false,
		sortable : true
	    } ],
	    autoExpandColumn : 'realname',
	    loadMask : true,
	    stateful : true,
	    viewConfig : {
		forceFit : false
	    },
	    store : this.store,
	    bbar : this.pagingBar,
	    tbar : [{
		text:'Add user',
		iconCls : 'icon-user-add',
		handler : function(){
		    var win = new Admin.AccountWindow();
		    win.show();
		    win.on('datachange', function() {
			this.store.reload();
		    }.createDelegate(this));
		}.createDelegate(this)
	    },{
		text:'Remove',
		iconCls : 'icon-user-delete',
		handler : function() {
		    var sm = this.getSelectionModel();
		    var rows = sm.getSelections();
		    var deleteIds = new Array();
		    Ext.each(rows, function(item, index, allItems) {

			deleteIds.push(item.id);

		    }, this)

		    if (deleteIds.length>0)
		    {
			Admin.Remoting.Base.deleteAccount(deleteIds, function(provider,response) {
			    this.store.reload();
			},this);
		    }

		}.createDelegate(this)
	    },{
		text:'Edit',
		iconCls : 'icon-user-edit',
		handler : function() {
		    var sm = this.getSelectionModel();
		    var row = sm.getSelected();
		    var account_id = row.get('account_id');
		    var win = new Admin.AccountWindow();
		    win.init(account_id);
		    win.show();
		    win.on('datachange', function() {
			this.store.reload();
		    }.createDelegate(this));
		}.createDelegate(this)
	    }]
	});

	Admin.AccountGridPanel.superclass.initComponent.apply(this, arguments);

	this.on('rowdblclick', function(grid, index, event){
	    var row = grid.store.getAt(index);
	    var account_id = row.get('account_id');
	    var win = new Admin.AccountWindow();
	    win.init(account_id);
	    win.show();
	    win.on('datachange', function() {
		this.store.reload();
	    }.createDelegate(this));
	});
    }
});
Ext.reg('admin.accountgridpanel', Admin.AccountGridPanel);