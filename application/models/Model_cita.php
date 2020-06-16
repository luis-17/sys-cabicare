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
			ci.estado
		", FALSE);
		$this->db->from('cita ci');
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
}