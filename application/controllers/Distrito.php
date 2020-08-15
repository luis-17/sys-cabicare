<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distrito extends CI_Controller {
	public function __construct()
  {
    parent::__construct();
    // Se le asigna a la informacion a la variable $sessionVP.
    $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
    $this->load->helper(array('fechas','otros'));
    $this->load->model(array('model_distrito'));
  }
	public function listar_distritos()
	{
		// $allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrListado = array();
		$lista = $this->model_distrito->m_cargar_distritos();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
          // 'iddistrito' 	=> $row['distritoId'],
          // 'codigo' 	=> $row['codigo'],
          // 'descripcion' 	=> strtoupper($row['nombre']),
          'id' => $row['distritoId'],
					'descripcion' => strtoupper($row['nombre'])
				)
			);
		}
		$arrData['datos'] = $arrListado;
		$arrData['message'] = '';
		$arrData['flag'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
}
