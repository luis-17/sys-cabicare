<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CentralReportes extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('fechas_helper', 'otros_helper', 'pdf_helper'));
    $this->load->model(array('model_cita', 'model_diagnostico'));
    $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
    $this->output->set_header("Pragma: no-cache");
    $this->sessionFactur = @$this->session->userdata('sess_cabi_'.substr(base_url(),-20,7));
    $this->load->library(array('excel','Fpdfext'));
    date_default_timezone_set("America/Lima");
  }
  public function ver_popup_reporte()
  {
    $this->load->view('centralReporte/popup_reporte');
  }
  public function report_ficha_atencion()
  {
    $allInputs = json_decode(trim($this->input->raw_input_stream),true); 
    $this->pdf = new Fpdfext();
    // var_dump($allInputs); exit();
    // $allInputs['id']
    // $idsedeempresaadmin = $this->sessionHospital['idsedeempresaadmin'];
    // $empresaAdmin = $this->model_empresa_admin->m_cargar_esta_sede_empresa_admin($idsedeempresaadmin);
    // $empresaAdmin['estado'] = $empresaAdmin['estado_emp'];
    // $empresaAdmin['mode_report'] = FALSE;
    
    // $arrConfig = array('estado' => );
    mostrar_plantilla_pdf($this->pdf,utf8_decode($allInputs['titulo']),FALSE,$allInputs['tituloAbv']);
    //$this->pdf->SetFont('Arial','',12);
    // $this->pdf->AddPage('P','A4');
    // $this->pdf->AliasNbPages();
    // $arrNumActoMedico = array();
    // if( !empty($allInputs['filas']) ){ 
    //   foreach ($allInputs['filas'] as $key => $row) {
    //     $arrNumActoMedico[] = $row['num_acto_medico'];
    //   }
    // } 
    //$arrNumActoMedico = array($allInputs['num_acto_medico']);
    
    $fAtencion = $this->model_cita->m_cargar_cita_por_id($allInputs);
    $arrDetalleAtencion = $this->model_cita->m_cargar_detalle_cita($allInputs);
    foreach ($arrDetalleAtencion as $key => $fDetAtencion) {
      if($fAtencion['edad'] > '1' ){
        $edadEnAtencion = $fAtencion['edad'] . ' AÑOS';
      }elseif($fAtencion['edad'] == '1' ){
        $edadEnAtencion = $fAtencion['edad'] . ' AÑO';
      }else{
        $edadEnAtencion = strtoupper_total(devolverEdadAtencion($fAtencion['fecha_nacimiento'],$fAtencion['fecha_atencion']));
      }
      $this->pdf->AddPage('P','A4');
      $this->pdf->AliasNbPages();

      $this->pdf->SetFont('Arial','B',10);
      $this->pdf->Cell(0,7,'DATOS DEL PACIENTE',1,0,'C');
      $this->pdf->Ln(8);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(90,6,'Nombres y Apellidos');
      $this->pdf->Cell(40,6,'Num Documento');
      $this->pdf->Cell(30,6,'Sexo:');
      $this->pdf->Cell(30,6,utf8_decode('Historia Nº'));
      $this->pdf->Ln(4);
      $this->pdf->SetFont('Arial','',8);
      $this->pdf->Cell(90,6,utf8_decode($fAtencion['paciente']));
      $this->pdf->Cell(40,6,$fAtencion['numeroDocumento']);
      $this->pdf->Cell(30,6,$fAtencion['sexo']);
      $this->pdf->Cell(30,6,$fAtencion['pacienteId']);
      $this->pdf->Ln(10);
      // APARTADO: ACTO MEDICO
      $this->pdf->SetFont('Arial','B',10);
      $this->pdf->Cell(0,7,'ACTO MEDICO',1,0,'C');
      $this->pdf->Ln(8);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(50,6,utf8_decode('Nº Acto Médico'));
      // $this->pdf->Cell(40,6,utf8_decode('Nº Orden'));
      $this->pdf->Cell(80,6,'Especialidad:');
      $this->pdf->Cell(100,6,utf8_decode('Profesional'));
      
      
      $this->pdf->Ln(4);
      $this->pdf->SetFont('Arial','',8);
      $this->pdf->Cell(50,6,$fAtencion['id'].'-'.$fDetAtencion['id']);
      // $this->pdf->Cell(40,6,$fAtencion['orden_venta']);
      $this->pdf->Cell(80,6,utf8_decode('OBSTETRICIA-GINECOLOGÍA'));
      $this->pdf->Cell(100,6,utf8_decode($fAtencion['medico']));
      

      $this->pdf->Ln(6);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(50,6,utf8_decode('Fecha de Atención'));
      $this->pdf->Cell(40,6,utf8_decode('Edad en la Atención'));
      $this->pdf->Cell(40,6,'Area Hospitalaria');
      $this->pdf->Cell(60,6,utf8_decode('Actividad Específica'));
      

      $this->pdf->Ln(4);
      
      
      $this->pdf->SetFont('Arial','',8);
      
      $this->pdf->Cell(50,6,formatoConDiaHora($fAtencion['fechaCita']));
      $this->pdf->Cell(40,6,utf8_decode($edadEnAtencion));
      $this->pdf->Cell(40,6,utf8_decode('ATENCIÓN AMBULATORIA'));
      // $this->pdf->Cell(60,6,utf8_decode($fAtencion['producto']));
      $this->pdf->SetWidths(array(60));
      $this->pdf->Row( 
        array(
          utf8_decode($fDetAtencion['producto'])
        )
      );
      // $this->pdf->Ln(4);

      // CONSULTA MEDICA
      if($fDetAtencion['tipoProductoId'] == 1){
        
        $this->pdf->Ln(4); // para q no salga pegado al apartado ANAMNESIS, el Row del producto ya le coloca Ln(6)
        
      
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(0,6,utf8_decode('INFORME'),1,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->MultiCell(0,6,utf8_decode($fDetAtencion['informe']));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(0,6,utf8_decode('SIGNOS VITALES'),1,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(47,6,utf8_decode('Presión Arterial'));
        $this->pdf->Cell(47,6,utf8_decode('Presión Arterial (Mm/Hg)'));
        $this->pdf->Cell(48,6,utf8_decode('Frec. Cardiaca (Lat. x Min.)'));
        $this->pdf->Cell(47,6,utf8_decode('Temperatura Corporal (ºC)'));
        // $this->pdf->Cell(48,6,utf8_decode('Frec. Respiratoria (x Min)'));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->Cell(47,6,$fAtencion['presionArterual']);
        $this->pdf->Cell(48,6,$fAtencion['frecuenciaCardiaca']);
        $this->pdf->Cell(47,6,$fAtencion['temperaturaCorporal']);
        // $this->pdf->Cell(48,6,$fAtencion['frec_respiratoria']);
        $this->pdf->Ln(10);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(0,6,utf8_decode('ANTROPOMETRÍA'),1,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(47,6,utf8_decode('Peso (Kg)'));
        $this->pdf->Cell(48,6,utf8_decode('Talla (m)'));
        $this->pdf->Cell(47,6,utf8_decode('IMC (%)'));
        $this->pdf->Cell(48,6,utf8_decode('Perímetro Abdo (cm)'));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->Cell(47,6,$fAtencion['peso']);
        $this->pdf->Cell(48,6,$fAtencion['talla']);
        $this->pdf->Cell(47,6,$fAtencion['imc']);
        $this->pdf->Cell(48,6,$fAtencion['perimetroAbdominal']);
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(50,6,utf8_decode('Examen Clínico'));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->MultiCell(0,6,utf8_decode($fDetAtencion['informe']));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(0,6,utf8_decode('PLAN DE TRABAJO, COMENTARIOS Y/O OBSERVACIONES'),1,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->MultiCell(0,6,utf8_decode($fDetAtencion['observaciones']));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(0,6,utf8_decode('DIAGNÓSTICOS'),1,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(47,6,utf8_decode('CÓDIGO'));
        $this->pdf->Cell(95,6,utf8_decode('DESCRIPCIÓN'));
        $this->pdf->Cell(48,6,utf8_decode('TIPO'));
        $this->pdf->Ln(6);
        $diagnosticos = $this->model_diagnostico->m_cargar_detalle_dx($fAtencion['id']);
        $this->pdf->SetFont('Arial','',8);
        foreach ($diagnosticos as $key => $value) {
            $this->pdf->SetWidths(array(25, 117, 48));
            $this->pdf->Row( 
              array(
                strtoupper($value['codigo']),
                strtoupper(iconv("windows-1252", "utf-8", $value['diagnostico'])),
                $value['tipoDiagnostico']
              )
            );
        }
        $this->pdf->Ln(4);
        // $arrParamRec = array();
        $recetas = $this->model_cita->m_cargar_detalle_receta_por_cita($fAtencion['id']);
        if( !empty($recetas) ){
          $this->pdf->SetFont('Arial','B',9);
          $this->pdf->Cell(0,6,utf8_decode('RECETA MÉDICA'),1,0,'C');
          $this->pdf->Ln(8);
          $this->pdf->SetFont('Arial','B',9);
          $this->pdf->Cell(80,6,utf8_decode('MEDICAMENTO'),0,0);
          $this->pdf->Cell(25,6,utf8_decode('CANTIDAD'),0,0);
          $this->pdf->Cell(117,6,strip_tags(utf8_decode('INDICACIONES')),0,0);
          $this->pdf->Ln(6);
          
          $this->pdf->SetFont('Arial','',8);
          foreach ($recetas as $key => $value) { 
            $this->pdf->SetWidths(array(80, 25, 117));
            $this->pdf->Row( 
              array(
                strtoupper($value['nombreMedicamento']),
                $value['cantidad'],
                nl2br($value['indicaciones'])
              )
            );
          }
        }
      }
      // EXAMEN AUXILIAR
      elseif($fDetAtencion['tipoProductoId'] == 2){
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(0,7,utf8_decode('EXAMEN AUXILIAR'),1,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(50,6,utf8_decode('Observaciones'));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->MultiCell(0,6,strip_tags(utf8_decode($fDetAtencion['observaciones'])));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(50,6,utf8_decode('Informe / Resultado'));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->MultiCell(0,6,strip_tags(utf8_decode($fDetAtencion['informe'])));
        $this->pdf->Ln(4);
      }
      // PROCEDIMIENTO
      elseif($fDetAtencion['tipoProductoId'] == 3){
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(0,7,utf8_decode('PROCEDIMIENTO CLÍNICO'),1,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(50,6,utf8_decode('Observaciones'));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->MultiCell(0,6,strip_tags(utf8_decode($fDetAtencion['observaciones'])));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(50,6,utf8_decode('Informe / Resultado'));
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->MultiCell(0,6,strip_tags(utf8_decode($fDetAtencion['informe'])));
        $this->pdf->Ln(4);
      }
      $this->pdf->Ln(30);
      $this->pdf->SetFont('Arial','',11);
      $this->pdf->Cell(100,6,'');
      $this->pdf->Cell(90,6,utf8_decode($fAtencion['medico']),0,0,'C');
      $this->pdf->Ln(4);
      $this->pdf->SetFont('Arial','',8);
      $this->pdf->Cell(100,6,'');
      $this->pdf->Cell(90,6,utf8_decode('Sello y firma'),0,0,'C');
      $this->pdf->Ln(8);
      $this->pdf->Cell(0,6,utf8_decode('COMPROMETIDOS CON TU SALUD'),0,0,'C');
    }
    $arrData['message'] = 'ERROR';
    $arrData['flag'] = 2;
    $timestamp = date('YmdHis');
    if($this->pdf->Output( 'F','assets/dinamic/pdfTemporales/tempPDF_'. $timestamp .'.pdf' )){
      $arrData['message'] = 'OK';
      $arrData['flag'] = 1;
    }
    $arrData = array(
      'urlTempPDF'=> 'assets/dinamic/pdfTemporales/tempPDF_'. $timestamp .'.pdf'
    );
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($arrData));
  }
  public function report_ficha_atencion_salud_ocup()
  {
    $allInputs = json_decode(trim($this->input->raw_input_stream),true); 
    $this->pdf = new Fpdfext();
    // var_dump($allInputs); exit(); 
    mostrar_plantilla_pdf($this->pdf,utf8_decode($allInputs['titulo']),FALSE,$allInputs['tituloAbv']);
    //$this->pdf->SetFont('Arial','',12);
    // $this->pdf->AddPage('P','A4');
    // $this->pdf->AliasNbPages();
    $arrIds = array();
    if( !empty($allInputs['filas']) ){ 
      foreach ($allInputs['filas'] as $key => $row) {
        $arrIds['arrIds'][] = $row['idatencionocupacional'];
      }
    } 
    //var_dump($arrIds); exit();
    //$arrIds = array($allInputs['num_acto_medico']);
    $listaAtenciones = $this->model_atencion_salud_ocup->m_cargar_estas_atencion_salud_ocupacional($arrIds); 
    
    foreach ($listaAtenciones as $key => $fAtencion) { 
      $this->pdf->AddPage('P','A4');
      $this->pdf->AliasNbPages();

      $this->pdf->SetFont('Arial','B',10);
      $this->pdf->Cell(0,7,'DATOS DEL PACIENTE',1,0,'C');
      $this->pdf->Ln(8);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(90,6,'Apellidos y Nombre');
      $this->pdf->Cell(40,6,'Num Documento');
      $this->pdf->Cell(30,6,'Sexo:');
      $this->pdf->Cell(30,6,utf8_decode('Historia Nº'));
      $this->pdf->Ln(4);
      $this->pdf->SetFont('Arial','',8);
      $this->pdf->Cell(90,6,utf8_decode($fAtencion['cliente']));
      $this->pdf->Cell(40,6,$fAtencion['num_documento']);
      $this->pdf->Cell(30,6,$fAtencion['sexo']);
      $this->pdf->Cell(30,6,$fAtencion['idhistoria']);
      $this->pdf->Ln(10);
      // APARTADO: ACTO MEDICO
      $this->pdf->SetFont('Arial','B',10);
      $this->pdf->Cell(0,7,'ACTO MEDICO',1,0,'C');
      $this->pdf->Ln(8);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(50,6,utf8_decode('Nº Acto Médico'));
      $this->pdf->Cell(40,6,utf8_decode('Nº Orden'));
      $this->pdf->Cell(40,6,'Especialidad:');
      $this->pdf->Cell(60,6,utf8_decode('Profesional Responsable'));
      
      
      $this->pdf->Ln(4);
      $this->pdf->SetFont('Arial','',8);
      $this->pdf->Cell(50,6,$fAtencion['idatencionocupacional']);
      $this->pdf->Cell(40,6,$fAtencion['orden_venta']);
      $this->pdf->Cell(40,6,utf8_decode($fAtencion['especialidad']));
      $this->pdf->Cell(60,6,utf8_decode($fAtencion['medico_responsable']));
      

      $this->pdf->Ln(10);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(50,6,utf8_decode('Fecha de Atención'));
      $this->pdf->Cell(40,6,utf8_decode('Edad en la Atención'));
      $this->pdf->Cell(40,6,'Area Hospitalaria');
      $this->pdf->Cell(60,6,utf8_decode('Perfil'));
     

      $this->pdf->Ln(4);
      
      
      $this->pdf->SetFont('Arial','',8);
      
      $this->pdf->Cell(50,6,$fAtencion['fecha_atencion']);
      $this->pdf->Cell(40,6,utf8_decode(devolverEdadAtencion($fAtencion['fecha_nacimiento'],$fAtencion['fecha_atencion'])));
      $this->pdf->Cell(40,6,utf8_decode('CONSULTA EXTERNA'));
      // $this->pdf->Cell(60,6,utf8_decode($fAtencion['producto'])); CONSULTA EXTERNA
      $this->pdf->SetWidths(array(60));
      $this->pdf->Row( 
        array(
          utf8_decode($fAtencion['producto'])
        )
      );
      $this->pdf->Ln(10);
      // SALUD OCUPACIONAL
      $strArchivoAdjuntoBool = 'SI';
      if(empty($fAtencion['nombre_archivo'])){
        $strArchivoAdjuntoBool = 'NO';
      }
      $this->pdf->SetFont('Arial','B',10);
      $this->pdf->Cell(0,7,'INFORME',1,0,'C');
      $this->pdf->Ln(8);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(50,6,utf8_decode('Informe / Resultado'));
      
      $this->pdf->Ln(4);
      $this->pdf->SetFont('Arial','',8);
      $this->pdf->MultiCell(0,6,strip_tags(utf8_decode($fAtencion['informe'])));
      
      $this->pdf->Ln(2);
      $this->pdf->SetFont('Arial','B',9);
      $this->pdf->Cell(50,6,utf8_decode('Archivo Adjunto')); // mostrar_plantilla_pdf
      $this->pdf->Ln(4);
      $this->pdf->SetFont('Arial','',8);
      $this->pdf->MultiCell(0,6,$strArchivoAdjuntoBool);
    }
    $arrData['message'] = 'ERROR';
    $arrData['flag'] = 2;
    $timestamp = date('YmdHis');
    if($this->pdf->Output( 'F','assets/img/dinamic/pdfTemporales/tempPDF_'. $timestamp .'.pdf' )){
      $arrData['message'] = 'OK';
      $arrData['flag'] = 1;
    }
    $arrData = array(
      'urlTempPDF'=> 'assets/img/dinamic/pdfTemporales/tempPDF_'. $timestamp .'.pdf'
    );
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($arrData));
  }
}
?>
