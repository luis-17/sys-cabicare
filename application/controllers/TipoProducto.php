<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoProducto extends CI_Controller {
	public function __construct()
  {
    parent::__construct();
    // Se le asigna a la informacion a la variable $sessionVP.
    $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
    $this->load->helper(array('fechas','otros'));
    $this->load->model(array('model_tipo_producto'));
  }
	public function listar_tipo_producto_cbo(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_tipo_producto->m_cargar_tipo_producto_cbo();
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
