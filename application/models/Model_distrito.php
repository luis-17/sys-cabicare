<?php
class Model_distrito extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
  public function m_cargar_distritos(){
    $this->db->select("
      di.id AS distritoId,
      di.codigo,
      di.nombre,
    ", FALSE);
    $this->db->from('distrito di');
    $this->db->where_in('di.provinciaCod', array('150100','070100'));

    return $this->db->get()->result_array();
  }
}
