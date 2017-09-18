<?php
/**
 *@package pXP
 *@file    FormSolicitud.php
 *@author  Rensi Arteaga Copari
 *@date    30-01-2014
 *@description permites subir archivos a la tabla de documento_sol
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.SolicitudInformacionSec=Ext.extend(Phx.frmInterfaz,{
        ActSave:'../../sis_adquisiciones/control/InformacionSecundaria/insertarInformacionSecundaria',
        tam_pag: 10,
        layout: 'fit',
        autoScroll: true,
        breset: false,
        labelSubmit: '<i class="fa fa-check"></i> Siguiente',
        constructor:function(config)
        {
            this.maestro = config;
            //declaracion de eventos
            //this.addEvents('beforesave');
            this.addEvents('successsave');
            console.log('conexion',this.maestro);
            if(this.maestro.new == 'new'){
                console.log('new');
                this.id_solicitud = this.maestro.ROOT.datos.id_solicitud;
                console.log('this.id_solicitud',this.id_solicitud);

            }else{
                this.cargar();
            }
            Phx.vista.SolicitudInformacionSec.superclass.constructor.call(this,config);

            this.init();

            //this.iniciarEventos();
            //this.iniciarEventosDetalle();
            //this.onNew();

            //this.Cmp.tipo_concepto.store.loadData(this.arrayStore['Bien'].concat(this.arrayStore['Servicio']));

        },

        cargar :  function () {
            console.log('cargar');
            Ext.Ajax.request({
                 url:'../../sis_adquisiciones/control/Solicitud/verificarInformacionSec',
                 params:{'id_solicitud':this.maestro.id_solicitud},
                 success: function (resp) {
                     var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));

                     if(reg.ROOT.datos.v_valid == 'true'){
                        this.id_solicitud = this.maestro.id_solicitud;
                     }else {
                         this.Cmp.id_informacion_sec.setValue(reg.ROOT.datos.v_id_informacion_sec);
                         this.Cmp.id_solicitud.setValue(reg.ROOT.datos.v_id_solicitud);
                         this.Cmp.antecedentes.setValue(reg.ROOT.datos.v_antecedentes);
                         this.Cmp.necesidad_contra.setValue(reg.ROOT.datos.v_necesidad_contra);
                         this.Cmp.beneficios_contra.setValue(reg.ROOT.datos.v_beneficios_contra);
                         this.Cmp.resultados.setValue(reg.ROOT.datos.v_resultados);
                         this.Cmp.concluciones.setValue(reg.ROOT.datos.v_conclusiones);
                         this.Cmp.recomendaciones.setValue(reg.ROOT.datos.v_recomendaciones);
                         this.Cmp.validez_oferta.setValue(reg.ROOT.datos.v_validez_oferta);
                         this.Cmp.garantias.setValue(reg.ROOT.datos.v_garantias);
                         this.Cmp.multas.setValue(reg.ROOT.datos.v_multas);
                         this.Cmp.forma_pago.setValue(reg.ROOT.datos.v_forma_pago);
                     }
                 },
                 failure: this.conexionFailure,
                 timeout:this.timeout,
                 scope:this
            });
        },

        Grupos : [{
                layout: 'column',
                border:false,
                defaults: {
                    border: false
                },
                items: [
                    {
                        bodyStyle: 'padding-right:10px;',
                        width: '50%',
                        items: [
                            {
                                xtype: 'fieldset',
                                title: 'INFORME',
                                //autoScroll: true,
                                autoHeight: true,
                                //layout: 'form',
                                items: [],
                                id_grupo: 0
                            }
                        ]
                    }
                    ,
                    {
                        bodyStyle: 'padding-right:10px;',
                        width: '50%',
                        items: [
                            {
                                xtype: 'fieldset',
                                title: 'DATOS TECNICOS',
                                //autoScroll: true,
                                autoHeight: true,
                                //layout: 'form',
                                items: [],
                                id_grupo: 1
                            }
                        ]
                    }
                ]
            }
            ]

        ,

        loadValoresIniciales:function(){
            Phx.vista.SolicitudInformacionSec.superclass.loadValoresIniciales.call(this);
        },

        /*successSave:function(resp)
        {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        },*/

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_informacion_sec'
                },
                type:'Field',
                form:true
            },
            {
                //configuracion del componente
                config:{
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_solicitud'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'antecedentes',
                    fieldLabel: 'Antecedentes',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:50,
                    qtip: 'Información que se debe proporcionar para los antecedentes de la solicitud de compra.'
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.antecedentes',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'necesidad_contra',
                    fieldLabel: 'Necesidad Contratatación',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:50,
                    qtip: 'Información que se debe proporcionar para la necesidad de compra de la solicitud de compra.'
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.antecedentes',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'beneficios_contra',
                    fieldLabel: 'Beneficios Contratación',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:50,
                    qtip: 'Información que se debe proporcionar sobre los beneficios de contratación de la solicitud de compra.'
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.beneficios_contra',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'resultados',
                    fieldLabel: 'Resultados Esperados',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:50,
                    qtip: 'Información sobre los resultados esperados de la solicitud de compra.'
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.resultados',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'concluciones',
                    fieldLabel: 'Concluciones',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:50,
                    qtip: 'Información sobre las conclusiones de la solicitud de compra.'
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.concluciones',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'recomendaciones',
                    fieldLabel: 'Recomendaciones',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:50,
                    qtip: 'Información sobre las recomendaciones de la solicitud de compra.'
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.recomendaciones',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'validez_oferta',
                    fieldLabel: 'Validez Oferta',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:100
                },
                type:'TextField',
                filters:{pfiltro:'infsec.validez_oferta',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'garantias',
                    fieldLabel: 'Garantias',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:5
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.garantias',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    name: 'multas',
                    fieldLabel: 'Multas(Si Corresponde)',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:5
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.multas',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'forma_pago',
                    fieldLabel: 'Forma de Pago',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:10000,
                    minLength:5
                },
                type:'TextArea',
                filters:{pfiltro:'infsec.forma_pago',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100
                },
                type:'TextField',
                filters:{pfiltro:'infsec.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }
        ],
        title: 'Form Información Secundaria.',
        onSubmit:function (o) {

            //var rec=Phx.CP.getPagina(this.config.idContenedorPadre).getSelectedData();
            //console.log('pensamiento',rec);
            if(this.maestro.new == 'new') {
                this.Cmp.id_solicitud.setValue(this.id_solicitud);
            }else{
                this.Cmp.id_solicitud.setValue(this.id_solicitud);
            }
            if(this.Cmp.antecedentes.getValue().length >=10  && this.Cmp.necesidad_contra.getValue().length >=10 && this.Cmp.beneficios_contra.getValue().length >=10 &&
                this.Cmp.resultados.getValue().length >=10 && this.Cmp.concluciones.getValue().length >=10 && this.Cmp.recomendaciones.getValue().length >=10 && this.Cmp.validez_oferta.getValue().length >=2 &&
                this.Cmp.garantias.getValue().length >=10 && this.Cmp.multas.getValue().length >=5 && this.Cmp.forma_pago.getValue().length >=5){

                Phx.vista.SolicitudInformacionSec.superclass.onSubmit.call(this,o);
            } else {

                Ext.Msg.show({
                    title: 'Información',
                    msg: 'Todos los campos tienen que tener una descripción, en caso de no rellenar estos campos, no podra dar continuidad a su Solicitud.',
                    buttons: Ext.Msg.OK,
                    width: 512,
                    icon: Ext.Msg.INFO
                });
            }
        },

        successSave : function (resp) {
            console.log('this.config', this.maestro, 'this.idContenedor', this.idContenedor, 'idContenedorPadre', this.idContenedorPadre);
            
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
            if(this.maestro.new == 'new') {
                Phx.CP.getPagina(this.idContenedorPadre).onSaveForm(this, this.maestro);
            }else{
                objRes = {ROOT:{datos:{}}};
                objRes.ROOT.datos.id_solicitud = this.maestro.id_solicitud;
                objRes.ROOT.datos.id_proceso_wf = this.maestro.id_proceso_wf;
                objRes.ROOT.datos.num_tramite = this.maestro.num_tramite;
                objRes.ROOT.datos.estado = this.maestro.estado;


                Phx.CP.getPagina(this.idContenedorPadre).onSaveForm(this, objRes);
            }

            //Phx.vista.SolicitudInformacionSec.superclass.successSave.call(this,resp);

        }

    })
</script>