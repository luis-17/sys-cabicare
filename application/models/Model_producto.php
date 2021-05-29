<?php
class Model_producto extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_producto($paramDatos, $paramPaginate){
		$this->db->select("pr.id AS productoId, pr.nombre, pr.precio, pr.procedencia, pr.tipoProductoId, 
			tp.nombre AS tipoProducto, se.nombre AS sede, se.id AS sedeId", FALSE);
		$this->db->from('producto pr');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->join('sede se', 'pr.sedeId = se.id');
		$this->db->where('pr.estado', 1);
		$this->db->where('se.id', $paramDatos['sede']['id']);
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

	public function m_count_producto($paramDatos, $paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('producto pr');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->join('sede se', 'pr.sedeId = se.id');
		$this->db->where('pr.estado', 1);
		$this->db->where('se.id', $paramDatos['sede']['id']);
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
	/**
	 * Autocompletado de productos para el registro de una cita
	 *
	 * @Creado: 13-06-2020
	 * @author Ing. Ruben Guevara <rguevarac@hotmail.es>
	 * @param [array] $datos
	 * @return [array]
	 */
	public function m_cargar_autocompletado_producto($datos){
		$this->db->select("
			pr.id AS productoId,
			pr.nombre,
			pr.precio,
			pr.procedencia,
			pr.tipoProductoId,
			tp.nombre AS tipoProducto,
			se.nombre AS sede,
			se.id AS sedeId
		", FALSE);
		$this->db->from('producto pr');
		$this->db->join('tipoproducto tp', 'pr.tipoProductoId = tp.id');
		$this->db->join('sede se', 'pr.sedeId = se.id');
		$this->db->where('pr.estado', 1);
		$this->db->where('pr.sedeId', $this->sessionFactur['idsede']);
		$this->db->like('pr.nombre', $datos['searchText']);
		$this->db->limit(10);

		return $this->db->get()->result_array();
	}

	public function m_registrar($datos)
	{
		$data = array(
			'tipoProductoId' => $datos['tipo_producto']['id'],
			'sedeId' => $datos['sede']['id'],
			'nombre' => strtoupper($datos['nombre']),
			'precio' => $datos['precio'],
			'procedencia' => $datos['procedencia']['id'],
			'estado' => 1,
			'updatedAt' => date("Y-m-d H:i:s"),
			'createdAt' => date("Y-m-d H:i:s")
		);
		return $this->db->insert('producto', $data);
	}

	public function m_editar($datos)
	{
		$data = array(
			'tipoProductoId' => $datos['tipo_producto']['id'],
			'sedeId' => $datos['sede']['id'],
			'nombre' => strtoupper($datos['nombre']),
			'updatedAt' => date("Y-m-d H:i:s"),
			'precio' => $datos['precio'],
			'procedencia' => $datos['procedencia']['id'],
		);
		$this->db->where('id',$datos['idproducto']);
		return $this->db->update('producto', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado' => 0
		);
		$this->db->where('id',$datos['idproducto']);
		return $this->db->update('producto', $data);
	}
}
?>
