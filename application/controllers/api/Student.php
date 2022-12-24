<?php

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';

class Student extends REST_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->database();  // load database
    $this->load->model(array("api/Student_Model"));
    $this->load->library("form_validation");
    $this->load->helper("security");
  }
  /*
    INSERT: POST REQUEST TYPE
    UPDATE: PUT/PATCH REQUEST TYPE
    DELETE: DELETE REQUEST TYPE
    LIST: GET REQUEST TYPE
  */ 

  // restserver/index.php/Student (POST Method)
  public function index_post() {
    // insert data
    // $data = json_decode(file_get_contents("php://input"));
    //
    // $name = isset($data->name) ? $data->name : "";
    // $email = isset($data->email) ? $data->email : "";
    // $mobile = isset($data->mobile) ? $data->mobile : "";
    // $course = isset($data->course) ? $data->course : "";
    
    // print_r($this->input->post());die;

    // Collecting form-data inputes
    $name = $this->security->xss_clean($this->input->post("name"));
    $email = $this->security->xss_clean($this->input->post("email"));
    $mobile = $this->security->xss_clean($this->input->post("mobile"));
    $course = $this->security->xss_clean($this->input->post("course"));

    // Validating collected form-data inputes
    $this->form_validation->set_rules("name", "Name", "required");
    $this->form_validation->set_rules("email", "Email", "required|valid_email");
    $this->form_validation->set_rules("mobile", "Mobile", "required");
    $this->form_validation->set_rules("course", "Course", "required");

    // Checking for errors
    if ($this->form_validation->run() === FALSE) {
      $this->response(
        array(
          "status" => 0,
          "message" => "Failed to validate form data"
        ), 
        REST_Controller::HTTP_NOT_FOUND
      );
    } else {
      if (!empty($name) && !empty($email) && !empty($mobile) && !empty($course)) {
        // all values are available
        $student = array(
          "name" => $name,
          "email" => $email,
          "mobile" => $mobile,
          "course" => $course
        );

        if ($this->Student_Model->postStudent($student)) {
          $this->response(
            array(
              "status" => 1,
              "message" => "Successfully added the student to the database",
            ),
            REST_Controller::HTTP_OK
          );
        } else {
          $this->response(
            array(
              "status" => 0,
              "message" => "Failed to add the student to the database"
            ), 
            REST_Controller::HTTP_INTERNAL_SERVER_ERROR
          );
        }
      } else {
        // there are some empty fields
        $this->response(
          array(
            "status" => 0,
            "message" => "All fields needs to be filled",
          ),
          REST_Controller::HTTP_NOT_FOUND
        );
      }
    }    
  }

  // restserver/index.php/Student (PUT Method)
  public function index_put() {
    // update data

    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->id) && isset($data->name) && isset($data->email) && isset($data->mobile) && isset($data->course)) {
      $id = $data->id;
      $info = array(
        "name" => $data->name,
        "email" => $data->email,
        "mobile" => $data->mobile,
        "course" => $data->course
      );

      if ($this->Student_Model->updateStudent($id, $info)) {
        $this->response(
          array(
            "status" => 1,
            "message" => "Successfully updated student record"
          ),
          REST_Controller::HTTP_OK
        );
      } else {
        $this->response(
          array(
            "status" => 0,
            "message" => "Failed to update student record"
          ),
          REST_Controller::HTTP_INTERNAL_SERVER_ERROR
        );
      }
    } else {
      $this->response(
        array(
          "status" => 0,
          "message" => "All fields require to be filled"
        ),
        REST_Controller::HTTP_NOT_FOUND
      );
    }
    
  }

  // restserver/index.php/Student (DELETE Method)
  public function index_delete() {
    // delete data

    $data = json_decode(file_get_contents("php://input"));
    $id = $this->security->xss_clean($data->id);

    if ($this->Student_Model->deleteStudent($id)) {
      // return true
      
      $this->response(
        array(
          "status" => 1,
          "message" => "Successfully deleted student"
        ),
        REST_Controller::HTTP_OK
      );
    } else {
      // return false
      
      $this->response(
        array(
          "status" => 0,
          "message" => "Failed to delete student"
        ),
        REST_Controller::HTTP_NOT_FOUND
      );
    }
    
  }

  // restserver/index.php/Student (GET Method)
  public function index_get() {
    // list data

    $students = $this->Student_Model->getStudents();

    if ($students  > 0) {
      $this->response(
        array(
          "status" => 1,
          "message" => "Successfully listed all students",
          "data" => $students 
        ), 
        REST_Controller::HTTP_OK
      );
    } else {
      $this->response(
        array(
          "status" => 0,
          "message" => "Failed to list all students",
          "data" => $students 
        ), 
        REST_Controller::HTTP_NOT_FOUND
      );
    }
  }
}
?>
