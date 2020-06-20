<?php
class Model_usuario extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_usuario($paramPaginate){
		$this->db->select("u.id AS usuarioId, u.username, u.lastConnection, u.perfilId,
			u.nombres, u.apellidos, u.correo, u.cmp, u.rne, pe.nombre AS perfil, pe.keyPerfil", FALSE);
		$this->db->from('usuario u');
		$this->db->join('perfil pe', 'u.perfilId = pe.id');
		$this->db->where('u.estado', 1);
		// var_dump($this->sessionFactur); exit();
		if( $this->sessionFactur['keyPerfil'] != 'key_root' ){
			$this->db->where_not_in('pe.keyPerfil', array('key_root'));
		}
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

	public function m_count_usuario($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('usuario u');
		$this->db->join('perfil pe', 'u.perfilId = pe.id');
		$this->db->where('u.estado', 1);
		if( $this->sessionFactur['keyPerfil'] != 'key_root' ){
			$this->db->where_not_in('pe.keyPerfil', array('key_root'));
		}
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

	public function m_listar_medico_autocomplete($datos)
	{
		$this->db->select("
			us.id,
			us.nombres,
			us.apellidos
		", FALSE);
		$this->db->from('usuario us');
		$this->db->where('us.estado', 1);
		$this->db->where('perfilId', 3); // solo perfil médico
		$this->db->like("CONCAT_WS(' ',us.nombres, us.apellidos)", $datos['searchText']);
		$this->db->limit('10');
		return $this->db->get()->result_array();
	}
	// VALIDACIONES
	public function m_validar_usuario_username($username, $excepcion = FALSE, $idusuario=NULL)
	{
		$this->db->select('u.id');
		$this->db->from('usuario u');
		$this->db->where('u.estado',1);
		$this->db->where('u.username',$username);
		if( $excepcion ){
			$this->db->where_not_in('u.id',$idusuario);
		}
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'perfilId' => $datos['perfil']['id'],
			'username' => $datos['username'],
			'password'=> md5($datos['passwordView']),
			'passwordView'=>strtoupper_total($datos['passwordView']),
			'nombres' => $datos['nombres'],
			'apellidos' => $datos['apellidos'],
			'correo' => $datos['correo'],
			'cmp' => $datos['cmp'],
			'rne' => $datos['rne'],
			'estado'=> 1,
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('usuario', $data);
	}
	public function m_editar($datos){
		// var_dump($datos);exit();
		$data = array(
			'perfilId' => $datos['perfil']['id'],
			'username' => $datos['username'],
			'nombres' => $datos['nombres'],
			'apellidos' => $datos['apellidos'],
			'correo' => $datos['correo'],
			'cmp' => $datos['cmp'],
			'rne' => $datos['rne'],
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['idusuario']);
		return $this->db->update('usuario', $data);
	}
	public function m_anular($datos)
	{
		$data = array(
			'estado' => 0,
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['idusuario']);
		return $this->db->update('usuario', $data);
	}

}
?>