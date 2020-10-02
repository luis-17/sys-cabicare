<?php
class Model_grafico extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_pacientes_por_recomendacion($paramDatos){
		$sql = 'SELECT COUNT(*) AS contador, pa.medioContacto 
					FROM paciente pa 
					WHERE pa.estado = 1 
					AND pa.medioContacto IS NOT NULL 
					AND DATE(pa.createdAt) BETWEEN ? AND ? 
					GROUP BY pa.medioContacto'; 
		$query = $this->db->query($sql, 
			array(
				darFormatoYMD($paramDatos['inicio']), 
				darFormatoYMD($paramDatos['fin'])
			) 
		); 
		return $query->result_array();
	}

	public function m_pacientes_por_mes($paramDatos){
		$sql = 'SELECT COUNT(*) AS contador, MONTHNAME(ci.fechaCita) AS mes, MONTH(ci.fechaCita) AS numMes
					FROM paciente pa 
					INNER JOIN cita ci ON pa.id = ci.pacienteId
					WHERE pa.estado = 1 
					AND ci.estado IN (2, 3)
					AND DATE(ci.fechaCita) BETWEEN ? AND ? 
					GROUP BY MONTHNAME(ci.fechaCita), MONTH ( ci.fechaCita )
					ORDER BY MONTH(ci.fechaCita)'; 
		$query = $this->db->query($sql, 
			array(
				darFormatoYMD($paramDatos['inicio']), 
				darFormatoYMD($paramDatos['fin'])
			) 
		); 
		return $query->result_array();
	}
}
