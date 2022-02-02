<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('fechas_helper', 'pdf_helper', 'otros_helper'));
		$this->load->model(array('model_cita', 'model_paciente', 'model_documento', 'model_reporte'));
		$this->load->library(array('excel','Fpdfext'));

		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");

	}

	public function ver_popup_reporte()
	{
		$this->load->view('reportes/popup_reporte');
	}

	public function listado_citas_excel()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		// TRATAMIENTO DE DATOS //
			$lista = array();

			$paramPaginate = $allInputs['paginate'];
			$paramPaginate['firstRow'] = FALSE;
			$paramPaginate['pageSize'] = FALSE;
			$paramDatos = $allInputs['filtro'];
			$nombre_reporte = 'citas';
			$lista = $this->model_cita->m_cargar_citas_excel($paramPaginate,$paramDatos);
			$listaHist = $this->model_cita->m_cargar_citas_excel_historico($paramDatos);

			$total = 0;
			$arrListadoProd = array();
			$i = 1;
			foreach ($lista as $row) {
				if ( $row['estado'] == 1 ){
					$estado = 'POR CONFIRMAR';
				}elseif ( $row['estado'] == 2 ){
					$estado = 'CONFIRMADO';
				}elseif ( $row['estado'] == 3 ) {
					$estado = 'ATENDIDO';
				}else {
					$estado = '';
				}
				// $existRow = false;
				// $filaHistTemp = array();
				// foreach ($lista as $rowHist) {
				// 	if ($rowHist['id'] == $row['id']) {
				// 		$existRow = true;
				// 		$filaHistTemp = $rowHist;
				// 	}
				// }
				// if ($existRow === false) {
				array_push($arrListadoProd,
					array(
						$i++,
						$row['id'],
						darFormatoDMY($row['fechaCita']),
						darFormatoHora($row['horaHasta']),
						$row['tipoDocumento'],
						$row['numeroDocumento'],
						$row['paciente'],
						$row['celular'],
						$row['medico'],
						$row['subtotal'],
						$row['igv'],
						$row['total'],
						$row['numSerie'],
						$row['numDoc'],
						$row['metodoPago'],
						$row['numOperacion'],
						$row['monto'],
						$row['anotacionesPago'],
						$estado
					)
				);
				// }
				// if ($existRow === true) {
				// array_push($arrListadoProd,
				// 	array(
				// 		$i++,
				// 		$filaHistTemp['id'],
				// 		darFormatoDMY($filaHistTemp['fechaCita']),
				// 		darFormatoHora($filaHistTemp['horaHasta']),
				// 		$filaHistTemp['tipoDocumento'],
				// 		$filaHistTemp['numeroDocumento'],
				// 		$filaHistTemp['paciente'],
				// 		$filaHistTemp['celular'],
				// 		$filaHistTemp['medico'],
				// 		$filaHistTemp['subtotal'],
				// 		$filaHistTemp['igv'],
				// 		$filaHistTemp['total'],
				// 		$filaHistTemp['numSerie'],
				// 		$filaHistTemp['numDoc'],
				// 		$filaHistTemp['metodoPago'],
				// 		$filaHistTemp['numOperacion'],
				// 		$filaHistTemp['monto'],
				// 		$filaHistTemp['anotacionesPago'],
				// 		$estado
				// 	)
				// );
				// }
			}

			// SETEO DE VARIABLES
			$dataColumnsTP = array(
				array( 'col' => '#',                'ancho' =>  7, 	'align' => 'L' ),
				array( 'col' => 'COD CITA',			'ancho' => 10, 	'align' => 'C' ),
				array( 'col' => "FECHA DE CITA",	'ancho' => 12, 	'align' => 'C' ),
				array( 'col' => 'HORA CITA', 		'ancho' => 12, 	'align' => 'C' ),
				array( 'col' => 'TIPO DOCUMENTO',	'ancho' => 12, 	'align' => 'C' ),
				array( 'col' => 'Nº DOCUMENTO',		'ancho' => 15, 	'align' => 'C' ),
				array( 'col' => 'PACIENTE',			'ancho' => 60, 	'align' => 'L' ),
				array( 'col' => 'CELULAR',			'ancho' => 60, 	'align' => 'L' ),
				array( 'col' => 'MEDICO',			'ancho' => 60, 	'align' => 'L' ),
				array( 'col' => 'SUBTOTAL',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'IGV',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'TOTAL',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'N° SERIE',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'N° DOC.',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'METODO PAGO',			'ancho' => 15, 	'align' => 'L' ),
				array( 'col' => 'N° OPERACION',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'MONTO DE PAGO',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'ANOTACIONES',			'ancho' => 15, 	'align' => 'L' ),
				array( 'col' => 'ESTADO',			'ancho' => 20, 	'align' => 'C' ),

			);
			$titulo = 'LISTADO DE CITAS';
			$nombre_hoja = 'Citas';


			$cantColumns = count($dataColumnsTP);
			$arrColumns = array();
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(2); // por defecto lo ponemos en 2 luego si se usa la columna se cambia
			$a = 'B'; // INICIO DE COLUMNA
			for ($x=0; $x < $cantColumns; $x++) {
				$arrColumns[] = $a++;
			}
			$endColum = end($arrColumns);
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle($nombre_hoja);
			$this->excel->getActiveSheet()->setShowGridlines(false);

		// ESTILOS
			$styleArrayTitle = array(
				'font'=>  array(
					'bold'  => false,
					'size'  => 18,
					'name'  => 'calibri',
					'color' => array('rgb' => 'FFFFFF')
			  	),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array( 'rgb' => '3A3838' )
				),
			);
			$styleArraySubTitle = array(
				'font'=>  array(
					'bold'  => false,
					'size'  => 12,
					'name'  => 'Microsoft Sans Serif',
					'color' => array('rgb' => 'FFFFFF')
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array( 'rgb' => '3A3838' )
				),
			);
			$styleArrayHeader = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			  	),
				'font'=>  array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'calibri',
					'color' => array('rgb' => 'FFFFFF')
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array( 'rgb' => '5B9BD5' )
				),
			);
		// TITULO
			$this->excel->getActiveSheet()->getCell($arrColumns[0].'1')->setValue($titulo);
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].'1')->applyFromArray($styleArrayTitle);
			$this->excel->getActiveSheet()->mergeCells($arrColumns[0].'1:'. $endColum .'1');


			$currentCellEncabezado = 4; // donde inicia el encabezado del listado
			$fila_mes = $currentCellEncabezado - 1;
			$fila = $currentCellEncabezado + 1;
			$pieListado = $fila + count($arrListadoProd);

		// ENCABEZADO DE LA LISTA
			$i=0;
			foreach ($dataColumnsTP as $key => $value) {
				$this->excel->getActiveSheet()->getColumnDimension($arrColumns[$i])->setWidth($value['ancho']);
				$this->excel->getActiveSheet()->getCell($arrColumns[$i].$currentCellEncabezado)->setValue($value['col']);
				if( $value['align'] == 'C' ){
					$this->excel->getActiveSheet()->getStyle($arrColumns[$i].$fila .':'.$arrColumns[$i].$pieListado)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}

				$i++;
			}
			$c1 = $i;
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado)->getAlignment()->setWrapText(true);
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].($currentCellEncabezado).':'.$endColum.($currentCellEncabezado))->applyFromArray($styleArrayHeader);
			$this->excel->getActiveSheet()->getRowDimension($currentCellEncabezado)->setRowHeight(45);
			$this->excel->getActiveSheet()->setAutoFilter($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado);

		// LISTA
			$this->excel->getActiveSheet()->fromArray($arrListadoProd, null, $arrColumns[0].$fila);
			$this->excel->getActiveSheet()->freezePane($arrColumns[0].$fila);


		$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		$time = date('YmdHis_His');
		$objWriter->save('assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx');

		$arrData = array(
		  'urlTempEXCEL'=> 'assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx',
		  'flag'=> 1
		);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function listado_detalle_citas_eliminados()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		// TRATAMIENTO DE DATOS //
			$lista = array();
			$paramDatos = $allInputs['filtro'];
			$nombre_reporte = 'citasAnuladas';
			$lista = $this->model_cita->m_cargar_detalle_citas_eliminadas_excel($paramDatos);
			$total = 0;
			$arrListadoProd = array();
			$i = 1;
			foreach ($lista as $row) {
				array_push($arrListadoProd,
					array(
						$i++,
						$row['paciente'],
						darFormatoDMY($row['fechaCita']),
						darFormatoHora($row['horaDesde']),
						$row['createdAt'],
						$row['updatedAt'],
						$row['fechaAtencion'],
						$row['username'],
						$row['usuarioAnulacionDet'],
						$row['motivoAnulacionDet'],
						$row['precioReal'],
						$row['producto'],
						$row['medico'],
						$row['informe'],
						$row['observaciones'],
						$row['motivoConsulta'],
						$row['plan']
					)
				);
			}

			// SETEO DE VARIABLES
			$dataColumnsTP = array(
				array( 'col' => '#',                'ancho' =>  7, 	'align' => 'L' ),
				array( 'col' => 'PACIENTE',			'ancho' => 35, 	'align' => 'C' ),
				array( 'col' => "FECHA CITA",	'ancho' => 12, 	'align' => 'C' ),
				array( 'col' => 'HORA CITA', 		'ancho' => 12, 	'align' => 'C' ),
				array( 'col' => 'FECHA CREACION',	'ancho' => 26, 	'align' => 'C' ),
				array( 'col' => 'FECHA ULTIMA ACTUALIZACION',		'ancho' => 26, 	'align' => 'C' ),
				array( 'col' => 'FECHA ATENCION',			'ancho' => 26, 	'align' => 'L' ),
				array( 'col' => 'USUARIO CREACION',			'ancho' => 16, 	'align' => 'L' ),
				array( 'col' => 'USUARIO ANULACION',			'ancho' => 16, 	'align' => 'L' ),
				array( 'col' => 'MOTIVO ANULACION',			'ancho' => 35, 	'align' => 'R' ),
				array( 'col' => 'PRECIO',			'ancho' => 15, 	'align' => 'R' ),
				array( 'col' => 'PRODUCTO',			'ancho' => 30, 	'align' => 'R' ),
				array( 'col' => 'MEDICO',			'ancho' => 35, 	'align' => 'R' ),
				array( 'col' => 'INFORME MEDICO',			'ancho' => 38, 	'align' => 'L' ),
				array( 'col' => 'OBSERVACIONES',			'ancho' => 38, 	'align' => 'L' ),
				array( 'col' => 'MOTIVO DE CONSULTA',			'ancho' => 38, 	'align' => 'L' ),
				array( 'col' => 'PLAN',			'ancho' => 38, 	'align' => 'L' )
			);
			$titulo = 'LISTADO DE PRODUCTOS ANULADOS';
			$nombre_hoja = 'Citas';

			$cantColumns = count($dataColumnsTP);
			$arrColumns = array();
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(2); // por defecto lo ponemos en 2 luego si se usa la columna se cambia
			$a = 'B'; // INICIO DE COLUMNA
			for ($x=0; $x < $cantColumns; $x++) {
				$arrColumns[] = $a++;
			}
			$endColum = end($arrColumns);
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle($nombre_hoja);
			$this->excel->getActiveSheet()->setShowGridlines(false);

		// ESTILOS
			$styleArrayTitle = array(
				'font'=>  array(
					'bold'  => false,
					'size'  => 18,
					'name'  => 'calibri',
					'color' => array('rgb' => 'FFFFFF')
			  	),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array( 'rgb' => '3A3838' )
				),
			);
			$styleArraySubTitle = array(
				'font'=>  array(
					'bold'  => false,
					'size'  => 12,
					'name'  => 'Microsoft Sans Serif',
					'color' => array('rgb' => 'FFFFFF')
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array( 'rgb' => '3A3838' )
				),
			);
			$styleArrayHeader = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			  	),
				'font'=>  array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'calibri',
					'color' => array('rgb' => 'FFFFFF')
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array( 'rgb' => '5B9BD5' )
				),
			);
			// TITULO
			$this->excel->getActiveSheet()->getCell($arrColumns[0].'1')->setValue($titulo);
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].'1')->applyFromArray($styleArrayTitle);
			$this->excel->getActiveSheet()->mergeCells($arrColumns[0].'1:'. $endColum .'1');


			$currentCellEncabezado = 4; // donde inicia el encabezado del listado
			$fila_mes = $currentCellEncabezado - 1;
			$fila = $currentCellEncabezado + 1;
			$pieListado = $fila + count($arrListadoProd);

			// ENCABEZADO DE LA LISTA
			$i=0;
			foreach ($dataColumnsTP as $key => $value) {
				$this->excel->getActiveSheet()->getColumnDimension($arrColumns[$i])->setWidth($value['ancho']);
				$this->excel->getActiveSheet()->getCell($arrColumns[$i].$currentCellEncabezado)->setValue($value['col']);
				if( $value['align'] == 'C' ){
					$this->excel->getActiveSheet()->getStyle($arrColumns[$i].$fila .':'.$arrColumns[$i].$pieListado)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
				$i++;
			}
			$c1 = $i;
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado)->getAlignment()->setWrapText(true);
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].($currentCellEncabezado).':'.$endColum.($currentCellEncabezado))->applyFromArray($styleArrayHeader);
			$this->excel->getActiveSheet()->getRowDimension($currentCellEncabezado)->setRowHeight(45);
			$this->excel->getActiveSheet()->setAutoFilter($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado);

			// LISTA
			$this->excel->getActiveSheet()->fromArray($arrListadoProd, null, $arrColumns[0].$fila);
			$this->excel->getActiveSheet()->freezePane($arrColumns[0].$fila);


		$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		$time = date('YmdHis_His');
		$objWriter->save('assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx');

		$arrData = array(
		  'urlTempEXCEL'=> 'assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx',
		  'flag'=> 1
		);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function listado_documentos_excel()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		// TRATAMIENTO DE DATOS //
		$lista = array();

		$paramPaginate = $allInputs['paginate'];
		$paramPaginate['firstRow'] = FALSE;
		$paramPaginate['pageSize'] = FALSE;
		$paramDatos = $allInputs['filtro'];
		$nombre_reporte = 'documentos';
		$lista = $this->model_documento->m_cargar_documentos_excel($paramDatos);

		$total = 0;
		$arrListadoProd = array();
		$i = 1;
		foreach ($lista as $row) {
			array_push($arrListadoProd,
				array(
					$i++,
					$row['documentoId'],
					$row['categoria'],
					$row['anio'].'/'.$row['mes'].'/'.$row['dia'],
					$row['numSerie'],
					$row['numDoc'],
					$row['ruc'],
					$row['razonSocial'],
					$row['observaciones'],
					$row['monto'],
					$row['moneda'],
					formatoFechaReporte4($row['fechaCreacion'])
				)
			);
		}

		// SETEO DE VARIABLES
		$dataColumnsTP = array(
			array( 'col' => '#',                'ancho' =>  7, 	'align' => 'L' ),
			array( 'col' => 'COD DOCUMENTO',			'ancho' => 10, 	'align' => 'C' ),
			array( 'col' => "CATEGORIA",	'ancho' => 24, 	'align' => 'C' ),
			array( 'col' => 'FECHA DOCUMENTO', 		'ancho' => 25, 	'align' => 'C' ),
			array( 'col' => 'Nº SERIE',		'ancho' => 15, 	'align' => 'C' ),
			array( 'col' => 'Nº DOCUMENTO',		'ancho' => 15, 	'align' => 'C' ),
			array( 'col' => 'RUC',			'ancho' => 45, 	'align' => 'L' ),
			array( 'col' => 'RAZON SOCIAL',			'ancho' => 45, 	'align' => 'L' ),
			array( 'col' => 'ANOTACIONES',			'ancho' => 60, 	'align' => 'L' ),
			array( 'col' => 'MONTO',			'ancho' => 15, 	'align' => 'R' ),
			array( 'col' => 'MONEDA',			'ancho' => 15, 	'align' => 'R' ),
			array( 'col' => 'FECHA DE CREACION',			'ancho' => 15, 	'align' => 'R' )
		);
		$titulo = 'LISTADO DE DOCUMENTOS';
		$nombre_hoja = 'Documentos';


		$cantColumns = count($dataColumnsTP);
		$arrColumns = array();
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(2); // por defecto lo ponemos en 2 luego si se usa la columna se cambia
		$a = 'B'; // INICIO DE COLUMNA
		for ($x=0; $x < $cantColumns; $x++) {
			$arrColumns[] = $a++;
		}
		$endColum = end($arrColumns);
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle($nombre_hoja);
		$this->excel->getActiveSheet()->setShowGridlines(false);

		// ESTILOS
		$styleArrayTitle = array(
			'font'=>  array(
				'bold'  => false,
				'size'  => 18,
				'name'  => 'calibri',
				'color' => array('rgb' => 'FFFFFF')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array( 'rgb' => '3A3838' )
			),
		);
		$styleArraySubTitle = array(
			'font'=>  array(
				'bold'  => false,
				'size'  => 12,
				'name'  => 'Microsoft Sans Serif',
				'color' => array('rgb' => 'FFFFFF')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array( 'rgb' => '3A3838' )
			),
		);
		$styleArrayHeader = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'font'=>  array(
				'bold'  => false,
				'size'  => 10,
				'name'  => 'calibri',
				'color' => array('rgb' => 'FFFFFF')
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array( 'rgb' => '5B9BD5' )
			),
		);
		// TITULO
		$this->excel->getActiveSheet()->getCell($arrColumns[0].'1')->setValue($titulo);
		$this->excel->getActiveSheet()->getStyle($arrColumns[0].'1')->applyFromArray($styleArrayTitle);
		$this->excel->getActiveSheet()->mergeCells($arrColumns[0].'1:'. $endColum .'1');


		$currentCellEncabezado = 4; // donde inicia el encabezado del listado
		$fila_mes = $currentCellEncabezado - 1;
		$fila = $currentCellEncabezado + 1;
		$pieListado = $fila + count($arrListadoProd);

		// ENCABEZADO DE LA LISTA
		$i=0;
		foreach ($dataColumnsTP as $key => $value) {
			$this->excel->getActiveSheet()->getColumnDimension($arrColumns[$i])->setWidth($value['ancho']);
			$this->excel->getActiveSheet()->getCell($arrColumns[$i].$currentCellEncabezado)->setValue($value['col']);
			if( $value['align'] == 'C' ){
				$this->excel->getActiveSheet()->getStyle($arrColumns[$i].$fila .':'.$arrColumns[$i].$pieListado)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}

			$i++;
		}
		$c1 = $i;
		$this->excel->getActiveSheet()->getStyle($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado)->getAlignment()->setWrapText(true);
		$this->excel->getActiveSheet()->getStyle($arrColumns[0].($currentCellEncabezado).':'.$endColum.($currentCellEncabezado))->applyFromArray($styleArrayHeader);
		$this->excel->getActiveSheet()->getRowDimension($currentCellEncabezado)->setRowHeight(45);
		$this->excel->getActiveSheet()->setAutoFilter($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado);

		// LISTA
		$this->excel->getActiveSheet()->fromArray($arrListadoProd, null, $arrColumns[0].$fila);
		$this->excel->getActiveSheet()->freezePane($arrColumns[0].$fila);

		$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		$time = date('YmdHis_His');
		$objWriter->save('assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx');

		$arrData = array(
		  'urlTempEXCEL'=> 'assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx',
		  'flag'=> 1
		);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function generar_pdf_receta()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = '';
    	$arrData['flag'] = 1;

    	$cita = $this->model_cita->m_cargar_cita_por_id($allInputs['cita']);
		$recetaDet = $this->model_cita->m_cargar_detalle_receta($cita);

		$this->pdf = new Fpdfext();
		mostrar_plantilla_pdf($this->pdf,utf8_decode($allInputs['titulo']),FALSE,$allInputs['tituloAbv']);
		$this->pdf->AddPage();
    	$this->pdf->SetMargins(10, 10);
    	$this->pdf->SetAutoPageBreak(false);

		$this->pdf->SetLeftMargin(14);


		// SetDrawColor($r, $g=null, $b=null)
		// RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
		$this->pdf->SetDrawColor(129,164,196);
		$this->pdf->SetLineWidth(1);
		$this->pdf->RoundedRect(12,44,181,26,5,'1234','');
    	$this->pdf->SetY(48);
		$this->pdf->SetFont('Arial','',15);
        $this->pdf->Cell(25,6,utf8_decode('Paciente: '),0,0,'L');
		$this->pdf->SetFont('Arial','',12);
        $this->pdf->Cell(165,6,utf8_decode($cita['paciente']),0,0,'L');
        $this->pdf->Ln(12);

		$this->pdf->SetFont('Arial','',15);
        $this->pdf->Cell(18,6,utf8_decode('Edad: '),0,0,'L');
		$this->pdf->SetFont('Arial','',12);
        $this->pdf->Cell(29,6,utf8_decode(devolverEdad($cita['fechaNacimiento']) . ' años'),0,0,'L');

		$this->pdf->SetFont('Arial','',15);
        $this->pdf->Cell(18,6,utf8_decode('Fecha: '),0,0,'L');
		$this->pdf->SetFont('Arial','',12);
        $this->pdf->Cell(43,6, darFormatoDMY2($cita['fechaReceta']),0,0,'L');


		$this->pdf->SetLeftMargin(10);
		$this->pdf->Ln(25);
		// $this->pdf->SetX(10);
		// $this->pdf->SetFont('Arial','B',8);
		// $this->pdf->Cell(80,6,utf8_decode('MEDICAMENTO'),1,0);
		// $this->pdf->Cell(10,6,utf8_decode('CANT.'),1,0);
		// $this->pdf->Cell(100,6,strip_tags(utf8_decode('INDICACIONES')),1,0);
		// $this->pdf->Ln(6);

		$this->pdf->SetFont('Arial','B',9);
       	$this->pdf->SetFillColor(214,225,242);
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetLineWidth(0);
       	$this->pdf->Cell(0,6,utf8_decode('RECETA MÉDICA'),1,0,'C', true);
       	$this->pdf->Ln(8);
       	$this->pdf->Cell(80,6,utf8_decode('MEDICAMENTO'),0,0);
       	$this->pdf->Cell(15,6,utf8_decode('CANT.'),0,0);
       	$this->pdf->Cell(95,6,utf8_decode('INDICACIONES'),0,0);
       	$this->pdf->Ln(6);

		$this->pdf->SetFont('Arial','',8);
		foreach ($recetaDet as $key => $value) {
			$this->pdf->SetX(10);
			$this->pdf->SetWidths(array(80, 15, 95));
			$this->pdf->Row(
				array(
				strtoupper($value['nombreMedicamento']),
				$value['cantidad'],
				nl2br($value['indicaciones'])
				)
			);
		}


		$this->pdf->Ln(20);
		$this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(190,6,utf8_decode('INDICACIONES GENERALES'),0,0);
		$this->pdf->Ln(6);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(190,6,utf8_decode($cita['indicacionesGenerales']),0,'L',FALSE);

        // SELLO Y FIRMA
		$this->pdf->SetXY(-85,260);
		// $this->pdf->SetFont('Arial','',11);
		// $this->pdf->Cell(100,6,'');
		// $this->pdf->Cell(90,6,utf8_decode($cita['medico']),0,0,'C');
		// $this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','',8);
		// $this->pdf->Cell(100,6,'');
		$this->pdf->Cell(50,6,utf8_decode('Firma / Sello'),'T',0,'C');
		$this->pdf->Ln(8);

		//salida
		$timestamp = date('YmdHis');
		$nombreArchivo = 'assets/dinamic/pdfTemporales/tempPDF_'. $timestamp .'.pdf';
		$result = $this->pdf->Output( 'F', $nombreArchivo);

		$arrData['urlTempPDF'] = $nombreArchivo;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function reporte_pacientes()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		// TRATAMIENTO DE DATOS //
		// $lista = array();

		// $paramPaginate = $allInputs['paginate'];
		// $paramPaginate['firstRow'] = FALSE;
		// $paramPaginate['pageSize'] = FALSE;
		// $paramDatos = $allInputs['filtro'];
		$nombre_reporte = 'pacientes';
		$lista = $this->model_paciente->m_cargar_pacientes_excel();

		$total = 0;
		$arrListadoProd = array();
		$i = 1;
		foreach ($lista as $row) {
			array_push($arrListadoProd,
				array(
					$i++,
					$row['pacienteId'],
					$row['tipoDocumento'],
					$row['numeroDocumento'],
					$row['paciente'],
					$row['email'],
					$row['celular'],
					$row['edad'],
					$row['distrito'],
					$row['createdAt']
				)
			);
		}

		// SETEO DE VARIABLES
		$dataColumnsTP = array(
			array( 'col' => '#',                'ancho' =>  7, 	'align' => 'L' ),
			array( 'col' => 'COD PAC.',			'ancho' => 10, 	'align' => 'C' ),
			array( 'col' => 'TIPO DOCUMENTO',	'ancho' => 12, 	'align' => 'C' ),
			array( 'col' => 'Nº DOCUMENTO',		'ancho' => 15, 	'align' => 'C' ),
			array( 'col' => 'PACIENTE',			'ancho' => 60, 	'align' => 'L' ),
			array( 'col' => 'EMAIL',			'ancho' => 60, 	'align' => 'L' ),
			array( 'col' => 'CELULAR',			'ancho' => 15, 	'align' => 'L' ),
			array( 'col' => 'EDAD',			'ancho' => 20, 	'align' => 'L' ),
			array( 'col' => 'DISTRITO',			'ancho' => 20, 	'align' => 'C' ),
			array( 'col' => 'FECHA CREACIÓN',			'ancho' => 20, 	'align' => 'C' )
		);
		$titulo = 'LISTADO DE PACIENTES';
		$nombre_hoja = 'PACIENTES';


		$cantColumns = count($dataColumnsTP);
		$arrColumns = array();
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(2); // por defecto lo ponemos en 2 luego si se usa la columna se cambia
		$a = 'B'; // INICIO DE COLUMNA
		for ($x=0; $x < $cantColumns; $x++) {
			$arrColumns[] = $a++;
		}
		$endColum = end($arrColumns);
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle($nombre_hoja);
		$this->excel->getActiveSheet()->setShowGridlines(false);

		// ESTILOS
		$styleArrayTitle = array(
			'font'=>  array(
				'bold'  => false,
				'size'  => 18,
				'name'  => 'calibri',
				'color' => array('rgb' => 'FFFFFF')
				),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array( 'rgb' => '3A3838' )
			),
		);
		$styleArraySubTitle = array(
			'font'=>  array(
				'bold'  => false,
				'size'  => 12,
				'name'  => 'Microsoft Sans Serif',
				'color' => array('rgb' => 'FFFFFF')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array( 'rgb' => '3A3838' )
			),
		);
		$styleArrayHeader = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
			'font'=>  array(
				'bold'  => false,
				'size'  => 10,
				'name'  => 'calibri',
				'color' => array('rgb' => 'FFFFFF')
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array( 'rgb' => '5B9BD5' )
			),
		);
		// TITULO
		$this->excel->getActiveSheet()->getCell($arrColumns[0].'1')->setValue($titulo);
		$this->excel->getActiveSheet()->getStyle($arrColumns[0].'1')->applyFromArray($styleArrayTitle);
		$this->excel->getActiveSheet()->mergeCells($arrColumns[0].'1:'. $endColum .'1');


		$currentCellEncabezado = 4; // donde inicia el encabezado del listado
		$fila_mes = $currentCellEncabezado - 1;
		$fila = $currentCellEncabezado + 1;
		$pieListado = $fila + count($arrListadoProd);

		// ENCABEZADO DE LA LISTA
		$i=0;
		foreach ($dataColumnsTP as $key => $value) {
			$this->excel->getActiveSheet()->getColumnDimension($arrColumns[$i])->setWidth($value['ancho']);
			$this->excel->getActiveSheet()->getCell($arrColumns[$i].$currentCellEncabezado)->setValue($value['col']);
			if( $value['align'] == 'C' ){
				$this->excel->getActiveSheet()->getStyle($arrColumns[$i].$fila .':'.$arrColumns[$i].$pieListado)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}

			$i++;
		}
		$c1 = $i;
		$this->excel->getActiveSheet()->getStyle($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado)->getAlignment()->setWrapText(true);
		$this->excel->getActiveSheet()->getStyle($arrColumns[0].($currentCellEncabezado).':'.$endColum.($currentCellEncabezado))->applyFromArray($styleArrayHeader);
		$this->excel->getActiveSheet()->getRowDimension($currentCellEncabezado)->setRowHeight(45);
		$this->excel->getActiveSheet()->setAutoFilter($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado);

		// LISTA
		$this->excel->getActiveSheet()->fromArray($arrListadoProd, null, $arrColumns[0].$fila);
		$this->excel->getActiveSheet()->freezePane($arrColumns[0].$fila);


		$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		$time = date('YmdHis_His');
		$objWriter->save('assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx');

		$arrData = array(
		  'urlTempEXCEL'=> 'assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx',
		  'flag'=> 1
		);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}

	public function listado_activos_pasivos()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		// TRATAMIENTO DE DATOS //
		$listaActivos = array();
		$listaPasivos = array();
		$paramDatos = $allInputs['filtro'];
		$nombre_reporte = 'activos-pasivos';
		$listaActivos = $this->model_reporte->m_cargar_activos($paramDatos);
		$listaPasivos = $this->model_reporte->m_cargar_pasivos($paramDatos);

		$totalActivos = 0;
		$totalPasivos = 0;
		$arrListadoAct = array();
		$arrListadoPas = array();
		$iAct = 1;
		$iPas = 1;
		foreach ($listaActivos as $row) {
			array_push($arrListadoAct,
				array(
					$iAct++,
					$row['numSerie'].'-'.$row['numDoc'],
					$row['numOperacion'],
					darFormatoDMY($row['fechaRegistro']),
					$row['monto']
				)
			);
			$totalActivos += $row['monto'];
		}
		foreach ($listaPasivos as $row) {
			array_push($arrListadoPas,
				array(
					$iPas++,
					$row['numSerie'].'-'.$row['numDoc'],
					$row['numOperacion'],
					darFormatoDMY($row['fechaRegistro']),
					$row['monto']
				)
			);
			$totalPasivos += $row['monto'];
		}

		// SETEO DE VARIABLES
		$dataColumnsTPAct = array(
			// activos
			array( 'col' => '#',                'ancho' =>  7, 	'align' => 'L' ),
			array( 'col' => 'N° COMPROBANTE',   'ancho' =>  15, 'align' => 'L' ),
			array( 'col' => "NUM. OPE.",		'ancho' => 15, 	'align' => 'R' ),
			array( 'col' => "FECHA REGISTRO",	'ancho' => 15, 	'align' => 'R' ),
			array( 'col' => 'MONTO',	'ancho' => 12, 	'align' => 'R' ),
			// pasivos
			array( 'col' => '#',                'ancho' =>  7, 	'align' => 'L' ),
			array( 'col' => 'N° COMPROBANTE',   'ancho' =>  15, 'align' => 'L' ),
			array( 'col' => "NUM. OPE.",		'ancho' => 15, 	'align' => 'R' ),
			array( 'col' => "FECHA REGISTRO",	'ancho' => 15, 	'align' => 'R' ),
			array( 'col' => 'MONTO',	'ancho' => 12, 	'align' => 'R' )
		);

		$titulo = 'LISTADO DE ACTIVOS Y PASIVOS';
		$nombre_hoja = 'Citas';


		$cantColumns = count($dataColumnsTPAct);
		// $cantColumns = 8;
		$arrColumns = array();
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(2); // por defecto lo ponemos en 2 luego si se usa la columna se cambia
		$a = 'B'; // INICIO DE COLUMNA
		for ($x=0; $x < $cantColumns; $x++) {
			$arrColumns[] = $a++;
		}
		$endColum = end($arrColumns);
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle($nombre_hoja);
		$this->excel->getActiveSheet()->setShowGridlines(false);

		// ESTILOS
			$styleArrayTitle = array(
				'font'=>  array(
					'bold'  => false,
					'size'  => 18,
					'name'  => 'calibri',
					'color' => array('rgb' => '000000')
			  	),
				'alignment' => array(
					// 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				// 'fill' => array(
				// 	'type' => PHPExcel_Style_Fill::FILL_SOLID,
				// 	'startcolor' => array( 'rgb' => '3A3838' )
				// ),
			);
			$styleFooter = array(
				'font'=>  array(
					'bold'  => true,
					'size'  => 10,
					'name'  => 'Microsoft Sans Serif',
					'color' => array('rgb' => '000000')
				),
				'alignment' => array(
					// 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array( 'rgb' => 'E6E6E6' )
				),
			);
			$styleArrayHeader = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			  	),
				'font'=>  array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'calibri'
					// 'color' => array('rgb' => 'FFFFFF')
				)
				// 'fill' => array(
				// 	'type' => PHPExcel_Style_Fill::FILL_SOLID,
				// 	'startcolor' => array( 'rgb' => '5B9BD5' )
				// ),
			);
		// TITULO
			$this->excel->getActiveSheet()->getCell($arrColumns[0].'1')->setValue($titulo);
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].'1')->applyFromArray($styleArrayTitle);
			// $this->excel->getActiveSheet()->mergeCells($arrColumns[0].'1:'. $endColum .'1');

			$this->excel->getActiveSheet()->getCell('B3')->setValue('DESDE:');
			$this->excel->getActiveSheet()->getCell('C3')->setValue($paramDatos['fechaDesde']);
			$this->excel->getActiveSheet()->getCell('B4')->setValue('HASTA:');
			$this->excel->getActiveSheet()->getCell('C4')->setValue($paramDatos['fechaHasta']);

			$currentCellEncabezado = 7; // donde inicia el encabezado del listado
			$fila_mes = $currentCellEncabezado - 1;
			$fila = $currentCellEncabezado + 1;
			$pieListado = $fila + count($arrListadoAct);
		
		// ANTE ENCABEZADO DE LISTA
			// $currentCellEncabezado
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
			$this->excel->getActiveSheet()->getCell('B6')->setValue('ACTIVOS');
			$this->excel->getActiveSheet()->mergeCells('B6:E6');

			$this->excel->getActiveSheet()->getCell('F6')->setValue('PASIVOS');
			$this->excel->getActiveSheet()->mergeCells('F6:I6');

			$this->excel->getActiveSheet()->getStyle('B6:I6')->applyFromArray($styleArrayHeader);

		// ENCABEZADO DE LA LISTA
			$i=0;
			foreach ($dataColumnsTPAct as $key => $value) {
				$this->excel->getActiveSheet()->getColumnDimension($arrColumns[$i])->setWidth($value['ancho']);
				$this->excel->getActiveSheet()->getCell($arrColumns[$i].$currentCellEncabezado)->setValue($value['col']);
				if( $value['align'] == 'C' ){
					$this->excel->getActiveSheet()->getStyle($arrColumns[$i].$fila .':'.$arrColumns[$i].$pieListado)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}

				$i++;
			}
			// $c1 = $i;
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado)->getAlignment()->setWrapText(true);
			$this->excel->getActiveSheet()->getStyle($arrColumns[0].($currentCellEncabezado).':'.$endColum.($currentCellEncabezado))->applyFromArray($styleArrayHeader);
			// $this->excel->getActiveSheet()->getRowDimension($currentCellEncabezado)->setRowHeight(45);
			// $this->excel->getActiveSheet()->setAutoFilter($arrColumns[0].$currentCellEncabezado.':'.$endColum.$currentCellEncabezado);

		// LISTA
			$this->excel->getActiveSheet()->fromArray($arrListadoAct, null, $arrColumns[0].$fila);
			$this->excel->getActiveSheet()->fromArray($arrListadoPas, null, 'F8');
			// $this->excel->getActiveSheet()->freezePane($arrColumns[0].$fila);
		
		// PIE DE TOTALES
			$cantFilasAct = count($arrListadoAct);
			$cantFilasPas = count($arrListadoPas);
			$inicioFila = 8;
			$filaFinalData = $inicioFila + ($cantFilasAct > $cantFilasPas ? $cantFilasAct : $cantFilasPas) + 4;

			$this->excel->getActiveSheet()->getCell('J'.$filaFinalData)->setValue('TOTAL ACTIVOS:');
			$this->excel->getActiveSheet()->getCell('K'.$filaFinalData)->setValue($totalActivos);

			$this->excel->getActiveSheet()->getCell('J'.strval($filaFinalData+1))->setValue('TOTAL PASIVOS:');
			$this->excel->getActiveSheet()->getCell('K'.strval($filaFinalData+1))->setValue($totalPasivos);

			$diferenciaTotal = $totalActivos - $totalPasivos;
			$this->excel->getActiveSheet()->getCell('J'.strval($filaFinalData+2))->setValue('DIFERENCIA:');
			$this->excel->getActiveSheet()->getCell('K'.strval($filaFinalData+2))->setValue($diferenciaTotal);

			$this->excel->getActiveSheet()->getStyle('J'.$filaFinalData.':K'.strval($filaFinalData+2))->applyFromArray($styleFooter);
			$this->excel->getActiveSheet()->getStyle('J'.$filaFinalData.':K'.strval($filaFinalData+2))->getAlignment()->setWrapText(true);

		$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		$time = date('YmdHis_His');
		$objWriter->save('assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx');

		$arrData = array(
		  'urlTempEXCEL'=> 'assets/dinamic/excelTemporales/'. $nombre_reporte . '_' . $time.'.xlsx',
		  'flag'=> 1
		);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arrData));
	}
}
