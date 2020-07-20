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
			ci.presionArterual,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.estado,
			ci.medioContacto,
			ci.metodoPago,
			ci.numOperacion,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.tipoDocumento,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
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
			ci.horaDesde,
			ci.horaHasta,
			ci.apuntesCita,
			ci.total,
			ci.peso,
			ci.talla,
			ci.imc,
			ci.presionArterual,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.estado,
			ci.medioContacto,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
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
			cp.estado
		", FALSE);
		$this->db->from('citaproducto cp');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->where('cp.citaId', $datos['id']);
		$this->db->where('cp.estado', 1);
		$this->db->order_by('pr.tipoProductoId', 'ASC');
		$this->db->order_by('cp.id', 'ASC');
		return $this->db->get()->result_array();
	}

	public function m_cargar_cita_por_id($datos){
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
			ci.presionArterual,
			ci.frecuenciaCardiaca,
			ci.temperaturaCorporal,
			ci.perimetroAbdominal,
			ci.observaciones,
			ci.medioContacto,
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
	public function m_editar_detalle($data, $id)
	{
		$this->db->where('id',$id);
		return $this->db->update('citaproducto', $data);
	}
	public function m_anular($datos)
	{
		$data = array(
			'estado' => 0,
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['idCita']);
		return $this->db->update('cita', $data);
	}

	public function m_registrar_receta($data)
	{
		$this->db->insert('receta', $data);
		return $this->db->insert_id();
	}
	public function m_editar_receta($data, $id)
	{
		$this->db->where('id',$id);
		return $this->db->update('receta', $data);
	}
}
