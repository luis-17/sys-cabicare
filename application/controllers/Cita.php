<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cita extends CI_Controller {
	public function __construct(){
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->load->helper(array('fechas_helper', 'otros_helper'));
        $this->load->model(array('model_cita'));

        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");

    }

	public function ver_popup_form_cita(){
		$this->load->view('cita/cita_formView');
	}

	/**
	 * Método para registrar una reserva de Cita, puede ser Por Confirmar o Confirmada
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

		$data = array(
			'pacienteId'	=> $allInputs['pacienteId'],
			'usuarioId'	=> $this->sessionFactur['usuarioId'],
			'sedeId'	=> $allInputs['sede']['id'],
			'fechaCita'	=> $allInputs['fecha'],
			'horaDesde'	=> $allInputs['hora_desde'],
			'horaHasta'	=> $allInputs['hora_hasta'],
			'apuntesCita'	=> empty($allInputs['apuntesCita'])? NULL : $allInputs['apuntesCita'],
			'total'	=> $allInputs['total_a_pagar'],
			'peso'	=> empty($allInputs['peso']) ? NULL : $allInputs['peso'],
			'talla'	=> empty($allInputs['talla']) ? NULL : $allInputs['talla'],
			'imc'	=> empty($allInputs['imc']) ? NULL : $allInputs['imc'],
			'frecuenciaCardiaca'	=> empty($allInputs['frecuenciaCardiaca']) ? NULL : $allInputs['frecuenciaCardiaca'],
			'temperaturaCorporal'	=> empty($allInputs['temperaturaCorporal']) ? NULL : $allInputs['temperaturaCorporal'],
			'perimetroAbdominal'	=> empty($allInputs['perimetroAbdominal']) ? NULL : $allInputs['perimetroAbdominal'],
			'observaciones'			=> empty($allInputs['observaciones']) ? NULL : $allInputs['observaciones'],
			'estado'	=> $allInputs['tipoCita'],
			'createdAt'	=> date('Y-m-d H:i:s'),
			'updatedAt'	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
		if($this->model_cita->m_registrar($data)) { // registro de cliente empresa
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}