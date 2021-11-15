<?php
class Model_consultorio extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_cbo(){
		$this->db->select("cl.id, cl.nombre");
		$this->db->from('consultorio cl');
		$this->db->where('cl.sedeId', $this->sessionFactur['idsede']);
		return $this->db->get()->result_array();
	}
}
?>
