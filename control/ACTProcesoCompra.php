<?php
/**
*@package pXP
*@file gen-ACTProcesoCompra.php
*@author  (admin)
*@date 19-03-2013 12:55:30
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

require_once(dirname(__FILE__).'/../../pxp/pxpReport/ReportWriter.php');
require_once(dirname(__FILE__).'/../reportes/RCuadroComparativo.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once(dirname(__FILE__).'/../reportes/RTiemposProcesoCompra.php');
require_once(dirname(__FILE__).'/../reportes/RepProcIniAdjEje.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleForm400.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleForm500.php');


class ACTProcesoCompra extends ACTbase{    
			
	function listarProcesoCompra(){
		$this->objParam->defecto('ordenacion','id_proceso_compra');

		$this->objParam->defecto('fecha_reg','desc');
		
		if($this->objParam->getParametro('filtro_campo')!=''){
            $this->objParam->addFiltro($this->objParam->getParametro('filtro_campo')." = ".$this->objParam->getParametro('filtro_valor'));  
        }
		/*
		if($this->objParam->getParametro('pendientes')=='1'){
             $this->objParam->addFiltro("(estado in (''pendiente'',''proceso'')) and (desc_cotizacion not like''%pago_habilitado%''  or desc_cotizacion is NULL)");
        }*/
		
		if(strtolower($this->objParam->getParametro('estado'))=='pendientes'){
             $this->objParam->addFiltro("(estado in (''pendiente'',''proceso'')) and (array_estados_cot is NULL  or  ''borrador''=ANY(array_estados_cot))");
        }
		
		if(strtolower($this->objParam->getParametro('estado'))=='anulados'){
             $this->objParam->addFiltro("(estado in (''pendiente'',''proceso'')) and (''anulado''=ANY(array_estados_cot))");
        }


		if(strtolower($this->objParam->getParametro('estado'))=='iniciados'){
             $this->objParam->addFiltro("(estado in (''pendiente'',''proceso'')) and (''cotizado''=ANY(array_estados_cot)  or ''adjudicado''=ANY(array_estados_cot) or ''recomendado''=ANY(array_estados_cot))");
        }
		if(strtolower($this->objParam->getParametro('estado'))=='contrato'){
             $this->objParam->addFiltro("(estado in (''pendiente'',''proceso'')) and ( ''contrato_pendiente''=ANY(array_estados_cot) or ''contrato_elaborado''=ANY(array_estados_cot))");
        }
        
		if(strtolower($this->objParam->getParametro('estado'))=='en pago'){
             $this->objParam->addFiltro(" (''pago_habilitado''=ANY(array_estados_cot)  or  ''finalizada''=ANY(array_estados_cot))  and estado != ''finalizado'' ");
        }
        if(strtolower($this->objParam->getParametro('estado'))=='finalizados'){
             $this->objParam->addFiltro("(estado in (''finalizado'',''anulado''))");
			 //$this->objParam->addFiltro("(''finalizada''=ANY(array_estados_cot) or (estado in (''finalizado'',''anulado'')))");
        }
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODProcesoCompra','listarProcesoCompra');
		} else{
			$this->objFunc=$this->create('MODProcesoCompra');
			
			$this->res=$this->objFunc->listarProcesoCompra($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarProcesoCompra(){
		$this->objFunc=$this->create('MODProcesoCompra');	
		if($this->objParam->insertar('id_proceso_compra')){
			$this->res=$this->objFunc->insertarProcesoCompra($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarProcesoCompra($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function asignarUsuarioProceso(){
        $this->objFunc=$this->create('MODProcesoCompra');   
        $this->res=$this->objFunc->asignarUsuarioProceso($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	function eliminarProcesoCompra(){
	    $this->objFunc=$this->create('MODProcesoCompra');	
		$this->res=$this->objFunc->eliminarProcesoCompra($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function revertirPresupuesto(){
        $this->objFunc=$this->create('MODProcesoCompra');   
        $this->res=$this->objFunc->revertirPresupuesto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function finalizarProceso(){
        $this->objFunc=$this->create('MODProcesoCompra');   
        $this->res=$this->objFunc->finalizarProceso($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	function listarReporteTiemposProcesoCompra(){

		$this->objParam->addParametro('tipo','detalle');
		$this->objFunc=$this->create('MODProcesoCompra');				
		$this->res=$this->objFunc->listarReporteTiemposProcesoCompra($this->objParam);
		$this->objParam->addParametro('datos',$this->res->datos);


		$this->objParam->addParametro('tipo','resumen');
		$this->objFunc=$this->create('MODProcesoCompra');		
		$this->res=$this->objFunc->listarReporteTiemposProcesoCompraResumen($this->objParam);		
		
		$this->objParam->addParametro('datos_resumen',$this->res->datos);	
		
		//obtener titulo del reporte
		$titulo = 'RepTiemposProcesoCompra';
		
		//Genera el nombre del archivo (aleatorio + titulo)
		$nombreArchivo=uniqid(md5(session_id()).$titulo);
		$nombreArchivo.='.xls';
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);			
		
		$this->objReporteFormato=new RTiemposProcesoCompra($this->objParam);			
		$this->objReporteFormato->imprimeDetalle();
		$this->objReporteFormato->imprimeResumen();	
		
		$this->objReporteFormato->generarReporte();
		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
										'Se generó con éxito el reporte: '.$nombreArchivo,'control');
										
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());			
	}

    function procesosIniciadosAdjudicadosEjecutados(){

        $this->objParam->addParametro('tipo','iniciados');
        $this->objFunc=$this->create('MODProcesoCompra');
        $this->res=$this->objFunc->procesosIniciadosAdjudicadosEjecutados($this->objParam);
        $this->objParam->addParametro('iniciados',$this->res->datos);

        $this->objParam->addParametro('tipo','adjudicados');
        $this->objFunc=$this->create('MODProcesoCompra');
        $this->res=$this->objFunc->procesosIniciadosAdjudicadosEjecutados($this->objParam);
        $this->objParam->addParametro('adjudicados',$this->res->datos);

        $this->objParam->addParametro('tipo','ejecutados');
        $this->objFunc=$this->create('MODProcesoCompra');
        $this->res=$this->objFunc->procesosIniciadosAdjudicadosEjecutados($this->objParam);
        $this->objParam->addParametro('ejecutados',$this->res->datos);

        $this->objFunc=$this->create('MODProcesoCompra');
        $this->res=$this->objFunc->procesosIniAdjuEjecResumen($this->objParam);
        $this->objParam->addParametro('datos_resumen',$this->res->datos);

        //obtener titulo del reporte
        $titulo = 'RepProcIniAdjEje';

        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->objReporteFormato=new RepProcIniAdjEje($this->objParam);
        $this->objReporteFormato->imprimeIniciados();
        $this->objReporteFormato->imprimeAdjudicados();
        $this->objReporteFormato->imprimeEjecutados();
        $this->objReporteFormato->imprimeResumen();

        $this->objReporteFormato->generarReporte();
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');

        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

	function cuadroComparativo(){
			
            $dataSource = new DataSource();
			
			
			$this->objParam->addParametroConsulta('ordenacion','id_proceso_compra');
            $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
            $this->objParam->addParametroConsulta('cantidad',1);
            $this->objParam->addParametroConsulta('puntero',0);
            
            
            //recupera datos del proceso de compra
            $this->objFunc = $this->create('MODProcesoCompra');
            $resultProcesoCompra = $this->objFunc->listarProcesoCompraPedido();
            $datosProcesoCompra = $resultProcesoCompra->getDatos();
           
            
            if($resultProcesoCompra->getTipo()=='ERROR'){
                              
                      $resultProcesoCompra->imprimirRespuesta($resultProcesoCompra-> generarMensajeJson());
                      exit;
            }
        	
        	
        	$idSolicitud=$datosProcesoCompra[0]['id_solicitud'];
        	

        	//armamos el array parametros y metemos ahi los data sets de las otras tablas
            $dataSource->putParameter('id_proceso_compra', $datosProcesoCompra[0]['id_proceso_compra']);
			$dataSource->putParameter('codigo_proceso', $datosProcesoCompra[0]['codigo_proceso']);
			$dataSource->putParameter('desc_solicitud', $datosProcesoCompra[0]['desc_solicitud']);
			
			
			
			//recupera el detalle de la solicituf
			$this->objParam->addParametroConsulta('ordenacion', 'id_solicitud_det');
            $this->objParam->addParametroConsulta('cantidad', 1000);
            $this->objParam->addParametroConsulta('puntero', 0);
            $this->objParam->addParametro('id_solicitud', $idSolicitud);
            
            $modSolicitudDet = $this->create('MODSolicitudDet');
            $resultSolicitudDet = $modSolicitudDet->listarSolicitudDet();        
            $datosResultSolicitudDet = $resultSolicitudDet->getDatos();
            if($resultSolicitudDet->getTipo()=='ERROR'){
                              
                      $resultSolicitudDet->imprimirRespuesta($resultSolicitudDet-> generarMensajeJson());
                      exit;
            }
            
            //var_dump($datosResultSolicitudDet);
			$solicitudDetDataSource = new DataSource();
			$solicitudDetDataSource->setDataSet($datosResultSolicitudDet);
			//registra el dataSoruce para los detalles de la solicitud de compra
			$dataSource->putParameter('detalleSolicitudDataSource', $solicitudDetDataSource);
								
                 
                 
             //recuepra las cotizaciones del proceso
             //get detalle
             //Reset all extra params:
		    $this->objParam->addParametroConsulta('ordenacion', 'id_cotizacion');
            $this->objParam->addParametroConsulta('cantidad', 1000);
            $this->objParam->addParametroConsulta('puntero', 0);
            
                    
            $modCotizacion = $this->create('MODCotizacion');
            $resultCotizacion = $modCotizacion->listarCotizacion();        
            $datosResultCotizacion = $resultCotizacion->getDatos();
            
            if($resultCotizacion->getTipo()=='ERROR'){
                              
                      $resultCotizacion->imprimirRespuesta($resultCotizacion-> generarMensajeJson());
                      exit;
            }
     
             //recorre las cotizaciones y recupera los datos ofertados
            for ($i=0; $i <count($datosResultCotizacion) ; $i++) {             
                
                    $idCotizacion = $datosResultCotizacion[$i]['id_cotizacion'];
                    
                    $this->objParam->addParametroConsulta('ordenacion', 'id_solicitud_det');
                    $this->objParam->addParametroConsulta('cantidad', 1000);
                    $this->objParam->addParametroConsulta('puntero', 0);
                    $this->objParam->addParametro('id_cotizacion', $idCotizacion);
                            
                    $modCotizacionDet = $this->create('MODCotizacionDet');
                    $resultCotizacionDet = $modCotizacionDet->listarCotizacionDet(); 
                    
                    $datosResultCotizacionDet=$resultCotizacionDet->getDatos();
                    $resultCotizacionDet->setDatos($datosResultCotizacionDet);
                    
                    //crea un data source para almacenar las cotizaciones
                    $cotizacionDetDataSource = new DataSource();
                    $cotizacionDetDataSource->setDataSet($resultCotizacionDet->getDatos());
                    //recursivamente agregar el detalle de la cotizacion como un dataset
                    $datosResultCotizacion[$i]['dataset']=$cotizacionDetDataSource;
                                   
             }
             
             $resultCotizacion->setDatos($datosResultCotizacion);
             
		     $cotizacionDataSource = new DataSource();        
    	     $cotizacionDataSource->setDataSet($resultCotizacion->getDatos());
    	     
    	     //alamcena en data source principal los datos de la cotizacion
    	     $dataSource->putParameter('cotizacionDataSource', $cotizacionDataSource);
    				        
             //build the report
             $reporte = new RCuadroComparativo();
             //configura el data set principal para armar el reporte
             $reporte->setDataSource($dataSource);
        	 $nombreArchivo = 'CuadroComparativo.xls';
              
             //almacena el archivo temporalmente 				
             $reportWriter = new ReportWriter($reporte, dirname(__FILE__).'/../../reportes_generados/'.$nombreArchivo);
             $reportWriter->writeReport('xls');
            
              //retorna el nombre del archivo temporal al cliente
             $mensajeExito = new Mensaje();
             $mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
                                            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
             
             $mensajeExito->setArchivoGenerado($nombreArchivo);
             $this->res = $mensajeExito;
             $this->res->imprimirRespuesta($this->res->generarJson());

  }
    function listarForm400(){

        switch($this->objParam->getParametro('pes_estado')) {
            case 'con_form':
                $this->objParam->addFiltro("tdw.chequeado = " . "''si''");
                break;
            case 'sin_form':
                $this->objParam->addFiltro("tdw.chequeado = " . "''no''");
                break;
        }

        if ($this->objParam->getParametro('id_usuario') != '') {
            $this->objParam->addFiltro("tpc.id_usuario_auxiliar = ". $this->objParam->getParametro('id_usuario'));
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODProcesoCompra','listarForm400');
        }else {

            $this->objFunc = $this->create('MODProcesoCompra');
            $this->res = $this->objFunc->listarForm400($this->objParam);
        }

		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function listarForm500(){

        switch($this->objParam->getParametro('pes_estado')) {
            case 'con_form':
                $this->objParam->addFiltro("tdw.chequeado = " . "''si''");
                break;
            case 'sin_form':
                $this->objParam->addFiltro("tdw.chequeado = " . "''no''");
                break;
        }

        if ($this->objParam->getParametro('id_usuario') != '') {
            $this->objParam->addFiltro("tpc.id_usuario_auxiliar = ". $this->objParam->getParametro('id_usuario'));
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODProcesoCompra','listarForm500');
        }else {

            $this->objFunc = $this->create('MODProcesoCompra');
            $this->res = $this->objFunc->listarForm500($this->objParam);
        }
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    /*(F.E.A)Permite controlar si hay procesos con formulario 400 y 500 que son igual o menores 5 dias
    para su vencimiento y se empieza a alertar a los auxiliares de adquisiciones.*/
	function alertarFormularios_4_5(){

        $this->objFunc=$this->create('MODProcesoCompra');
        //formulario 400
        $dataSource=$this->objFunc->alertarFormularios_4($this->objParam);
        $this->res = $dataSource;
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Detalle-Form400]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm400($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');
        
        $evento = "enviarMensajeUsuario";
        //datos para el websocket
        $data = array(
            "mensaje" => 'Por favor adjuntar formulario 400 para los siguientes procesos.',
            "tipo_mensaje" => 'notificacion',//documento_generado
            "titulo" => 'Control Documentos',
            "id_usuario" => $this->dataSource[0]['id_usuario'],
            "destino" => 'Unico',
            "evento" => $evento,
            "url" => $nombreArchivo
        );
        $send = array(
            "tipo" => "enviarMensajeUsuario",
            "data" => $data
        );
        $usuarios_socket = $this->dispararEventoWS($send);
        $usuarios_socket =json_decode($usuarios_socket, true);
        //end 400


        //formulario 500
        $this->objFunc=$this->create('MODProcesoCompra');
        $dataSource=$this->objFunc->reportePendientesForm500($this->objParam);
        $this->res = $dataSource;
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Detalle-Form500]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm500($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');

        $evento = "enviarMensajeUsuario";
        //datos para el websocket
        $data = array(
            "mensaje" => 'Por favor adjuntar formulario 500 para los siguientes procesos.',
            "tipo_mensaje" => 'notificacion',//documento_generado
            "titulo" => 'Control Documentos Form. 500',
            "id_usuario" => $this->dataSource[0]['id_usuario'],
            "destino" => 'Unico',
            "evento" => $evento,
            "url" => $nombreArchivo
        );
        $send = array(
            "tipo" => "enviarMensajeUsuario",
            "data" => $data
        );
        $usuarios_socket = $this->dispararEventoWS($send);
        $usuarios_socket =json_decode($usuarios_socket, true);

        //end 500
        
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        //$this->res->imprimirRespuesta($this->res->generarJson());

    }

    /*(F.E.A)Permite generara reporte pdf con el detalle de los procesos pendientes
    del formulario 400.*/
    function reportePendientesForm400(){

        $this->objFunc=$this->create('MODProcesoCompra');
        $dataSource=$this->objFunc->reportePendientesForm400($this->objParam);
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Pendientes-Form400]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm400($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    /*(F.E.A)Permite generara reporte pdf con el detalle de los procesos pendientes
    del formulario 500.*/
    function reportePendientesForm500(){

        $this->objFunc=$this->create('MODProcesoCompra');
        $dataSource=$this->objFunc->reportePendientesForm500($this->objParam);
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Pendientes-Form500]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm500($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    /*(f.e.a) Recupera el id_usuario y descripcion del usuario para la primera carga de la interfaz ConsultaForm400*/
    function getDatosUsuario () {
        $this->objFunc=$this->create('MODProcesoCompra');
        $this->res=$this->objFunc->getDatosUsuario($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    
}

?>