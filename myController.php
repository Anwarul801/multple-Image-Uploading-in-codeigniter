<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class myController extends CI_Controller {

    private $timestamp;
    private $admin_id;
    public $dist_id;
    public $project;
    public function __construct() {
        parent::__construct();
        $this->load->model('Common_model');
        $this->load->model('DayBook_Model');
        $this->load->model('MyModel');
        $this->load->model('Finane_Model');
        $this->load->model('Inventory_Model');
        $this->load->model('Sales_Model');
        $this->load->model('Pos_Model');
        $this->timestamp = date('Y-m-d H:i:s');
        $this->admin_id = $this->session->userdata('admin_id');
        $this->dist_id = $this->session->userdata('dis_id');

        if (empty($this->admin_id) || empty($this->dist_id)) {
            redirect(site_url());
        }

        $this->project = $this->session->userdata('project');
        $this->db_hostname = $this->session->userdata('db_hostname');
        $this->db_username = $this->session->userdata('db_username');
        $this->db_password = $this->session->userdata('db_password');
        $this->db_name = $this->session->userdata('db_name');
        $this->db->close();
        $config_app = switch_db_dinamico($this->db_username, $this->db_password, $this->db_name);
        $this->db = $this->load->database($config_app, TRUE);
    }

     function index(){
        $data = array();
        // If file upload form submitted
        if($this->input->post('fileSubmit') && !empty($_FILES['files']['name'])){
            $filesCount = count($_FILES['files']['name']);
            for($i = 0; $i < $filesCount; $i++){
                $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error']     = $_FILES['files']['error'][$i];
                $_FILES['file']['size']     = $_FILES['files']['size'][$i];

                $uploadPath = 'uploads/files/';
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('file')){
                    $fileData = $this->upload->data();
                    $uploadData[$i]['file_name'] = $fileData['file_name'];
                    $uploadData[$i]['uploaded_on'] = date("Y-m-d H:i:s");
                     $uploadData[$i]['imageName'] = $this->input->post('imageName');
                }
            }
            if(!empty($uploadData)){
                $insert = $this->MyModel->insert($uploadData);
                $statusMsg = $insert?'Images Uploaded Successfully.':'Images Uploaded Fail.';
                $this->session->set_flashdata('statusMsg',$statusMsg);
            }
        }
        $data['files'] = $this->MyModel->getRows();

        $data['mainContent'] = $this->load->view('distributor/test/multipleImageUpload', $data, true);
        $this->load->view('distributor/masterTemplate', $data);
    }




    public function searcht(){
        $term = $this->input->get('term');
        $this->db->like('title', $term);
        $data = $this->db->get("products")->result();
        echo json_encode( $data);
    }

     public function seachdetails()
    {



         $data['accountHeadList'] = $this->Common_model->getAccountHeadNew();
         $data['getalldayBook'] = $this->DayBook_Model->getalldayBook();

        /*page navbar details*/
        $data['title'] = get_phrase('search');
        $data['page_type'] = $this->page_type;

        $data['link_icon'] = "<i class='fa fa-list'></i>";
        /*page navbar details*/
        $data['mainContent'] = $this->load->view('distributor/test/search', $data, true);
        $this->load->view('distributor/masterTemplate', $data);
    }

     public function dayBookAdd($postingId = null)
    {

        if (isPostBack()) {


            //set some validation for input fields

            $this->form_validation->set_rules('daybook', 'daybook', 'required|is_unique[day_book_report_config.acc_group_id]');


            if ($this->form_validation->run() === FALSE) {
                $msg = "Required field can't be empty Or Not Same Value";
                $this->session->set_flashdata('error', $msg);
                redirect(site_url($this->project . '/dayBookAdd'));
            } else {
                 $this->db->trans_start();
                $data['acc_group_id'] = $this->input->post('daybook');


                $this->db->insert('day_book_report_config', $data);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $msg = "Your data can't be inserted";
                    $this->session->set_flashdata('error', $msg);
                    redirect(site_url($this->project . '/dayBookAdd'));
                } else {
                    $msg = "Your data successfully inserted into database";
                    $this->session->set_flashdata('success', $msg);
                    redirect(site_url($this->project . '/dayBookAdd'));

                }
            }

        }

         $data['accountHeadList'] = $this->Common_model->getAccountHeadNew();
         $data['getalldayBook'] = $this->DayBook_Model->getalldayBook();

        /*page navbar details*/
        $data['title'] = get_phrase('Day Book Add');
        $data['page_type'] = $this->page_type;

        $data['link_icon'] = "<i class='fa fa-list'></i>";
        /*page navbar details*/
        $data['mainContent'] = $this->load->view('distributor/daybook/dayBookConfig', $data, true);
        $this->load->view('distributor/masterTemplate', $data);
    }


    public function checkdayBooks($id){
       $this->DayBook_Model->checkdayBook($id);
//        echo '<pre>';
//        print_r($data);
//        exit();
    }
     public function checkdayBook(){
       $this->DayBook_Model->getalldayBook();
    }
    public function daybookDelete($id){

         $result = $this->DayBook_Model->delete_daybook($id);
        if ($result) {
            $this->session->set_flashdata('message', 'Deleted Sucessfully');
            redirect(site_url($this->project . '/dayBookAdd'));
        } else {
            $this->session->set_flashdata('message', 'Deleted Failed');
             redirect(site_url($this->project . '/dayBookAdd'));
        }
    }



}
