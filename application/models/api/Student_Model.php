<?php
class Student_Model extends CI_Model {
  public function __construct() {
    parent::__construct();
    $this->load->database();
  } 

  public function getStudents() {
    $this->db->select("*");
    $this->db->from("students");
    $query = $this->db->get();

    return $query->result();
  }

  public function postStudent($data = array()) {
    return $this->db->insert("students", $data);
  }

  public function deleteStudent($id) {
    $this->db->where("id", $id);
    return $this->db->delete("students");
  }

  public function updateStudent($id, $info) {
    $this->db->where("id", $id);
    return $this->db->update("students", $info);
  }
}
?>
