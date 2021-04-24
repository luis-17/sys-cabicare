<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// require __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
// use Twilio\Rest\Client;

class Nota extends CI_Controller {
	public function __construct(){
    parent::__construct();
		$this->load->helper(array('fechas_helper', 'otros_helper', 'imagen_helper'));
		$this->load->model(array('model_nota', 'model_cita'));

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
					'usuarioRegistro' => $row['usuarioRegistro'],
					'tipoNotaCreditoId' => $row['tipoNotaCreditoId'],
					'tipoNotaCreditoVal' => $row['tipoNotaCreditoVal'],
					'tipoNotaDebitoId' => $row['tipoNotaDebitoId'],
					'tipoNotaDebitoVal' => $row['tipoNotaDebitoVal'],
					'tipoNotaCredito' => array(
						'id' => $row['tipoNotaCreditoId'],
						'descripcion' => $row['tipoNotaCreditoVal']
					),
					'tipoNotaDebito' => array(
						'id' => $row['tipoNotaDebitoId'],
						'descripcion' => $row['tipoNotaDebitoVal']
					),
					'motivo' => $row['tipoNota'] == 'NOTA DE DEBITO' ? $row['tipoNotaDebitoVal'] : $row['tipoNotaCreditoVal']
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
		$fCita = $this->model_nota->m_buscar_cita_doc_asoc($allInputs['numSerieAsoc'], $allInputs['numDocAsoc']);
		if (empty($fCita)) {
			$arrData['message'] = 'El número de documento asociado no se encuentra en la base de datos.';
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}
		$allInputs['citaId'] = $fCita['citaId'];
		// calculo
		$allInputs['igv'] = $allInputs['total'] * 0.18;
		$allInputs['subtotal'] =  $allInputs['total'] - $allInputs['igv'];

		$clienteTipoDoc = '-';
		$serie = null;
		$tokenSede = $fCita['token'];
		if($allInputs['tipoNota']['descripcion'] == 'NOTA DE DEBITO'){
			$tipoDocCont = '4';
			$tipoNotaCredito = '';
			$tipoNotaDebito = $allInputs['tipoNotaDebito']['id'];
		}
		if($allInputs['tipoNota']['descripcion'] == 'NOTA DE CREDITO'){
			$tipoDocCont = '3';
			$tipoNotaCredito = $allInputs['tipoNotaCredito']['id'];
			$tipoNotaDebito = '';
		}
		if ($fCita['tipoDocumentoCont'] === 'BOLETA') {
			$docModificaTipo = 2;
			$docModificaSerie = $fCita['serieb'];
			$serie = $fCita['serieb'];
			// doc cliente
			if($fCita['tipoDocumento'] == 'DNI'){
				$clienteTipoDoc = '1';
			}
			if($fCita['tipoDocumento'] == 'PAS'){
				$clienteTipoDoc = '7';
			}
			if($fCita['tipoDocumento'] == 'CEX'){
				$clienteTipoDoc = '4';
			}
			if($fCita['tipoDocumento'] == 'DNI'){
				$clienteTipoDoc = '1';
			}
			$clienteNumDoc = $fCita['numeroDocumento'];
			$clienteDenominacion = $fCita['nombres'].' '.$fCita['apellidoPaterno'].' '.$fCita['apellidoMaterno'];
			$clienteDireccion = $fCita['direccionPersona'];
		}
		if ($fCita['tipoDocumentoCont'] === 'FACTURA') {
			$docModificaTipo = 1;
			$docModificaSerie = $fCita['serief'];
			$serie = $fCita['serief'];
			$clienteTipoDoc = '6';
			$clienteNumDoc = $fCita['ruc'];
			$clienteDenominacion = $fCita['razonSocial'];
			$clienteDireccion = $fCita['direccionFiscal'];
		}
		$docModificaNumero = $fCita['numDoc'];
		$fFact = $this->model_cita->m_obtener_ultimo_correlativo($serie, $tipoDocCont);
		if (empty($fFact['correlativo'])) {
			$numDocGen = 1;
		} else {
			$numDocGen = (int)$fFact['correlativo'] + 1;
		}

		// DETALLE DOCUMENTO
		$arrDetalle = array(
			array(
				"unidad_de_medida" 					=> 'ZZ',
				// "codigo" 										=> $row['idproducto'],
				"descripcion" 							=> $allInputs['tipoNotaDebito']['descripcion'],
				"cantidad" 									=> '1',
				"valor_unitario" 						=> $allInputs['subtotal'],
				"precio_unitario" 					=> $allInputs['total'],
				"descuento"                 => "",
				"subtotal"                  => $allInputs['subtotal'], // "500",
				"tipo_de_igv"               => "1",
				"igv"                       => $allInputs['igv'], // 90
				"total"                     => $allInputs['total'], // 590
				"anticipo_regularizacion"   => "false",
				"anticipo_documento_serie"  => "",
				"anticipo_documento_numero" => ""
			)
		);

		$data = array(
			"operacion"													=> "generar_comprobante",
			"tipo_de_comprobante"               => $tipoDocCont,
			"serie"                             => $serie,
			"numero"														=> $numDocGen,
			"sunat_transaction"									=> "1",
			"cliente_tipo_de_documento"					=> $clienteTipoDoc, // "6"
			"cliente_numero_de_documento"				=> $clienteNumDoc, // "20600695771",
			"cliente_denominacion"              => $clienteDenominacion,
			"cliente_direccion"                 => $clienteDireccion, // "CALLE LIBERTAD 116 MIRAFLORES - LIMA - PERU",
			"cliente_email"                     => $fCita['email'],
			"cliente_email_1"                   => "",
			"cliente_email_2"                   => "",
			"fecha_de_emision"                  => date('d-m-Y'),
			"fecha_de_vencimiento"              => "",
			"moneda"                            => "1", // soles
			"tipo_de_cambio"                    => "",
			"porcentaje_de_igv"                 => "18.00",
			"descuento_global"                  => "",
			"descuento_global"                  => "",
			"total_descuento"                   => "",
			"total_anticipo"                    => "",
			"total_gravada"                     => $allInputs['subtotal'],
			"total_inafecta"                    => "",
			"total_exonerada"                   => "",
			"total_igv"                         => $allInputs['igv'],
			"total_gratuita"                    => "",
			"total_otros_cargos"                => "",
			"total"                             => $allInputs['total'],
			"percepcion_tipo"                   => "",
			"percepcion_base_imponible"         => "",
			"total_percepcion"                  => "",
			"total_incluido_percepcion"         => "",
			"detraccion"                        => "false",
			"observaciones"                     => $allInputs['anotaciones'],
			"documento_que_se_modifica_tipo"    => $docModificaTipo,
			"documento_que_se_modifica_serie"   => $docModificaSerie,
			"documento_que_se_modifica_numero"  => $docModificaNumero,
			"tipo_de_nota_de_credito"           => $tipoNotaCredito,
			"tipo_de_nota_de_debito"            => $tipoNotaDebito,
			"enviar_automaticamente_a_la_sunat" => "true",
			"enviar_automaticamente_al_cliente" => "true",
			"codigo_unico"                      => $fCita['citaId'],
			"condiciones_de_pago"               => "",
			"medio_de_pago"                     => "",
			"placa_vehiculo"                    => "",
			"orden_compra_servicio"             => "",
			"tabla_personalizada_codigo"        => "",
			"formato_de_pdf"                    => "",
			"items" => $arrDetalle
		);
		$data_json = json_encode($data);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, NB_LINK);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Token token="'.$tokenSede.'"',
			'Content-Type: application/json',
			// 'Expect:',
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		// 'CURLOPT_SSLVERSION' => 'CURL_SSLVERSION_TLSv1',
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // CURLOPT_TIMEOUT        => 30,
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$respuesta  = curl_exec($ch);

		if ($respuesta === false) {
			$respuesta = curl_error($ch);
			echo stripslashes($respuesta);
		}
		curl_close($ch);

		$leer_respuesta = json_decode($respuesta, true);
		print_r('Leer respuesta:');
		print_r($leer_respuesta);
		print_r('...End');

		if (isset($leer_respuesta['errors'])) {
			//Mostramos los errores si los hay
			$arrData['message'] = $leer_respuesta['errors'].'| CÓDIGO: '.$leer_respuesta['codigo'];
			$arrData['flag'] = 0;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($arrData));
			return;
		}

		$this->db->trans_start();

		if($this->model_nota->m_registrar($allInputs) ){
			$arrData['message'] = 'Se registraron los datos correctamente.';
			$arrData['flag'] = 1;
			// $arrData['idreceta'] = $allInputs['idreceta'];
			$arrDataFact = array(
				'tipoDocumento' => $tipoDocCont,
				'numSerie' => $serie,
				'numDocumento' => $numDocGen,
				'estado' => 1,
				'citaId' => $fCita['citaId'],
				'fechaRegistro' => date('Y-m-d H:i:s'),
				'link_pdf' => $leer_respuesta['enlace_del_pdf'],
				'key_nubefact' => $leer_respuesta['key']
			);
			$this->model_cita->m_registrar_facturacion($arrDataFact);
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
