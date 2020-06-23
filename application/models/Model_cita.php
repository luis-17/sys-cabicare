<?php
class Model_cita extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_citas($datos){
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
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente,
			pa.numeroDocumento,
			ci.medicoId,
			concat_ws(' ', us.nombres, us.apellidos) AS medico,
		", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('usuario us', 'ci.medicoId = us.id','left');
		return $this->db->get()->result_array();
	}
	public function m_cargar_detalle_cita($datos)
	{
		$this->db->select("
			cp.id,
			cp.productoId AS idproducto,
			pr.nombre AS producto,
			tp.nombre AS tipoProducto,
			cp.citaId,
			cp.precioReal AS precio,
			cp.estado
		", FALSE);
		$this->db->from('citaproducto cp');
		$this->db->join('producto pr', 'cp.productoId = pr.id');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->where('cp.citaId', $datos['id']);
		$this->db->where('cp.estado', 1);
		$this->db->order_by('cp.id', 'ASC');
		return $this->db->get()->result_array();
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
}