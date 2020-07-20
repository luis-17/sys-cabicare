<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diagnostico extends CI_Controller {
	public function __construct()
  {
    parent::__construct();
    // Se le asigna a la informacion a la variable $sessionVP.
    $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
    $this->load->helper(array('fechas','otros'));
    $this->load->model(array('model_diagnostico'));
  }
	public function listar_autocompletado_diagnostico()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrListado = array();
		$lista = $this->model_diagnostico->m_cargar_autocompletado($allInputs);

		if(empty($lista)){
			$arrData['datos'] = $arrListado;
			$arrData['flag'] = 0;
			$arrData['message'] = 'No se encontraron resultados';
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		foreach ($lista as $row) {

			array_push($arrListado,
				array(
          'iddiagnostico' 	=> $row['diagnosticoId'],
          'codigo' 	=> $row['codigo'],
					'descripcion' 	=> strtoupper($row['nombre']),
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
	public function listar_detalle_dx()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// $arrData['datos'] = $this->model_diagnostico->m_cargar_detalle_dx($allInputs['datos']['citaId']);
		$arrListado = array();
		$lista = $this->model_diagnostico->m_cargar_detalle_dx($allInputs['datos']['citaId']);
		foreach ($lista as $key => $row) {
			array_push(
				$arrListado,
				array(
					'id' => $row['id'],
					'iddiagnostico' => $row['diagnosticoId'],
					'diagnostico' => $row['diagnostico'],
					'codigo' => $row['codigo'],
					'tipoDiagnostico' => array(
						'id'=> $row['tipoDiagnostico'],
						'descripcion' => $row['tipoDiagnostico']
 					)
				)
			);
		}

		$arrData['datos'] = $arrListado;
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
