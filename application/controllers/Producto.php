<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto extends CI_Controller {
	public function __construct()
  {
    parent::__construct();
    // Se le asigna a la informacion a la variable $sessionVP.
    $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
    $this->load->helper(array('fechas','otros'));
    $this->load->model(array('model_producto'));
  }

	public function listar_producto(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_producto->m_cargar_producto($paramPaginate);
		$fCount = $this->model_producto->m_count_producto($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'idproducto' => $row['productoId'],
					'nombre' => strtoupper($row['nombre']),
					'tipo_producto' => array(
						'id'=> $row['tipoProductoId'],
						'descripcion'=> strtoupper($row['tipoProducto'])
					),
					'precio' => $row['precio'],
					'procedenciaStr' => $row['procedencia'] === 'EXT' ? 'EXTERNO':'INTERNO',
					'procedencia' => $row['procedencia']
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

	public function listar_autocompletado_producto()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrListado = array();
		$lista = $this->model_producto->m_cargar_autocompletado_producto($allInputs);

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
					'idproducto' 	=> $row['productoId'],
					'descripcion' 	=> strtoupper($row['nombre']),
					'producto' 		=> strtoupper($row['nombre']),
					'precio' 		=> $row['precio'],
					'tipo_producto' => array(
						'id'			=> $row['tipoProductoId'],
						'descripcion'	=> strtoupper($row['tipoProducto'])
					)
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
		$this->load->view('producto/mant_producto');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES

    	$this->db->trans_start();
		if($this->model_producto->m_registrar($allInputs)) { // registro de elemento
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

    	$this->db->trans_start();
		if($this->model_producto->m_editar($allInputs)) { // edicion de elemento
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
		if( $this->model_producto->m_anular($allInputs) ){
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}