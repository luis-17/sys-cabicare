<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// var_dump('dir ==> ', __DIR__ . '/twilio-php-main/src/Twilio/autoload.php');
require __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
use Twilio\Rest\Client;

class Cita extends CI_Controller {
	public function __construct(){
    parent::__construct();
		$this->load->helper(array('fechas_helper', 'otros_helper', 'imagen_helper'));
		$this->load->model(array('model_cita', 'model_diagnostico', 'model_paciente'));

		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");

  }

	public function listar_citas_en_grilla()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_cita->m_cargar_citas_en_grilla($paramPaginate,$paramDatos);
		$arrListado = array();
		if(empty($lista)){
			$arrData['flag'] = 0;
			$arrData['datos'] = $arrListado;
			$arrData['paginate']['totalRows'] = 0;
    	$arrData['message'] = '';
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		$fCount = $this->model_cita->m_count_citas_en_grilla($paramPaginate,$paramDatos);

		foreach ($lista as $row) {

			if ( $row['estado'] == 1 ){
				$clase = 'label-warning';
				$estado = 'POR CONFIRMAR';
			}elseif ( $row['estado'] == 2 ){
				$clase = 'label-primary';
				$estado = 'CONFIRMADO';
			}elseif ( $row['estado'] == 3 ) {
				$clase = 'label-success';
				$estado = 'ATENDIDO';
			}elseif ( $row['estado'] == 0 ) {
				$clase = 'label-default';
				$estado = 'ANULADO';
			}else {
				$clase = '';
				$estado = '';
			}
			array_push(
				$arrListado,
				array(
					'id' => $row['id'],
					'horaDesde' => darFormatoHora($row['horaDesde']),
					'horaHasta' => darFormatoHora($row['horaHasta']),
					'fechaCita' => darFormatoDMY($row['fechaCita']),
					'fecha' => $row['fechaCita'],
					'fechaUltimaRegla' => darFormatoDMY($row['fechaUltimaRegla']),
					'fechaProbableParto' => darFormatoFecha($row['fechaProbableParto']),
					'semanaGestacion' => $row['semanaGestacion'],
					'tipoDocumento' =>  $row['tipoDocumento'],
					'numeroDocumento' =>  $row['numeroDocumento'],
					'paciente' =>  $row['paciente'],
					'peso' =>  $row['peso'],
					'talla' =>  $row['talla'],
					'imc' =>  $row['imc'],
					'apuntesCita' =>  $row['apuntesCita'],
					'frecuenciaCardiaca' =>  $row['frecuenciaCardiaca'],
					'temperaturaCorporal' =>  $row['temperaturaCorporal'],
          			// 'medico' => $row['medico'],
          			'medico' => array(
						'id' => $row['medicoId'],
						'medico' => $row['medico']
					),
					'total' => $row['total'],
          'medioContacto'=> array(
						'id'=> $row['medioContacto'],
						'descripcion'=> $row['medioContacto']
					 ),
					 'numSerie' => $row['numSerie'],
					 'numDoc' => $row['numDoc'], 
					'numOperacion' => $row['numOperacion'],
					'anotacionesPago' => $row['anotacionesPago'],
          'metodoPago'=> array(
						'id'=> $row['metodoPago'],
						'descripcion'=> $row['metodoPago']
					),
					'tipoDocumentoCont'=> array(
						'id'=> $row['tipoDocumentoCont'],
						'descripcion'=> $row['tipoDocumentoCont']
					),
					'tipoCita' => $row['estado'],
					'estado' => array(
						'string' => $estado,
						'clase' =>$clase,
						'bool' =>$row['estado']
					)
				)
			);
		}

		$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $fCount['contador'];
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));

	}

	public function listar_atenciones_grilla()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_cita->m_cargar_atenciones_en_grilla($paramPaginate,$paramDatos);
		$arrListado = array();
		if(empty($lista)){
			$arrData['flag'] = 0;
			$arrData['datos'] = $arrListado;
			$arrData['paginate']['totalRows'] = 0;
    	$arrData['message'] = '';
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		$fCount = $this->model_cita->m_count_atenciones_en_grilla($paramPaginate,$paramDatos);

		foreach ($lista as $row) {

			if ( $row['estado'] == 2 ){
				$clase = 'label-primary';
				$estado = 'CONFIRMADO';
			}elseif ( $row['estado'] == 3 ) {
				$clase = 'label-success';
				$estado = 'ATENDIDO';
			}elseif ( $row['estado'] == 0 ) {
				$clase = 'label-default';
				$estado = 'ANULADO';
			}else {
				$clase = '';
				$estado = '';
			}
			array_push(
				$arrListado,
				array(
					'id' => $row['id'],
					'horaDesde' => darFormatoHora($row['horaDesde']),
					'horaHasta' => darFormatoHora($row['horaHasta']),
					'fechaCita' => darFormatoDMY($row['fechaCita']),
					'fecha' => $row['fechaCita'],
					'fechaUltimaRegla' => darFormatoDMY($row['fechaUltimaRegla']),
					'fechaProbableParto' => darFormatoFecha($row['fechaProbableParto']),
					'semanaGestacion' => $row['semanaGestacion'],
					'edad' =>  $row['edad'],
					'tipoDocumento' =>  $row['tipoDocumento'],
					'numeroDocumento' =>  $row['numeroDocumento'],
					'paciente' =>  $row['paciente'],
					'peso' =>  $row['peso'],
					'talla' =>  $row['talla'],
					'imc' =>  $row['imc'],
					'apuntesCita' =>  $row['apuntesCita'],
					'frecuenciaCardiaca' =>  $row['frecuenciaCardiaca'],
					'presionArterial'	=> $row['presionArterial'],
					'temperaturaCorporal' =>  $row['temperaturaCorporal'],
          'medico' => array(
						'id' => $row['medicoId'],
						'medico' => $row['medico']
					),
					'username'=> $row['username'],
					'estado' => array(
						'string' => $estado,
						'clase' =>$clase,
						'bool' =>$row['estado']
					)
				)
			);
		}

		$arrData['datos'] = $arrListado;
		$arrData['paginate']['totalRows'] = $fCount['contador'];
		$arrData['message'] = '';
		$arrData['flag'] = 1;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function listar_otras_atenciones()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// $paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_cita->m_cargar_atenciones_paciente($paramDatos);
		$arrListado = array();
		if(empty($lista)){
			$arrData['flag'] = 0;
			$arrData['datos'] = $arrListado;
			$arrData['paginate']['totalRows'] = 0;
    	$arrData['message'] = '';
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		// $fCount = $this->model_cita->m_count_atenciones_pacientes($paramPaginate,$paramDatos);

		foreach ($lista as $row) {

			if ( $row['estado'] == 2 ){
				$clase = 'label-primary';
				$estado = 'CONFIRMADO';
			}elseif ( $row['estado'] == 3 ) {
				$clase = 'label-success';
				$estado = 'ATENDIDO';
			}elseif ( $row['estado'] == 0 ) {
				$clase = 'label-default';
				$estado = 'ANULADO';
			}else {
				$clase = '';
				$estado = '';
			}
			array_push(
				$arrListado,
				array(
					'id' => $row['id'],
					'horaDesde' => darFormatoHora($row['horaDesde']),
					'horaHasta' => darFormatoHora($row['horaHasta']),
					'fechaCita' => darFormatoDMY($row['fechaCita']),
					'fecha' => $row['fechaCita'],
					'fechaUltimaRegla' => darFormatoDMY($row['fechaUltimaRegla']),
					'fechaProbableParto' => darFormatoFecha($row['fechaProbableParto']),
					'semanaGestacion' => $row['semanaGestacion'],
					'edad' =>  $row['edad'],
					'tipoDocumento' =>  $row['tipoDocumento'],
					'numeroDocumento' =>  $row['numeroDocumento'],
					'paciente' =>  $row['paciente'],
					'peso' =>  $row['peso'],
					'talla' =>  $row['talla'],
					'imc' =>  $row['imc'],
					'apuntesCita' =>  $row['apuntesCita'],
					'frecuenciaCardiaca' =>  $row['frecuenciaCardiaca'],
					'presionArterial'	=> $row['presionArterial'],
					'temperaturaCorporal' =>  $row['temperaturaCorporal'],
          'medico' => array(
						'id' => $row['medicoId'],
						'medico' => $row['medico']
					),
					'username'=> $row['username'],
					'estado' => array(
						'string' => $estado,
						'clase' =>$clase,
						'bool' =>$row['estado']
					)
				)
			);
		}

		$arrData['datos'] = $arrListado;
		// $arrData['paginate']['totalRows'] = $fCount['contador'];
		$arrData['message'] = '';
		$arrData['flag'] = 1;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
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
					'fechaUltimaRegla' => darFormatoDMY($row['fechaUltimaRegla']),
					'fechaProbableParto' => darFormatoFecha($row['fechaProbableParto']),
					'semanaGestacion' => $row['semanaGestacion'],
					'numeroDocumento' =>  $row['numeroDocumento'],
					'paciente' =>  $row['paciente'],
					'peso' =>  $row['peso'],
					'talla' =>  $row['talla'],
					'imc' =>  $row['imc'],
					'apuntesCita' =>  $row['apuntesCita'],
					'frecuenciaCardiaca' =>  $row['frecuenciaCardiaca'],
					'temperaturaCorporal' =>  $row['temperaturaCorporal'],
					'presionArterial'	=> $row['presionArterial'],
					'medico' => array(
						'id' => $row['medicoId'],
						'medico' => $row['medico']
					),
					'tipoDocumentoCont'=> array(
						'id'=> $row['tipoDocumentoCont'],
						'descripcion'=> $row['tipoDocumentoCont']
					),
          'medioContacto'=> array(
						'id'=> $row['medioContacto'],
						'descripcion'=> $row['medioContacto']
					),
					'numSerie' => $row['numSerie'],
					'numDoc' => $row['numDoc'],
					'numOperacion' => $row['numOperacion'],
					'anotacionesPago' => $row['anotacionesPago'],
          'metodoPago'=> array(
						'id'=> $row['metodoPago'],
						'descripcion'=> $row['metodoPago']
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

	public function listar_cita_por_id()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$rowCita = $this->model_cita->m_cargar_cita_por_id($allInputs);
		// var_dump('<pre>', $rowCita);
		$rowCita['detalle'] = $this->model_cita->m_cargar_detalle_cita($allInputs);
		$rowCita['fechaUltimaRegla'] = date('d-m-Y',strtotime($rowCita['fechaUltimaRegla']));
		$rowCita['edad'] = devolverEdad($rowCita['fechaNacimiento']) . ' años';
		$rowCita['peso'] = empty($rowCita['peso'])? NULL : $rowCita['peso'];
		$rowCita['talla'] = empty($rowCita['talla'])? NULL : $rowCita['talla'];
		$rowCita['medico'] = array(
			'id'=> $rowCita['medicoId'],
			'descripcion'=> $rowCita['medico'],
    );
    $rowCita['gestando'] = array(
			'id'=> $rowCita['gestando'],
			'descripcion'=> $rowCita['gestando'] == 1 ? 'SI' : 'NO',
		);
		$arrData['datos'] = $rowCita;
    $arrData['message'] = '';
    $arrData['flag'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function listar_detalle_cita()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['datos'] = $this->model_cita->m_cargar_detalle_cita($allInputs['datos']);


		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function listar_detalle_pagos()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrListado = $this->model_cita->m_cargar_detalle_pagos($allInputs['citaId']);
		foreach($arrListado as $key => $row) {
			$arrListado[$key]['metodoPago'] = array(
				'id' => $row['metodoPago'],
				'descripcion' => $row['metodoPago']
			);
		};

		$arrData['datos'] = $arrListado;
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function ver_popup_form_cita(){
		$this->load->view('cita/cita_formView');
	}

	public function ver_popup_form_metodo_pago(){
			$this->load->view('cita/metodo_pago_formView');
	}

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

		if(!($horadesde  >= $hora_inicio_calendar &&  $horahasta <= $hora_fin_calendar)){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar un rango de horas permitido.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}

		$igv = round($allInputs['total_a_pagar'] * 0.18, 2);
		$subtotal =  round($allInputs['total_a_pagar'] - $igv, 2);

		$data = array(
			'pacienteId'			=> $allInputs['pacienteId'],
			'usuarioId'				=> $this->sessionFactur['usuarioId'],
			'sedeId'				=> $allInputs['sede']['id'],
			'fechaCita'				=> date('Y-m-d',strtotime($allInputs['fecha'])),
			'horaDesde' 			=> date('H:i',$horadesde),
			'horaHasta' 			=> date('H:i',$horahasta),
			'apuntesCita'			=> empty($allInputs['apuntesCita'])? NULL : $allInputs['apuntesCita'],
			'medicoId'				=> empty($allInputs['medico']) ? NULL : $allInputs['medico']['id'],
			'total'					=> $allInputs['total_a_pagar'],
			'igv'					=> $igv,
			'subtotal'					=> $subtotal,
			'peso'					=> empty($allInputs['peso']) ? NULL : $allInputs['peso'],
			'talla'					=> empty($allInputs['talla']) ? NULL : $allInputs['talla'],
			'imc'					=> empty($allInputs['imc']) ? NULL : $allInputs['imc'],
			// 'frecuenciaCardiaca'	=> empty($allInputs['frecuenciaCardiaca']) ? NULL : $allInputs['frecuenciaCardiaca'],
			'presionArterial'	=> empty($allInputs['presionArterial']) ? NULL : $allInputs['presionArterial'],
			'temperaturaCorporal'	=> empty($allInputs['temperaturaCorporal']) ? NULL : $allInputs['temperaturaCorporal'],
			'perimetroAbdominal'	=> empty($allInputs['perimetroAbdominal']) ? NULL : $allInputs['perimetroAbdominal'],
			'observaciones'			=> empty($allInputs['observaciones']) ? NULL : $allInputs['observaciones'],
      'estado'				=> $allInputs['tipoCita'],
			'medioContacto'			=> empty($allInputs['medioContacto']) ? NULL : $allInputs['medioContacto']['id'],
			'smsEnviadoCita'	=> 'POR_ENVIAR',
			'metodoPago' => $allInputs['metodoPago']['id'],
			'numSerie' => empty($allInputs['numSerie']) ? NULL : $allInputs['numSerie'],
			'numDoc' => empty($allInputs['numDoc']) ? NULL : $allInputs['numDoc'],
			'numOperacion' => empty($allInputs['numOperacion']) ? NULL : $allInputs['numOperacion'],
			'anotacionesPago' => empty($allInputs['anotacionesPago']) ? NULL : $allInputs['anotacionesPago'],
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
			// ENVIO DE SMS CONFIRMACION DE CITA
			if(
				$allInputs['tipoCita'] == '2' && 
				strtotime($allInputs['fecha']) > strtotime(date("Y-m-d")) && STAGE === 'PROD'
			){
				$dataPac = array('id' => $allInputs['pacienteId']);
				$fPaciente = $this->model_paciente->m_cargar_paciente_por_id($dataPac);
				if (!empty($fPaciente) && !empty($fPaciente['celular']) && strlen($fPaciente['celular']) == 9 ) {
					$account_sid = TW_SID;
					$auth_token = TW_TOKEN;
					$twilio_number = TW_NUMBER; // "+18442780963";
					$client = new Client($account_sid, $auth_token);
					$body = 'Su cita se ha reservado con éxito en CABICARE con el Dr. '.$allInputs['medico']['medico'].' el '.date('d-m-Y',strtotime($allInputs['fecha'])).' - '.date('H:i', strtotime($allInputs['hora_desde'])).'. Recuerde asistir 20 minutos antes. Para cualquier cambio de cita contactenos. "Contigo en todas tus etapas".';
					$client->messages->create(
						'+51'.$fPaciente['celular'],
						array(
								'from' => $twilio_number,
								'body' => $body
						)
					);
					// almacenar log
				$dataLog = array(
					'citaId'=> $citaId,
					'celular'=> $fPaciente['celular'],
					'fechaEnvio'=> date('Y-m-d H:i:s'),
					'contenido'=> $body
				);
				$this->model_cita->m_registrar_log_sms($dataLog);
				}
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

		if(strtotime($allInputs['hora_desde']) >= strtotime($allInputs['hora_hasta'])){
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


		if(!($horadesde  >= $hora_inicio_calendar &&  $horahasta <= $hora_fin_calendar)){
			$arrData['flag'] = 0;
			$arrData['message'] = 'Debe seleccionar un rango de horas permitido.';
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
		    return;
		}
		$igv = round($allInputs['total_a_pagar'] * 0.18, 2);
		$subtotal =  round($allInputs['total_a_pagar'] - $igv, 2);
		$data = array(
			'sedeId'				=> $allInputs['sede']['id'],
			'fechaCita'				=> Date('Y-m-d',strtotime($allInputs['fecha'])),
			'horaDesde' 			=> Date('H:i',$horadesde),
			'horaHasta' 			=> Date('H:i',$horahasta),
			'apuntesCita'			=> empty($allInputs['apuntesCita'])? NULL : $allInputs['apuntesCita'],
			'medicoId'				=> empty($allInputs['medico']) ? NULL : $allInputs['medico']['id'],
			'total'					=> $allInputs['total_a_pagar'],
			'igv'					=> $igv,
			'subtotal'					=> $subtotal,
			'peso'					=> empty($allInputs['peso']) ? NULL : $allInputs['peso'],
			'talla'					=> empty($allInputs['talla']) ? NULL : $allInputs['talla'],
			'imc'					=> empty($allInputs['imc']) ? NULL : $allInputs['imc'],
			// 'frecuenciaCardiaca'	=> empty($allInputs['frecuenciaCardiaca']) ? NULL : $allInputs['frecuenciaCardiaca'],
			'presionArterial'	=> empty($allInputs['presionArterial']) ? NULL : $allInputs['presionArterial'],
			'temperaturaCorporal'	=> empty($allInputs['temperaturaCorporal']) ? NULL : $allInputs['temperaturaCorporal'],
			'perimetroAbdominal'	=> empty($allInputs['perimetroAbdominal']) ? NULL : $allInputs['perimetroAbdominal'],
			'observaciones'			=> empty($allInputs['observaciones']) ? NULL : $allInputs['observaciones'],
			'estado'				=> $allInputs['tipoCita'],
			'medioContacto'			=> empty($allInputs['medioContacto']) ? NULL : $allInputs['medioContacto']['id'],
			// 'metodoPago' => $allInputs['metodoPago']['id'],
			'tipoDocumentoCont' => empty($allInputs['tipoDocumentoCont']) ? NULL : $allInputs['tipoDocumentoCont']['id'],
			'numSerie' => empty($allInputs['numSerie']) ? NULL : $allInputs['numSerie'],
			'numDoc' => empty($allInputs['numDoc']) ? NULL : $allInputs['numDoc'],
			'numOperacion' => empty($allInputs['numOperacion']) ? NULL : $allInputs['numOperacion'],
			'anotacionesPago' => empty($allInputs['anotacionesPago']) ? NULL : $allInputs['anotacionesPago'],
			'updatedAt'				=> date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
		$fCita = $this->model_cita->m_obtener_cita($allInputs['id']);
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
				}else{
					$data_det = array(
						'precioReal' 	=> $row['precio'],
						'updatedAt'		=> date('Y-m-d H:i:s')
					);
					$this->model_cita->m_editar_detalle($data_det, $row['id']);
				}
			}

			foreach ($allInputs['eliminados'] as $row_el) {
				$this->model_cita->m_eliminar_detalle($row_el);
			}

			if(!empty($allInputs['detalleCont'])){
				foreach ($allInputs['detalleCont'] as $key => $row) {
					if( empty($row['id']) ){ //si es nuevo se registra
						$data_det = array(
							'citaId' 		=> $allInputs['id'],
							'numOperacion' 	=> empty($row['numOperacion']) ? null : $row['numOperacion'],
							'monto' 	=> $row['monto'],
							'metodoPago' 	=> $row['metodoPago']['id'],
							'estado' 		=> 1,
							'fechaRegistro'		=> date('Y-m-d H:i:s'),
							'updatedAt'		=> date('Y-m-d H:i:s')
						);
						$this->model_cita->m_registrar_detalle_cont($data_det);
					}else{
						$data_det = array(
							'numOperacion' 	=> $row['numOperacion'],
							'monto' 	=> $row['monto'],
							'updatedAt'		=> date('Y-m-d H:i:s')
						);
						$this->model_cita->m_editar_detalle_cont($data_det, $row['id']);
					}
				}
			}
			foreach ($allInputs['eliminadosCont'] as $row_cont) {
				$this->model_cita->m_eliminar_detalle_cont($row_cont);
			}
			// ENVIO DE SMS CONFIRMACION DE CITA
			if(
				$allInputs['tipoCita'] == '2' && 
				trim($fCita['estado']) != trim($allInputs['tipoCita']) && 
				strtotime($allInputs['fecha']) > strtotime(date("Y-m-d"))
			){
				// $dataPac = array('id' => $allInputs['pacienteId']);
				// $fPaciente = $this->model_paciente->m_cargar_paciente_por_id($dataPac);
				if (!empty($fCita['celular']) && strlen($fCita['celular']) == 9) {
					$account_sid = TW_SID;
					$auth_token = TW_TOKEN;
					$twilio_number = TW_NUMBER; // "+18442780963";
					$client = new Client($account_sid, $auth_token);
					$client->messages->create(
						'+51'.$fCita['celular'],
						array(
								'from' => $twilio_number,
								'body' => 'Su cita se ha reservado con éxito en CABICARE con el Dr. '.$allInputs['medico']['medico'].' el '.date('d-m-Y',strtotime($allInputs['fecha'])).' - '.date('H:i', strtotime($allInputs['hora_desde'])).'. 
									Recuerde asistir 20 minutos antes. Para cualquier cambio de cita contactenos. "Contigo en todas tus etapas".'
						)
					);
				}
			}
			$arrData['message'] = 'Se registraron los datos correctamente.';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	
	public function mover_cita()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['flag'] = 0;
		$arrData['message'] = 'Ha ocurrido un error actualizando la cita';


		$data = array(
			'horaDesde' => date('H:i',strtotime($allInputs['event']['start'])),
			'horaHasta' => date('H:i',strtotime($allInputs['event']['end'])),
			'fechaCita' => date('Y-m-d',strtotime($allInputs['event']['start'])),
			'updatedAt' => date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
		if($this->model_cita->m_editar($data, $allInputs['event']['id'])){
			$arrData['flag'] = 1;
			$arrData['message'] = 'Cita actualizada.';
		}
		$this->db->trans_complete();

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
  	}
	public function agregar_metodo_pago()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
			$arrData['flag'] = 0;
			$arrData['message'] = 'Ha ocurrido un error actualizando la cita';

			$data = array(
				'metodoPago' => $allInputs['metodoPago']['id'],
				'numOperacion' => empty($allInputs['numOperacion']) ? NULL : $allInputs['numOperacion'],
				'anotacionesPago' => empty($allInputs['anotacionesPago']) ? NULL : $allInputs['anotacionesPago'],
			);

			$this->db->trans_start();
			if ($this->model_cita->m_editar($data, $allInputs['id'])) {
				$arrData['flag'] = 1;
				$arrData['message'] = 'Método de pago actualizado.';
			}
			$this->db->trans_complete();

			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
	}
	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo anular los datos';
		$arrData['flag'] = 0;
		$allInputs['username'] = $this->sessionFactur['username'];
		if( $this->model_cita->m_anular($allInputs) ){
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	// public function liberar_atencion()
	// {
	// 	$allInputs = json_decode(trim($this->input->raw_input_stream),true);
	// 	$arrData['message'] = 'No se pudo anular los datos';
    // 	$arrData['flag'] = 0;
	// 	if( $this->model_cita->m_liberar_cita($allInputs) ){
	// 		$arrData['message'] = 'Se liberó la cita correctamente.';
    // 	$arrData['flag'] = 1;
	// 	}
	// 	$this->output
	// 	    ->set_content_type('application/json')
	// 	    ->set_output(json_encode($arrData));
	// }

	public function registrar_atencion()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo registrar los datos';
    	$arrData['flag'] = 0;


		$data = array(
			'fechaAtencion' => date('Y-m-d H:i:s'),
      		'estado' => 3,
			'gestando' => empty($allInputs['gestando']) ? 2 : $allInputs['gestando']['id'],
			'fechaUltimaRegla' => date('Y-m-d',strtotime($allInputs['fechaUltimaRegla'])),
			'fechaProbableParto' => $allInputs['fechaProbableParto'],
			'semanaGestacion' => $allInputs['semanaGestacion'],
			'updatedAt' => date('Y-m-d H:i:s'),
			'peso' => $allInputs['peso'],
			'talla' => $allInputs['talla'],
			'imc' => $allInputs['imc'],
			'presionArterial' => $allInputs['presionArterial'],
			'frecuenciaCardiaca' => $allInputs['frecuenciaCardiaca'],
			'temperaturaCorporal' => $allInputs['temperaturaCorporal'],
			// 'perimetroAbdominal' => $allInputs['perimetroAbdominal'],
		);
		if ($this->sessionFactur['keyPerfil'] == 'key_root' && $allInputs['medico']['id']) {
			$data['medicoId'] = $allInputs['medico']['id'];
		}

		$this->db->trans_start();
		if($this->model_cita->m_editar($data, $allInputs['id'])) {

			foreach ($allInputs['detalle'] as $row) {
				$data_det = array(
					'informe' 	=> ($row['informe']),
					'observaciones' 	=> ($row['observaciones']),
					'motivoConsulta' => ($row['motivoConsulta']),
					'antecedentesFamiliares' => ($row['antecedentesFamiliares']),
					'examenFisico' => ($row['examenFisico']),
					'antecedentesPersonales' => ($row['antecedentesPersonales']),
					'plan' => ($row['plan']),
					'updatedAt'		=> date('Y-m-d H:i:s')
				);
				$this->model_cita->m_editar_detalle($data_det, $row['id']);
			}
			// eliminar diagnosticos y crearlos denuevo
      $this->model_diagnostico->m_eliminar($allInputs['id']);
      if(!empty($allInputs['diagnostico'])){
        foreach ($allInputs['diagnostico'] as $row) {
          $data_det = array(
            'citaId'=> $allInputs['id'],
            'diagnosticoId' 	=> $row['iddiagnostico'],
            'tipoDiagnostico' 	=> $row['tipoDiagnostico']['id'],
            'createdAt'		=> date('Y-m-d H:i:s')
          );
          $this->model_diagnostico->m_registrar_diagnostico($data_det);
        }
      }
			
			$arrData['message'] = 'Se registraron los datos correctamente.';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function registrar_receta()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo registrar los datos';
    	$arrData['flag'] = 0;

		$data = array(
			'citaId' => $allInputs['id'],
      		'indicacionesGenerales'	=> $allInputs['indicacionesGenerales'],
			'fechaReceta'			=> date('Y-m-d H:i:s'),
			'estado' 		=> 1,
			'createdAt'		=> date('Y-m-d H:i:s'),
			'updatedAt'		=> date('Y-m-d H:i:s')
		);
		$this->db->trans_start();
		if(empty($allInputs['idreceta'])){

			$idreceta = $this->model_cita->m_registrar_receta($data);
			if($idreceta) {
				// REGISTRAR DETALLE
				foreach ($allInputs['detalle'] as $row) {
					$datos = array(
						'recetaId' => $idreceta,
						'nombreMedicamento' =>  $row['nombreMedicamento'],
						'cantidad' =>  $row['cantidad'],
						'indicaciones' =>  $row['indicaciones'],
						'estado'	=> 1,
						'createdAt'		=> date('Y-m-d H:i:s'),
						'updatedAt'		=> date('Y-m-d H:i:s')
					);
					$this->model_cita->m_registrar_detalle_receta($datos);
				}
				$arrData['message'] = 'Se registraron los datos correctamente.';
				$arrData['flag'] = 1;
				$arrData['idreceta'] = $idreceta;
			}
		}else{
			$data = array(
				'indicacionesGenerales'	=> $allInputs['indicacionesGenerales'],
				'updatedAt'		=> date('Y-m-d H:i:s')
			);
			if($this->model_cita->m_editar_receta($data, $allInputs['idreceta']) ){
				$arrData['message'] = 'Se editaron los datos correctamente.';
				$arrData['flag'] = 1;
				$arrData['idreceta'] = $allInputs['idreceta'];
			}
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function listar_detalle_receta()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = '';
    $arrData['flag'] = 0;

		$arrData['datos'] = $this->model_cita->m_cargar_detalle_receta($allInputs['datos']);


		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
	public function listar_detalle_imagenes()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = '';
    $arrData['flag'] = 0;
		$arrData['datos'] = $this->model_cita->m_cargar_detalle_imagenes($allInputs['datos']);
		foreach ($arrData['datos'] as $key => $row) {
			$arrData['datos'][$key]['srcImagen'] = array(
				'link' => URL_PREVIEW.'assets/dinamic/imagenes/'.$row['srcImagen'],
				'texto' => 'Ver Documento'
			);
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
	public function listar_detalle_lab()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = '';
    $arrData['flag'] = 0;
		$arrData['datos'] = $this->model_cita->m_cargar_detalle_lab($allInputs['datos']);
		foreach ($arrData['datos'] as $key => $row) {
			$arrData['datos'][$key]['srcDocumento'] = array(
				'link' => URL_PREVIEW.'assets/dinamic/laboratorio/'.$row['srcDocumento'],
				'texto' => 'Ver Documento'
			);
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
	public function registrar_imagen()
	{
		// $allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$allInputs = array();
		$arrData['message'] = 'No se pudo registrar los datos';
		$arrData['flag'] = 0;

		// VALIDACIONES
		if( empty($this->input->post('citaId')) ){
			$arrData['message'] = 'No ha seleccionado la cita correctamente. Recargue la página y vuelva a intentarlo.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		if( empty($_FILES['srcImagen_blob']) ){
			$arrData['message'] = 'No ha cargado un archivo para subir. Cargue el archivo para seguir con el proceso.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}

		$allInputs['citaId'] = $this->input->post('citaId');
		$allInputs['tipoImagen'] = $this->input->post('tipoImagen');
		// $allInputs['tipoImagen'] = $allInputs['tipoImagen'];
		$allInputs['descripcion'] = $this->input->post('descripcion');
		$allInputs['fechaSubida'] = date('Y-m-d H:i:s');
		$this->db->trans_start();
		if( !empty($_FILES['srcImagen_blob']) ){
			$extension = pathinfo($_FILES['srcImagen_blob']['name'], PATHINFO_EXTENSION);
			$nuevoNombreArchivo = strtotime("now").'-'.$allInputs['tipoImagen'].'.'.$extension;
			if( subir_fichero('assets/dinamic/imagenes','srcImagen_blob',$nuevoNombreArchivo) ){
				$allInputs['srcImagen'] = $nuevoNombreArchivo;
			}
			if($this->model_cita->m_agregar_imagen($allInputs) ){
				$arrData['message'] = 'Se agregaron los datos correctamente.';
				$arrData['flag'] = 1;
				// $arrData['idreceta'] = $allInputs['idreceta'];
			}
		}
		$this->db->trans_complete();
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
	public function quitar_imagen()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo quitar los datos';
		$arrData['flag'] = 0;
		// $allInputs['username'] = $this->sessionFactur['username'];
		if( $this->model_cita->m_quitar_imagen($allInputs) ){
			$arrData['message'] = 'Se quitaron los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function listar_atencion_cita()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$fAte = $this->model_cita->m_obtener_atencion_consulta($allInputs['datos']['citaId']);
		
		$arrData = array(
			'plan' => $fAte['plan'],
			'observaciones' => $fAte['observaciones']
		);
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function calcular_semana_gestacion($fur = FALSE, $param = FALSE) 
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrListado = array();
		if(empty($fur)) { 
			if( empty($param) ){
				$fur = $allInputs['fur'];
			}else{
				return;
			}
		}
		$arrData['flag'] = 0; 
		$arrFur = explode("-", $fur); // var_dump($arrFur); exit(); 
		if( is_numeric($arrFur[1]) && is_numeric($arrFur[0]) && is_numeric($arrFur[2]) ){
			if(checkdate($arrFur[1], $arrFur[0], $arrFur[2])){ 
				$desde = date('Y-m-d',strtotime("$fur"));
				$hasta = date('Y-m-d');
				$arrListado['diasTranscurridos'] = get_dias_transcurridos($desde,$hasta);
				if($arrListado['diasTranscurridos'] && $arrListado['diasTranscurridos'] > 0){
					$arrListado['semanasTranscurridas'] = ($arrListado['diasTranscurridos'] / 7);
				}
				$arrListado['semanasTranscurridas'] = floor($arrListado['semanasTranscurridas']);
				$diasResiduo = $arrListado['diasTranscurridos'] - (7 * $arrListado['semanasTranscurridas']);
				$arrListado['strSemanasDias'] = $arrListado['semanasTranscurridas'].' SEMANAS + '.$diasResiduo.' DIA(S)';
		    	$arrData['datos'] = $arrListado;
		    	$arrData['message'] = '';
		    	$arrData['flag'] = 1;
			}
		}
		if(empty($arrListado['semanasTranscurridas'])){ 
			$arrData['flag'] = 0; 
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function calcular_FPP()
	{
		// CALCULO DE LA FECHA PROBABLE DE PARTO 
		// FPP = FUR + 1 año - 3 meses + 7 días
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrListado = array();
		$fur = $allInputs['fur'];
		$arrData['flag'] = 1; 
		// $arrFur = explode("-", $fur); // var_dump($arrFur); exit(); 
		$furMasUnAnio = date('Y-m-d',strtotime("$fur+1year")); 
		$furMasUnAnioMenosTresMeses = date('Y-m-d',strtotime("$furMasUnAnio-3months")); 
		$arrListado['fpp'] = date('Y-m-d',strtotime("$furMasUnAnioMenosTresMeses+5days")); // era 7
		if(empty($arrListado['fpp'])){ 
			$arrData['flag'] = 0; 
		}else{
			$arrData['datos'] = $arrListado; 
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function enviarSMSCitas()
	{
		$lista = $this->model_cita->m_obtener_citas_sin_sms();
		$contador = 0;
		foreach ($lista as $row) {
			// $dataPac = array('id' => $allInputs['pacienteId']);
			// $fPaciente = $this->model_paciente->m_cargar_paciente_por_id($dataPac);
			if (!empty($row['celular']) && STAGE === 'PROD') {
				$account_sid = TW_SID;
				$auth_token = TW_TOKEN;
				$twilio_number = TW_NUMBER; // "+18442780963";
				$client = new Client($account_sid, $auth_token);
				$body = 'Le recordamos que su cita ha sido confirmada en CABICARE con el Dr. '.$row['medico'].' el dia '.date('d-m-Y',strtotime($row['fechaCita'])).' - '.date('H:i', strtotime($row['horaDesde'])).'. 
				"Contigo en todas tus etapas".';
				$client->messages->create(
					'+51'.$row['celular'],
					array(
							'from' => $twilio_number,
							'body' => $body,
					)
				);
				// almacenar log
				$dataLog = array(
					'citaId'=> $row['id'],
					'celular'=> $row['celular'],
					'fechaEnvio'=> date('Y-m-d H:i:s'),
					'contenido'=> $body
				);
				$this->model_cita->m_actualizar_cita_sms($row['id']);
				$this->model_cita->m_registrar_log_sms($dataLog);
				$contador ++;
			}
		}
		$arrResponse = array('Mensaje'=> 'Ejecución de tarea terminada. Se enviaron '.$contador.' mensajes.');
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrResponse));
	}
}
