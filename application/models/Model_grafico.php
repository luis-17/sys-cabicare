<?php
class Model_grafico extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_pacientes_por_distrito($paramDatos){
		$sql = 'SELECT COUNT(*) AS contador, di.nombre, di.abreviatura, di.posx, di.posy 
					FROM paciente pa 
					INNER JOIN distrito di ON pa.distritoId = di.id
					WHERE pa.estado = 1 
					AND pa.sedeId = ? 
					AND DATE(pa.createdAt) BETWEEN ? AND ? 
					GROUP BY di.nombre, di.abreviatura, di.posx, di.posy
					ORDER BY COUNT(*) DESC
					LIMIT ?';
		$query = $this->db->query($sql, 
			array(
				$this->sessionFactur['idsede'],
				darFormatoYMD($paramDatos['inicio']), 
				darFormatoYMD($paramDatos['fin']),
				$paramDatos['ultimo']['id']
			) 
		); 
		return $query->result_array();
	}

	public function m_pacientes_por_recomendacion($paramDatos){
		$sql = 'SELECT COUNT(*) AS contador, pa.medioContacto 
					FROM paciente pa 
					WHERE pa.estado = 1 
					AND pa.sedeId = ? 
					AND pa.medioContacto IS NOT NULL 
					AND DATE(pa.createdAt) BETWEEN ? AND ? 
					GROUP BY pa.medioContacto'; 
		$query = $this->db->query($sql, 
			array(
				$this->sessionFactur['idsede'],
				darFormatoYMD($paramDatos['inicio']), 
				darFormatoYMD($paramDatos['fin'])
			) 
		); 
		return $query->result_array();
	}

	public function m_pacientes_por_mes($paramDatos, $vieneDistrito){
		if ($vieneDistrito === 'si') {
			$sql = 'SELECT COUNT(*) AS contador, CONCAT( MONTHNAME(ci.fechaCita),"-",YEAR(ci.fechaCita) ) AS anio_mes, YEAR(ci.fechaCita), MONTH(ci.fechaCita), di.nombre AS distrito
					FROM paciente pa 
					INNER JOIN cita ci ON pa.id = ci.pacienteId
					INNER JOIN distrito di ON pa.distritoId = di.id AND pa.distritoId = ? 
					WHERE pa.estado = 1 
					AND pa.sedeId = ? 
					AND ci.estado IN (3)
					AND DATE(ci.fechaCita) BETWEEN ? AND ? 
					GROUP BY CONCAT( MONTHNAME(ci.fechaCita),"-",YEAR(ci.fechaCita) ), YEAR(ci.fechaCita), MONTH(ci.fechaCita), di.nombre
					ORDER BY YEAR(ci.fechaCita), MONTH(ci.fechaCita), di.nombre';
			$query = $this->db->query($sql, 
				array(
					$paramDatos['distrito']['id'],
					$this->sessionFactur['idsede'],
					darFormatoYMD($paramDatos['inicio']), 
					darFormatoYMD($paramDatos['fin'])
				)
			);
		}
		if ($vieneDistrito === 'no') {
			$sql = 'SELECT COUNT(*) AS contador, CONCAT( MONTHNAME(ci.fechaCita),"-",YEAR(ci.fechaCita) ) AS anio_mes, YEAR(ci.fechaCita), MONTH(ci.fechaCita)
					FROM paciente pa 
					INNER JOIN cita ci ON pa.id = ci.pacienteId
					WHERE pa.estado = 1 
					AND pa.sedeId = ? 
					AND ci.estado IN (3)
					AND DATE(ci.fechaCita) BETWEEN ? AND ? 
					GROUP BY CONCAT( MONTHNAME(ci.fechaCita),"-",YEAR(ci.fechaCita) ), YEAR(ci.fechaCita), MONTH(ci.fechaCita)
					ORDER BY YEAR(ci.fechaCita), MONTH(ci.fechaCita)';
			$query = $this->db->query($sql,
				array(
					$this->sessionFactur['idsede'],
					darFormatoYMD($paramDatos['inicio']), 
					darFormatoYMD($paramDatos['fin'])
				) 
			); 
		}
		
		return $query->result_array();
	}

	public function m_pacientes_nuevos_por_mes($paramDatos, $vieneDistrito){
		if ($vieneDistrito === 'si') {
			$sql = 'SELECT COUNT(*) AS contador, CONCAT( MONTHNAME(pa.createdAt),"-",YEAR(pa.createdAt) ) AS anio_mes, YEAR(pa.createdAt), MONTH(pa.createdAt), di.nombre AS distrito
					FROM paciente pa
					INNER JOIN distrito di ON pa.distritoId = di.id AND pa.distritoId = ? 
					WHERE pa.estado = 1 
					AND pa.sedeId = ? 
					AND DATE(pa.createdAt) BETWEEN ? AND ?
					GROUP BY CONCAT( MONTHNAME(pa.createdAt),"-",YEAR(pa.createdAt) ), YEAR(pa.createdAt), MONTH(pa.createdAt), di.nombre
					ORDER BY YEAR(pa.createdAt), MONTH(pa.createdAt), di.nombre';
			$query = $this->db->query($sql, 
				array(
					$paramDatos['distrito']['id'],
					$this->sessionFactur['idsede'],
					darFormatoYMD($paramDatos['inicio']), 
					darFormatoYMD($paramDatos['fin'])
				)
			);
		}
		if ($vieneDistrito === 'no') {
			$sql = 'SELECT COUNT(*) AS contador, CONCAT( MONTHNAME(pa.createdAt),"-",YEAR(pa.createdAt) ) AS anio_mes, YEAR(pa.createdAt), MONTH(pa.createdAt)
					FROM paciente pa
					WHERE pa.estado = 1 
					AND pa.sedeId = ? 
					AND DATE(pa.createdAt) BETWEEN ? AND ?
					GROUP BY CONCAT( MONTHNAME(pa.createdAt),"-",YEAR(pa.createdAt) ), YEAR(pa.createdAt), MONTH(pa.createdAt)
					ORDER BY YEAR(pa.createdAt), MONTH(pa.createdAt)';
			$query = $this->db->query($sql, 
				array(
					$this->sessionFactur['idsede'],
					darFormatoYMD($paramDatos['inicio']), 
					darFormatoYMD($paramDatos['fin'])
				)
			);
		}
		
		return $query->result_array();
	}

	public function m_medico_prod_mes($paramDatos){
		$sql = 'SELECT 
						COUNT(*) AS contador, 
						SUM(ci.total) AS suma, 
						MONTHNAME(ci.fechaCita) AS mes, 
						MONTH(ci.fechaCita) AS numMes,
						us.nombres AS medico
					FROM cita ci 
					INNER JOIN usuario us ON ci.medicoId = us.id
					WHERE us.estado = 1 
					AND ci.sedeId = ? 
					AND ci.estado IN (3)
					AND YEAR(ci.fechaCita) = ? 
					GROUP BY MONTHNAME(ci.fechaCita), MONTH (ci.fechaCita), us.nombres
					ORDER BY MONTH(ci.fechaCita), us.nombres'; 
		$query = $this->db->query($sql, 
			array(
				$this->sessionFactur['idsede'],
				$paramDatos['anio']['id']
			) 
		); 
		return $query->result_array();
	}

	public function m_pacientes_embarazo($paramDatos)
	{
		$sql = 'SELECT COUNT(*) AS contador, COALESCE(ci.gestando, 2) AS gestando 
					FROM paciente pa 
					INNER JOIN cita ci ON pa.id = ci.pacienteId
					WHERE pa.estado = 1 
					AND ci.estado IN (3)
					AND ci.sedeId = ? 
					AND DATE(ci.fechaCita) BETWEEN ? AND ? 
					GROUP BY COALESCE(ci.gestando, 2)'; 
		$query = $this->db->query($sql, 
			array(
				$this->sessionFactur['idsede'],
				darFormatoYMD($paramDatos['inicio']), 
				darFormatoYMD($paramDatos['fin'])
			) 
		); 
		return $query->result_array();
	}

	public function m_pacientes_embarazo_tl_general($paramDatos)
	{
		$sql = 'SELECT COUNT(*) AS contador, 
							MONTHNAME(ci.fechaCita) AS mes, 
							MONTH(ci.fechaCita) AS numMes,
							ci.gestando
					FROM paciente pa 
					INNER JOIN cita ci ON pa.id = ci.pacienteId
					WHERE pa.estado = 1 
					AND ci.estado IN (3)
					AND ci.sedeId = ? 
					AND YEAR(ci.fechaCita) = ?
					AND ci.gestando = 1
					GROUP BY MONTHNAME(ci.fechaCita), MONTH(ci.fechaCita), ci.gestando
					ORDER BY MONTH(ci.fechaCita), ci.gestando';
		$query = $this->db->query($sql, 
			array(
				$this->sessionFactur['idsede'],
				$paramDatos['anio']['id']
			)
		);
		return $query->result_array();
	}
	public function m_pacientes_embarazo_tl_medico($paramDatos)
	{
		$sql = 'SELECT COUNT(*) AS contador, 
							MONTHNAME(ci.fechaCita) AS mes, 
							MONTH(ci.fechaCita) AS numMes,
							us.nombres AS medico,
							ci.gestando
					FROM paciente pa
					INNER JOIN cita ci ON pa.id = ci.pacienteId
					INNER JOIN usuario us ON ci.medicoId = us.id
					WHERE pa.estado = 1 
					AND us.estado = 1 
					AND pa.sedeId = ? 
					AND ci.estado IN (3)
					AND YEAR(ci.fechaCita) = ?
					AND ci.gestando = 1
					GROUP BY MONTHNAME(ci.fechaCita), MONTH(ci.fechaCita), us.nombres, ci.gestando
					ORDER BY MONTH(ci.fechaCita), us.nombres, ci.gestando';
		$query = $this->db->query($sql, 
			array(
				$this->sessionFactur['idsede'],
				$paramDatos['anio']['id']
			)
		);
		return $query->result_array();
	}
}
