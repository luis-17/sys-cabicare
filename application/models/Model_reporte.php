<?php
class Model_reporte extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_activos($paramDatos){
		$this->db->select("ci.numSerie, ci.numDoc, pg.fechaRegistro, pg.monto", FALSE);
		$this->db->from('cita ci');
		$this->db->join('pago pg', 'ci.id = pg.citaId');
        $this->db->where_in('ci.estado', array(1, 2, 3));
        $this->db->where_in('pg.estado', array(1));
		$this->db->where('pg.fechaRegistro BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['fechaDesde']).' 00:00') .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['fechaHasta']).' 11:59'));

		return $this->db->get()->result_array();
	}

    public function m_cargar_pasivos($paramDatos){
        $this->db->select("do.numSerie, do.numDoc, do.mes, do.anio, do.dia, do.monto", FALSE);
        $this->db->select("CONCAT_WS('-', do.anio, do.mes, do.dia) AS fechaRegistro", FALSE);
		$this->db->from('documento do');
        $this->db->where_in('do.estado', array(1));
		$this->db->where("CONCAT_WS('-', do.anio, do.mes, do.dia) BETWEEN ". $this->db->escape( darFormatoYMD($paramDatos['fechaDesde']).' 00:00') .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['fechaHasta']).' 11:59'));

		return $this->db->get()->result_array();
    }
}
?>
