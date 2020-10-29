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

	public function listar_paciente_distrito()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// PACIENTES ATENDIDOS POR DISTRITO 
		$arrResult = array();
		$lista = $this->model_grafico->m_pacientes_por_distrito($allInputs['datos']);
		foreach ($lista as $key => $row) { 
			if (!empty($row['nombre'])) {
				$rowSliced = FALSE;
				$rowSelected = FALSE;
				if($key === 0){ 
					$rowSliced = TRUE;
					$rowSelected = TRUE;
				}
				$arrResult[] = array( 
					'name'=> $row['nombre'],
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

  public function listar_paciente_recomendacion()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// PACIENTES ATENDIDOS POR RECOM. 
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
		if($allInputs['datos']['tg']['id'] == 'CPM'){
			$lista = $this->model_grafico->m_pacientes_por_mes($allInputs['datos']);
		}
		if($allInputs['datos']['tg']['id'] == 'PNPM'){
			$lista = $this->model_grafico->m_pacientes_nuevos_por_mes($allInputs['datos']);
		}
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

	public function listar_prod_medico_mes()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrResult = array();
		$lista = $this->model_grafico->m_medico_prod_mes($allInputs['datos']);
		$indexParam = $allInputs['datos']['mc']['id'] === 'PC' ? 'contador' : 'suma';
		$arrGroupMeses = array();
		$arrGroupMedicos = array();
		foreach ($lista as $key => $row) {
			$arrGroupMeses[$row['mes']] = $row['mes'];
			$arrGroupMedicos[$row['medico']] = $row['medico'];
		}
		$arrGroupMeses = array_values($arrGroupMeses);
		$arrGroupMedicos = array_values($arrGroupMedicos);
		$arrDataSeries = array();
		foreach ($arrGroupMedicos as $key => $value) {
			array_push($arrDataSeries, array(
				'name' => $value,
				'data' => array_pad(array(), count($arrGroupMeses), 0)
			));
		}
		foreach($lista as $key => $row) {
			$perteneceMes = FALSE;
			$perteneceMed = FALSE;
			$keyMesSelected = null;
			foreach($arrGroupMeses as $keyMes => $valMes){
				if($row['mes'] == $valMes){
					$perteneceMes = TRUE;
					$keyMesSelected = $keyMes;
				}
			}
			foreach($arrGroupMedicos as $keyMedi => $valMedi){
				if($row['medico'] == $valMedi){
					$perteneceMed = TRUE;
				}
			}
			if($perteneceMed && $perteneceMes){
				foreach($arrDataSeries as $keySerie => $valSerie){
					foreach($valSerie['data'] as $keyDetSer => $valDetSerie) {
						if ($keyDetSer === $keyMesSelected && $valSerie['name'] == $row['medico']) {
							$arrDataSeries[$keySerie]['data'][$keyDetSer] = (int)$row[$indexParam];
						}
					}
				}
			}
		}
		$arrData['datos'] = array(
			'categories' => $arrGroupMeses,
			'series' => $arrDataSeries,
		);
		$arrData['message'] = '';
		$arrData['flag'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function listar_prod_medico_tiempo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrResult = array();
		$lista = $this->model_grafico->m_medico_prod_mes($allInputs['datos']);
		$indexParam = $allInputs['datos']['mc']['id'] === 'PC' ? 'contador' : 'suma';
		$arrGroupMeses = array();
		$arrGroupMedicos = array();
		foreach ($lista as $key => $row) {
			$arrGroupMeses[$row['mes']] = $row['mes'];
			$arrGroupMedicos[$row['medico']] = $row['medico'];
		}
		$arrGroupMeses = array_values($arrGroupMeses);
		$arrGroupMedicos = array_values($arrGroupMedicos);
		$arrDataSeries = array();
		foreach ($arrGroupMedicos as $key => $value) {
			array_push($arrDataSeries, array(
				'name' => $value,
				'data' => array_pad(array(), count($arrGroupMeses), null)
			));
		}
		foreach($lista as $key => $row) {
			$perteneceMes = FALSE;
			$perteneceMed = FALSE;
			$keyMesSelected = null;
			foreach($arrGroupMeses as $keyMes => $valMes){
				if($row['mes'] == $valMes){
					$perteneceMes = TRUE;
					$keyMesSelected = $keyMes;
				}
			}
			foreach($arrGroupMedicos as $keyMedi => $valMedi){
				if($row['medico'] == $valMedi){
					$perteneceMed = TRUE;
				}
			}
			if($perteneceMed && $perteneceMes){
				foreach($arrDataSeries as $keySerie => $valSerie){
					foreach($valSerie['data'] as $keyDetSer => $valDetSerie) {
						if ($keyDetSer === $keyMesSelected && $valSerie['name'] == $row['medico']) {
							$arrDataSeries[$keySerie]['data'][$keyDetSer] = (int)$row[$indexParam];
						}
					}
				}
			}
		}
		$arrData['datos'] = array(
			'categories' => $arrGroupMeses,
			'series' => $arrDataSeries,
		);
		$arrData['message'] = '';
		$arrData['flag'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
}
