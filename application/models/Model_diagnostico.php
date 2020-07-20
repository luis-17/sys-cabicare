<?php
class Model_diagnostico extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
  public function m_cargar_autocompletado($datos){
    $this->db->select("
      di.id AS diagnosticoId,
      di.codigo,
      di.nombre,
    ", FALSE);
    $this->db->from('diagnostico di');
    // $this->db->join('tipoproducto tp', 'di.tipoProductoId = tp.id');
    $this->db->where('di.estado', 1);
    $this->db->like('di.nombre', strtoupper($datos['searchText']));
    $this->db->limit(10);

    return $this->db->get()->result_array();
  }
  public function m_registrar_diagnostico($data){
		return $this->db->insert('citadiagnostico', $data);
  }
  public function m_eliminar($citaId)
	{
		$this->db->where('citaId',$citaId);
		return $this->db->delete('citadiagnostico');
  }
  public function m_cargar_detalle_dx($citaId)
  {
    $this->db->select("
			cd.id,
			cd.diagnosticoId,
      dx.nombre AS diagnostico,
      dx.codigo,
			cd.tipoDiagnostico,
			cd.citaId", FALSE);
		$this->db->from('citadiagnostico cd');
		$this->db->join('diagnostico dx', 'cd.diagnosticoId = dx.id');
		// $this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->where('cd.citaId', $citaId);
		// $this->db->where('cd.estado', 1);
		// $this->db->order_by('pr.tipoProductoId', 'ASC');
		$this->db->order_by('cd.id', 'ASC');
		return $this->db->get()->result_array();
  }
}
