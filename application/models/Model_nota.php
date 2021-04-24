<?php
class Model_nota extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_nota($paramPaginate, $paramDatos){
		$this->db->select("no.id AS notaId, no.tipoNota, no.numSerie, no.numDoc, no.numDocAsoc, no.anotaciones, 
        no.subtotal, no.igv, no.total, no.fechaNota, no.fechaRegistro, us.id AS usuarioId, 
        concat_ws(' ', us.nombres, us.apellidos) AS usuarioRegistro, no.tipoNotaCreditoId,
				no.tipoNotaCreditoVal, no.tipoNotaDebitoId, no.tipoNotaDebitoVal", FALSE);
		$this->db->from('nota no');
		$this->db->join('usuario us', 'no.usuarioCreacionId = us.id');
    $this->db->where('no.estado', 1);

		if( $paramDatos['tipoNota']['id'] != 'ALL' ){
				$this->db->where('no.tipoNota', $paramDatos['tipoNota']['id']);
		}
		$this->db->where('no.fechaNota BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}

		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_nota($paramPaginate, $paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('nota no');
		$this->db->join('usuario us', 'no.usuarioCreacionId = us.id');
        $this->db->where('no.estado', 1);
        if( $paramDatos['tipoNota']['id'] != 'ALL' ){
            $this->db->where('no.tipoNota', $paramDatos['tipoNota']['id']);
        }
        $this->db->where('no.fechaNota BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}

	public function m_buscar_cita_doc_asoc($numSerie, $numDoc) 
	{
		$this->db->select("ci.id AS citaId, ci.estado, pa.celular, pa.tipoDocumento, pa.numeroDocumento, se.serief, se.serieb, se.token,
			pa.nombres, pa.apellidoPaterno, pa.ruc, pa.direccionPersona, pa.direccionFiscal, pa.razonSocial,
			pa.apellidoMaterno, pa.email, ci.tipoDocumentoCont, ci.subtotal, ci.igv, ci.total, ci.numDoc", FALSE);
		$this->db->from('cita ci');
		$this->db->join('paciente pa', 'ci.pacienteId = pa.id');
		$this->db->join('sede se', 'ci.sedeId = se.id');
		$this->db->where('ci.estado <> ', 0);
		$this->db->where('UPPER(ci.numDoc)', strtoupper($numDoc));
		$this->db->where('UPPER(ci.numSerie)', strtoupper($numSerie));
		return $this->db->get()->row_array();
	}

	public function m_obtener_ultimo_correlativo($numSerie)
	{

	}

	public function m_registrar($datos)
	{
		// echo ($datos['fechaNota']);
		$data = array(
			'tipoNota' => $datos['tipoNota']['id'],
			'numSerie'=> $datos['numSerie'],
			'numDoc'=> $datos['numDoc'],
			'numDocAsoc'=> $datos['numSerieAsoc'].'-'.$datos['numDocAsoc'],
			'subtotal'=> empty($datos['subtotal']) ? NULL : $datos['subtotal'],
			'igv'=> empty($datos['igv']) ? NULL : $datos['igv'],
			'total'=> empty($datos['total']) ? NULL : $datos['total'],
			'fechaNota'=> darFormatoYMD($datos['fechaNota']),
			'fechaRegistro' => date('Y-m-d H:i:s'),
			'citaId'=> $datos['citaId'],
			'estado'=> 1,
			'usuarioCreacionId'=> $this->sessionFactur['usuarioId'],
			'anotaciones'=> empty($datos['anotaciones']) ? NULL : $datos['anotaciones'],
			'tipoNotaDebitoId'=> $datos['tipoNotaDebito']['id'] == 0 ? NULL : $datos['tipoNotaDebito']['id'],
			'tipoNotaDebitoVal'=> $datos['tipoNotaDebito']['id'] == 0 ? NULL : $datos['tipoNotaDebito']['descripcion'],
			'tipoNotaCreditoId'=> $datos['tipoNotaCredito']['id'] == 0 ? NULL : $datos['tipoNotaCredito']['id'],
			'tipoNotaCreditoVal'=> $datos['tipoNotaCredito']['id'] == 0 ? NULL : $datos['tipoNotaCredito']['descripcion']
		);
		$this->db->insert('nota', $data);
		return $this->db->insert_id();
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado' => 2,
			'fechaAnulacion' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['notaId']);
		return $this->db->update('nota', $data);
	}
}
?>
