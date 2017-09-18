<?php
/**
 *@package pXP
 *@file gen-Informe.php
 *@author  (admin)
 *@date 11-08-2016 01:52:07
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.VerificarFormularios=Ext.extend(Phx.gridInterfaz,{

        bnew : false,
        bedit : false,
        bdel : false,
        ActList:'../../sis_adquisiciones/control/Cotizacion/listarFormularios',
        tipo_fil: '',
        gruposBarraTareas:[
            {name:'form_400',title:'<H1 align="center"><i class="fa fa-file-text"></i>Formulario 400</h1>',grupo:1,height:0, width: 100},
            {name:'form_500',title:'<H1 align="center"><i class="fa fa-file-text"></i>Formulario 500</h1>',grupo:0,height:0, width: 100}


        ],
        bactGroups:  [0,1],
        bexcelGroups: [0,1],

        actualizarSegunTab: function(name, indice){
            console.log('this.cmbTipo.getValue()',this.tipo_fil);
            this.store.baseParams.tipo=this.tipo_fil;
            this.store.baseParams.pes_estado = name;

            this.load({params:{start:0, limit:this.tam_pag}});
        },

        constructor:function(config){
            this.tbarItems = ['-',
                this.cmbTipo,'-'

            ];
            this.maestro=config;
            //llama al constructor de la clase padre
            Phx.vista.VerificarFormularios.superclass.constructor.call(this,config);
            this.init();
            //this.store.baseParams={};
            this.tipo_fil = 'Proceso';
            this.store.baseParams = {pes_estado: 'form_400', nro_tramite:this.maestro.num_tramite,tipo: this.tipo_fil};
            this.load({params:{start:0, limit: 50}});
            this.cmbTipo.setRawValue('Proceso');
            this.cmbTipo.on('select',this.capturarEventos, this);

            this.addButton('btnChequeoDocumentosWf',{
                text: 'Documentos',
                grupo: [0,1,2,3,4,5],
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.loadCheckDocumentosRecWf,
                tooltip: '<b>Documentos del Reclamo</b><br/>Subir los documetos requeridos en el Reclamo seleccionado.'
            });

            this.addButton('diagrama_gantt',{
                grupo:[0,1,2,3,4,5],
                text:'Gant',
                iconCls: 'bgantt',
                disabled:true,
                handler:this.diagramGantt,
                tooltip: '<b>Diagrama Gantt de proceso macro</b>'
            });

        },

        loadCheckDocumentosRecWf: function() {
            var rec=this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Chequear documento del WF',
                {
                    width:'90%',
                    height:500
                },
                rec.data,
                this.idContenedor,
                'DocumentoWf'
            )
        },

        diagramGantt: function() {
            var data=this.sm.getSelected().data.id_proceso_wf;
            //Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                params:{'id_proceso_wf':data},
                success:this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        cmbTipo: new Ext.form.ComboBox({
            name: 'origen',
            //fieldLabel: 'Origen',
            allowBlank: true,
            anchor: '80%',
            gwidth: 100,
            maxLength: 25,
            typeAhead:true,
            forceSelection: true,
            triggerAction:'all',
            mode:'local',
            store:[ 'Todos','Proceso']
        }),

        capturarEventos: function () {
            this.tipo_fil = this.cmbTipo.getValue();
            this.store.baseParams.tipo=this.cmbTipo.getValue();
            this.load({params:{start:0, limit:50}});
        },

        Atributos:[
            {
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_proceso_compra'
                },
                type:'Field',
                form:false
            },
            {
                config:{
                    name: 'num_tramite',
                    fieldLabel: 'Nro. Tramite',
                    allowBlank: true,
                    anchor: '50%',
                    gwidth: 150,
                    maxLength:20,
                    readOnly:true,
                    renderer: function(value,p,record) {
                        return String.format('<b><font color="green">{0}</font></b>', value);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'tc.num_tramite',type:'string'},
                id_grupo:0,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'nro_cuota',
                    fieldLabel: 'Nro. Cuota',
                    allowBlank: true,
                    anchor: '50%',
                    gwidth: 150,
                    maxLength:20,
                    readOnly:true,
                    renderer: function(value,p,record) {
                        return String.format('<b><font color="green">{0}</font></b>', value);
                    }
                },
                type:'TextField',
                /*filters:{pfiltro:'trec.nro_tramite',type:'string'},*/
                id_grupo:0,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'id_funcionario',
                    fieldLabel: 'Funcionario Solicitante',
                    allowBlank: false,
                    anchor: '50%',
                    gwidth: 150/*,
                    renderer:function (value, p, record){return String.format('{0}', record.data['desc_funcionario']);}*/
                },
                type:'TextField',
                /*filters:{pfiltro:'trec.id_funcionario',type:'string'},*/
                id_grupo:0,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'tieneform400',
                    fieldLabel: 'Formulario 400',
                    allowBlank: true,
                    anchor: '50%',
                    height: 80,
                    gwidth: 150,
                    maxLength:100
                },
                type:'TextField',
                /*filters:{pfiltro:'conformidad',type:'string'},*/
                id_grupo:0,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'conformidad',
                    fieldLabel: 'Conformidad Ultima Cuota',
                    allowBlank: true,
                    anchor: '50%',
                    height: 80,
                    gwidth: 150,
                    maxLength:100
                },
                type:'TextField',
                /*filters:{pfiltro:'conformidad',type:'string'},*/
                id_grupo:0,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'tiene_form500',
                    fieldLabel: 'Formulario 500',
                    allowBlank: true,
                    anchor: '50%',
                    height: 80,
                    gwidth: 150,
                    maxLength:100
                },
                type:'TextField',
                /*filters:{pfiltro:'form500',type:'string'},*/
                id_grupo:0,
                grid:true,
                form:false
            }
        ],
        tam_pag:50,
        title:'VerificarFormularios',
        id_store:'id_proceso_compra',
        fields: [
            
            {name:'id_cotizacion', type: 'numeric'},
            {name:'num_tramite', type: 'string'},
            {name:'id_funcionario', type: 'string'},
            {name:'tiene_form500', type: 'string'},
            {name:'conformidad', type: 'string'},
            {name:'nro_cuota', type: 'numeric'},
            {name:'tieneform400', type: 'numeric'},
        ],
        sortInfo:{
            field: 'id_cotizacion',
            direction: 'ASC'
        },
        bsave:false,
        btest: false,

        preparaMenu: function(n){
            var rec = this.getSelectedData();
            var tb =this.tbar;
            Phx.vista.ConsultaForm500.superclass.preparaMenu.call(this,n);
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnChequeoDocumentosWf').setDisabled(false);

        },

        liberaMenu:function(){
            var tb = Phx.vista.ConsultaForm500.superclass.liberaMenu.call(this);
            if(tb){
                this.getBoton('btnChequeoDocumentosWf').setDisabled(true);
                this.getBoton('diagrama_gantt').disable();
            }
            return tb
        }

    });
</script>
