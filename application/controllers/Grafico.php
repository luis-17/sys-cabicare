<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grafico extends CI_Controller {
	public function __construct()
  {
    parent::__construct();
    // Se le asigna a la informacion a la variable $sessionVP.
    $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
    $this->load->helper(array('fechas','otros'));
    $this->load->model(array('model_grafico'));
  }

  public function listar_paciente_recomendacion()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// PACIENTES ATENDIDOS POR GENERO 
		$arrResult = array();
		$lista = $this->model_grafico->m_pacientes_por_recomendacion($allInputs['datos']); 
		foreach ($lista as $key => $row) { 
			if (!empty($row['medioContacto'])) {
				$rowSliced = FALSE;
				$rowSelected = FALSE;
				if($row['medioContacto'] === 'POR RECOMENDACION'){ 
					$rowSliced = TRUE;
					$rowSelected = TRUE;
				}
				$arrResult[] = array( 
					'name'=> $row['medioContacto'],
					'y'=> (float)$row['contador'],
					'sliced'=> $rowSliced,
					'selected'=> $rowSelected
				);
			}
			
		}
		$arrData['datos'] = $arrResult;
		$arrData['message'] = '';
		$arrData['flag'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function listar_paciente_mes()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// PACIENTES POR MES
		$arrResult = array();
		$lista = $this->model_grafico->m_pacientes_por_mes($allInputs['datos']); 
		foreach ($lista as $key => $row) { 
			$rowSliced = FALSE;
			$rowSelected = FALSE;
			if($key === 0){ 
				$rowSliced = TRUE;
				$rowSelected = TRUE;
			}
			$arrResult[] = array( 
				'name'=> $row['mes'],
				'y'=> (float)$row['contador'],
				'sliced'=> $rowSliced,
        'selected'=> $rowSelected
			);
		}
		$arrData['datos'] = $arrResult;
		$arrData['message'] = '';
		$arrData['flag'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
}
