<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Acceso extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('security','config'));
		$this->load->model(array('model_acceso'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");		
	}

	public function index(){
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$arrData['flag'] = 0;
    $arrData['message'] = 'Rellene todos los campos.';
		if(empty($allInputs['usuario']) || empty($allInputs['password']) ){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Llene los campos correctamente y vuelva a intentarlo.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
			return;
		}
		$loggedUser = $this->model_acceso->m_logging_user($allInputs);
		if(empty($loggedUser)){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Usuario o contraseña inválida. Inténtelo nuevamente.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		  return;
		}
		$arrData['flag'] = 1;
		$arrData['message'] = 'Usuario inició sesión correctamente';
		$this->session->set_userdata('sess_cabi_'.substr(base_url(),-20,7),$loggedUser);
		$params = array(
			'idusuario'=> $loggedUser['usuarioId']
		);
		$this->model_acceso->m_actualizar_datos_usuario_ultima_sesion($params);
		$this->output
	    ->set_content_type('application/json')
	    ->set_output(json_encode($arrData));
	}
	public function getSessionCI(){
		$arrData['flag'] = 0;
		$arrData['datos'] = array();
		// var_dump($_SESSION['sess_cabi_'.substr(base_url(),-20,7)]); exit();
		if( $this->session->has_userdata( 'sess_cabi_'.substr(base_url(),-20,7) ) 
			&& !empty($_SESSION['sess_cabi_'.substr(base_url(),-20,7)]['usuarioId']) ){ 
			$arrData['flag'] = 1;
			$arrData['datos'] = $_SESSION['sess_cabi_'.substr(base_url(),-20,7) ]; 
			// $arrConfig = obtener_parametros_configuracion(); 
			// $arrData['datos']['config'] = $arrConfig;
		} 
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function logoutSessionCI(){
		$this->session->unset_userdata('sess_cabi_'.substr(base_url(),-20,7));
    $arrData['flag'] = 1;
		$arrData['datos'] = 'Cerró sesión correctamente.';
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}