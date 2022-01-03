/**
 * Filename: master_pos.masterRepresentative.js
 * Generated 2019-03-24 at 02:18:39 PM
 */
Ext.define('ExtApp.modules.master_pos.model.model_masterRepresentative', {
    extend: 'Ext.data.Model',
    alias: 'model_masterRepresentative',
    fields: [{
        name: 'id',
        type: 'int'
    }, {
        name: 'representative_name',
        type: 'string'
    }, {
        name: 'representative_email',
        type: 'string'
    }, {
        name: 'representative_address',
        type: 'string'
    }, {
        name: 'representative_phone',
        type: 'string'
    }, {
        name: 'representative_signature',
        type: 'string'
    }],
    proxy: {
        type: 'ajax',
        url: appUrl + 'master_pos/masterRepresentative/gridData',
        extraParams: {},
        actionMethods: {
            read: 'POST'
        },
        reader: {
            type: 'json',
            root: 'data',
            idProperty: 'id',
            totalProperty: 'totalCount'
        }
    }
});
Ext.define('ExtApp.modules.master_pos.store.store_masterRepresentative', {
    extend: 'Ext.data.Store',
    model: 'ExtApp.modules.master_pos.model.model_masterRepresentative',
    autoLoad: false,
    remoteSort: true
});
Ext.define('ExtApp.modules.master_pos.store.store_masterStatus', {
    extend: 'Ext.data.Store',
    fields: ['val', 'name'],
    data: [{
        "val": "ok",
        "name": "OK"
    }, {
        "val": "warning",
        "name": "Warning"
    }, {
        "val": "blacklist",
        "name": "Blaclist"
    }],
    autoLoad: false
});
Ext.define("ExtApp.modules.master_pos.view.masterRepresentative", {
    openWindow: function (me) {
        var theApp = me.app;
        var desktop = theApp.getDesktop();
        var phpJs = me.app.getHelper('phpJs');
        var add_parameter = '_dc=' + phpJs.mktime();
        add_parameter += '&module=master_pos';
        add_parameter += '&file=masterRepresentative';
        add_parameter += '&action=print_masterRepresentative';
        var url_report = '';
        var store_masterRepresentative_masterRepresentative = theApp.getStore('store_masterRepresentative_masterRepresentative', false);
        if (store_masterRepresentative_masterRepresentative == false) {
            store_masterRepresentative_masterRepresentative = theApp.copyStore('master_pos', 'store_masterRepresentative', 'store_masterRepresentative_masterRepresentative');
        }
        store_masterRepresentative_masterRepresentative.proxy.extraParams.limit = 25;
        store_masterRepresentative_masterRepresentative.proxy.extraParams.is_dropdown = 0;
        store_masterRepresentative_masterRepresentative.proxy.extraParams.is_active = 0;
        store_masterRepresentative_masterRepresentative.proxy.extraParams.show_all_text = 0;
        store_masterRepresentative_masterRepresentative.proxy.extraParams.show_choose_text = 0;
        store_masterRepresentative_masterRepresentative.proxy.extraParams.keywords = '';
        var helperGrid = theApp.getHelper('Grid');
        var selModel_masterRepresentative = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function (sm, selections) {
                    Ext.getCmp('grid_masterRepresentative').down('#deleteButton_masterRepresentative').setDisabled(selections.length == 0);
                }
            }
        });
        me.pagingtb_masterRepresentative = helperGrid.paging({
            ds: store_masterRepresentative_masterRepresentative,
            title: 'Perwakilan}'
        });
        return desktop.createWindow({
            id: me.id,
            title: 'Perwakilan',
            width: 900,
            height: 450,
            iconCls: 'icon-grid',
            animCollapse: false,
            constrainHeader: true,
            resizable: true,
            maximized: false,
            border: 0,
            layout: {
                type: 'fit'
            },
            items: [{
                xtype: 'gridpanel',
                margins: '0 0 0 0',
                region: 'center',
                store: store_masterRepresentative_masterRepresentative,
                id: 'grid_masterRepresentative',
                scroll: true,
                selModel: selModel_masterRepresentative,
                columns: [{
                    xtype: 'gridcolumn',
                    dataIndex: 'id',
                    text: 'ID',
                    hidden: true,
                    hideable: false
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'representative_name',
                    text: 'Nama',
                    width: 180
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'representative_email',
                    text: 'Email',
                    width: 110
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'source_from',
                    text: 'Source',
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'representative_status_text',
                    text: 'Status',
                    width: 90
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'is_active_text',
                    text: 'Active',
                    width: 90
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'representative_phone',
                    text: 'Phone',
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'representative_address',
                    text: 'Alamat',
                    width: 150
                }],
                viewConfig: {
                    stripeRows: true,
                    forceFit: true
                },
                listeners: {
                    itemdblclick: function (view, rec, item, index, eventObj) {
                        var getSelection = Ext.getCmp('grid_masterRepresentative').getSelectionModel().selected;
                        if (getSelection.length > 0) {
                            me.formType = 'edit';
                            me.edit_data = rec.data;
                            me.createWindow(me, 'add_masterRepresentative');
                        }
                    }
                },
                bbar: me.pagingtb_masterRepresentative,
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        xtype: 'textfield',
                        name: 'keywords',
                        id: 'keywords_masterRepresentative',
                        width: 200,
                        labelSeparator: '',
                        emptyText: 'Keywords: Nama/Email',
                        anchor: '100%'/*,
                        listeners: {
                            specialkey: function (field, e) {
                                if (e.getKey() == e.ENTER) {
                                    me.search_masterCustomer();
                                }
                            }
                        }*/
                    }, {
                        xtype: 'button',
                        text: 'Search',
                        itemId: 'btnFilterSearch_masterRepresentative',
                        tooltip: 'Search',
                        iconCls: 'btn-search',
                        handler: function () {
                            me.search_masterRepresentative();
                        }
                    }, {
                        xtype: 'button',
                        text: 'Reset',
                        itemId: 'btnResetFilterSearch_masterRepresentative',
                        tooltip: 'Reset',
                        iconCls: 'btn-reset',
                        handler: function () {
                            me.search_masterRepresentative(true);
                        }
                    }, '->', {
                        text: 'Add',
                        itemId: 'addButton_masterRepresentative',
                        tooltip: 'Add Representative',
                        iconCls: 'btn-add',
                        handler: function () {
                            me.formType = 'add';
                            me.createWindow(me, 'add_masterRepresentative');
                        }
                    }, '-', {
                        text: 'Delete',
                        itemId: 'deleteButton_masterRepresentative',
                        tooltip: 'Delete Representative',
                        iconCls: 'btn-delete',
                        disabled: true,
                        handler: function () {
                            var getSelection = Ext.getCmp('grid_masterRepresentative').getSelectionModel().selected;
                            if (getSelection.length > 0) {
                                me.delete_data = getSelection.items[0].data;
                                me.deleteConfirm_masterRepresentative();
                            } else {
                                ExtApp.Msg.info('Please Select Representative!');
                            }
                        }
                    }, '-', {
                        text: 'Print',
                        itemId: 'Button_printMasterRepresentative',
                        tooltip: 'Print Data',
                        iconCls: 'icon-print',
                        handler: function () {
                            me.print_masterRepresentative();
                        }
                    }, {
                        text: 'Download',
                        itemId: 'Button_downloadMasterRepresentative',
                        tooltip: 'Download Data (Excel)',
                        iconCls: 'icon-download',
                        handler: function () {
                            me.download_masterRepresentativeExcel();
                        }
                    }]
                }]
            }, {
                xtype: "component",
                id: 'print_area_masterRepresentative',
                autoEl: {
                    tag: "iframe",
                    src: url_report
                },
                border: false,
                hidden: true
            }],
            listeners: {
                boxready: function () {
                    me.search_masterRepresentative();
                },
                show: function () {}
            }
        });
    },
    add_masterRepresentative: function (me) {
        var theApp = me.app;
        var desktop = theApp.getDesktop();
        var Titletext = 'Add New ';
        if (me.formType == 'edit') {
            Titletext = 'Update ';
        }
        var store_masterStatus = theApp.getStore('master_pos.store_masterStatus', false);
        return desktop.createWindow({
            id: me.id + '_add_masterRepresentative',
            title: Titletext,
            width: 420,
            height: 350,
            iconCls: 'btn-add',
            animCollapse: false,
            constrainHeader: true,
            resizable: false,
            minimizable: false,
            maximizable: false,
            modal: true,
            border: 0,
            layout: {
                type: 'fit'
            },
            items: [{
                xtype: 'form',
                id: 'form_masterRepresentative',
                border: 0,
                bodyPadding: 10,
                margin: '0 0 0 0',
                items: [{
                    xtype: 'hidden',
                    name: 'form_type_masterRepresentative',
                    value: me.formType
                }, {
                    xtype: 'hidden',
                    name: 'id'
                }, {
                    xtype: 'textfield',
                    name: 'representative_name',
                    fieldLabel: 'Cust.Name',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    name: 'customer_contact_person',
                    fieldLabel: 'Contact Person',
                    anchor: '100%',
                }, {
                    xtype: 'textfield',
                    name: 'customer_address',
                    fieldLabel: 'Address',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    name: 'customer_phone',
                    fieldLabel: 'Phone',
                    anchor: '80%',
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    name: 'customer_email',
                    fieldLabel: 'Email',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'checkbox',
                    name: 'is_active',
                    fieldLabel: 'Status Active',
                    inputValue: '1'
                }, {
                    xtype: 'combobox',
                    name: 'customer_status',
                    id: 'customer_status_masterCustomer',
                    fieldLabel: 'Status',
                    store: store_masterStatus,
                    labelSeparator: ':',
                    width: 200,
                    displayField: 'name',
                    queryMode: 'local',
                    valueField: 'val',
                    typeAhead: true,
                    minChars: 1,
                    forceSelection: true,
                    listeners: {
                        select: function (combo, records, eOpts) {}
                    }
                }, {
                    xtype: 'textfield',
                    name: 'keterangan_blacklist',
                    fieldLabel: 'Ket.Blacklist',
                    anchor: '100%',
                    allowBlank: true
                }]
            }],
            buttons: [{
                text: 'Save',
                formBind: true,
                id: 'btnSave_masterCustomer',
                iconCls: 'btn-save',
                handler: function () {
                    me.save_masterCustomer();
                }
            }, {
                text: 'Cancel',
                iconCls: 'btn-cancel',
                handler: function () {
                    me.doClose(me.id + '_add_masterCustomer');
                }
            }],
            listeners: {
                show: function () {
                    var form = Ext.getCmp('form_masterCustomer').getForm();
                    if (me.formType == 'edit') {
                        form.setValues(me.edit_data);
                    } else {
                        form.reset();
                        form.findField('is_active').setValue(1);
                    }
                }
            }
        });
    }
});