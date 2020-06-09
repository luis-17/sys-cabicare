<?php
class Model_tipo_producto extends CI_Model {
	public function __construct()
	{
		parent::__construct();
  }
  
	public function m_cargar_tipo_producto_cbo(){
		$this->db->select("tp.id, tp.nombre, tp.estado");
		$this->db->from('tipoproducto tp');
		$this->db->where('estado', 1);
		return $this->db->get()->result_array();
	}
}
