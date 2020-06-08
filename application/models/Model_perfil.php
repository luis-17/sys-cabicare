<?php
class Model_perfil extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_perfil($paramPaginate){ 
		$this->db->select("pe.id, pe.nombre, pe.keyPerfil, pe.descripcion");
		$this->db->from('perfil pe');
		$this->db->where('estado', 1);
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

	public function m_count_perfil($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('perfil pe');
		$this->db->where('estado', 1);
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

	public function m_cargar_perfiles_cbo(){
		$this->db->select("pe.id, pe.nombre");
		$this->db->from('perfil pe');
		$this->db->where('estado', 1);
		if( $this->sessionFactur['keyPerfil'] != 'key_root' ){ 
			$this->db->where_not_in('pe.keyPerfil', array('key_root'));
		}
		return $this->db->get()->result_array();
	}
}
?>