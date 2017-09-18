<?php
/**
*@package pXP
*@file gen-ACTInformacionSecundaria.php
*@author  (admin)
*@date 27-04-2017 15:15:26
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTInformacionSecundaria extends ACTbase{    
			
	function listarInformacionSecundaria(){
		$this->objParam->defecto('ordenacion','id_informacion_sec');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODInformacionSecundaria','listarInformacionSecundaria');
		} else{
			$this->objFunc=$this->create('MODInformacionSecundaria');
			
			$this->res=$this->objFunc->listarInformacionSecundaria($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarInformacionSecundaria(){
		$this->objFunc=$this->create('MODInformacionSecundaria');	
		if($this->objParam->insertar('id_informacion_sec')){
			$this->res=$this->objFunc->insertarInformacionSecundaria($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarInformacionSecundaria($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarInformacionSecundaria(){
			$this->objFunc=$this->create('MODInformacionSecundaria');	
		$this->res=$this->objFunc->eliminarInformacionSecundaria($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>