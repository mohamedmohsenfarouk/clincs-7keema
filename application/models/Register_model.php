<?php
class Register_model extends CI_Model
{
 function insert($data)
 {
  $this->db->insert('ea_customers', $data);
  return $this->db->insert_id();
 }
}

?>