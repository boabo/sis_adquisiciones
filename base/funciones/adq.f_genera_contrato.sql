CREATE OR REPLACE FUNCTION adq.f_genera_contrato (
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_cotizacion integer,
  p_id_proceso_wf integer,
  p_id_estado_wf integer,
  p_codigo_ewf varchar
)
RETURNS boolean AS
$body$
/*
Autor: JRR
Fecha: 22/09/2014
Descripción: Generar la solicitud de elaboracion de contrato en el Sistema de Contratos


*/

DECLARE

	v_cotizacion 	 				record;
    v_solicitud 	 				text;
    v_resp			 				varchar;
    v_nombre_funcion 				varchar;
    v_tipo							varchar;
    v_id_tipo_estado_registro		integer;
    v_id_funcionario_responsable	integer;
    v_id_estado_registro			integer;
    		
    
    

BEGIN

	v_nombre_funcion='adq.f_genera_contrato';
     select c.* into v_cotizacion
     from adq.vcotizacion c
     where id_cotizacion = p_id_cotizacion;
     
     if (v_cotizacion.nombre_categoria = 'Compra Internacional') then
     	v_tipo = 'administrativo_internacional';
     elsif (v_cotizacion.tipo_concepto = 'alquiler_inmueble') then
     	v_tipo = 'administrativo_alquiler';
     ELSE
     	v_tipo = 'administrativo';
     end if;
     
     v_solicitud = 'Por favor realizar contrato para la solicitud de compra con numero de tramite : ' || 
     				v_cotizacion.v_num_tramite || '. Categoria : ' || v_cotizacion.nombre_categoria || '. Concepto : ' || v_cotizacion.tipo_concepto;
     
     /*Obtener el estado de registro*/
     select id_tipo_estado into v_id_tipo_estado_registro
     from wf.ttipo_estado te
     inner join wf.ttipo_proceso tp
     	on te.id_tipo_proceso = tp.id_tipo_proceso
     inner join wf.tproceso_wf p
     	on p.id_tipo_proceso = tp.id_tipo_proceso
     where te.codigo = 'registro' and p.id_proceso_wf = p_id_proceso_wf;
     
     /*Obtener el funcionario responsable para el siguiente estado*/
     select wf.f_funcionario_wf_sel(p_id_usuario, v_id_estado_registro) into v_id_funcionario_responsable;
     
     /*Registrar el estado de registro*/
     v_id_estado_registro =  wf.f_registra_estado_wf(v_id_tipo_estado_registro,   --p_id_tipo_estado_siguiente
                                                         v_id_funcionario_responsable, 
                                                         p_id_estado_wf,   --  p_id_estado_wf_anterior
                                                         p_id_proceso_wf,
                                                         p_id_usuario,
                                                         p_id_usuario_ai,
                                                         p_usuario_ai,
                                                         NULL,
                                                         'Paso de estado automatico por proceso de adquisiciones');
     /*Insertar el contrato con el ultimo estado*/
     INSERT INTO 
          leg.tcontrato
        (
          id_usuario_reg,
          id_usuario_ai,
          usuario_ai,  
          id_estado_wf,
          id_proceso_wf,
          estado,
          tipo,           
          id_gestion,  
          id_proveedor,
          solicitud,
          monto,
          id_moneda
        ) 
        VALUES (
          p_id_usuario,          
          p_id_usuario_ai,
          p_usuario_ai,
          v_id_estado_registro,
          p_id_proceso_wf,
          'registro',
          v_tipo,          
          v_cotizacion.id_gestion,
          v_cotizacion.id_proveedor,
          v_solicitud,
          v_cotizacion.monto_total_adjudicado,
          v_cotizacion.id_moneda
        );
     

    return true;
    
EXCEPTION
	WHEN OTHERS THEN
      v_resp='';
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
      v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
      v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
      raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;