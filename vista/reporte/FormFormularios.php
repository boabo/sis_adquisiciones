<?php
/**
 *@package pXP
 *@file    FormFormularios.php
 *@author  Franklin Espinoza A.
 *@date    30-10-2016
 *@description permite filtrar varios campos antes de mostrar el contenido de una grilla
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormFormularios=Ext.extend(Phx.frmInterfaz,{

        nombreVista: 'FormFormularios',
        constructor:function(config)
        {
            //this.panelResumen = new Ext.Panel({html:''});
            this.Grupos = [{

                xtype: 'fieldset',
                border: false,
                autoScroll: true,
                layout: 'form',
                items: [],
                id_grupo: 0
                /*width: 500,
                 height:1000*/

            }/*,
             this.panelResumen*/
            ];

            Phx.vista.FormFormularios.superclass.constructor.call(this,config);
            //this.store.baseParams={tipo_interfaz:'FormGlobal'};
            this.init();
            //this.iniciarEventos();



        },

        Atributos:[
            /*{
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'tipo_interfaz',
                    value:'filtros'
                },
                type: 'Field',
                form: true,
                id_grupo:1
            },
            {
                config:{
                    name : 'id_gestion',
                    origen : 'GESTION',
                    fieldLabel : 'Gestion',
                    allowBlank : true,
                    width: 150
                },
                type : 'ComboRec',
                id_grupo : 0,
                form : true
            },
            {
                config:{
                    name: 'desde',
                    fieldLabel: 'Desde',
                    allowBlank: true,
                    format: 'd/m/Y',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config:{
                    name: 'hasta',
                    fieldLabel: 'Hasta',
                    allowBlank: true,
                    format: 'd/m/Y',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },*/
            {
                config: {
                    name: 'nro_tramite',
                    allowBlank: true,
                    fieldLabel: 'Nro. de Reclamo',
                    width: 150
                },
                type: 'Field',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'id_depto',
                    fieldLabel: 'Departamento',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_organigrama/control/Oficina/listarOficina',
                        id: 'id_oficina',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['id_oficina', 'nombre', 'codigo','nombre_lugar'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'ofi.nombre#ofi.codigo#lug.nombre'}
                    }),
                    valueField: 'id_oficina',
                    displayField: 'nombre',
                    gdisplayField: 'desc_oficina_registro_incidente',
                    hiddenName: 'id_oficina',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 10,
                    queryDelay: 1000,
                    anchor: '70%',
                    gwidth: 150,
                    minChars: 2,
                    resizable:true,
                    listWidth:'240',
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_oficina_registro_incidente']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 4,
                filters: {pfiltro: 'ofi.nombre#ofi.codigo#lug.nombre', type: 'string'},
                grid: true,
                form: true
            }
        ],
        labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
        east: {
            url: '../../../sis_adquisiciones/vista/reporte/VerificarFormularios.php',
            title: 'Detalle Filtro',
            width: '70%',
            cls: 'VerificarFormularios'
        },



        title: 'Filtros para verificar Fomularios',
        // Funcion guardar del formulario
        onSubmit: function(o) {
            var me = this;
            if (me.form.getForm().isValid()) {

                var parametros = me.getValForm()

                console.log('parametros ....', parametros);

                this.onEnablePanel(this.idContenedor + '-east', parametros)
            }
        }/*,

        iniciarEventos:function(){

        }*/
    })
</script>