<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {
	public function __construct()
  {
      parent::__construct();
      // Se le asigna a la informacion a la variable $sessionVP.
      $this->load->helper(array('fechas','otros'));
      $this->load->model(array('model_usuario'));
      $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
  }

	public function listar_usuario(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];

		$lista = $this->model_usuario->m_cargar_usuario($paramPaginate);
		$fCount = $this->model_usuario->m_count_usuario($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'idusuario' => $row['usuarioId'],
					'perfil' => array(
						'id'=> $row['perfilId'],
						'descripcion'=> $row['perfil']
					),
					'username' => $row['username'],
					'ult_inicio_sesion' => formatoFechaReporte4($row['lastConnection']),
					'nombres'=> strtoupper($row['nombres']),
					'apellidos'=> strtoupper($row['apellidos']),
					'cmp'=> $row['cmp'],
					'rne'=> $row['rne'],
					'correo'=> strtoupper($row['correo'])
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

	public function obtener_sedes_usuario()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrListado = array();
		$lista = $this->model_usuario->m_obtener_sedes($allInputs);
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'idusuariosede'=> $row['idusuariosede'],
					'sedeId'=> strtoupper($row['sedeId']),
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

	public function listar_medico_cbo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrListado = array();
		$lista = $this->model_usuario->m_listar_medico_cbo();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id'=> $row['id'],
					'descripcion'=> strtoupper($row['medico']),
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
	public function listar_medico_autocomplete()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrListado = array();
		$lista = $this->model_usuario->m_listar_medico_autocomplete($allInputs);

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
					'id'		 	=> $row['id'],
					'medico' 	=> strtoupper($row['nombres'] . ' ' . $row['apellidos']),
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

	public function ver_popup_formulario()
	{
		$this->load->view('usuario/mant_usuario');
	}

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    $arrData['flag'] = 0;

    // VALIDACIONES

		// VALIDAR QUE AL MENOS REGISTRE UNA SEDE
		// print_r(@$allInputs['checkHiguereta']);
		// print_r(@$allInputs['checkSanMiguel']);
		
		if( empty($allInputs['checkHiguereta']) && empty($allInputs['checkSanMiguel']) ){
			$arrData['message'] = 'Debe agregar al menos una sede.';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}
		// exit();

    /* VALIDAR QUE SE HAYA REGISTRADO CLAVE */
		if( empty($allInputs['password']) || empty($allInputs['passwordView']) ){
			$arrData['message'] = 'Los campos de contraseña están vacios.';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}

    	/* VALIDAR QUE LAS CLAVES COINCIDAN */
		if($allInputs['password'] != $allInputs['passwordView']){
			$arrData['message'] = 'Las contraseñas no coinciden, inténtelo nuevamente';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}
		/* VALIDAR SI EL USUARIO YA EXISTE */
    	$fUsuario = $this->model_usuario->m_validar_usuario_username($allInputs['username']);
    	if( !empty($fUsuario) ) {
    		$arrData['message'] = 'El usuario ingresado ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}

		$this->db->trans_start();
		if($this->model_usuario->m_registrar($allInputs)) { // registro de usuario
			$arrData['idusuario'] = GetLastId('id','usuario');
			// reg sede usuario
			$arrSedes = array();
			if (!empty($allInputs['checkHiguereta'])) {
				array_push($arrSedes, $allInputs['checkHiguereta']);
			}
			if (!empty($allInputs['checkSanMiguel'])) {
				array_push($arrSedes, $allInputs['checkSanMiguel']);
			}
			$default = 1;
			foreach ($arrSedes as $key => $value) {
				$dataSedeUser = array(
					'usuarioId' => $arrData['idusuario'],
					'sedeId' => $value,
					'default' => $default
				);
				$this->model_usuario->m_registrar_sede_usuario($dataSedeUser);
				$default = 0;
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
    // VALIDACIONES
		// VALIDAR QUE AL MENOS REGISTRE UNA SEDE
		if( empty($allInputs['checkHiguereta']) && empty($allInputs['checkSanMiguel']) ){
			$arrData['message'] = 'Debe agregar al menos una sede.';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}
		/* VALIDAR SI EL USUARIO YA EXISTE */
  	$fUsuario = $this->model_usuario->m_validar_usuario_username($allInputs['username'],TRUE,$allInputs['idusuario']);
  	if( $fUsuario ) {
  		$arrData['message'] = 'El Usuario ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
			return;
		}
		if (!empty($allInputs['checkCambioClave']) && empty($allInputs['password'])) {
			$arrData['message'] = 'Digita la clave para proceder con el cambio de clave.';
			$arrData['flag'] = 0;
			$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
			return;
		}
  	$this->db->trans_start();

		if($this->model_usuario->m_editar($allInputs)) { // edicion de elemento

			$arrSedes = array();
			if (!empty($allInputs['checkHiguereta'])) {
				array_push($arrSedes, $allInputs['checkHiguereta']);
			}
			if (!empty($allInputs['checkSanMiguel'])) {
				array_push($arrSedes, $allInputs['checkSanMiguel']);
			}
			// eliminar usuarios sedes
			$this->model_usuario->eliminar_usuarios_sede($allInputs['idusuario']);
			$default = 1;
			foreach ($arrSedes as $key => $value) {
				$dataSedeUser = array(
					'usuarioId' => $allInputs['idusuario'],
					'sedeId' => $value,
					'default' => $default
				);
				$this->model_usuario->m_registrar_sede_usuario($dataSedeUser);
				$default = 0;
			}

			$arrData['message'] = 'Se editaron los datos correctamente';
			$arrData['flag'] = 1;
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
		if( $this->model_usuario->m_anular($allInputs) ){
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

}