<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->helper(array('fechas_helper'));
        $this->load->model(array('model_cita'));
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
			$lista = $this->model_cita->m_cargar_citas_en_grilla($paramPaginate,$paramDatos);

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
				array_push($arrListadoProd,
					array(
						$i++,
						$row['id'],
						darFormatoDMY($row['fechaCita']),
						darFormatoHora($row['horaHasta']),
						$row['tipoDocumento'],
						$row['numeroDocumento'],
						$row['paciente'],
						$row['medico'],
						$row['total'],
						$estado
					)
				);
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
				array( 'col' => 'MEDICO',			'ancho' => 60, 	'align' => 'L' ),
				array( 'col' => 'TOTAL',			'ancho' => 15, 	'align' => 'R' ),
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

	public function generar_pdf_receta()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = '';
    	$arrData['flag'] = 1;

    	$cita = $this->model_cita->m_cargar_cita_por_id($allInputs['cita']);

		$fechaUT = strtotime($cita['fechaReceta']);
		$d	= date('d', $fechaUT);
		$m	= date('m', $fechaUT);
		$y	= date('Y', $fechaUT);

		$this->pdf = new Fpdfext();
		$this->pdf->AddPage();
    	$this->pdf->SetMargins(0, 10, 10);
    	$this->pdf->SetAutoPageBreak(false);
    	$variablePosY = 0;

		$this->pdf->SetFont('Arial','B',11);

        $this->pdf->SetXY(38,48);
        $this->pdf->Cell(65,6,utf8_decode($cita['paciente']),0,0,'L');
        $this->pdf->SetXY(38,61);
        $this->pdf->Cell(65,6,utf8_decode(devolverEdad($cita['fechaNacimiento']) . ' años'),0,0,'L');

        $this->pdf->SetXY(78,61);
        $this->pdf->Cell(12,6,$d,0,0,'L');

        $this->pdf->SetXY(89,61);
        $this->pdf->Cell(12,6,$m,0,0,'L');

        $this->pdf->SetXY(100,61);
        $this->pdf->Cell(12,6,$y,0,0,'L');

        $this->pdf->SetXY(17,90);
        $this->pdf->TextArea(array(utf8_decode($cita['indicacionesGenerales'])),0,0,FALSE,5,20);
		//salida
		$timestamp = date('YmdHis');
		$nombreArchivo = 'assets/dinamic/pdfTemporales/tempPDF_'. $timestamp .'.pdf';
		$result = $this->pdf->Output( 'F', $nombreArchivo);

		$arrData['urlTempPDF'] = $nombreArchivo;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

}