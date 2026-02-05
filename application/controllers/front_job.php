<?php 	error_reporting(0);

class Front_Job extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->model('front_job_model');
	}
	function UrlAlias ($string, $table, $id = NULL) {
        //remove any '-' and '_' from the string they will be used as concatonater
        $str = str_replace(array('-', '_'), ' ', $string);
        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);

        // lowercase and trim
        $str = trim(strtolower($str));

  		// checking if in db or not
 		 if($id == NULL){
			$sql = "SELECT * FROM `".$table."` WHERE 1 AND `alias` = ?";
			$res = $this->db->query($sql, array($str));
			} else {
			$sql = "SELECT * FROM `".$table."` WHERE 1 AND `alias` = ? AND `id` <> ?";
			$res = $this->db->query($sql, array($str, $id));
			}
			$rowcount = $res->num_rows();

			if($rowcount == 0) {
			return $str;
			} else {
			return false;
			}
    		}

	function index()
	{
        if(!$this->session->userdata('is_translator')){
		$this->load->view('translator/vwLogin');
		} else {
        //all the posts sent by the view
        $search_string = $this->input->post('search_string');

		$language_to= $this->input->post('language_to');
		$language_from = $this->input->post('language_from')."/".$language_to;
        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');
        $job_type = $this->input->post('job_type');

        //pagination settings
        $config['per_page'] = 10;
        $config['base_url'] = base_url().'front/jobs';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $page = $this->uri->segment(3);

        $limit_end=($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        }


        if($order_type){
            $filter_session_data['order_type'] = $order_type;
        }
        else{

            if($this->session->userdata('order_type')){
                $order_type = $this->session->userdata('order_type');
            }else{

                $order_type = 'Desc';
            }
        }

        $data['order_type_selected'] = $order_type;

  if($language_from !=='' || $search_string !=='' || $order !=='' || $this->uri->segment(3) == true){
  			if($language_from){
                $filter_session_data['language_from_selected'] = $language_from;
            }else{
                $language_from =$this->session->userdata('language_from_selected');
				$filter_session_data['language_from_selected'] = $language_from;
            }
            $data['language_from_selected'] = $language_from;




            if($search_string){
                $filter_session_data['search_string_selected'] = $search_string;
            }else{
                $search_string =$this->session->userdata('search_string_selected');
				$filter_session_data['search_string_selected'] = $search_string;
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data['order'] = $order;
            }
            else{
                $order =$this->session->userdata('order');
            }
            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);



            $data['count_jobpost']= $this->front_job_model->count_jobpost($language_from,$search_string, $order, $job_type);
            $config['total_rows'] = $data['count_jobpost'];
            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['jobpost'] = $this->front_job_model->get_jobpost($language_from,$search_string, $order, $order_type, $job_type, $config['per_page'],$limit_end);
                }else{
                    $data['jobpost'] = $this->front_job_model->get_jobpost($language_from,$search_string, '', $order_type, $job_type,  $config['per_page'],$limit_end);
                }
            }else{
                if($order){
                    $data['jobpost'] = $this->front_job_model->get_jobpost($language_from, '', $order, $order_type, $job_type,  $config['per_page'],$limit_end);
                }else{
                    $data['jobpost'] = $this->front_job_model->get_jobpost($language_from,'', '', $order_type, $job_type,  $config['per_page'],$limit_end);
                }
            }

        }else{

            //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
			$filter_session_data['language_from_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['language_from_selected'] = '';
			$data['language_to_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays

            $data['count_jobpost']= $this->front_job_model->count_jobpost(null, null, null, $job_type);
            $data['jobpost'] = $this->front_job_model->get_jobpost('','','', '', $order_type, $job_type, $config['per_page'],$limit_end);
            $config['total_rows'] = $data['count_jobpost'];

          }
        $this->pagination->initialize($config);
        $this->load->view('translator/vwJobs',$data);
	   }
	}


    function __encrip_password($password) {
        return md5($password);
    }

    /**
    * check the username and the password with the database
    * @return void
    */

	public function search()
	{
		if(!$this->session->userdata('is_translator')){
				$this->load->view('translator/vwLogin');
			} else {
	$search=$this->input->post('search');
	$language=$this->input->post('job_language');
	$this->load->model('translators_model');
	$data['searchquery']=$this->translators_model->get_jobpost();
	$this->load->view('translator/vwJobs',$data);
			}}


	function jobdetails()
    {
        if (!$this->session->userdata('is_translator')) {
            $this->load->view('translator/vwLogin');
        } else {
            $job_id = $alias = $this->uri->segment(2);
            $alias = $this->uri->segment(3);
//            echo $alias; die;
            $sql = "SELECT *, j.id AS id, j.created AS created, p1.id AS proofread_job_id FROM jobpost j";
            $sql .= " LEFT JOIN proofread_jobs p1 ON p1.job_id = j.id ";
            $sql .= "WHERE j.alias = '" . $alias . "' ";
            $sql .= " and j.id={$job_id}";
            $val = $this->db->query($sql);
            $data['results'] = $val->result_array();
//print_r($data['results']);exit();
            // echo $this->db->last_query(); exit;
            // echo '<pre>'; print_r($data['results']); exit;

            $this->load->view('translator/vwJobdetails', $data);
        }
    }

   function reset()
   {
   $this->session->unset_userdata('search_string_selected');
   $this->session->unset_userdata('language_from_selected');
   $this->session->unset_userdata('language_to_selected');
   $this->index();
   }

   public function viewer()
   {
       if ($this->session->userdata('is_translator')) {

           $proofread_job_doc_id = (int) $this->uri->segment(5);
           $doc_reference = $this->uri->segment(6);

           if ($doc_reference != 'original_file' and $doc_reference != 'translated_file') {
               $this->session->set_flashdata('error', 'Incorrect document reference');
               redirect($this->agent->referrer());
           }

           $this->load->helper('file');

           $document_obj = $this->db->select($doc_reference)->from('proofread_jobs_docs')->where('id', $proofread_job_doc_id)->get();

           if ($document_obj->num_rows()) {

               $file_path = './uploads/review/'.str_replace(' ', '_', $document_obj->row()->$doc_reference);
               $file_info = get_file_info($file_path);
               $file_type = get_mime_by_extension($file_path);
               $data['document']  = 'review/'.str_replace(' ', '_', $document_obj->row()->$doc_reference);
               $data['file_info'] = $file_info;
               $data['file_type'] = $file_type;

               $this->load->view('translator/vwDocumentViewer', $data);
           } else {
               redirect($this->agent->referrer());
           }

       } else {
           redirect('translator/index');
       }
   }

   public function viewer1()
   {
       if ($this->session->userdata('is_translator')) {
if($this->uri->segment(4)) {
    $file = str_replace('&slash&','/', $this->uri->segment(4));
}else{
$file = false;
}
       }

           $this->load->helper('file');


           if ($file) {

                       $file_path = "./uploads/jobpost/{$file}";
                       $file_info = get_file_info($file_path);
                       $file_type = get_mime_by_extension($file_path);

                       $data['document'] = 'jobpost/'.$file;
                       $data['file_info'] = $file_info;
                       $data['file_type'] = $file_type;

               $this->load->view('translator/vwDocumentViewer', $data);

       } else {
           redirect('translator/index');
       }
   }

}
