<?php
/**
*@package pXP
*@file gen-MODInformacionSecundaria.php
*@author  (admin)
*@date 27-04-2017 15:15:26
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODInformacionSecundaria extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarInformacionSecundaria(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='adq.ft_informacion_secundaria_sel';
		$this->transaccion='ADQ_INFSEC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_informacion_sec','int4');
		$this->captura('id_solicitud','int4');
		$this->captura('validez_oferta','int4');
		$this->captura('resultados','text');
		$this->captura('antecedentes','text');
		$this->captura('garantias','text');
		$this->captura('nro_cite','varchar');
		$this->captura('necesidad_contra','text');
		$this->captura('multas','text');
		$this->captura('concluciones','text');
		$this->captura('recomendaciones','text');
		$this->captura('beneficios_contra','text');
		$this->captura('estado_reg','varchar');
		$this->captura('forma_pago','text');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarInformacionSecundaria(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_informacion_secundaria_ime';
		$this->transaccion='ADQ_INFSEC_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_solicitud','id_solicitud','int4');
		$this->setParametro('validez_oferta','validez_oferta','text');
		$this->setParametro('resultados','resultados','text');
		$this->setParametro('antecedentes','antecedentes','text');
		$this->setParametro('garantias','garantias','text');
		//$this->setParametro('nro_cite','nro_cite','varchar');
		$this->setParametro('necesidad_contra','necesidad_contra','text');
		$this->setParametro('multas','multas','text');
		$this->setParametro('concluciones','concluciones','text');
		$this->setParametro('recomendaciones','recomendaciones','text');
		$this->setParametro('beneficios_contra','beneficios_contra','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('forma_pago','forma_pago','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarInformacionSecundaria(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_informacion_secundaria_ime';
		$this->transaccion='ADQ_INFSEC_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_informacion_sec','id_informacion_sec','int4');
		$this->setParametro('id_solicitud','id_solicitud','int4');
		$this->setParametro('validez_oferta','validez_oferta','text');
		$this->setParametro('resultados','resultados','text');
		$this->setParametro('antecedentes','antecedentes','text');
		$this->setParametro('garantias','garantias','text');
		$this->setParametro('nro_cite','nro_cite','varchar');
		$this->setParametro('necesidad_contra','necesidad_contra','text');
		$this->setParametro('multas','multas','text');
		$this->setParametro('concluciones','concluciones','text');
		$this->setParametro('recomendaciones','recomendaciones','text');
		$this->setParametro('beneficios_contra','beneficios_contra','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('forma_pago','forma_pago','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarInformacionSecundaria(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_informacion_secundaria_ime';
		$this->transaccion='ADQ_INFSEC_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_informacion_sec','id_informacion_sec','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>