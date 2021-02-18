<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// require __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
// use Twilio\Rest\Client;

class Nota extends CI_Controller {
	public function __construct(){
    parent::__construct();
		$this->load->helper(array('fechas_helper', 'otros_helper', 'imagen_helper'));
		$this->load->model(array('model_nota'));

		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
  }

	public function listar_documento()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_nota->m_cargar_nota($paramPaginate,$paramDatos);
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
		$fCount = $this->model_nota->m_count_nota($paramPaginate,$paramDatos);

		foreach ($lista as $row) {
			array_push(
				$arrListado,
				array(
					'notaId' => $row['notaId'],
					'tipoNota' => array(
						'id' => $row['tipoNota'],
						'descripcion' => $row['tipoNota'],
					),
					'numSerie' => $row['numSerie'],
					'numDoc' => $row['numDoc'],
					'numDocAsoc' => $row['numDocAsoc'],
					'anotaciones' => $row['anotaciones'],
					'subtotal' => $row['subtotal'],
					'total' => $row['total'],
					'igv' => $row['igv'],
					'fechaNota' => darFormatoDMY($row['fechaNota']),
					'fechaRegistro' => darFormatoDMY($row['fechaRegistro']),
					'usuarioRegistro' => $row['usuarioRegistro']
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

  	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo registrar los datos';
		$arrData['flag'] = 0;

		// VALIDACIONES
		if( empty($allInputs['tipoNota']['id']) ){
			$arrData['message'] = 'No ha seleccionado el tipo de nota correctamente. Seleccione y vuelva a intentarlo.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}

		// BUSCAR CITA POR NUMDOC
		// $numAsoc = $allInputs['numSerieAsoc'].'-'.$allInputs['numDocAsoc']
		$filaCita = $this->model_nota->m_buscar_cita_doc_asoc($allInputs['numSerieAsoc'], $allInputs['numDocAsoc']);
		if (empty($filaCita)) {
			$arrData['message'] = 'El número de documento asociado no se encuentra en la base de datos.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}

		$this->db->trans_start();
		$allInputs['citaId'] = $filaCita['citaId'];
		// calculo
		$allInputs['igv'] = $allInputs['total'] * 0.18;
		$allInputs['subtotal'] =  $allInputs['total'] - $allInputs['igv'];
		if($this->model_nota->m_registrar($allInputs) ){
			$arrData['message'] = 'Se registraron los datos correctamente.';
			$arrData['flag'] = 1;
			// $arrData['idreceta'] = $allInputs['idreceta'];
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
		if( $this->model_nota->m_anular($allInputs) ){
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function ver_popup_formulario()
	{
		$this->load->view('nota/mant_nota');
	}

	public function ver_nota()
	{
		$this->load->view('nota/ver_nota');
	}
}
