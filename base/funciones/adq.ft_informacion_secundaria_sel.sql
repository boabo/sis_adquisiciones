CREATE OR REPLACE FUNCTION "adq"."ft_informacion_secundaria_sel"(
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_informacion_secundaria_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tinformacion_secundaria'
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

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'adq.ft_informacion_secundaria_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_INFSEC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		27-04-2017 15:15:26
	***********************************/

	if(p_transaccion='ADQ_INFSEC_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						infsec.id_informacion_sec,
						infsec.id_solicitud,
						infsec.validez_oferta,
						infsec.resultados,
						infsec.antecedentes,
						infsec.garantias,
						infsec.nro_cite,
						infsec.necesidad_contra,
						infsec.multas,
						infsec.concluciones,
                        infsec.recomendaciones,
						infsec.beneficios_contra,
						infsec.estado_reg,
						infsec.forma_pago,
						infsec.id_usuario_ai,
						infsec.id_usuario_reg,
						infsec.fecha_reg,
						infsec.usuario_ai,
						infsec.fecha_mod,
						infsec.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from adq.tinformacion_secundaria infsec
						inner join segu.tusuario usu1 on usu1.id_usuario = infsec.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = infsec.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_INFSEC_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		27-04-2017 15:15:26
	***********************************/

	elsif(p_transaccion='ADQ_INFSEC_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_informacion_sec)
					    from adq.tinformacion_secundaria infsec
					    inner join segu.tusuario usu1 on usu1.id_usuario = infsec.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = infsec.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	else

		raise exception 'Transaccion inexistente';

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
ALTER FUNCTION "adq"."ft_informacion_secundaria_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
