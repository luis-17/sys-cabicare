<?php
class Model_acceso extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	// ACCESO AL SISTEMA
	public function m_logging_user($data){
		$this->db->select('us.id AS usuarioId, us.username, us.nombres, us.apellidos, se.serief, se.serieb, se.token, se.nombre AS sede,
			us.correo, pe.id AS perfilId, pe.nombre AS perfil, pe.keyPerfil, uss.default, uss.sedeId AS idsede', FALSE);
		$this->db->from('usuario us');
		$this->db->join('perfil pe', 'us.perfilId = pe.id AND pe.estado = 1');
		$this->db->join('usuariosede uss', 'us.id = uss.usuarioId');
		$this->db->join('sede se', 'uss.sedeId = se.id');
		$this->db->where('uss.default', 1);
		$this->db->where('us.username', $data['usuario']);
		$this->db->where('us.password', do_hash($data['password'] , 'md5'));
		$this->db->where('us.estado', 1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_combo_sede_matriz_session()
	{
		/* LOGICA MULTISEDE: */
		
		$this->db->select('uss.idusuariosede, uss.usuarioId, uss.sedeId, se.nombre AS sede', FALSE);
		$this->db->from('usuariosede uss');
		$this->db->join('sede se','uss.sedeId = se.id');
		$this->db->where('se.estado', 1);
		// $this->db->where('estado_ea', 1);
		$this->db->where('uss.usuarioId', $this->sessionFactur['usuarioId']);
		return $this->db->get()->result_array();
	}
	public function m_cambiar_sede_session($idusuariosede)
	{
		$this->db->select('us.id AS usuarioId, us.username, us.nombres, us.apellidos, se.serief, se.serieb, se.token, se.nombre AS sede,
			us.correo, pe.id AS perfilId, pe.nombre AS perfil, pe.keyPerfil, uss.default, uss.sedeId AS idsede', FALSE);
		$this->db->from('usuario us');
		$this->db->join('perfil pe', 'us.perfilId = pe.id AND pe.estado = 1');
		$this->db->join('usuariosede uss', 'us.id = uss.usuarioId');
		$this->db->join('sede se', 'uss.sedeId = se.id');
		$this->db->where('uss.idusuariosede',$idusuariosede);
		$this->db->where('us.estado', 1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_actualizar_datos_usuario_ultima_sesion($datos)
	{
		$data = array(
			'lastConnection' => date('Y-m-d H:i:s'),
			'ultDireccionIp'=>  $_SERVER['REMOTE_ADDR']
		);
		$this->db->where('id',$datos['idusuario']);
		return $this->db->update('usuario', $data);
	}
}
?>