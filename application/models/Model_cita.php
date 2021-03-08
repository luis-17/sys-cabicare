<?php
class Model_cita extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_citas_en_grilla($paramPaginate,$paramDatos){
		$desde = $this->db->escape(darFormatoYMD($paramDatos['fechaDesde']));
 		$hasta = $this->db->escape(darFormatoYMD($paramDatos['fechaHasta']));
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.usuarioId,
			ci.sedeId,
			ci.fechaAtencion,
			ci.fechaCita,
			ci.horaDesde,
			ci.horaHasta,
			ci.apuntesCita,
			ci.total,
			ci.peso,
			ci.talla,
			ci.imc,
			ci.presionArterial,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.estado,
			ci.medioContacto,
			ci.gestando,
			ci.metodoPago,
			ci.numOperacion,
			ci.fechaUltimaRegla,
			ci.fechaProbableParto,
			ci.semanaGestacion,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.tipoDocumento,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
			ci.anotacionesPago,
			ci.tipoDocumentoCont,
			ci.numDoc,
			ci.numSerie
		", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		// $this->db->where('ci.estado <> ', 0);
		$this->db->where('pa.estado', 1);
		$this->db->where('ci.fechaCita BETWEEN ' . $desde .' AND ' . $hasta);

		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}

		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}

	public function m_cargar_citas_excel($paramPaginate,$paramDatos){
		$desde = $this->db->escape(darFormatoYMD($paramDatos['fechaDesde']));
 		$hasta = $this->db->escape(darFormatoYMD($paramDatos['fechaHasta']));
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.usuarioId,
			ci.sedeId,
			ci.fechaAtencion,
			ci.fechaCita,
			ci.horaDesde,
			ci.horaHasta,
			ci.apuntesCita,
			ci.subtotal,
			ci.igv,
			ci.total,
			ci.peso,
			ci.talla,
			ci.imc,
			ci.presionArterial,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.estado,
			ci.medioContacto,
			ci.gestando,
			ci.fechaUltimaRegla,
			ci.fechaProbableParto,
			ci.semanaGestacion,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.tipoDocumento,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
			ci.anotacionesPago,
			ci.tipoDocumentoCont,
			ci.numSerie,
			ci.numDoc,
			pg.numOperacion,
			pg.metodoPago,
			pg.monto,
			pg.fechaRegistro
		", FALSE);
		$this->db->from('cita ci');
		$this->db->join('pago pg', 'ci.id = pg.citaId');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		$this->db->where('ci.estado <> ', 0);
		$this->db->where('pa.estado', 1);
		$this->db->where('pg.estado', 1);
		$this->db->where('ci.fechaCita BETWEEN ' . $desde .' AND ' . $hasta);

		if( isset($paramPaginate['search'] ) && @$paramPaginate['search'] ){
			foreach (@$paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}

		if( @$paramPaginate['sortName'] ){
			$this->db->order_by(@$paramPaginate['sortName'], @$paramPaginate['sort']);
		}
		if( @$paramPaginate['firstRow'] || @$paramPaginate['pageSize'] ){
			$this->db->limit(@$paramPaginate['pageSize'],@$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}

	public function m_cargar_documentos_excel($paramDatos) {
		$this->db->select("do.id AS documentoId, do.mes, do.anio, do.categoria, do.codigoExterno, do.observaciones, 
		do.monto, do.estado, do.numSerie, do.numDoc, do.ruc, do.moneda, do.dia,
        do.nombreArchivo, do.fechaCreacion, us.id AS usuarioId, concat_ws(' ', us.nombres, us.apellidos) AS usuarioRegistro,", FALSE);
		$this->db->from('documento do');
		$this->db->join('usuario us', 'do.usuarioEnvioId = us.id');
        $this->db->where('do.estado', 1);

        if( $paramDatos['categoriaDoc']['id'] != 'ALL' ){
            $this->db->where('do.categoria', $paramDatos['categoriaDoc']['id']);
        }
        if( $paramDatos['mesDoc']['id'] != 'ALL' ){
            $this->db->where('do.mes', $paramDatos['mesDoc']['id']);
        }
        if( $paramDatos['anioDoc']['id'] != 'ALL' ){
            $this->db->where('do.anio', $paramDatos['anioDoc']['id']);
        }
		
        if( !($this->sessionFactur['keyPerfil'] == 'key_root' || $this->sessionFactur['keyPerfil'] == 'key_cont') ){
            $this->db->where('do.usuarioEnvioId', $this->sessionFactur['usuarioId']);
        }
		return $this->db->get()->result_array();
	}

	public function m_count_citas_en_grilla($paramPaginate,$paramDatos){
		$desde = $this->db->escape(darFormatoYMD($paramDatos['fechaDesde']));
 		$hasta = $this->db->escape(darFormatoYMD($paramDatos['fechaHasta']));
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		// $this->db->where('ci.estado <> ', 0);
		$this->db->where('pa.estado', 1);
		$this->db->where('ci.fechaCita BETWEEN ' . $desde .' AND ' . $hasta);

		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}

	public function m_cargar_atenciones_en_grilla($paramPaginate,$paramDatos){
		$desde = $this->db->escape(darFormatoYMD($paramDatos['fechaDesde']));
 		$hasta = $this->db->escape(darFormatoYMD($paramDatos['fechaHasta']));
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.usuarioId,
			ci.sedeId,
			ci.fechaAtencion,
			ci.fechaCita,
			ci.horaDesde,
			ci.horaHasta,
			ci.apuntesCita,
			ci.total,
			ci.peso,
			ci.talla,
			ci.imc,
			ci.presionArterial,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.estado,
			ci.medioContacto,
			ci.metodoPago,
			ci.numOperacion,
			ci.fechaUltimaRegla,
			ci.fechaProbableParto,
			ci.semanaGestacion,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.tipoDocumento,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
			uscr.username
		", FALSE);
		// $this->db->select("DATE_PART('YEAR',AGE(pa.fechaNacimiento)) AS edad",FALSE);
		// $this->db->select("EXTRACT(YEAR FROM AGE(pa.fechaNacimiento)) AS edad",FALSE);
		$this->db->select("FLOOR(DATEDIFF(NOW(), pa.fechaNacimiento)/365) AS edad", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		$this->db->join('usuario uscr', 'ci.usuarioId = uscr.id');
		$this->db->where_in('ci.estado', array(0, 2, 3));
		$this->db->where('pa.estado', 1);
		$this->db->where('ci.fechaCita BETWEEN ' . $desde .' AND ' . $hasta);

		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}

		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_atenciones_en_grilla($paramPaginate,$paramDatos){
		$desde = $this->db->escape(darFormatoYMD($paramDatos['fechaDesde']));
 		$hasta = $this->db->escape(darFormatoYMD($paramDatos['fechaHasta']));
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		$this->db->join('usuario uscr', 'ci.usuarioId = uscr.id');
		$this->db->where_in('ci.estado', array(0, 2, 3));
		$this->db->where('pa.estado', 1);
		$this->db->where('ci.fechaCita BETWEEN ' . $desde .' AND ' . $hasta);

		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}

	// atenciones de paciente
	public function m_cargar_atenciones_paciente($paramDatos){
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.usuarioId,
			ci.sedeId,
			ci.fechaAtencion,
			ci.fechaCita,
			ci.horaDesde,
			ci.horaHasta,
			ci.apuntesCita,
			ci.total,
			ci.peso,
			ci.talla,
			ci.imc,
			ci.presionArterial,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.estado,
			ci.medioContacto,
			ci.metodoPago,
			ci.numOperacion,
			ci.fechaUltimaRegla,
			ci.fechaProbableParto,
			ci.semanaGestacion,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.tipoDocumento,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
			uscr.username
		", FALSE);
		// $this->db->select("DATE_PART('YEAR',AGE(pa.fechaNacimiento)) AS edad",FALSE);
		// $this->db->select("EXTRACT(YEAR FROM AGE(pa.fechaNacimiento)) AS edad",FALSE);
		$this->db->select("FLOOR(DATEDIFF(NOW(), pa.fechaNacimiento)/365) AS edad", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		$this->db->join('usuario uscr', 'ci.usuarioId = uscr.id');
		$this->db->where_in('ci.estado', array(3));
		$this->db->where('pa.estado', 1);
		$this->db->where('pa.id', $paramDatos['idpaciente']);
		return $this->db->get()->result_array();
	}

	public function m_cargar_citas($datos){
		$arrEstados = array(1, 2, 3);
		if ($datos['origen'] === 'ate') {
			$arrEstados = array(2, 3);
		}
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.usuarioId,
			ci.sedeId,
			ci.fechaAtencion,
			ci.fechaCita,
			ci.fechaUltimaRegla,
			ci.fechaProbableParto,
			ci.semanaGestacion,
			ci.horaDesde,
			ci.horaHasta,
			ci.apuntesCita,
			ci.total,
			ci.peso,
			ci.talla,
			ci.imc,
			ci.presionArterial,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.estado,
			ci.medioContacto,
			ci.gestando,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
			ci.numOperacion,
			ci.anotacionesPago,
			ci.metodoPago,
			ci.tipoDocumentoCont,
			ci.numSerie,
			ci.numDoc
		", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		$this->db->where_in('ci.estado', $arrEstados);
		$this->db->where('pa.estado', 1);
		return $this->db->get()->result_array();
	}

	public function m_cargar_detalle_cita($datos)
	{
		$this->db->select("
			cp.id,
			cp.productoId AS idproducto,
			pr.nombre AS producto,
			pr.tipoProductoId,
			tp.nombre AS tipoProducto,
			cp.citaId,
			cp.precioReal AS precio,
			cp.informe,
			cp.observaciones,
			cp.motivoConsulta,
			cp.antecedentesFamiliares,
			cp.examenFisico,
			cp.antecedentesPersonales,
			cp.plan,
			cp.estado
		", FALSE);
		$this->db->from('citaproducto cp');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->where('cp.citaId', $datos['id']);
		$this->db->where('cp.estado', 1);
		// $this->db->where('pr.tipoProductoId <>', 4);
		$this->db->order_by('pr.tipoProductoId', 'ASC');
		$this->db->order_by('cp.id', 'ASC');
		return $this->db->get()->result_array();
	}
	public function m_cargar_detalle_pagos($citaId) // ME QUEDE AQUI, MODIFICAR ESTE METODO
	{
		$this->db->select("
			pg.id,
			pg.citaId,
			pg.numOperacion,
			pg.metodoPago,
			pg.monto,
			pg.fechaRegistro
		", FALSE);
		$this->db->from('pago pg');
		$this->db->where('pg.citaId', $citaId);
		$this->db->where('estado', 1);
		$this->db->order_by('fechaRegistro', 'ASC');
		// $this->db->order_by('cp.id', 'ASC');
		return $this->db->get()->result_array();
	}
	public function m_cargar_detalle_cita_atendida_por_paciente($datos)
	{
		$this->db->select("
			cp.id,
			cp.productoId AS idproducto,
			pr.nombre AS producto,
			pr.tipoProductoId,
			tp.nombre AS tipoProducto,
			cp.citaId,
			cp.precioReal AS precio,
			cp.informe,
			cp.observaciones,
			cp.motivoConsulta,
			cp.antecedentesFamiliares,
			cp.examenFisico,
			cp.antecedentesPersonales,
			cp.plan,
			cp.estado
		", FALSE);
		$this->db->from('cita ci');
		$this->db->join('citaproducto cp', 'ci.id = cp.citaId');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->where('ci.pacienteId', $datos['id']);
		$this->db->where('cp.estado', 1);
		$this->db->where_in('ci.estado', 3); // atendida
		$this->db->order_by('pr.tipoProductoId', 'ASC');
		$this->db->order_by('cp.id', 'ASC');
		return $this->db->get()->result_array();
	}
	public function m_obtener_cita($citaId) {
		$this->db->select("ci.id, ci.estado, pa.celular");
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->where('ci.id', $citaId);
		$this->db->limit('1');
		return $this->db->get()->row_array();
	}
	public function m_cargar_cita_por_id($datos){
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.usuarioId,
			ci.sedeId,
			ci.fechaAtencion,
			ci.fechaCita,
			ci.fechaUltimaRegla,
			ci.fechaProbableParto,
			ci.semanaGestacion,
			ci.horaDesde,
			ci.horaHasta,
			ci.apuntesCita,
			ci.total,
			ci.peso,
			ci.talla,
			ci.imc,
			ci.presionArterial,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.medioContacto,
			ci.gestando,
			ci.estado,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.tipoDocumento,
			pa.numeroDocumento,
			pa.sexo,
			pa.fechaNacimiento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
			rec.id AS idreceta,
			rec.fechaReceta,
			rec.indicacionesGenerales
		", FALSE);
		// $this->db->select("EXTRACT(YEAR FROM AGE(pa.fechaNacimiento)) AS edad",FALSE);
		// $this->db->select("EXTRACT(YEAR FROM AGE(pa.fechaNacimiento)) AS edad",FALSE);
		$this->db->select("FLOOR(DATEDIFF(NOW(), pa.fechaNacimiento)/365) AS edad", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('receta rec', 'ci.id = rec.Citaid', 'left');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		$this->db->where('ci.estado <> ', 0);
		$this->db->where('pa.estado', 1);
		$this->db->where('ci.id', $datos['id']);
		$this->db->limit('1');
		return $this->db->get()->row_array();
	}

	public function m_registrar($data)
	{
		$this->db->insert('cita', $data);
		return $this->db->insert_id();
	}

	public function m_registrar_detalle($data)
	{
		return $this->db->insert('citaproducto', $data);
	}

	public function m_registrar_detalle_cont($data)
	{
		return $this->db->insert('pago', $data);
	}

	public function m_editar($data,$id)
	{
		$this->db->where('id',$id);
		return $this->db->update('cita', $data);
	}
	public function m_eliminar_detalle($datos)
	{
		$data = array(
			'estado' 		=> 0,
			'updatedAt'		=> date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['id']);
		return $this->db->update('citaproducto', $data);
	}
	public function m_eliminar_detalle_cont($datos)
	{
		$data = array(
			'estado' 		=> 0,
			'updatedAt'		=> date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['id']);
		return $this->db->update('pago', $data);
	}
	public function m_editar_detalle($data, $id)
	{
		$this->db->where('id',$id);
		return $this->db->update('citaproducto', $data);
	}
	public function m_editar_detalle_cont($data, $id)
	{
		$this->db->where('id',$id);
		return $this->db->update('pago', $data);
	}
	public function m_anular($datos)
	{
		$data = array(
			'estado' => 0,
			'userAnulacion'=> $datos['username'],
			'updatedAt' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['idCita']);
		return $this->db->update('cita', $data);
	}
	public function m_liberar_cita($datos)
	{
		$data = array(
			'estado' => 2, // confirmada
			'updatedAt' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['idCita']);
		return $this->db->update('cita', $data);
	}
	public function m_registrar_receta($data)
	{
		$this->db->insert('receta', $data);
		return $this->db->insert_id();
	}
	public function m_agregar_imagen($data)
	{
		return $this->db->insert('imagen', $data);
		// return $this->db->insert_id();
	}
	public function m_editar_receta($data, $id)
	{
		$this->db->where('id',$id);
		return $this->db->update('receta', $data);
	}
	public function m_registrar_detalle_receta($datos)
	{
		$this->db->insert('recetamedicamento', $datos);
		return $this->db->insert_id();
	}
	public function m_cargar_detalle_imagenes($data)
	{
		$this->db->select("
			im.id,
			im.citaId,
			im.tipoImagen,
			im.srcImagen,
			im.fechaSubida,
			im.descripcion
		", FALSE);
		$this->db->from('imagen im');
		$this->db->where('im.citaId', $data['idcita']);
		return $this->db->get()->result_array();
	}
	public function m_cargar_detalle_lab($data)
	{
		$this->db->select("
			la.id,
			la.pacienteId,
			la.fechaExamen,
			la.srcDocumento,
			la.descripcion
		", FALSE);
		$this->db->from('laboratorio la');
		$this->db->where('la.pacienteId', $data['pacienteId']);
		return $this->db->get()->result_array();
	}
	public function m_cargar_detalle_receta($data)
	{
		$this->db->select("
			rm.id,
			rm.recetaId,
			rm.nombreMedicamento,
			rm.cantidad,
			rm.indicaciones,
			rm.estado
		", FALSE);
		$this->db->from('recetamedicamento rm');
		$this->db->where('rm.recetaId', $data['idreceta']);
		return $this->db->get()->result_array();
	}

	public function m_cargar_detalle_receta_por_cita($idcita)
	{
		$this->db->select("
			re.indicacionesGenerales,
			re.citaId,
			rm.id,
			rm.recetaId,
			rm.nombreMedicamento,
			rm.cantidad,
			rm.indicaciones,
			rm.estado
		", FALSE);
		$this->db->from('receta re');
		$this->db->join('recetamedicamento rm', 're.id = rm.recetaId');
		$this->db->where('re.citaId', $idcita);
		$this->db->where('re.estado', 1);
		return $this->db->get()->result_array();
	}
	public function m_quitar_imagen($data)
	{
		$this->db->where('id', $data['id']);
		return $this->db->delete('imagen');
	}

	public function m_obtener_atencion_consulta($citaId)
	{
		$this->db->select("
			cp.plan,
			cp.observaciones
		", FALSE);
		$this->db->from('citaproducto cp');
		$this->db->where('cp.citaId', $citaId);
		$this->db->order_by('cp.productoId', 'ASC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}

	// REPORTES
	public function m_obtener_produccion_medicos($params)
	{
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.fechaAtencion,
			ci.total,
			ci.estado,
			ci.medicoId,
			ci.metodoPago,
			pa.numeroDocumento,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			cp.precioReal,
			pr.nombre AS producto,
			tp.nombre AS tipo_producto
		", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('citaproducto cp', 'ci.id = cp.citaId');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->where('pa.estado', 1);
		$this->db->where('cp.estado', 1);
		if($params['origen']['id'] === 'INT'){
			$this->db->where('pr.procedencia', 'INT');
		}
		if($params['origen']['id'] === 'EXT'){
			$this->db->where('pr.procedencia', 'EXT');
		}
		$this->db->where('ci.estado', 3);
		$this->db->where("DATE(ci.fechaCita) BETWEEN '" . darFormatoYMD($params['desde']) ."' AND '" . darFormatoYMD($params['hasta'])."'");
		$this->db->where('ci.medicoId', $params['medico']['id']);
		$this->db->order_by('ci.fechaCita', 'ASC');
		return $this->db->get()->result_array();
	}
	public function m_obtener_produccion_medicos_group_producto($params)
	{
		$this->db->select("COUNT(*) AS contador, SUM(cp.precioReal) AS total, pr.nombre AS producto", FALSE);
		$this->db->from('cita ci');
		$this->db->join('citaproducto cp', 'ci.id = cp.citaId');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->where('ci.estado', 3);
		$this->db->where('pa.estado', 1);
		$this->db->where('cp.estado', 1);
		if($params['origen']['id'] === 'INT'){
			$this->db->where('pr.procedencia', 'INT');
		}
		if($params['origen']['id'] === 'EXT'){
			$this->db->where('pr.procedencia', 'EXT');
		}
		$this->db->where("DATE(ci.fechaCita) BETWEEN '" . darFormatoYMD($params['desde']) ."' AND '" . darFormatoYMD($params['hasta'])."'");
		$this->db->where('ci.medicoId', $params['medico']['id']);
		$this->db->group_by('pr.id');
		if($params['orden']['id'] == 'OC'){
			$this->db->order_by('COUNT(*)', 'DESC');
		}
		if($params['orden']['id'] == 'OM'){
			$this->db->order_by('SUM(cp.precioReal)', 'DESC');
		}
		return $this->db->get()->result_array();
	}

	public function m_obtener_produccion_general($params)
	{
		$this->db->select("
			ci.id,
			ci.pacienteId,
			ci.fechaAtencion,
			ci.total,
			ci.estado,
			ci.medicoId,
			ci.metodoPago,
			pa.numeroDocumento,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
			us.nombres AS nombreMedico,
			cp.precioReal,
			pr.nombre AS producto,
			tp.nombre AS tipo_producto
		", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id');
		$this->db->join('citaproducto cp', 'ci.id = cp.citaId');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->where('pa.estado', 1);
		$this->db->where('cp.estado', 1);
		if($params['origen']['id'] === 'INT'){
			$this->db->where('pr.procedencia', 'INT');
		}
		if($params['origen']['id'] === 'EXT'){
			$this->db->where('pr.procedencia', 'EXT');
		}
		$this->db->where('ci.estado', 3);
		$this->db->where("DATE(ci.fechaCita) BETWEEN '" . darFormatoYMD($params['desde']) ."' AND '" . darFormatoYMD($params['hasta'])."'");
		$this->db->order_by('ci.fechaCita', 'ASC');
		// $this->db->where('ci.medicoId', $params['medico']['id']);
		return $this->db->get()->result_array();
	}
	public function m_obtener_produccion_general_group_producto($params)
	{
		$this->db->select("COUNT(*) AS contador, SUM(cp.precioReal) AS total, pr.nombre AS producto", FALSE);
		$this->db->from('cita ci');
		$this->db->join('citaproducto cp', 'ci.id = cp.citaId');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->where('ci.estado', 3);
		$this->db->where('pa.estado', 1);
		$this->db->where('cp.estado', 1);
		if($params['origen']['id'] === 'INT'){
			$this->db->where('pr.procedencia', 'INT');
		}
		if($params['origen']['id'] === 'EXT'){
			$this->db->where('pr.procedencia', 'EXT');
		}
		$this->db->where("DATE(ci.fechaCita) BETWEEN '" . darFormatoYMD($params['desde']) ."' AND '" . darFormatoYMD($params['hasta'])."'");
		$this->db->group_by('pr.id');
		if($params['orden']['id'] == 'OC'){
			$this->db->order_by('COUNT(*)', 'DESC');
		}
		if($params['orden']['id'] == 'OM'){
			$this->db->order_by('SUM(cp.precioReal)', 'DESC');
		}
		return $this->db->get()->result_array();
	}
	public function m_obtener_produccion_general_group_medico($params)
	{
		$this->db->select("COUNT(*) AS contador, SUM(cp.precioReal) AS total, concat_ws(' ', us.nombres, us.apellidos) AS medico", FALSE);
		$this->db->from('cita ci');
		$this->db->join('usuario us', 'ci.medicoId = us.id');
		$this->db->join('citaproducto cp', 'ci.id = cp.citaId');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->where('ci.estado', 3);
		$this->db->where('pa.estado', 1);
		$this->db->where('cp.estado', 1);
		if($params['origen']['id'] === 'INT'){
			$this->db->where('pr.procedencia', 'INT');
		}
		if($params['origen']['id'] === 'EXT'){
			$this->db->where('pr.procedencia', 'EXT');
		}
		$this->db->where("DATE(ci.fechaCita) BETWEEN '" . darFormatoYMD($params['desde']) ."' AND '" . darFormatoYMD($params['hasta'])."'");
		$this->db->group_by('ci.medicoId');
		if($params['orden']['id'] == 'OC'){
			$this->db->order_by('COUNT(*)', 'DESC');
		}
		if($params['orden']['id'] == 'OM'){
			$this->db->order_by('SUM(cp.precioReal)', 'DESC');
		}
		return $this->db->get()->result_array();
	}
	public function m_actualizar_cita_sms($idcita)
	{
		$data = array(
			'smsEnviadoCita' => 'ENVIADO'
		);
		$this->db->where('id', $idcita);
		return $this->db->update('cita', $data);
	}
	public function m_obtener_citas_sin_sms()
	{
		$this->db->select("ci.id, pa.celular, concat_ws(' ', us.nombres, us.apellidos) AS medico, ci.fechaCita, ci.horaDesde", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		$this->db->where('pa.estado', 1);
		$this->db->where('ci.estado', 2); // confirmada

		$this->db->where('ci.smsEnviadoCita', 'POR_ENVIAR');
		$this->db->where('ci.fechaCita = DATE(NOW())');
		$this->db->where('DATE_SUB(ci.horaDesde, INTERVAL 3 HOUR) < TIME(NOW())');
		$this->db->where('ci.horaDesde > TIME(NOW())');
		return $this->db->get()->result_array();
	}
	public function m_registrar_log_sms($data)
	{
		$this->db->insert('logenviosms', $data);
		return $this->db->insert_id();
	}
}
