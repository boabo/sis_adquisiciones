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
	Phx.vista.FormSolicitud=Ext.extend(Phx.frmInterfaz,{
		ActSave:'../../sis_adquisiciones/control/Solicitud/insertarSolicitudCompleta',
		tam_pag: 10,
		//layoutType: 'wizard',
		layout: 'fit',
		autoScroll: false,
		breset: false,
		labelSubmit: '<i class="fa fa-check"></i> Siguiente',
		constructor:function(config)
		{

			//declaracion de eventos
			this.addEvents('beforesave');
			this.addEvents('successsave');
			//console.log('cosas',config,this.idContenedorPadre, this.idContenedor);
			this.maestro = config;
			this.desc_gestion = '';
			this.id_gestion = undefined;
			this.buildComponentesDetalle();
			this.buildDetailGrid();
			this.buildGrupos();

			Phx.vista.FormSolicitud.superclass.constructor.call(this,config);
			this.init();

			this.iniciarEventos();
			this.iniciarEventosDetalle();
			this.onNew();

			this.Cmp.tipo_concepto.store.loadData(this.arrayStore['Bien'].concat(this.arrayStore['Servicio'])/*.concat(this.arrayStore['Boa'])*/);

		},
		buildComponentesDetalle: function(){
			this.detCmp = {
				'id_concepto_ingas': new Ext.form.ComboBox({
					name: 'id_concepto_ingas',
					msgTarget: 'title',
					fieldLabel: 'Concepto',
					allowBlank: false,
					emptyText : 'Concepto...',
					store : new Ext.data.JsonStore({
						url:'../../sis_parametros/control/ConceptoIngas/listarConceptoIngasMasPartida',
						id : 'id_concepto_ingas',
						root: 'datos',
						sortInfo:{
							field: 'desc_ingas',
							direction: 'ASC'
						},
						totalProperty: 'total',
						fields: ['id_concepto_ingas','tipo','desc_ingas','movimiento','desc_partida','id_grupo_ots','filtro_ot','requiere_ot'],
						remoteSort: true,
						baseParams:{par_filtro:'desc_ingas#par.codigo',movimiento:'gasto', autorizacion: 'adquisiciones'}
					}),
					valueField: 'id_concepto_ingas',
					displayField: 'desc_ingas',
					hiddenName: 'id_concepto_ingas',
					forceSelection:true,
					typeAhead: false,
					triggerAction: 'all',
					listWidth:500,
					resizable:true,
					lazyRender:true,
					mode:'remote',
					pageSize:10,
					queryDelay:1000,
					minChars:2,
					qtip:'Si el conceto de gasto que necesita no existe por favor  comuniquese con el área de presupuestos para solictar la creación',
					tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{desc_ingas}</b></p><strong>{tipo}</strong><p>PARTIDA: {desc_partida}</p></div></tpl>',
				}),
				'id_centro_costo': new Ext.form.ComboRec({
					name:'id_centro_costo',
					msgTarget: 'title',
					origen:'CENTROCOSTO',
					fieldLabel: 'Centro de Costos',
					url: '../../sis_parametros/control/CentroCosto/listarCentroCostoFiltradoXDepto',
					emptyText : 'Centro Costo...',
					allowBlank: false,
					baseParams:{filtrar:'grupo_ep'}
				}),
				'id_orden_trabajo': new Ext.form.ComboRec({
					name:'id_orden_trabajo',
					msgTarget: 'title',
					sysorigen:'sis_contabilidad',
					fieldLabel: 'Orden Trabajo',
					origen:'OT',
					allowBlank:true
				}),

				'descripcion': new Ext.form.TextArea({
					name: 'descripcion',
					msgTarget: 'title',
					fieldLabel: 'Descripcion',
					allowBlank: false,
					anchor: '80%',
					maxLength:5000
				}),
				'cantidad_sol': new Ext.form.NumberField({
					name: 'cantidad_sol',
					msgTarget: 'title',
					fieldLabel: 'Cantidad',
					allowBlank: false,
					allowDecimals: false,
					maxLength:10
				}),
				'precio_unitario': new Ext.form.NumberField({
					name: 'precio_unitario',
					msgTarget: 'title',
					currencyChar:' ',
					fieldLabel: 'Prec. Unit.',
					minValue: 0.0001,
					allowBlank: false,
					allowDecimals: true,
					allowNegative:false,
					decimalPrecision:2
				}),
				'precio_total': new Ext.form.NumberField({
					name: 'precio_total',
					msgTarget: 'title',
					readOnly: true,
					allowBlank: true
				})

			}


		},
		iniciarEventosDetalle: function(){


			this.detCmp.precio_unitario.on('valid',function(field){
				var pTot = this.detCmp.cantidad_sol.getValue() *this.detCmp.precio_unitario.getValue();
				this.detCmp.precio_total.setValue(pTot);
			} ,this);

			this.detCmp.cantidad_sol.on('valid',function(field){
				var pTot = this.detCmp.cantidad_sol.getValue() * this.detCmp.precio_unitario.getValue();
				this.detCmp.precio_total.setValue(pTot);

			} ,this);

			this.detCmp.id_concepto_ingas.on('change',function( cmb, rec, ind){
				this.detCmp.id_orden_trabajo.reset();
			},this);

			this.detCmp.id_concepto_ingas.on('select',function( cmb, rec, ind){

				this.detCmp.id_orden_trabajo.store.baseParams = {
					filtro_ot:rec.data.filtro_ot,
					requiere_ot:rec.data.requiere_ot,
					id_grupo_ots:rec.data.id_grupo_ots
				};
				this.detCmp.id_orden_trabajo.modificado = true;
				if(rec.data.requiere_ot =='obligatorio'){
					this.detCmp.id_orden_trabajo.allowBlank = false;
					this.detCmp.id_orden_trabajo.setReadOnly(false);
				}
				else{
					this.detCmp.id_orden_trabajo.allowBlank = true;
					this.detCmp.id_orden_trabajo.setReadOnly(true);
				}
				this.detCmp.id_orden_trabajo.reset();


			},this);
		},

		onInitAdd: function(){


		},
		onCancelAdd: function(re,save){
			if(this.sw_init_add){
				this.mestore.remove(this.mestore.getAt(0));
			}

			this.sw_init_add = false;
			this.evaluaGrilla();
		},
		onUpdateRegister: function(){
			this.sw_init_add = false;
		},

		onAfterEdit:function(re, o, rec, num){
			//set descriptins values ...  in combos boxs

			var cmb_rec = this.detCmp['id_concepto_ingas'].store.getById(rec.get('id_concepto_ingas'));
			if(cmb_rec){
				rec.set('desc_concepto_ingas', cmb_rec.get('desc_ingas'));
			}

			var cmb_rec = this.detCmp['id_orden_trabajo'].store.getById(rec.get('id_orden_trabajo'));
			if(cmb_rec){
				rec.set('desc_orden_trabajo', cmb_rec.get('desc_orden'));
			}

			var cmb_rec = this.detCmp['id_centro_costo'].store.getById(rec.get('id_centro_costo'));
			if(cmb_rec){
				rec.set('desc_centro_costo', cmb_rec.get('codigo_cc'));
			}

		},

		evaluaRequistos: function(){
			//valida que todos los requistosprevios esten completos y habilita la adicion en el grid
			var i = 0;
			sw = true;
			while( i < this.Componentes.length) {

				if(!this.Componentes[i].isValid()){
					sw = false;
					//i = this.Componentes.length;
				}
				i++;
			}



			return sw
		},

		bloqueaRequisitos: function(sw){
			this.Cmp.id_depto.setDisabled(sw);
			this.Cmp.id_moneda.setDisabled(sw);

			this.Cmp.tipo_concepto.setDisabled(sw);
			this.Cmp.fecha_soli.setDisabled(sw);
			this.cargarDatosMaestro();

		},

		cargarDatosMaestro: function(){


			this.detCmp.id_orden_trabajo.store.baseParams.fecha_solicitud = this.Cmp.fecha_soli.getValue().dateFormat('d/m/Y');
			this.detCmp.id_orden_trabajo.modificado = true;

			this.detCmp.id_centro_costo.store.baseParams.id_gestion = this.Cmp.id_gestion.getValue();

			this.detCmp.id_centro_costo.store.baseParams.codigo_subsistema = 'ADQ';

			this.detCmp.id_centro_costo.store.baseParams.id_depto = this.Cmp.id_depto.getValue();
			this.detCmp.id_centro_costo.modificado = true;
			//cuando esta el la inteface de presupeustos no filtra por bienes o servicios
			this.detCmp.id_concepto_ingas.store.baseParams.tipo=this.Cmp.tipo.getValue();
			this.detCmp.id_concepto_ingas.store.baseParams.id_gestion=this.Cmp.id_gestion.getValue();
			this.detCmp.id_concepto_ingas.modificado = true;
            console.log('gestion', this.Cmp.id_gestion.getValue(), 'depto', this.Cmp.id_depto.getValue());
		},

		evaluaGrilla: function(){
			//al eliminar si no quedan registros en la grilla desbloquea los requisitos en el maestro
			var  count = this.mestore.getCount();
			if(count == 0){
				this.bloqueaRequisitos(false);
			}
		},


		buildDetailGrid: function(){

			//cantidad,detalle,peso,totalo
			var Items = Ext.data.Record.create([{
				name: 'cantidad_sol',
				type: 'int'
			}, {
				name: 'id_concepto_ingas',
				type: 'int'
			}, {
				name: 'id_centro_costo',
				type: 'int'
			}, {
				name: 'id_orden_trabajo',
				type: 'int'
			},{
				name: 'precio_unitario',
				type: 'float'
			},{
				name: 'precio_total',
				type: 'float'
			}
			]);

			this.mestore = new Ext.data.JsonStore({
				url: '../../sis_adquisiciones/control/SolicitudDet/listarSolicitudDet',
				id: 'id_solicitud_det',
				root: 'datos',
				totalProperty: 'total',
				fields: ['id_solicitud_det','id_centro_costo','descripcion', 'precio_unitario',
					'id_solicitud','id_orden_trabajo','id_concepto_ingas','precio_total','cantidad_sol',
					'desc_centro_costo','desc_concepto_ingas','desc_orden_trabajo'
				],remoteSort: true,
				baseParams: {dir:'ASC',sort:'id_solicitud_det',limit:'50',start:'0'}
			});

			this.editorDetail = new Ext.ux.grid.RowEditor({
				saveText: 'Aceptar',
				name: 'btn_editor'

			});

			this.summary = new Ext.ux.grid.GridSummary();
			// al iniciar la edicion
			this.editorDetail.on('beforeedit', this.onInitAdd , this);

			//al cancelar la edicion
			this.editorDetail.on('canceledit', this.onCancelAdd , this);

			//al cancelar la edicion
			this.editorDetail.on('validateedit', this.onUpdateRegister, this);

			this.editorDetail.on('afteredit', this.onAfterEdit, this);







			this.megrid = new Ext.grid.GridPanel({
				layout: 'fit',
				store:  this.mestore,
				region: 'center',
				split: true,
				border: false,
				plain: true,
				//autoHeight: true,
				plugins: [ this.editorDetail, this.summary ],
				stripeRows: true,
				tbar: [{
					/*iconCls: 'badd',*/
					text: '<i class="fa fa-plus-circle fa-lg"></i> Agregar Concepto',
					scope: this,
					width: '100',
					handler: function(){
						if(this.evaluaRequistos() === true){

							var e = new Items({
								id_concepto_ingas: undefined,
								cantidad_sol: 1,
								descripcion: '',
								precio_total: 0,
								precio_unitario: undefined
							});
							this.editorDetail.stopEditing();
							this.mestore.insert(0, e);
							this.megrid.getView().refresh();
							this.megrid.getSelectionModel().selectRow(0);
							this.editorDetail.startEditing(0);
							this.sw_init_add = true;

							this.bloqueaRequisitos(true);
						}
						else{
							//alert('Verifique los requisitos');
						}

					}
				},{
					ref: '../removeBtn',
					text: '<i class="fa fa-trash fa-lg"></i> Eliminar',
					scope:this,
					handler: function(){
						this.editorDetail.stopEditing();
						var s = this.megrid.getSelectionModel().getSelections();
						for(var i = 0, r; r = s[i]; i++){
							this.mestore.remove(r);
						}
						this.evaluaGrilla();
					}
				}],

				columns: [
					new Ext.grid.RowNumberer(),
					{
						header: 'Concepto',
						dataIndex: 'id_concepto_ingas',
						width: 200,
						sortable: false,
						renderer:function(value, p, record){return String.format('{0}', record.data['desc_concepto_ingas']);},
						editor: this.detCmp.id_concepto_ingas
					},
					{

						header: 'Centro de Costo',
						dataIndex: 'id_centro_costo',
						align: 'center',
						width: 200,
						renderer:function (value, p, record){return String.format('{0}', record.data['desc_centro_costo']);},
						editor: this.detCmp.id_centro_costo
					},
					{

						header: 'Orden de Trabajo',
						dataIndex: 'id_orden_trabajo',
						align: 'center',
						width: 150,
						renderer:function(value, p, record){return String.format('{0}', record.data['desc_orden_trabajo']?record.data['desc_orden_trabajo']:'');},
						editor: this.detCmp.id_orden_trabajo
					},
					{

						header: 'Descripción',
						dataIndex: 'descripcion',

						align: 'center',
						width: 200,
						editor: this.detCmp.descripcion
					},
					{

						header: 'Cantidad',
						dataIndex: 'cantidad_sol',
						align: 'center',
						width: 50,
						summaryType: 'sum',
						editor: this.detCmp.cantidad_sol
					},


					{

						header: 'P / Unit',
						dataIndex: 'precio_unitario',
						align: 'center',
						width: 50,
						trueText: 'Yes',
						falseText: 'No',
						minValue: 0.001,
						summaryType: 'sum',
						editor: this.detCmp.precio_unitario
					},
					{
						xtype: 'numbercolumn',
						header: 'Importe Total',
						dataIndex: 'precio_total',
						format: '$0,0.00',
						width: 50,
						sortable: false,
						summaryType: 'sum',
						editor: this.detCmp.precio_total
					}]
			});
		},
		buildGrupos: function(){
			this.Grupos = [{
				layout: 'border',
				border: false,
				frame:true,
				items:[
					{
						xtype: 'fieldset',
						border: false,
						split: true,
						layout: 'column',
						region: 'north',
						autoScroll: true,
						autoHeight: true,
						collapseFirst : false,
						collapsible: true,
						width: '100%',
						//autoHeight: true,
						padding: '0 0 0 10',
						items:[
							{
								bodyStyle: 'padding-right:5px;',

								border: false,
								autoHeight: true,
								items: [{
									xtype: 'fieldset',
									//frame: true,
									layout: 'form',
									title: ' DATOS BÁSICOS ',
									width: '33%',
									//border: false,
									//margins: '0 0 0 5',
									padding: '0 0 0 10',
									bodyStyle: 'padding-left:5px;',
									id_grupo: 0,
									items: [],
								}]
							},
							{
								bodyStyle: 'padding-right:5px;',

								autoHeight: true,
								border: false,
								items:[
									{
										xtype: 'fieldset',
										/*frame: true,
										border: false,*/
										layout: 'form',
										title: 'DATOS COTIZACIÓN',
										width: '33%',

										//margins: '0 0 0 5',
										padding: '0 0 0 10',
										bodyStyle: 'padding-left:5px;',
										id_grupo: 1,
										items: [],
									}]
							},
							{
								bodyStyle: 'padding-right:2px;',

								border: false,
								autoHeight: true,
								items: [{
									xtype: 'fieldset',
									//frame: true,
									layout: 'form',
									title: 'DATOS DESCRIPTIVOS',
									width: '33%',
									//border: false,
									//margins: '0 0 0 5',
									padding: '0 0 0 10',
									bodyStyle: 'padding-left:2px;',
									id_grupo: 2,
									items: [],
								}]
							}
						]
					},
					this.megrid
				]
			}];


		},

		loadValoresIniciales:function() {

			Phx.vista.FormSolicitud.superclass.loadValoresIniciales.call(this);

		},

		/*successSave:function(resp)
		{
			Phx.CP.loadingHide();
			Phx.CP.getPagina(this.idContenedorPadre).reload();
			this.panel.close();
		},*/


		arrayStore :{
			'Bien':[
				['bien','Bienes'],
				['boa_po','Boa PO']
				//['inmueble','Inmuebles'],
				//['vehiculo','Vehiculos']
			],
			'Servicio':[
				['servicio','Servicios'],
				['consultoria_personal','Consultoria de Personas'],
				['consultoria_empresa','Consultoria de Empresas'],
				['servicio_c','Servicio C.'],
				['boa_rep','Boa Rep.'],
				['capacitacion','Capacitacion']
				//['alquiler_inmueble','Alquiler Inmuebles']
			]/*,
			'Boa':[


			]*/
		},
		Atributos:[
			{
				//configuracion del componente
				config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_solicitud'
				},
				type:'Field',
				form:true
			},

			/*{
				//configuracion del componente
				config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_gestion'
				},
				type:'Field',
				form:true
			},*/

			{
				//configuracion del componente
				config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'tipo'
				},
				type:'Field',
				form:true
			},

			{
				config:{
					name : 'id_gestion',
					origen : 'GESTION',
					fieldLabel : 'Gestión',
					allowBlank : false,
					forceSelection:false,
					width: 125,
					listWidth:'232',
					pageSize: 5
				},
				type : 'ComboRec',
				id_grupo : 0,
				form : true
			},

			{
				config:{
					name: 'tipo_concepto',
					fieldLabel: 'Tipo',
					allowBlank: false,
					emptyText: 'Tipo...',
					store: new Ext.data.ArrayStore({
						fields :['variable','valor'],
						data :  []}),

					valueField: 'variable',
					displayField: 'valor',
					forceSelection: true,
					triggerAction: 'all',
					lazyRender: true,
					resizable:true,
					mode: 'local',
					width: '80%'
				},
				type:'ComboBox',
				id_grupo:0,
				form:true
			},

			{
				config: {
					name: 'id_categoria_compra',
					hiddenName: 'id_categoria_compra',
					fieldLabel: 'Categoria de Compra',
					typeAhead: false,
					forceSelection: false,
					allowBlank: false,
					emptyText: 'Categorias...',
					store: new Ext.data.JsonStore({
						url: '../../sis_adquisiciones/control/CategoriaCompra/listarCategoriaCompra',
						id: 'id_categoria_compra',
						root: 'datos',
						sortInfo: {
							field: 'catcomp.nombre',
							direction: 'ASC'
						},
						totalProperty: 'total',
						fields: ['id_categoria_compra', 'nombre', 'codigo'],
						// turn on remote sorting
						remoteSort: true,
						baseParams: { par_filtro: 'catcomp.nombre#catcomp.codigo', codigo_subsistema: 'ADQ'}
					}),
					valueField: 'id_categoria_compra',
					displayField: 'nombre',
					triggerAction: 'all',
					lazyRender: true,
					mode: 'remote',
					pageSize: 20,
					queryDelay: 200,
					listWidth:280,
					minChars: 2,
					width: '80%',
					tpl: '<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p>Codigo: <strong>{codigo}</strong> </div></tpl>'
				},
				type: 'ComboBox',
				id_grupo: 0,
				form: true
			},

			{
				config:{
					name:'id_moneda',
					origen:'MONEDA',
					allowBlank: false ,
					width: '80%',
					fieldLabel: 'Moneda'
				},
				type: 'ComboRec',
				id_grupo: 0,
				form: true
			},

			{
				config:{
					name:'id_funcionario',
					hiddenName: 'id_funcionario',
					origen: 'FUNCIONARIOCAR',
					fieldLabel:'Funcionario Solicitante',
					allowBlank: false,
					valueField: 'id_funcionario',
					width: '80%',
					baseParams: { es_combo_solicitud : 'si' }
				},
				type: 'ComboRec',//ComboRec
				id_grupo: 0,
				form: true
			},
			{
				config:{
					name:'id_depto',
					hiddenName: 'id_depto',
					url: '../../sis_parametros/control/Depto/listarDeptoFiltradoXUsuario',
					origen: 'DEPTO',
					allowBlank: false,
					fieldLabel: 'Depto',
					disabled: true,
					width: '80%',
					baseParams: { estado:'activo', codigo_subsistema: 'ADQ' },
				},
				type:'ComboRec',
				id_grupo: 0,
				form:true
			},

			{
				config:{
					name: 'nro_cuotas',
					fieldLabel: 'Nro. Cuotas',
					qtip: 'Establesca el Nro. de Cuotas aproximado para la Compra de un Bien o Contratación de un Servicio.',
					allowBlank: true,
					width: '100%'
				},
				type: 'NumberField',
				id_grupo: 0,
				form: true
			},


			{
				config:{
					name: 'fecha_soli',
					fieldLabel: 'Fecha Solicitud',
					allowBlank: false,
					disabled: false,
					readOnly: true,
					hidden:true,
					width: 125,
					format: 'd/m/Y'
				},
				type: 'DateField',
				id_grupo: 2,
				form: true
			},

			{
				config:{
					name: 'justificacion',
					fieldLabel: 'Justificación Compra',
					qtip:'Justifique, ¿por que la necesidad de esta compra?',
					allowBlank: false,
					width: 250,
					maxLength:500
				},
				type:'TextArea',
				id_grupo: 2,
				form:true
			},

			{
				config:{
					name: 'lugar_entrega',
					fieldLabel: 'Lugar de Entrega',
					qtip:'Proporcionar una buena descripcion para informar al proveedor, Ej. Entrega en oficinas de aeropuerto Cochabamba, Jaime Rivera #28',
					allowBlank: false,
					width: '100%',
					maxLength:255
				},
				type:'TextArea',
				id_grupo: 2,
				form:true
			},

			{
				config:{
					name: 'fecha_inicio',
					fieldLabel: 'Fecha de Inicio',
					qtip:'En que fecha se estima el inicio del servicio',
					allowBlank: false,
					disabled: false,
					format: 'd/m/Y',
					width: 105
				},
				type:'DateField',
				id_grupo: 2,
				form:true
			},

			{
				config:{
					name: 'fecha_fin',
					fieldLabel: 'Fecha Fin',
					qtip:'En que fecha se estima la finalización del servicio',
					allowBlank: false,
					disabled: false,
					format: 'd/m/Y',
					width: 105
				},
				type:'DateField',
				id_grupo: 2,
				form:true
			},

			{
				config:{
					name: 'dias_plazo_entrega',
					fieldLabel: 'Plazo de Entrega (Dias Calendario)',
					qtip: '¿Después de cuantos días calendario de emitida  la orden de compra se hara la entrega de los bienes?. EJM. Quedara de esta forma en la orden de Compra:  (Tiempo de entrega: X días calendario  a partir del dia siguiente de emitida la presente orden)',
					allowBlank: false,
					allowDecimals: false,
					width: 125,
					minValue:1,
					minLength:1,
					maxLength:50
				},
				type:'NumberField',
				filters:{pfiltro:'sold.dias_plazo_entrega',type:'numeric'},
				id_grupo: 2,
				form:true
			},
			{
				config:{
					name:'id_proveedor',
					hiddenName: 'id_proveedor',
					origen:'PROVEEDOR',
					fieldLabel:'Proveedor Precotizacion',
					allowBlank:false,
					tinit:false,
					width: '80%',
					valueField: 'id_proveedor'
				},
				type:'ComboRec',//ComboRec
				id_grupo: 1,
				form:true
			},
			{
				config:{
					name:'proveedor_unico',
					fieldLabel:'Proveedor Unico',
					allowBlank: true
				},
				type:'Checkbox',//ComboRec
				id_grupo: 1,
				form:true
			},

			{
				config:{
					name: 'correo_proveedor',
					fieldLabel: 'Email Proveedor',
					qtip: 'El correo del proveedor es necesario para el envió de notificaciones (como la orden de compra o invitación), asegúrese de que sea el correcto',
					allowBlank: true,
					width: '100%'

				},
				type: 'TextField',
				id_grupo: 1,
				form: true
			},

			{
				config:{
					name: 'fecha_ini_cot',
					fieldLabel: 'Fecha de Cotización.',
					qtip:'Fecha en la que se hace la Cotización',
					allowBlank: true,
					disabled: false,
					format: 'd/m/Y',
					width: 105
				},
				type:'DateField',
				id_grupo: 1,
				form:true
			},

			{
				config:{
					name: 'fecha_ven_cot',
					fieldLabel: 'Fecha Vencimiento Cotización',
					qtip:'Fecha Limite de la Cotización',
					allowBlank: true,
					disabled: false,
					format: 'd/m/Y',
					width: 105
				},
				type:'DateField',
				id_grupo: 1,
				form:true
			},

			{
				config:{
					name: 'nro_po',
					fieldLabel: 'Nro. de P.O.',
					qtip:'Ingrese el nro. de P.O.',
					allowBlank: false,
					disabled: false,
					//anchor: '80%',
					width: 125,
					gwidth: 100,
					maxLength:255
				},
				type:'TextField',
				id_grupo:1,
				grid:false,
				form:true
			},

			{
				config:{
					name: 'fecha_po',
					fieldLabel: 'Fecha de P.O.',
					qtip:'Fecha del P.O.',
					allowBlank: false,
					gwidth: 100,
					width: 125,
					format: 'd/m/Y',
					renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
				},
				type:'DateField',
				id_grupo:1,
				grid:false,
				form:true
			},

			{
				config:{
					name: 'precontrato',
					fieldLabel: 'Tipo de Contrato',
					qtip: 'Si tine un contrato de adhesion',
					allowBlank: false,
					emptyText: 'Tipo...',
					typeAhead: true,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'local',
					gwidth: 100,
					width: 125,
					store: ['no_necesita','contrato_nuevo','contrato_adhesion','ampliacion_contrato']
				},
				type: 'ComboBox',
				id_grupo: 2,
				filters:{
					type: 'list',
					pfiltro:'sol.tipo',
					options: ['no_necesita','contrato_nuevo','contrato_adhesion','ampliacion_contrato'],
				},
				valorInicial: 'no_necesita',
				grid:false,
				form:true
			}
		],
		title: 'Frm solicitud',

		iniciarEventos:function(){
			//Se carga el campo gestion con la gestion actual en la que se realiza la solicitud pero puede ser modifado 
			//y poder elegir una gestion siguiente.
			Ext.Ajax.request({
				url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
				params:{fecha:new Date()},
				success:function (resp) {
					var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
					if(!reg.ROOT.error){
						this.Cmp.id_gestion.setValue(reg.ROOT.datos.id_gestion);
						this.Cmp.id_gestion.setRawValue(reg.ROOT.datos.anho);
						this.desc_gestion = reg.ROOT.datos.anho;
						this.id_gestion = reg.ROOT.datos.id_gestion
					}else{

						alert('Ocurrio un error al obtener la Gestión')
					}
				},
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
			});

			this.Cmp.id_gestion.on('blur', function (rec) {

				this.Cmp.id_gestion.setValue(this.id_gestion);
				this.Cmp.id_gestion.setRawValue(this.desc_gestion);
			},this);

			this.cmpFechaSoli = this.getComponente('fecha_soli');
			this.cmpIdDepto = this.getComponente('id_depto');
			//this.cmpIdGestion = this.getComponente('id_gestion');


			/*Se aumentaron campos fecha_inicio, fecha_fin para establecer el rango de prestacion de servicios, nro_po, fecha_po
			para para la categoria Boa P.O.*/
			this.ocultarComponente(this.Cmp.fecha_inicio);
			this.ocultarComponente(this.Cmp.fecha_fin);
			this.ocultarComponente(this.Cmp.nro_po);
			this.ocultarComponente(this.Cmp.fecha_po);

			//inicio de eventos
			//En caso de que el campo fecha solicitud este deshabilitada


			/*this.Cmp.id_funcionario.enable();
			this.Cmp.id_funcionario.store.baseParams.fecha =this.cmpFechaSoli.getValue().dateFormat(this.cmpFechaSoli.format);*/
			/*this.cmpFechaSoli.on('change',function(f){

				//this.obtenerGestion(this.cmpFechaSoli);
				this.Cmp.id_funcionario.enable();
				this.Cmp.id_funcionario.store.baseParams.fecha = this.cmpFechaSoli.getValue().dateFormat(this.cmpFechaSoli.format);

			},this);*/

			this.Cmp.fecha_ini_cot.on('select',function( o, newValue, oldValue ){
				this.Cmp.fecha_ven_cot.setMinValue(newValue);
				this.Cmp.fecha_ven_cot.setMaxValue(new Date('12/31/'+newValue.getFullYear()));
				this.Cmp.fecha_ven_cot.reset();
			}, this);

			this.Cmp.fecha_inicio.on('select',function(o, newValue, oldValue ){
				this.Cmp.fecha_fin.setMinValue(newValue);
				maxValue = new Date('12/31/'+newValue.getFullYear());
				this.Cmp.fecha_fin.setMaxValue(maxValue);
				if(this.Cmp.tipo_concepto.getValue()=='boa_rep' || this.Cmp.tipo_concepto.getValue()=='boa_po'){
					this.Cmp.fecha_fin.setValue((this.Cmp.fecha_inicio.getValue().getDate()+30));
					this.Cmp.dias_plazo_entrega.setValue((this.Cmp.fecha_fin.getValue()-this.Cmp.fecha_inicio.getValue())/(1000*60*60*24));
				}
			},this);


			this.Cmp.tipo_concepto.on('select',function(cmp,rec){
				this.Cmp.fecha_inicio.reset();
				this.Cmp.fecha_fin.reset();
				this.Cmp.dias_plazo_entrega.reset();
				//Mostramos el campo nro_po y fecha_po para el tipo de servicion Boa PO
				if(cmp.value == 'boa_po'){
					this.mostrarComponente(this.Cmp.nro_po);
					this.mostrarComponente(this.Cmp.fecha_po);


				}else{
					this.ocultarComponente(this.Cmp.nro_po);
					this.ocultarComponente(this.Cmp.fecha_po);
				}


				//identificamos si es un bien o un servicio o tipo 'Boa', requerimiento reciente
				if(this.isInArray(rec.json, this.arrayStore['Bien'])){

					this.Cmp.tipo.setValue('Bien');
				}
				else if(this.isInArray(rec.json, this.arrayStore['Servicio'])){

					this.Cmp.tipo.setValue('Servicio');
				}/*else if(this.isInArray(rec.json, this.arrayStore['Boa'])){
					this.Cmp.tipo.setValue('Boa');
				}*/


				if(this.Cmp.tipo.getValue() == 'Bien'){
					this.Cmp.lugar_entrega.setValue('Almacenes de Oficina Cochabamba');
					this.mostrarComponente(this.Cmp.fecha_inicio);
					this.mostrarComponente(this.Cmp.fecha_fin);
					this.Cmp.dias_plazo_entrega.allowBlank = false;

				}
				else if(this.Cmp.tipo.getValue() == 'Servicio'){
					this.Cmp.lugar_entrega.setValue('');
					this.mostrarComponente(this.Cmp.fecha_inicio);
					this.mostrarComponente(this.Cmp.fecha_fin);
					this.Cmp.dias_plazo_entrega.allowBlank = true;

				}/*else if(this.Cmp.tipo.getValue() == 'Boa'){

					this.Cmp.lugar_entrega.setValue('');
					this.mostrarComponente(this.Cmp.fecha_inicio);
					this.mostrarComponente(this.Cmp.fecha_fin);
					this.Cmp.dias_plazo_entrega.allowBlank = true;


				}*/

				this.mostrarComponente(this.Cmp.dias_plazo_entrega);

			},this);



			this.Cmp.id_funcionario.on('select', function(combo, record, index){

				if(!record.data.id_lugar){
					alert('El funcionario no tiene oficina definida');
					return
				}

				this.Cmp.id_depto.reset();
				this.Cmp.id_depto.store.baseParams.id_lugar = record.data.id_lugar;
				this.Cmp.id_depto.modificado = true;
				this.Cmp.id_depto.enable();

				this.Cmp.id_depto.store.load({params:{start:0,limit:this.tam_pag},
					callback : function (r) {
						if (r.length == 1 ) {
							this.Cmp.id_depto.setValue(r[0].data.id_depto);
						}

					}, scope : this
				});


			}, this);

			this.Cmp.id_proveedor.on('select', function(combo, record, index){
				this.Cmp.correo_proveedor.reset();
				this.Cmp.correo_proveedor.setValue(record.data.email);

			}, this);

			this.Cmp.id_categoria_compra.on('select', function(combo, record, index){

				if(combo.lastSelectionText=='Compra Internacional'){

					Ext.Ajax.request({
						url:'../../sis_adquisiciones/control/Solicitud/listarMoneda',
						params:{nombre_moneda:'$us'},
						success:function (resp) {
							var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
							console.log(reg.ROOT.datos);
							this.Cmp.id_moneda.setValue(reg.ROOT.datos.id_moneda);
							this.Cmp.id_moneda.setRawValue(reg.ROOT.datos.moneda);
						},
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
					});
					
				}else if(combo.lastSelectionText=='Compra Nacional'){

					Ext.Ajax.request({
						url:'../../sis_adquisiciones/control/Solicitud/listarMoneda',
						params:{nombre_moneda:'Bs'},
						success:function (resp) {

							var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
							console.log(reg.ROOT.datos);
							this.Cmp.id_moneda.setValue(reg.ROOT.datos.id_moneda);
							this.Cmp.id_moneda.setRawValue(reg.ROOT.datos.moneda);
						},
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
					});
					
				}

			}, this);

		},

		/*obtenerGestion:function(x){

			var fecha = x.getValue().dateFormat(x.format);
			Phx.CP.loadingShow();
			Ext.Ajax.request({
				// form:this.form.getForm().getEl(),
				url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
				params:{fecha:fecha},
				success:this.successGestion,
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
			});
		},
		successGestion:function(resp){
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if(!reg.ROOT.error){

				this.cmpIdGestion.setValue(reg.ROOT.datos.id_gestion);


			}else{

				alert('ocurrio al obtener la gestion')
			}
		},*/
		onEdit:function(){

			this.cmpFechaSoli.disable();
			this.cmpIdDepto.disable();
			this.Cmp.id_categoria_compra.disable();


			this.Cmp.tipo.disable();
			this.Cmp.tipo_concepto.disable();
			this.Cmp.id_moneda.disable();
			this.Cmp.id_funcionario.store.baseParams.fecha = this.cmpFechaSoli.getValue().dateFormat(this.cmpFechaSoli.format);
			//this.Cmp.fecha_soli.fireEvent('change');
			//this.Cmp.fecha_inicio.fireEvent('change');

			if(this.Cmp.tipo.getValue() == 'Bien' ||  this.Cmp.tipo.getValue() == 'Bien - Servicio'){
				this.ocultarComponente(this.Cmp.fecha_inicio);
				this.Cmp.dias_plazo_entrega.allowBlank = false;
			}
			else{
				this.mostrarComponente(this.Cmp.fecha_inicio);
				this.Cmp.dias_plazo_entrega.allowBlank = true;
			}
			this.mostrarComponente(this.Cmp.dias_plazo_entrega);
		},

		onNew: function(){
			this.cmpIdDepto.disable();
			this.form.getForm().reset();
			this.loadValoresIniciales();

			//No es Necesario que el primer componente tenga el foco por defecto ya que se carga con un valor por defecto
			/*if(this.getValidComponente(0)){
				this.getValidComponente(0).focus(false, 100);
			}*/

			this.Cmp.id_categoria_compra.enable();

			//this.Cmp.id_funcionario.disable();
			this.Cmp.fecha_soli.enable();
			this.Cmp.fecha_soli.setValue(new Date());
			//this.Cmp.fecha_soli.fireEvent('change');
			//this.Cmp.fecha_inicio.fireEvent('change');

			this.Cmp.id_funcionario.store.baseParams.fecha = this.cmpFechaSoli.getValue().dateFormat(this.cmpFechaSoli.format);//Linea
			this.Cmp.tipo.enable();
			this.Cmp.tipo_concepto.enable();
			this.Cmp.id_moneda.enable();


			this.Cmp.id_categoria_compra.store.load({params:{start:0,limit:this.tam_pag},
				callback : function (r) {
					if (r.length == 1 ) {
						this.Cmp.id_categoria_compra.setValue(r[0].data.id_categoria_compra);
					}

				}, scope : this
			});

			/*
			 this.Cmp.id_depto.store.load({params:{start:0,limit:this.tam_pag},
			 callback : function (r) {
			 if (r.length == 1 ) {
			 this.Cmp.id_depto.setValue(r[0].data.id_depto);
			 }

			 }, scope : this
			 });*/


			this.Cmp.id_funcionario.store.load({params:{start:0,limit:this.tam_pag},
				callback : function (r) {
					if (r.length == 1 ) {
						this.Cmp.id_funcionario.setValue(r[0].data.id_funcionario);
						this.Cmp.id_funcionario.fireEvent('select', this.Cmp.id_funcionario, r[0]);
					}

				}, scope : this
			});

		},

		onSubmit: function(o) {
			//console.log('cosas',this.idContenedorPadre, this.idContenedor, Phx.CP.getPagina(this.idContenedorPadre));
			//  validar formularios
			var arra = [], i, me = this;
			for (i = 0; i < me.megrid.store.getCount(); i++) {
				record = me.megrid.store.getAt(i);
				arra[i] = record.data;
				arra[i].precio_ga = record.data.precio_total;
				arra[i].precio_sg = 0.0;
			}


			me.argumentExtraSubmit = { 'json_new_records': JSON.stringify(arra, function replacer(key, value) {
				/*if (typeof value === 'string') {
				 return String(value).replace(/&/g, "%26")
				 }*/
				return value;
			}) };

			if( i > 0 &&  !this.editorDetail.isVisible()){
				//Phx.vista.FormSolicitud.superclass.onSubmit.call(this, o, undefined, true);

			    if(this.Cmp.id_categoria_compra.getRawValue() == 'Compra Internacional') {
                    Ext.Ajax.request({
                        url: '../../sis_adquisiciones/control/Solicitud/validarNroPo',
                        params: {
                            nro_po: this.Cmp.nro_po.getValue(),
                            id_funcionario: this.Cmp.id_funcionario.getValue()

                        },
                        argument: {},
                        success: function (resp) {
                            var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.datos.v_valid == 'true') {
                                Ext.Msg.alert('Alerta', 'El P.O. Nro. <b>' + this.Cmp.nro_po.getValue() + '</b> ya fue registrado por el Funcionario <b> ' + reg.ROOT.datos.v_id_funcionario + '</b> , desea continuar el registro ');
                            }
                            else {
                                Phx.vista.FormSolicitud.superclass.onSubmit.call(this, o, undefined, true);
                            }

                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
                else{
                    Phx.vista.FormSolicitud.superclass.onSubmit.call(this, o, undefined, true);
                }
				
			}
			else{
				alert('no tiene ningun concepto  para comprar')
			}
		},

		successSave: function(resp){


			Phx.CP.loadingHide();
			//var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			Phx.CP.getPagina(this.idContenedorPadre).reload();
			this.panel.close();
			objRes.new = 'new';
			thos = this;
			Phx.CP.loadWindows('../../../sis_adquisiciones/vista/solicitud/SolicitudInformacionSec.php',
				'Información Secundaria',
				{
					width:'80%',
					height:'80%'
				},
				objRes,
				this.idContenedorPadre,
				'SolicitudInformacionSec'
			);
			//this.fireEvent('successsave',this,objRes);
		},
		
		loadCheckDocumentosSolWf:function(data) {
			//TODO Eventos para cuando ce cierre o destruye la interface de documentos
			Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
				'Documentos del Proceso',
				{
					width:'90%',
					height:500
				},
				data,
				this.idContenedor,
				'DocumentoWf'
			);

		}//Ventana que permite almacenar informacion secundaria para especificacion tecnica e informe de solicitud
		
	})
</script>