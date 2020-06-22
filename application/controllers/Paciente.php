<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paciente extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_paciente'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_paciente()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_paciente->m_cargar_paciente($paramPaginate);
		$fCount = $this->model_paciente->m_count_paciente($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) {
			$row['desc_sexo'] = NULL;
			if( @$row['sexo'] == 'M' ){
				$row['desc_sexo'] = 'MASCULINO';
			}
			if( @$row['sexo'] == 'F' ){
				$row['desc_sexo'] = 'FEMENINO';
			}
			switch ($row['tipoDocumento']) {
				case 'DNI':
					$strTipoDoc = 'DOCUMENTO NACIONAL DE IDENTIDAD';
					break;
				case 'PAS':
					$strTipoDoc = 'PASAPORTE';
					break;
				case 'CEX':
					$strTipoDoc = 'CARNET DE EXTRANJERIA';
					break;
				case 'PTP':
					$strTipoDoc = 'PERMISO TEMPORAL DE PERMANENCIA';
					break;
				default:
					$strTipoDoc = NULL;
					break;
			}
			array_push($arrListado,
				array(
					'idpaciente' => trim($row['pacienteId']),
					'nombres' => strtoupper($row['nombres']),
					'apellido_paterno' => strtoupper($row['apellidoPaterno']),
					'apellido_materno' => strtoupper($row['apellidoMaterno']),
					'tipo_documento'=> array(
						'id'=> $row['tipoDocumento'],
						'descripcion'=> $strTipoDoc
					),
					'num_documento' => $row['numeroDocumento'],
					'sexo'=> array(
						'id'=> $row['sexo'],
						'descripcion'=> $row['desc_sexo']
					),
					'edad' => devolverEdad($row['fechaNacimiento']),
					'fecha_nacimiento' => darFormatoDMY($row['fechaNacimiento']),
					'fecha_nacimiento_str' => formatoFechaReporte3($row['fechaNacimiento']),
					'operador' => array(
						'id'=> $row['operador'],
						'descripcion'=> $row['operador']
					),
					'celular' => $row['celular'],
					'email' => strtoupper($row['email']),
					'alergias' => $row['alergias'],
					'antecedentes' => $row['antecedentes']
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

	/**
	 * Método para buscar un paciente por su número de documento.
	 * Se utiliza en el registro de una Cita
	 *
	 * @Creado 14-06-2020
	 * @author Ing. Ruben Guevara <rguevarac@hotmail.es>
	 * @return void
	 */
	public function buscar_paciente_por_num_documento()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se encontró al paciente';
    	$arrData['flag'] = 0;

		$rowPac = $this->model_paciente->m_cargar_paciente_por_numero_documento($allInputs);
		if($rowPac){
			$rowPac['paciente'] = $rowPac['nombres'] . ' ' . $rowPac['apellidoPaterno'] . ' ' . $rowPac['apellidoMaterno'];
			$arrData['datos'] = $rowPac;
			$arrData['message'] = 'Se encontró al paciente en el sistema.';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function ver_popup_formulario()
	{
		$this->load->view('paciente/mant_paciente');
	}
	public function ver_popup_busqueda_paciente()
	{
		$this->load->view('paciente/busq_paciente_popup');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	if( empty($allInputs['sexo']['id']) ){
    		$arrData['message'] = 'Debe tener sexo para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['nombres']) ){
    		$arrData['message'] = 'Debe llenar el campo nombre para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
	    /* VALIDAR SI EL DNI YA EXISTE */
    	$fCliente = $this->model_paciente->m_validar_paciente_num_documento($allInputs['num_documento']);
    	if( !empty($fCliente) ) {
    		$arrData['message'] = 'El documento de identidad ingresado ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
    	$this->db->trans_start();
		$pacienteId = $this->model_paciente->m_registrar($allInputs);
		if($pacienteId) { // registro de cliente empresa
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
			$arrData['datos'] = $pacienteId;
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
    	// VALIDACIONES
    	if( empty($allInputs['sexo']['id']) ){
    		$arrData['message'] = 'Debe tener sexo para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['nombres']) ){
    		$arrData['message'] = 'Debe llenar el campo nombre para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
		/* VALIDAR SI EL RUC YA EXISTE */
    	$fCliente = $this->model_paciente->m_validar_paciente_num_documento($allInputs['num_documento'],TRUE,$allInputs['idpaciente']);
    	if( $fCliente ) {
    		$arrData['message'] = 'El RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
		if($this->model_paciente->m_editar($allInputs)){
			$arrData['message'] = 'Se editaron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['message'] = 'No se pudo anular los datos';
    	$arrData['flag'] = 0;
		if( $this->model_paciente->m_anular($allInputs) ){
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

}