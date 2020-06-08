<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends CI_Controller {
	public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('fechas','otros')); 
    $this->load->model(array('model_perfil'));
    $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
  }

	public function listar_perfiles()
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_perfil->m_cargar_perfil($paramPaginate);
		$fCount = $this->model_perfil->m_count_perfil($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'idperfil' => $row['id'],
					'nombre' => strtoupper($row['nombre']),
					'descripcion' => $row['descripcion']
				)
			);
		}
  	$arrData['datos'] = $arrListado;
  	$arrData['paginate']['totalRows'] = $fCount['contador'];
  	$arrData['message'] = '';
  	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
	    ->set_content_type('application/json')
	    ->set_output(json_encode($arrData));
	}
	public function listar_perfiles_cbo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_perfil->m_cargar_perfiles_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['id'],
					'descripcion' => strtoupper($row['nombre'])
				)
			);
		} 
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
