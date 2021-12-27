<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// require __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
// use Twilio\Rest\Client;

class Documento extends CI_Controller {
	public function __construct(){
    parent::__construct();
		$this->load->helper(array('fechas_helper', 'otros_helper', 'imagen_helper'));
		$this->load->model(array('model_documento'));

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
		$lista = $this->model_documento->m_cargar_documento($paramPaginate,$paramDatos);
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
		$fCount = $this->model_documento->m_count_documento($paramPaginate,$paramDatos);

		foreach ($lista as $row) {
			array_push(
				$arrListado,
				array(
					'documentoId' => $row['documentoId'],
					'fechaDocumento' => $row['dia'].'-'.$row['mes'].'-'.$row['anio'],
					'anio' => array(
						'id' => $row['anio'],
						'descripcion' => $row['anio'],
					),
					'mes' => array(
						'id' => $row['mes'],
						'descripcion' => $row['mes'],
					),
					'categoria' => array(
						'id' => $row['categoria'],
						'descripcion' => $row['categoria'],
					),
					'moneda' => array(
						'id' => $row['moneda'],
						'descripcion' => $row['moneda'],
					),
					'ruc' => $row['ruc'],
					'razonSocial' => $row['razonSocial'],
					'numDoc' => $row['numDoc'],
					'numSerie' => $row['numSerie'],
					'codigoExterno' => $row['codigoExterno'],
					'fechaCreacion' => formatoFechaReporte4($row['fechaCreacion']),
					'observaciones' => $row['observaciones'],
					'monto' => $row['monto'],
					// 'nombreArchivo' => $row['nombreArchivo'],
					'usuarioRegistro' => $row['usuarioRegistro'],
					'nombreArchivo' => array(
						'link' => URL_PREVIEW.'assets/dinamic/documentos/'.$row['nombreArchivo'],
						'texto' => 'Ver Documento'
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

  public function registrar()
	{
		// $allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$allInputs = array();
		$arrData['message'] = 'No se pudo registrar los datos';
		$arrData['flag'] = 0;

		// VALIDACIONES
		if( empty($this->input->post('categoria')) ){
			$arrData['message'] = 'No ha seleccionado la categoria correctamente. Seleccione y vuelva a intentarlo.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		if( empty($this->input->post('anio')) ){
			$arrData['message'] = 'No ha seleccionado el año correctamente. Seleccione y vuelva a intentarlo.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		if( empty($this->input->post('mes')) ){
			$arrData['message'] = 'No ha seleccionado el mes correctamente. Seleccione y vuelva a intentarlo.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		if( empty($_FILES['nombreArchivo_blob']) ){
			$arrData['message'] = 'No ha cargado un archivo para subir. Cargue el archivo para seguir con el proceso.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}

		// $allInputs['citaId'] = $this->input->post('citaId');
		$allInputs['mes'] = $this->input->post('mes');
		$allInputs['anio'] = $this->input->post('anio');
		$allInputs['dia'] = $this->input->post('dia');
		$allInputs['categoria'] = $this->input->post('categoria');
		$allInputs['codigoExterno'] = $this->input->post('codigoExterno');
		$allInputs['observaciones'] = $this->input->post('observaciones');
		$allInputs['monto'] = $this->input->post('monto');

		$allInputs['numDoc'] = $this->input->post('numDoc');
		$allInputs['numSerie'] = $this->input->post('numSerie');
		$allInputs['moneda'] = $this->input->post('moneda');
		$allInputs['ruc'] = $this->input->post('ruc');
		$allInputs['razonSocial'] = $this->input->post('razonSocial');

		$allInputs['fechaPago'] = $allInputs['anio'].'-'.getNumeroMes($mesPago).'-'.$allInputs['dia'];

		// $allInputs['fechaSubida'] = date('Y-m-d H:i:s');
		$this->db->trans_start();
		if( !empty($_FILES['nombreArchivo_blob']) ){
			$extension = pathinfo($_FILES['nombreArchivo_blob']['name'], PATHINFO_EXTENSION);
			$nuevoNombreArchivo = strtotime("now").'-'.$allInputs['mes'].$allInputs['anio'].$allInputs['dia'].'.'.$extension;
			if( subir_fichero('assets/dinamic/documentos','nombreArchivo_blob',$nuevoNombreArchivo) ){
				$allInputs['nombreArchivo'] = $nuevoNombreArchivo;
			}
			if($this->model_documento->m_registrar($allInputs) ){
				$arrData['message'] = 'Se registraron los datos correctamente.';
				$arrData['flag'] = 1;
				// $arrData['idreceta'] = $allInputs['idreceta'];
			}
		}
		// ENVIO DE CORREO
			$to = 'luisls1717@gmail.com';
      $subject = strtoupper($this->sessionFactur['nombres']).' SUBIÓ UN NUEVO DOCUMENTO AL SISTEMA';
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: Cabicare < notificaciones@cabicarela.com >\r\n";
      $tipo="'Century Gothic'";
      $message = '
      <html>
        <body style="font-size: 12px; width: 100%; font-family: '.$tipo.', CenturyGothic, AppleGothic, sans-serif;">
          <div style="padding-top: 2%; text-align: right; padding-right: 15%;">
            <img src="https://cabicarela.com/wp-content/uploads/2019/08/logo-png.png" width="17%" style="text-align: right;"></img>
          </div>
          <div style="padding-right: 15%; padding-left: 8%;"><b><label style="color: #000000;">
            <p> '.strtoupper($this->sessionFactur['nombres']).'Subió un nuevo documento al sistema</p>
          </div>
					<br>
					<br>
					<br>
					<div style="background-color: #BF3434; padding-top: 0.5%; padding-bottom: 0.5%">
						<p> Entra al sistema para ver el documento</p>
            <div style="text-align: center;"><b>
              <a href="http://104.131.176.122/sys-cabicare" style="text-decoration-color: #FFFFFF; text-decoration: none; color:  #FFFFFF;"> ENTRAR AL SISTEMA </a></b>
            </div>
          </div>
        </body>
      </html>';
       
		// mail($to, $subject, $message, $headers);
			
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
		// $allInputs['username'] = $this->sessionFactur['username'];
		if( $this->model_documento->m_anular($allInputs) ){
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function ver_popup_formulario()
	{
		$this->load->view('documento/mant_documento');
	}

	public function ver_documento()
	{
		$this->load->view('documento/ver_documento');
	}
}
