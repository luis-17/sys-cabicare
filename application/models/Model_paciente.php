<?php
class Model_paciente extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_paciente($paramPaginate){
		$this->db->select("pa.id AS pacienteId, pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno, 
		pa.direccionPersona, pa.direccionFiscal, pa.ruc, pa.razonSocial,
		pa.tipoDocumento, pa.numeroDocumento, pa.medioContacto, pa.distritoId, di.nombre AS distrito,
		pa.sexo, pa.fechaNacimiento, pa.celular, pa.email, pa.alergias, pa.operador, pa.antecedentes, pa.createdAt", FALSE);
		$this->db->from('paciente pa');
		$this->db->join('distrito di', 'pa.distritoId = di.id','left');
		$this->db->where('pa.estado', 1);
		// $this->db->where('pa.idempresaadmin', $this->sessionFactur['idempresaadmin']);
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
	public function m_count_paciente($paramPaginate=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('paciente pa');
		$this->db->join('distrito di', 'pa.distritoId = di.id','left');
		$this->db->where('pa.estado', 1);
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

	public function m_cargar_paciente_por_numero_documento($datos)
	{
		$this->db->select("
			pa.id AS pacienteId,
			pa.nombres,
			pa.apellidoPaterno,
			pa.apellidoMaterno,
			pa.tipoDocumento,
			pa.numeroDocumento,
			pa.medioContacto,
			pa.sexo,
			pa.fechaNacimiento,
			pa.celular,
			pa.email,
			pa.alergias,
			pa.operador,
			pa.antecedentes,
			pa.distritoId,
			pa.direccionFiscal,
			pa.direccionPersona,
			pa.razonSocial,
			pa.ruc,
			di.nombre AS distrito,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente
		", FALSE);
		$this->db->from('paciente pa');
		$this->db->join('distrito di', 'pa.distritoId = di.id','left');
		$this->db->where('pa.numeroDocumento', $datos['numeroDocumento']);
		$this->db->where('pa.estado', 1);
		$this->db->limit('1');
		return $this->db->get()->row_array();
	}

	public function m_cargar_pacientes_excel()
	{
		$this->db->select("pa.id AS pacienteId, pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno, 
		pa.tipoDocumento, pa.numeroDocumento, pa.medioContacto, pa.distritoId, di.nombre AS distrito,
		pa.sexo, pa.fechaNacimiento, pa.celular, pa.email, pa.alergias, pa.operador, pa.antecedentes, pa.createdAt", FALSE);
		$this->db->select("concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente", FALSE);
		$this->db->select("FLOOR(DATEDIFF(NOW(), pa.fechaNacimiento)/365) AS edad", FALSE);
		$this->db->from('paciente pa');
		$this->db->join('distrito di', 'pa.distritoId = di.id','left');
		$this->db->where('pa.estado', 1);
		$this->db->order_by('pa.id', 'DESC');
		return $this->db->get()->result_array();
	}

	public function m_cargar_paciente_por_id($datos)
	{
		$this->db->select("
			pa.id AS pacienteId,
			pa.nombres,
			pa.apellidoPaterno,
			pa.apellidoMaterno,
			pa.tipoDocumento,
			pa.numeroDocumento,
			pa.sexo,
			pa.medioContacto,
			pa.fechaNacimiento,
			pa.celular,
			pa.email,
			pa.alergias,
			pa.operador,
			pa.antecedentes,
			concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno) AS paciente
		", FALSE);
		$this->db->from('paciente pa');
		$this->db->where('pa.id', $datos['id']);
		$this->db->limit('1');
		return $this->db->get()->row_array();
	}
	// VALIDACIONES
	public function m_validar_paciente_num_documento($numDocumento,$excepcion = FALSE,$idpaciente=NULL)
	{
		$this->db->select('pa.id');
		$this->db->from('paciente pa');
		$this->db->where('pa.estado',1);
		$this->db->where('pa.numeroDocumento',$numDocumento);
		if( $excepcion ){
			$this->db->where_not_in('pa.id',$idpaciente);
		}
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'tipoDocumento' => $datos['tipo_documento']['id'],
			'numeroDocumento' => $datos['num_documento'],
			'nombres' => strtoupper($datos['nombres']),
			'apellidoPaterno' => empty($datos['apellido_paterno']) ? NULL : strtoupper($datos['apellido_paterno']),
			'apellidoMaterno' => empty($datos['apellido_materno']) ? NULL : strtoupper($datos['apellido_materno']),
			'email' => empty($datos['email']) ? NULL : strtoupper($datos['email']),
			'celular' => empty($datos['celular']) ? NULL : $datos['celular'],
			'fechaNacimiento' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),
			'tipoSangre'=> empty($datos['tipo_sangre']) ? NULL : strtoupper($datos['tipo_sangre']),
			'sexo' => $datos['sexo']['id'],
			'medioContacto' => $datos['medioContacto']['id'],
			'distritoId' => empty($datos['distrito']) ? NULL : $datos['distrito']['id'],
			'operador' => $datos['operador']['id'],
			'alergias' => empty($datos['alergias']) ? NULL : $datos['alergias'],
			'antecedentes' => empty($datos['antecedentes']) ? NULL : $datos['antecedentes'],
			'ruc' => empty($datos['ruc']) ? NULL : $datos['ruc'],
			'razonSocial' => empty($datos['razonSocial']) ? NULL : $datos['razonSocial'],
			'direccionFiscal' => empty($datos['direccionFiscal']) ? NULL : $datos['direccionFiscal'],
			'direccionPersona' => empty($datos['direccionPersona']) ? NULL : $datos['direccionPersona'],
			'estado'=> 1,
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->insert('paciente', $data);
		return $this->db->insert_id();
	}
	public function m_editar($datos){
		$data = array(
			'tipoDocumento' => $datos['tipo_documento']['id'],
			'numeroDocumento' => $datos['num_documento'],
			'nombres' => strtoupper($datos['nombres']),
			'apellidoPaterno' => empty($datos['apellido_paterno']) ? NULL : strtoupper($datos['apellido_paterno']),
			'apellidoMaterno' => empty($datos['apellido_materno']) ? NULL : strtoupper($datos['apellido_materno']),
			'email' => empty($datos['email']) ? NULL : strtoupper($datos['email']),
			'celular' => empty($datos['celular']) ? NULL : $datos['celular'],
			'fechaNacimiento' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),
			'tipoSangre'=> empty($datos['tipo_sangre']) ? NULL : strtoupper($datos['tipo_sangre']),
			'sexo' => $datos['sexo']['id'],
			'medioContacto' => $datos['medioContacto']['id'],
			'distritoId' => empty($datos['distrito']) ? NULL : $datos['distrito']['id'],
			'operador' => $datos['operador']['id'],
			'alergias' => empty($datos['alergias']) ? NULL : $datos['alergias'],
			'antecedentes' => empty($datos['antecedentes']) ? NULL : $datos['antecedentes'],
			'ruc' => empty($datos['ruc']) ? NULL : $datos['ruc'],
			'razonSocial' => empty($datos['razonSocial']) ? NULL : $datos['razonSocial'],
			'direccionFiscal' => empty($datos['direccionFiscal']) ? NULL : $datos['direccionFiscal'],
			'direccionPersona' => empty($datos['direccionPersona']) ? NULL : $datos['direccionPersona'],
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['idpaciente']);
		return $this->db->update('paciente', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado' => 0,
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$datos['idpaciente']);
		return $this->db->update('paciente', $data);
	}

	// laboratorio
	public function m_agregar_lab($data)
	{
		return $this->db->insert('laboratorio', $data);
	}
	public function m_quitar_lab($data)
	{
		$this->db->where('id', $data['id']);
		return $this->db->delete('laboratorio');
	}
}
?>
