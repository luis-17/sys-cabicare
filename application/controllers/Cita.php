<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cita extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->helper(array('fechas_helper', 'otros_helper'));
        $this->load->model(array('model_cita'));

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");

    }

	public function listar_citas()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_cita->m_cargar_citas($allInputs);
		$arrListado = array();
		if(empty($lista)){
			$arrData['flag'] = 0;
			$arrData['datos'] = $arrListado;
    		$arrData['message'] = '';
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}

		foreach ($lista as $row) {

			if ( $row['estado'] == 1 ){
				$clases = 'b-warning';
			}elseif ( $row['estado'] == 2 ){
				$clases = 'b-primary';
			}elseif ( $row['estado'] == 3 ) {
				$clases = 'b-success';
			}else {
				$clases = '';
			}
			array_push(
				$arrListado,
				array(
					'id' => $row['id'],
					'horaDesde' => $row['horaDesde'],
					'horaHasta' => $row['horaHasta'],
					'fecha' => $row['fechaCita'],
					'numeroDocumento' =>  $row['numeroDocumento'],
					'paciente' =>  $row['paciente'],
					'peso' =>  $row['peso'],
					'talla' =>  $row['talla'],
					'imc' =>  $row['imc'],
					'apuntesCita' =>  $row['apuntesCita'],
					'frecuenciaCardiaca' =>  $row['frecuenciaCardiaca'],
					'temperaturaCorporal' =>  $row['temperaturaCorporal'],
					'medico' => array(
						'id' => $row['medicoId'],
						'medico' => $row['medico']
					),

					'className' => $clases,
					'start' => $row['fechaCita'] .' '. $row['horaDesde'],
					'end' => $row['fechaCita'] .' '. $row['horaHasta'],

					'title' => darFormatoHora($row['horaDesde']). ' - ' . darFormatoHora($row['horaHasta']) . ' | '.$row['paciente'],
					'allDay' => FALSE,
					'durationEditable' => FALSE,
					'tipoCita' => $row['estado'],
					'estado' => $row['estado']

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

	/**
	 * Método que lista los productos de una cita.
	 * Vista Calendario
	 *
	 * @Creado: 17-06-2020
	 * @author Ing. Ruben Guevara <rguevarac@hotmail.es>
	 * @return void
	 */
	public function listar_detalle_cita()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['datos'] = $this->model_cita->m_cargar_detalle_cita($allInputs['datos']);


		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function ver_popup_form_cita(){
		$this->load->view('cita/cita_formView');
	}

	/**
	 * Método para registrar una reserva de Cita, puede ser Por Confirmar o Confirmada
	 * Tambien se registra el detalle, es decir productos de la cita
	 *
	 * @Creado 14-06-2020
	 * @author Ing. Ruben Guevara <rguevarac@hotmail.es>
	 * @return void
	 */
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	if( empty($allInputs['pacienteId']) ){
    		$arrData['message'] = 'Debe seleccionar un paciente';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}

		if(empty($allInputs['fecha'])){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar una fecha.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		if(empty($allInputs['hora_desde']) || empty($allInputs['hora_hasta'])){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar horas validas.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		if(strtotime($allInputs['hora_desde']) >= strtotime($allInputs['hora_hasta'])){
		// if(strtotime($allInputs['hora_desde_str']) >= strtotime($allInputs['hora_hasta_str'])){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar un rango de horas valido.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		$hora_inicio_calendar = strtotime('07:00:00');
		$hora_fin_calendar = strtotime('23:00:00');

		$horadesde = strtotime($allInputs['hora_desde']);
		$horahasta = strtotime($allInputs['hora_hasta']);
		// if(strlen($allInputs['hora_desde_str']) == 7){
		// 	$horadesde = '0' . strtotime(substr($allInputs['hora_desde_str'], 0,4) . ':00');
		// }else{
		// 	$horadesde = strtotime(substr($allInputs['hora_desde_str'], 0,5) . ':00');
		// }

		// if(strlen($allInputs['hora_hasta_str']) == 7){
		// 	$horahasta = '0' . strtotime(substr($allInputs['hora_hasta_str'], 0,4) . ':00');
		// }else{
		// 	$horahasta = strtotime(substr($allInputs['hora_hasta_str'], 0,5) . ':00');
		// }

		if(!($horadesde  >= $hora_inicio_calendar &&  $horahasta <= $hora_fin_calendar)){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar un rango de horas permitido.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}


		$data = array(
			'pacienteId'			=> $allInputs['pacienteId'],
			'usuarioId'				=> $this->sessionFactur['usuarioId'],
			'sedeId'				=> $allInputs['sede']['id'],
			'fechaCita'				=> Date('Y-m-d',strtotime($allInputs['fecha'])),
			'horaDesde' 			=> Date('H:i',$horadesde),
			'horaHasta' 			=> Date('H:i',$horahasta),
			'apuntesCita'			=> empty($allInputs['apuntesCita'])? NULL : $allInputs['apuntesCita'],
			'medicoId'				=> empty($allInputs['medico']) ? NULL : $allInputs['medico']['id'],
			'total'					=> $allInputs['total_a_pagar'],
			'peso'					=> empty($allInputs['peso']) ? NULL : $allInputs['peso'],
			'talla'					=> empty($allInputs['talla']) ? NULL : $allInputs['talla'],
			'imc'					=> empty($allInputs['imc']) ? NULL : $allInputs['imc'],
			'frecuenciaCardiaca'	=> empty($allInputs['frecuenciaCardiaca']) ? NULL : $allInputs['frecuenciaCardiaca'],
			'temperaturaCorporal'	=> empty($allInputs['temperaturaCorporal']) ? NULL : $allInputs['temperaturaCorporal'],
			'perimetroAbdominal'	=> empty($allInputs['perimetroAbdominal']) ? NULL : $allInputs['perimetroAbdominal'],
			'observaciones'			=> empty($allInputs['observaciones']) ? NULL : $allInputs['observaciones'],
			'estado'				=> $allInputs['tipoCita'],
			'createdAt'				=> date('Y-m-d H:i:s'),
			'updatedAt'				=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
		$citaId =  $this->model_cita->m_registrar($data);
		if($citaId) {
			foreach ($allInputs['detalle'] as $row) {
				$data_det = array(
					'productoId' 	=> $row['idproducto'],
					'citaId' 		=> $citaId,
					'precioReal' 	=> $row['precio'],
					'estado' 		=> 1,
					'createdAt'		=> date('Y-m-d H:i:s'),
					'updatedAt'		=> date('Y-m-d H:i:s')
				);
				$this->model_cita->m_registrar_detalle($data_det);
			}
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;

		if(empty($allInputs['fecha'])){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar una fecha.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		if(empty($allInputs['hora_desde']) || empty($allInputs['hora_hasta'])){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar horas validas.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		if(strtotime($allInputs['hora_desde_str']) >= strtotime($allInputs['hora_hasta_str'])){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar un rango de horas valido.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		$hora_inicio_calendar = strtotime('07:00:00');
		$hora_fin_calendar = strtotime('23:00:00');

		if(strlen($allInputs['hora_desde_str']) == 7){
			$horadesde = '0' . strtotime(substr($allInputs['hora_desde_str'], 0,4) . ':00');
		}else{
			$horadesde = strtotime(substr($allInputs['hora_desde_str'], 0,5) . ':00');
		}

		if(strlen($allInputs['hora_hasta_str']) == 7){
			$horahasta = '0' . strtotime(substr($allInputs['hora_hasta_str'], 0,4) . ':00');
		}else{
			$horahasta = strtotime(substr($allInputs['hora_hasta_str'], 0,5) . ':00');
		}

		if(!($horadesde  >= $hora_inicio_calendar &&  $horahasta <= $hora_fin_calendar)){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar un rango de horas permitido.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		$data = array(
			'sedeId'				=> $allInputs['sede']['id'],
			'fechaCita'				=> Date('Y-m-d',strtotime($allInputs['fecha'])),
			'horaDesde' 			=> Date('H:i:s',$horadesde),
			'horaHasta' 			=> Date('H:i:s',$horahasta),
			'apuntesCita'			=> empty($allInputs['apuntesCita'])? NULL : $allInputs['apuntesCita'],
			'medicoId'				=> empty($allInputs['medico']) ? NULL : $allInputs['medico']['id'],
			'total'					=> $allInputs['total_a_pagar'],
			'peso'					=> empty($allInputs['peso']) ? NULL : $allInputs['peso'],
			'talla'					=> empty($allInputs['talla']) ? NULL : $allInputs['talla'],
			'imc'					=> empty($allInputs['imc']) ? NULL : $allInputs['imc'],
			'frecuenciaCardiaca'	=> empty($allInputs['frecuenciaCardiaca']) ? NULL : $allInputs['frecuenciaCardiaca'],
			'temperaturaCorporal'	=> empty($allInputs['temperaturaCorporal']) ? NULL : $allInputs['temperaturaCorporal'],
			'perimetroAbdominal'	=> empty($allInputs['perimetroAbdominal']) ? NULL : $allInputs['perimetroAbdominal'],
			'observaciones'			=> empty($allInputs['observaciones']) ? NULL : $allInputs['observaciones'],
			'estado'				=> $allInputs['tipoCita'],
			'updatedAt'				=> date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
		if($this->model_cita->m_editar($data, $allInputs['id'])) {
			foreach ($allInputs['detalle'] as $row) {
				if( empty($row['id']) ){ //si es nuevo se registra

					$data_det = array(
						'productoId' 	=> $row['idproducto'],
						'citaId' 		=> $allInputs['id'],
						'precioReal' 	=> $row['precio'],
						'estado' 		=> 1,
						'createdAt'		=> date('Y-m-d H:i:s'),
						'updatedAt'		=> date('Y-m-d H:i:s')
					);
					$this->model_cita->m_registrar_detalle($data_det);
				}
			}

			foreach ($allInputs['eliminados'] as $row_el) {
				$this->model_cita->m_eliminar_detalle($row_el);
			}
			$arrData['message'] = 'Se registraron los datos correctamente.';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}