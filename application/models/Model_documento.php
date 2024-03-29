<?php
class Model_documento extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_documento($paramPaginate, $paramDatos){
		$this->db->select("do.id AS documentoId, do.mes, do.anio, do.categoria, do.codigoExterno, do.observaciones, do.numOperacion,
		do.monto, do.estado, do.numSerie, do.numDoc, do.ruc, do.moneda, do.dia, do.razonSocial,
        do.nombreArchivo, do.fechaCreacion, us.id AS usuarioId, concat_ws(' ', us.nombres, us.apellidos) AS usuarioRegistro,", FALSE);
		$this->db->from('documento do');
		$this->db->join('usuario us', 'do.usuarioEnvioId = us.id');
        $this->db->where('do.estado', 1);

        if( $paramDatos['categoria']['id'] != 'ALL' ){
            $this->db->where('do.categoria', $paramDatos['categoria']['id']);
        }
        if( $paramDatos['mes']['id'] != 'ALL' ){
            $this->db->where('do.mes', $paramDatos['mes']['id']);
        }
        if( $paramDatos['anio']['id'] != 'ALL' ){
            $this->db->where('do.anio', $paramDatos['anio']['id']);
        }
		
        if( !($this->sessionFactur['keyPerfil'] == 'key_root' || $this->sessionFactur['keyPerfil'] == 'key_cont') ){
            $this->db->where('do.usuarioEnvioId', $this->sessionFactur['usuarioId']);
        }
		
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
	public function m_count_documento($paramPaginate, $paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('documento do');
		$this->db->join('usuario us', 'do.usuarioEnvioId = us.id');
        $this->db->where('do.estado', 1);
        if( $paramDatos['categoria']['id'] != 'ALL' ){
            $this->db->where('do.categoria', $paramDatos['categoria']['id']);
        }
        if( $paramDatos['mes']['id'] != 'ALL' ){
            $this->db->where('do.mes', $paramDatos['mes']['id']);
        }
        if( $paramDatos['anio']['id'] != 'ALL' ){
            $this->db->where('do.anio', $paramDatos['anio']['id']);
        }

        if( $this->sessionFactur['keyPerfil'] != 'key_root' ){
            $this->db->where('do.usuarioEnvioId', $this->sessionFactur['usuarioId']);
        }
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

	public function m_cargar_documentos_excel($paramDatos) {
		$this->db->select("do.id AS documentoId, do.mes, do.anio, do.categoria, do.codigoExterno, do.observaciones, do.numOperacion,
		do.monto, do.estado, do.numSerie, do.numDoc, do.ruc, do.moneda, do.dia, do.razonSocial,
        do.nombreArchivo, do.fechaCreacion, us.id AS usuarioId, concat_ws(' ', us.nombres, us.apellidos) AS usuarioRegistro,", FALSE);
		$this->db->from('documento do');
		$this->db->join('usuario us', 'do.usuarioEnvioId = us.id');
        $this->db->where('do.estado', 1);

        if( $paramDatos['categoriaDoc']['id'] != 'ALL' ){
            $this->db->where('do.categoria', $paramDatos['categoriaDoc']['id']);
        }
        if( $paramDatos['mesDoc']['id'] != 'ALL' ){
            $this->db->where('do.mes', $paramDatos['mesDoc']['id']);
        }
        if( $paramDatos['anioDoc']['id'] != 'ALL' ){
            $this->db->where('do.anio', $paramDatos['anioDoc']['id']);
        }
		
        if( !($this->sessionFactur['keyPerfil'] == 'key_root' || $this->sessionFactur['keyPerfil'] == 'key_cont') ){
            $this->db->where('do.usuarioEnvioId', $this->sessionFactur['usuarioId']);
        }
		return $this->db->get()->result_array();
	}


	public function m_registrar($datos)
	{
		$data = array(
			'mes' => $datos['mes'],
			'anio'=> $datos['anio'],
			'dia'=> $datos['dia'],
			'fechaPago'=> $datos['fechaPago'],
			'categoria'=> $datos['categoria'],
			'codigoExterno'=> empty($datos['codigoExterno']) ? NULL : $datos['codigoExterno'],
			'observaciones'=> empty($datos['observaciones']) ? NULL : $datos['observaciones'],
			'monto'=> empty($datos['monto']) ? NULL : $datos['monto'],
			'numDoc'=> empty($datos['numDoc']) ? NULL : $datos['numDoc'],
			'numSerie'=> empty($datos['numSerie']) ? NULL : $datos['numSerie'],
			'moneda'=> empty($datos['moneda']) ? NULL : $datos['moneda'],
			'ruc'=> empty($datos['ruc']) ? NULL : $datos['ruc'],
			'razonSocial'=> empty($datos['razonSocial']) ? NULL : $datos['razonSocial'],
			'numOperacion'=> empty($datos['numOperacion']) ? NULL : $datos['numOperacion'],
			'nombreArchivo'=> $datos['nombreArchivo'],
			'usuarioEnvioId'=> $this->sessionFactur['usuarioId'],
			'fechaCreacion' => date('Y-m-d H:i:s')
		);
		$this->db->insert('documento', $data);
		return $this->db->insert_id();
	}

	public function m_editar($datos)
	{
		$data = array(
			'mes' => $datos['mes'],
			'anio'=> $datos['anio'],
			'dia'=> $datos['dia'],
			'fechaPago'=> $datos['fechaPago'],
			'categoria'=> $datos['categoria'],
			'codigoExterno'=> empty($datos['codigoExterno']) ? NULL : $datos['codigoExterno'],
			'observaciones'=> empty($datos['observaciones']) ? NULL : $datos['observaciones'],
			'monto'=> empty($datos['monto']) ? NULL : $datos['monto'],
			'numDoc'=> empty($datos['numDoc']) ? NULL : $datos['numDoc'],
			'numSerie'=> empty($datos['numSerie']) ? NULL : $datos['numSerie'],
			'moneda'=> empty($datos['moneda']) ? NULL : $datos['moneda'],
			'ruc'=> empty($datos['ruc']) ? NULL : $datos['ruc'],
			'razonSocial'=> empty($datos['razonSocial']) ? NULL : $datos['razonSocial'],
			'numOperacion'=> empty($datos['numOperacion']) ? NULL : $datos['numOperacion']
			// 'nombreArchivo'=> $datos['nombreArchivo']
		);
		if( !empty($datos['nombreArchivo']) ){
			$data['nombreArchivo'] = $datos['nombreArchivo'];
		}
		$this->db->where('id',$datos['documentoId']);
		return $this->db->update('documento', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado' => 2,
			'fechaAnulacion' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['documentoId']);
		return $this->db->update('documento', $data);
	}
}
?>
