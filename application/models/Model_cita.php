<?php
class Model_cita extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_registrar($data)
	{
		return $this->db->insert('cita', $data);
	}
}