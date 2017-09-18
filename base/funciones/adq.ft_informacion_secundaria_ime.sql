CREATE OR REPLACE FUNCTION "adq"."ft_informacion_secundaria_ime" (
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_informacion_secundaria_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tinformacion_secundaria'
 AUTOR: 		 (admin)
 FECHA:	        27-04-2017 15:15:26
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_informacion_sec	integer;

BEGIN

    v_nombre_funcion = 'adq.ft_informacion_secundaria_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_INFSEC_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		27-04-2017 15:15:26
	***********************************/

	if(p_transaccion='ADQ_INFSEC_INS')then

        begin
        	--Sentencia de la insercion
        	insert into adq.tinformacion_secundaria(
			id_solicitud,
			validez_oferta,
			resultados,
			antecedentes,
			garantias,
			nro_cite,
			necesidad_contra,
			multas,
			concluciones,
            recomendaciones,
			beneficios_contra,
			estado_reg,
			forma_pago,
			id_usuario_ai,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_solicitud,
			v_parametros.validez_oferta,
			v_parametros.resultados,
			v_parametros.antecedentes,
			v_parametros.garantias,
			v_parametros.nro_cite,
			v_parametros.necesidad_contra,
			v_parametros.multas,
			v_parametros.concluciones,
            v_parametros.recomendaciones,
			v_parametros.beneficios_contra,
			'activo',
			v_parametros.forma_pago,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_informacion_sec into v_id_informacion_sec;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Informacion Secundaria almacenado(a) con exito (id_informacion_sec'||v_id_informacion_sec||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_informacion_sec',v_id_informacion_sec::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_INFSEC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		27-04-2017 15:15:26
	***********************************/

	elsif(p_transaccion='ADQ_INFSEC_MOD')then

		begin
			--Sentencia de la modificacion
			update adq.tinformacion_secundaria set
			id_solicitud = v_parametros.id_solicitud,
			validez_oferta = v_parametros.validez_oferta,
			resultados = v_parametros.resultados,
			antecedentes = v_parametros.antecedentes,
			garantias = v_parametros.garantias,
			nro_cite = v_parametros.nro_cite,
			necesidad_contra = v_parametros.necesidad_contra,
			multas = v_parametros.multas,
			concluciones = v_parametros.concluciones,
            recomendaciones = v_parametros.recomendaciones,
			beneficios_contra = v_parametros.beneficios_contra,
			forma_pago = v_parametros.forma_pago,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_informacion_sec=v_parametros.id_informacion_sec;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Informacion Secundaria modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_informacion_sec',v_parametros.id_informacion_sec::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_INFSEC_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		27-04-2017 15:15:26
	***********************************/

	elsif(p_transaccion='ADQ_INFSEC_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from adq.tinformacion_secundaria
            where id_informacion_sec=v_parametros.id_informacion_sec;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Informacion Secundaria eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_informacion_sec',v_parametros.id_informacion_sec::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "adq"."ft_informacion_secundaria_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
