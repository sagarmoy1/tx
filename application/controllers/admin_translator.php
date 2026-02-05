<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_Translator extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('admintranslator_model');
        $this->load->model('translators_model');

       /* if(!$this->session->userdata('is_admin')){
            redirect('admin/login');
        }*/
		//echo '<pre>'; print_r($this->session->userdata);
    }

	/**
    * Load the main view with all the current model model's data.
    * @return void
    */
    public function index()
    {
	if(!$this->session->userdata('is_admin')){
            redirect('admin/login');
        }else{
		$filter_session_data="";
        //all the posts sent by the view      
        $search_string = $this->input->post('search_string');        
        $order = $this->input->post('order'); 
        $order_type = $this->input->post('order_type'); 

        //pagination settings
        $config['per_page'] = 10;
        $config['base_url'] = base_url().'admin/translatorlist';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
        if($order_type){
            $filter_session_data['order_type'] = $order_type;
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata('order_type')){
                $order_type = $this->session->userdata('order_type');    
            }else{
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';    
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;        


       
        if($search_string !== false && $order !== false || $this->uri->segment(3) == true){ 

            if($search_string){
                $filter_session_data['search_string_selected'] = $search_string;
            }else{
                $search_string = $this->session->userdata('search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data['order'] = $order;
            }
            else{
                $order = $this->session->userdata('order');
            }
            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);

            $data['count_translator']= $this->admintranslator_model->count_translator($search_string, $order);
            $config['total_rows'] = $data['count_translator'];

            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['translator'] = $this->admintranslator_model->get_translator($search_string, $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['translator'] = $this->admintranslator_model->get_translator($search_string, '', $order_type, $config['per_page'],$limit_end);           
                }
            }else{
                if($order){
                    $data['translator'] = $this->admintranslator_model->get_translator('', $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['translator'] = $this->admintranslator_model->get_translator('', '', $order_type, $config['per_page'],$limit_end);        
                }
            }

        }else{

            //clean filter data inside section;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['count_translator']= $this->admintranslator_model->count_translator();
            $data['translator'] = $this->admintranslator_model->get_translator('', '', $order_type, $config['per_page'],$limit_end);   
            $config['total_rows'] = $data['count_translator'];

        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   
			
        //load the view
        $this->load->view('admin/vwTranslist',$data);
		}
   }//index
	
	
	public function add()
    {		
			
			if($this->session->userdata('is_admin')){
				$data['message_error'] = "";
				$data['message_success'] = "";
				$this->form_validation->set_rules('email', 'Translator email', 'required');
				if ($this->form_validation->run()== FALSE){
				$this->session->set_flashdata('flash_error', 'errorValidation');
				//$this->load->view('admin/vwAddTranslator',$data);
			} else
				{
					$data_to_store = array(
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'email' => $this->input->post('email'),
					'created' => date('Y-m-d h:i:s')
					);
				$sql = "SELECT * FROM invite WHERE email = ?";
            	$val = $this->db->query($sql, array($this->input->post('email')));
				if ($val->num_rows) {
				$data['message_error'] = "Email Address already taken. ";

				} else{
					$query=$this->db->insert('invite',$data_to_store);
					if($query){
					$mail=$this->input->post('email');
					$sql="SELECT * FROM `invite` WHERE `email` = ?";
				    $val=$this->db->query($sql, array($mail));
				    $fetch=$val->row();
					
					$mailTo=$mail;
					$first_name=$fetch->first_name;
					$last_name=$fetch->last_name;
					$name=$fetch->first_name.''.$fetch->last_name;
					$mailId=$fetch->id;
					$date=$fetch->created;
					$data['name'] =$name;
					$data['first_name'] =$first_name;
					$data['last_name'] =$last_name;
					$data['id'] =$mailId;
					$data['created'] =$date;
					$this->email->set_mailtype("html");
					$this->email->from('info@montesinotranslation.com');
					$this->email->to($mailTo);
					$this->email->subject('Invitation Email');
					$html_email = $this->load->view('email/vwSendInvitation', $data ,true);
					$this->email->message($html_email);
					$this->email->send(FALSE);			
					echo $this->email->print_debugger(array('headers'));

					$data['message_success'] = "Translator email added successfully."; 
					}
				 }
				 
				 
				 }
			$this->load->view('admin/vwAddTranslator',$data); 
			} 
			else {
				$this->session->set_flashdata('error_message', 'Not Permited');
				 redirect('admin/index');
			}
    }
	
	public function mail(){
		$id=$this->uri->segment(4);
		//echo $id;die;
		
					
					$sql="SELECT * FROM `invite` WHERE `id`='$id'";
					//echo $sql;die;
				    $val=$this->db->query($sql);
				    $fetch=$val->row();
					
					$mailTo=$fetch->email;;
					$first_name=$fetch->first_name;
					$last_name=$fetch->last_name;
					$name=$fetch->first_name.''.$fetch->last_name;
					$mailId=$fetch->id;
					$date=$fetch->created;
					$data['name'] =$name;
					$data['first_name'] =$first_name;
					$data['last_name'] =$last_name;
					$data['id'] =$mailId;
					$data['created'] =$date;
					$this->email->set_mailtype("html");
					$this->email->from('info@montesinotranslation.com');
					$this->email->to($mailTo);
					$this->email->subject('Invitation Email');
					$html_email = $this->load->view('email/vwSendInvitation', $data ,true);
					$this->email->message($html_email);
					$this->email->send();			
					//$data['message_success'] = "Email Send successfully."; 
					$this->session->set_flashdata('message_success', 'Email Send successfully.');
					redirect('admin/translatorlist');
					//$this->load->view('admin/vwTranslist',$data);
	}
	
	
	public function invitation_check()
	{//echo "hello";die;
	
		if($this->session->userdata('translator_id'))
		{
		$this->session->set_userdata('translator_id','');
		$this->session->set_userdata('user_name','');
		$this->session->set_userdata('email_addres','');
		$this->session->set_userdata('is_logged_in',false);
		$this->session->set_userdata('is_translator',false);
		
		}

		$data['message_error'] = "";
		$data['message_success'] = "";
		$translator_id = $this->uri->segment(3);
		//$job_alias = $this->uri->segment(4);
		
		$sql ="SELECT * FROM translator WHERE id = " . $translator_id;// "SELECT * FROM invite WHERE id = '" . $translator_id . "'";
		//echo $sql;
		$val = $this->db->query($sql);
		$results = $val->result_array();
		//print_r($results);die;

		if($results!=null){

			//echo'<pre>';print_r($results);die;
			$user_name = $results[0]['email'];
			//echo $email_address;die;
			/*$sql = "SELECT * FROM translator WHERE id = '" . $translator_id  . "' AND verified = '1' ";
			$val = $this->db->query($sql);*/
			$is_valid = $val->num_rows;
			/*if($is_valid>0)
			{	
				$this->session->set_flashdata('message_success_new', 'Login to bid.');			
				redirect('translator/login');	
			}
			else
			{*/
				//print_r($results);die;
				$results2=$val->result_array();
				$status=$results[0]['status'];
				$verified=$results[0]['verified'];
				$de_act_status=$results[0]['de_act_status'];
				if($status==2 && $verified==2 && $de_act_status==1)
				{
					$this->session->set_flashdata('message_error', 'Your account is not verified or inactive .');
					redirect('translator/login');
				}else{
				$update=$this->translators_model->update_data(['verified'=>1,'status'=>1],['id'=>$translator_id]);
				$data = array(
	                                'translator_id' => $translator_id,
	                                'user_name' => $results[0]['user_name'],
	                                'email_addres' => $results2[0]['email_address'],
	                                'is_logged_in' => true,
	                                'is_translator' => true
	                            );
	            $this->session->set_userdata($data);
				/*$data = array(
							'email_name' => $results[0]['email'],
							'first_name' => $results[0]['first_name'],
							'last_name' => $results[0]['last_name'],
							'is_invited_translator' => true
						);
								//echo'<pre>';print_r($data);die;
				$this->session->set_userdata($data);
				$this->session->set_flashdata('message_success_new', 'Fill up the Form .');*/
				redirect('translator/dashboard');
				}
			//}

		}
		
		else
		{//echo"false";die;

			$data = array(
						'email_name' => $results[0]['email'],
						'first_name' => $results[0]['first_name'],
						'last_name' => $results[0]['last_name'],
						'is_invited_translator' => true
					);
							//echo'<pre>';print_r($data);die;
			$this->session->set_userdata($data);
			$this->session->set_flashdata('message_success_new', 'Fill up the Form .');
			redirect('translator/registration');
		}
		
    	
		
	}

	 public function delete()
    {		
	if($this->session->userdata('is_admin')){
       $id = $this->uri->segment(3);
	   //echo $id;die;
		$sql = "SELECT * FROM invite WHERE id = " . $id . " ";
		$val = $this->db->query($sql);
		$row = $val->row_array();
			
		$this->admintranslator_model->delete_translator($id);
		$this->session->set_flashdata('success_message', 'Successfully Deleted');
        redirect('admin/translatorlist');
      } else {
		$this->session->set_flashdata('error_message', ' Not Deleted');
        redirect('admin/translatorlist');
      }
		
    }
	public function edit()
	{
		
	    if($this->session->userdata('is_admin')){
        $id= $this->uri->segment(4);
		
		//echo $id;die;
		$sql="SELECT * FROM `invite` where `id`='$id' ";
		$qry=$this->db->query($sql);
		if($qry->num_rows()=='1')
		{
        $data['fetch']=$qry->row();
		$this->load->view('admin/vwEditTranslator', $data);
		}
		
      } else {
        $this->session->set_flashdata('error_message', 'Not Permited');
        redirect('admin/index');
      }
	}
	
	
		function editprofile() { 
		
		if(!$this->session->userdata('is_admin')){
		$this->load->view('admin/vwLogin');	
			} else {		
			$id= $this->uri->segment(4);   
			//echo $id;die;
			$this->form_validation->set_rules('first_name', 'Translator first name', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Translator last name', 'trim|required');
			$this->form_validation->set_rules('email', 'Translator email', 'trim|required');
			if($this->form_validation->run() == FALSE) {
			//$this->session->set_flashdata('flash_error','Error Validation');
			//redirect('admin_jobpost/edit/'.$job_id);
			} else {
			
			$sql = "UPDATE `invite` SET `first_name` = ?, `last_name` = ?, `email` = ?, `modified` = ? WHERE `id` = ?";
			$val = $this->db->query($sql, array(
				$this->input->post('first_name'),
				$this->input->post('last_name'),
				$this->input->post('email'),
				date('Y-m-d h:i:s'),
				$id
			));
			
			if($val == TRUE){
			$this->session->set_flashdata('success_message', 'Successfully Updated');
			redirect('admin/translator/edit/'.$id);
			}else{
			$this->session->set_flashdata('error_message', 'Not Updated');
			redirect('admin/translator/edit/'.$id);
			}
			
			
			} 
						  
			
			    $sql="SELECT * FROM `invite` where `id`='$id' ";
			    $qry=$this->db->query($sql);
			    if($qry->num_rows()=='1')
				{
				$data['fetch']=$qry->row();
				$this->load->view('admin/vwEditTranslator', $data);
				}
			}
		}
		
	 function viewdemo() {
	  if(!$this->session->userdata('is_admin')){
			$this->load->view('admin/index');	
		} else {

		$this->load->view('admin/vwdemo');
		}
		 }	
 		function deactive_account()
         {
            $id= $this->uri->segment(3);
	            $archive_data_check=$this->translators_model->check_data(['id'=>$id]);
	            $mailTo=$archive_data_check[0]->email_address;
	           /* $data['name']=ucwords($archive_data_check[0]->first_name.' '.$archive_data_check[0]->last_name);
	            $data['msg']='Your account has been deactivated by administrator on '.date('d-m-Y H:i:s A');

		        $this->email->set_mailtype("html");
		        $this->email->from('info@montesinotranslation.com');
		        $this->email->to($mailTo);
		        $this->email->subject('Account Deactivation Confirmation Email');
		        $html_email = $this->load->view('email/email_account_deactivation', $data, true);

		        $this->email->message($html_email);
		        $this->email->send();*/
             $this->translators_model->copy_table($id);
             $data=array(
                "status" => 2,
                 "verified" => 2,
                 "de_act_status" => 1,
                 "email_address" => "Not mentioned"
             );
             $this->admintranslator_model->deac_account($id,$data);

             
             
             redirect("admin_translators");
           //  echo $id;exit;
         }

         function reactive_account()
         {
            $id= $this->uri->segment(3);
	            $archive_data_check=$this->admintranslator_model->get_translator_archive_data($id);
	            //print_r($archive_data_check);die;
	            $mailTo=$archive_data_check[0]->email_address;
	            $data['name']=ucwords($archive_data_check[0]->first_name.' '.$archive_data_check[0]->last_name);
	            $data['msg']='Your account has been reactivated by administrator on '.date('d-m-Y H:i:s A');

		        $this->email->set_mailtype("html");
		        $this->email->from('info@montesinotranslation.com');
		        $this->email->to($mailTo);
		        $this->email->subject('Account Activation Confirmation Email');
		        $html_email = $this->load->view('email/email_account_deactivation', $data, true);

		        $this->email->message($html_email);
		        $this->email->send();
             $data=array(
                "status" => 1,
                 "verified" => 1,
                 "de_act_status" => 0,
                 "email_address" => $archive_data_check[0]->email_address,
                 "language"=>$archive_data_check[0]->language
             );
             $this->admintranslator_model->reac_account($id,$data);
             $this->admintranslator_model->delete_translator_archive_data($id);
             
             redirect("admin_translators");
           //  echo $id;exit;
         }

}