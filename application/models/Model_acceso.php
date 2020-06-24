<?php
class Model_acceso extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	// ACCESO AL SISTEMA
	public function m_logging_user($data){
		$this->db->select('us.id AS usuarioId, us.username, us.nombres, us.apellidos, 
			us.correo, pe.id AS perfilId, pe.nombre AS perfil, pe.keyPerfil', FALSE);
		$this->db->from('usuario us');
		$this->db->join('perfil pe', 'us.perfilId = pe.id AND pe.estado = 1');
		$this->db->where('us.username', $data['usuario']);
		$this->db->where('us.password', do_hash($data['password'] , 'md5'));
		$this->db->where('us.estado', 1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_combo_empresa_admin_matriz_session($idusuario=FALSE) 
	{
		/* LOGICA MULTIEMPRESA: */ 
		
		$this->db->select('uea.idusuarioempresaadmin, ea.idempresaadmin, ea.razon_social AS empresa_admin');
		$this->db->from('usuario_empresa_admin uea');
		$this->db->join('empresa_admin ea','uea.idempresaadmin = ea.idempresaadmin');
		$this->db->where('estado_uea', 1);
		$this->db->where('estado_ea', 1);
		if( empty($idusuario) ){
			$this->db->where('idusuario', $this->sessionFactur['idusuario']);
		}else{
			$this->db->where('idusuario', $idusuario);
		}
		return $this->db->get()->result_array();
	}
	public function m_cambiar_empresa_session($idusuarioempresaadmin)
	{
		$this->db->select('ea.idempresaadmin, ea.razon_social, ea.nombre_comercial, ea.ruc, ea.nombre_logo, 
			uea.select_por_defecto, uea.idusuarioempresaadmin');
		$this->db->from('usuario_empresa_admin uea');
		$this->db->join('empresa_admin ea','uea.idempresaadmin = ea.idempresaadmin'); 
		$this->db->where('uea.idusuarioempresaadmin',$idusuarioempresaadmin);
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