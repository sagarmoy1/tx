<?php error_reporting(1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_Jobpost extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        //$this->load->library('My_PHPMailer');
        $this->load->model('adminjobpost_model');
        $this->load->model('adminbidjob_model');
        $this->load->model('awardjob_model');
        $this->load->model('adminreview_model');
        $this->load->helper('path');
        if(!$this->session->userdata('is_admin')){
            redirect('admin/login');
        }
		//echo '<pre>'; print_r($this->session->userdata);

        date_default_timezone_set('America/New_York');
    }
	public function email_sender(){
			echo  "testing parser";
			$to      = 'john.diegor@gmail.com';
			$subject = 'the subject';
			$message = 'hello111';
			$headers = 'From: info@translatorexchange.com' . "\r\n" .
				'Reply-To:info@translatorexchange.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
			 //  mail("paulo.talingting@gmail.com", 'test', 'Other sent option failed');
			$this->email->set_mailtype("html");
			$this->email->from('info@montesinotranslation.com');
			$this->email->to('john.diegor@gmail.com');
			$this->email->subject('Test Invitation');
			$html_email = "test";
			$this->email->message($html_email);
			$this->email->send();
			 echo $this->email->print_debugger();
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
				$number=mt_rand ( 100 , 999);
				return $str.$number;
			}
    		}

    public function index()
    {
        //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        $search_string = preg_replace('/[^A-Za-z0-9\s\-\:]/', '', $search_string);
        $search_string = trim($search_string);

        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] =10;
        $config['base_url'] = base_url().'admin/joblist';
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


        if ($order_type){
            $filter_session_data['order_type'] = $order_type;
        } else {

            if($this->session->userdata('order_type')){
                $order_type = $this->session->userdata('order_type');
            }else{

                $order_type = 'Asc';
            }
        }

        $data['order_type_selected'] = $order_type;

        if($search_string !='' || $order !='' || $this->uri->segment(3) == true){
            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string =$this->session->userdata('search_string_selected');
				$filter_session_data['search_string_selected'] = $search_string;
            }

            $data['search_string_selected'] = $search_string;

            if ($order) {
                $filter_session_data['order'] = $order;
            } else {
                $order =$this->session->userdata('order');
            }

            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);

            $data['count_jobpost']= $this->adminjobpost_model->count_jobpost($search_string, $order);
            $config['total_rows'] = $data['count_jobpost'];

            //fetch sql data into arrays
            if ($search_string) {
                if ($order) {
                    $data['jobpost'] = $this->adminjobpost_model->get_jobpost($search_string, $order, $order_type, $config['per_page'],$limit_end);
                } else {
                    $data['jobpost'] = $this->adminjobpost_model->get_jobpost($search_string, '', $order_type, $config['per_page'],$limit_end);
                }
            } else {
                if ($order) {
                    $data['jobpost'] = $this->adminjobpost_model->get_jobpost('', $order, $order_type, $config['per_page'],$limit_end);
                } else {
                    $data['jobpost'] = $this->adminjobpost_model->get_jobpost('', '', $order_type, $config['per_page'],$limit_end);
                }
            }

        } else {
            //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
			$filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['count_jobpost']= $this->adminjobpost_model->count_jobpost();
            $data['jobpost'] = $this->adminjobpost_model->get_jobpost('', '', $order_type, $config['per_page'],$limit_end);
            $config['total_rows'] = $data['count_jobpost'];

        }

        $this->pagination->initialize($config);
        $this->load->view('admin/jobpost/vwJoblist',$data);
    }


	public function upload()
		{error_reporting(0);
			if(isset($_FILES["myfile"]))
				{
				$newRet  = "";
				$ret =  array();
				$error =$_FILES["myfile"]["error"];
					if(!is_array($_FILES["myfile"]['name'])) //single file
					{
						$newdir=time();
						$output_dir = "./uploads/jobpost/".$newdir."/";
						$dir = $newdir."/";
							if (!is_dir($output_dir)) {
								mkdir($output_dir);
							}
						$RandomNum   = time();
                        if (!is_dir($output_dir)) {
                            mkdir($output_dir);
                        }
                        $RandomNum = time();

                        if(!preg_match('/[^\x20-\x7f]/',$_FILES['myfile']['name'])) {
                            $ImageName = str_replace(' ', '-', strtolower($_FILES['myfile']['name']));
                            $ImageName = str_replace('/', '-', strtolower($ImageName));
                            $ImageName = str_replace('(', '-', strtolower($ImageName));
                            $ImageName = str_replace(')', '-', strtolower($ImageName));
                            $ImageName = $ImageName;
                        }else{
                            if(strpos($_FILES['myfile']['name'],'.')!= false) {
                                $ext = '.'.end(explode('.', $_FILES['myfile']['name']));
                            }else{
                                $ext = '';
                            }
                            $ImageName = time().$ext;
                        }
						$ImageType      = $_FILES['myfile']['type']; //"image/png", image/jpeg etc.
						$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
						$ImageExt       = str_replace('.','',$ImageExt);
						$ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);

						$NewImageName = $ImageName.'.'.$ImageExt;
						move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir. $NewImageName);
							 $newRet .= $dir.$NewImageName."##";
							 echo $newRet;
					}


			}
		}

    public function update_job_document()
    {
        if ($this->session->userdata('is_admin')) {
            $id   = $this->input->post('id');
            $file = $this->input->post('file');

            $sql = "UPDATE jobpost SET file = CONCAT(file, ?), totalfile = CONCAT(totalfile, ?) WHERE id = ?";
            return $this->db->query($sql, array($file, $file, $id));
        }
    }

	public function add()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";

            //if save button was clicked, get the data sent via post
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                // Storing all file data in session so if user has error on page
//               if($this->input->post('totalFile')){
//                   $this->session->set_userdata('totalFile',$this->input->post('totalFile'));
//                   $all_files = explode('#',$this->input->post('totalFile'));
//                   $session_file = [];
//                   $i = 0;
//                   foreach ($all_files as $all_file){
//                       $all_file_exp = explode('/',$all_file);
//                       if($all_file_exp[1]!= '') {
//                           $session_file['up_images'][$i] = $all_file_exp[1];
//                           $i++;
//                       }
//                   }
//                   $this->session->set_userdata($session_file);
//
//               }
                $this->form_validation->set_rules('name', 'jobpost name', 'required');
                $this->form_validation->set_rules('lineNumber', 'line number', 'trim');
    			$this->form_validation->set_rules('price', 'jobpost price', 'trim|required|numeric');
    			$this->form_validation->set_rules('desc', 'jobpost Description', 'required');
    			$this->form_validation->set_rules('language', 'jobpost language', 'required');
    			$this->form_validation->set_rules('stage', 'jobpost stage', 'required');
                $this->form_validation->set_rules('dueDate', 'due date', 'required');

    		    if ($this->form_validation->run()) {
        			$alias = $this->input->post('alias');
        			$name = $this->input->post('name');

        			if ($alias == '') {
                        $str=$this->UrlAlias ($name, 'jobpost');
                	} else {
                        $str=$this->UrlAlias ($alias,'jobpost');
                	}

                    $line_month = $this->input->post('lineMonth') ? $this->input->post('lineMonth') : $this->input->post('_lineMonth');
                    $line_year = $this->input->post('lineYear') ? $this->input->post('lineYear') : $this->input->post('_lineYear');
                    $line_number = $this->input->post('lineNumber') ? $this->input->post('lineNumber') : $this->input->post('_lineNumber');

                    $line_number_code = 'M'.$line_month.$line_year.'L'.$line_number;
                    // $line_number_code = 'M'.$this->input->post('lineMonth').$this->input->post('lineYear').'L'.$this->input->post('lineNumber');

                    if ($str) {

                        if ($this->input->post('remaining_balance') != '') {
                            $price = $this->input->post('remaining_balance');
                        } else {
                            $price = $this->input->post('price');
                        }

                        // if -1, means this job will have a pending approval in review jobs
                        if ($this->input->post("proofread_required") == 1) {
                            $proofread_required = -1;
                        } else {
                            $proofread_required = 0;
                        }

                        $data_to_store = array(
                            'name' => $this->input->post('name'),
                            'alias'=>$str,
                            'file' =>$this->input->post('totalFile'),
                            'description' => $this->input->post('desc'),
                            'language' => $this->input->post('language_from')."/".$this->input->post('language'),
                            'price' => $this->input->post('price'),
                            'totalFile' => $this->input->post('totalFile'),
                            'status' => 1,
                            'stage' =>$this->input->post('stage'),
                            'job_type' =>$this->input->post('type'),
                            'created' => date('Y-m-d H:i:s'),
                            'dueDate' => (string)($this->input->post('dueDate').' '.$this->input->post('hour').':'.$this->input->post('minute').' '.$this->input->post('ampm')),

                            'lineNumberCode' => $line_number_code,
                            'lineNumber' => $line_number,
                            'lineMonth' => $line_month,
                            'lineYear' => $line_year,

                            'proofread_required' => $proofread_required,
                            'proofreadType' => $this->input->post("proofreadType")
                        );

                        if ($this->adminjobpost_model->store_jobpost($data_to_store)) {
                            $job_id = $this->db->insert_id();
                            $sql1="select * from jobpost where id=$job_id and job_type=0";
                            $val1 = $this->db->query($sql1);
                            $check=$val1->num_rows();

                            if ($check==1) {
                                $rows = $val1->row();

                                $job_name=$rows->name;
                                $job_desc=$rows->description;
                                $job_alias=$rows->alias;

                                $job_language=$rows->language;

                                /*$inIds = "'".str_replace("/", "','", $job_language)."'";
                                $sql_lan="SELECT name FROM `languages` WHERE `id` IN(".$inIds.")";*/
                                $lang= explode("/",$job_language);
                                $sql_lan0="SELECT name FROM `languages` WHERE `id` =  $lang[0]";
                                $val0 = $this->db->query($sql_lan0);
                                $row_lang0 = $val0->result();
                                $from_lang = $row_lang0[0]->name;

                                $sql_lan1="SELECT name FROM `languages` WHERE `id` =  $lang[1]";
                                $val1 = $this->db->query($sql_lan1);
                                $row_lang1 = $val1->result();
                                $to_lang = $row_lang1[0]->name;

                                $lang2=$from_lang.' to '.$to_lang;


                                $lang= $this->input->post('language_from')."/".$this->input->post('language');
                                $sql = "SELECT * FROM translator WHERE language like '%".",".$lang.","."%' ";
                                //echo $sql;die;
                                $val = $this->db->query($sql);
                                $row_email = $val->result();

                                $proofread_required=$this->input->post("proofread_required");
                                $proofreadType=$this->input->post("proofreadType");

                                $data = array(
                                    'job_name' => $job_name,
                                    'job_id'  => $job_id,
                                    'description' => $job_desc,
                                    'proofread_required' => $proofread_required,
                                    'proofreadType' => $proofreadType,
                                    'job_alias' => $job_alias,
                                    'translate_to'=>$lang2,
                                    'due_date' => $this->input->post('dueDate').' '.$this->input->post('hour').':'.$this->input->post('minute').' '.$this->input->post('ampm'),
                                    'created' => date('Y-m-d H:i:s')
                                );

                                foreach ($row_email as $key => $value) {
                                    $mailTo = $value->email_address;
                                    $mailName = $value->first_name;
                                    $mailhash = $value->hash;

                                    $mailId=$value->id;
                                    $data['name'] = $mailName;
                                    $data['hash'] = $mailhash;
                                    $data['id'] = $mailId;

                                    $this->email->set_mailtype("html");
                                    $this->email->from('info@montesinotranslation.com');
                                    $this->email->to($mailTo);
                                    $this->email->subject('Invitation');
                                    $html_email = $this->load->view('email/vwTranslatorSend', $data ,true);
                                    $this->email->message($html_email);
                                    $this->email->send();
                                }

                                $data['message_success'] = "Successfully Job Added";
                            }
                        } else {
                            $data['message_error'] =  "Please try another alias!";
                        }
                    }
                }
            }

            //load the view
            $this->load->view('admin/jobpost/vwAddjobpost',$data);
        } else {
            redirect('admin_jobpost/add');
        }
    }

    public function viewsummary()
    {
        if ($this->session->userdata('is_admin')){
            $data['prompt'] = $this->input->get('prompt');
            $this->load->view('admin/vwSummary',$data);
        }
    }

    public function get_job_price()
    {
        $line_month  = $this->input->get('line_month');
        $line_year   = $this->input->get('line_year');
        $line_number = $this->input->get('line_number');

        $sql = "SELECT * FROM jobpost WHERE lineMonth = {$line_month} AND lineYear = {$line_year} AND lineNumber = {$line_number} ORDER BY created DESC";
        $query = $this->db->query($sql);

        $ids = null;

        if ($query->num_rows()) {

            $job_info = $query->result_array();

            foreach ($job_info as $job) {
                $ids_arr[] = $job['id'];
            }

            $ids = implode(',', $ids_arr);

            // get total expenses for the selected jobpost
            $sql = "SELECT SUM(price) AS price FROM bidjob WHERE awarded = 1 AND job_id IN ({$ids})";
            $bidjob_query = $this->db->query($sql);

            $total_bidjob = $bidjob_query->row();

            $remaining_balance = $job_info[0]['price'] - $total_bidjob->price;

            $data_string = [
                'price' => $remaining_balance,
                'original_price' => $job_info[0]['price'],
                'bid_price' => $total_bidjob->price
            ];

            $data = json_encode($data_string);

        } else {
            $data = null;
        }

        echo $data; exit;
    }

    public function check_line_numbers()
    {
        $line_month  = $this->input->get('line_month');
        $line_year   = $this->input->get('line_year');
        $line_number = $this->input->get('line_number');

        if ($line_number and $line_month and $line_year) {
            $sql = "SELECT * FROM jobpost WHERE lineMonth = {$line_month} AND lineYear = {$line_year} AND lineNumber = {$line_number}";
            $query = $this->db->query($sql);

            if ($query->num_rows()) {
                $check_obj = $query->row();

                $language = explode('/', $check_obj->language);

                $sql = "SELECT `name` FROM languages WHERE id = {$language[0]}";
                $qry_from = $this->db->query($sql);
                $from_lang = $qry_from->row();

                $sql = "SELECT `name` FROM languages WHERE id = {$language[1]}";
                $qry_to = $this->db->query($sql);
                $to_lang = $qry_to->row();

                $data_string = [
                    'job_name'      => ($check_obj->name!='')?$check_obj->name:'Job Manually Entered',
                    'language_from' => $from_lang->name,
                    'language_to'   => $to_lang->name,
                    'price'         => $check_obj->price,
                    'date_added'    => date('jS F Y', strtotime($check_obj->created))
                ];

                $data = json_encode($data_string);
            } else {
                $data = null;
            }
        } else {
            $data = null;
        }


        echo $data; exit;
    }


    public function edit()
	{
        if ($this->session->userdata('is_admin')) {
            $id= $this->uri->segment(3);
            $sql="SELECT * FROM `jobpost` where `id`='$id' ";
            $qry=$this->db->query($sql);

            if ($qry->num_rows()=='1') {
                $data['fetch']=$qry->row();
                //$this->load->view('admin/vwEditjob', $data);
                $this->load->view('admin/vwJobDetails', $data);
            }
        } else {
            $this->session->set_flashdata('error_message', 'Not Permited');
            redirect('admin/index');
        }
	}


	function editprofile() {

		if(!$this->session->userdata('is_admin')) {
			$this->load->view('admin/vwLogin');
		} else {
			$job_id= $this->uri->segment(3);

            // echo '<pre>'; print_r($this->input->post()); exit;

			$this->form_validation->set_rules('job_title', 'Job Title', 'trim|required|xss_clean');
			$this->form_validation->set_rules('job_price', 'Job Price', 'trim|required|numeric|xss_clean');
			$this->form_validation->set_rules('job_description', 'Job Description', 'trim|required|xss_clean');

			if($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('flash_error','Error Validation');
				//redirect('admin_jobpost/edit/'.$job_id);
			} else {
				$number_of_files = sizeof($_FILES['userfile']['tmp_name']);
				$files = $_FILES['userfile'];

                if ($this->input->post('proofread_required') == 1) {
                    $proofread_required = -1;
                } else {
                    $proofread_required = 0;
                }

				if ($this->input->post('totalFile')!= "") {
					 $a=$this->input->post('stage');
					 $alias=$this->input->post('job_alias');
					 $prefile=$this->input->post('prefile');
					 $newfile=$prefile.$this->input->post('totalFile');
					 $str=$this->UrlAlias ($alias,'jobpost',$job_id);

                     $sql = "UPDATE `jobpost` SET
                     `name`   = '".$this->input->post('job_title')."',
                     `description`   = '". $this->input->post('job_description') ."',
                     `job_type`    = '". $this->input->post('type') ."',
                     `language`   = '".$this->input->post('language_from')."/". $this->input->post('job_language') ."',
                     `lineNumber` = ".$this->input->post('lineNumber').",
                     `lineMonth` = '".$this->input->post('lineMonth')."',
                     `lineYear` = '".$this->input->post('lineYear')."',
                     `dueDate` = '".$this->input->post('dueDate').' '.$this->input->post('hour').':'.$this->input->post('minute').' '.$this->input->post('ampm')."',
                     `price`    = '". $this->input->post('job_price') ."',
                     `file`    = '".$newfile."',
                     `stage`    = '". $this->input->post('job_stage') ."',
                     `status`    = '". 1 ."',
                     `proofread_required` = ".$proofread_required.",
                     `proofreadType` = '".$this->input->post('proofreadType')."',
                     `modified`    = '". date('Y-m-d H:i:s') ."'
                     WHERE `id` = '" .$job_id. "'";

                     $path = './uploads/jobpost/'.$this->input->post('prefile');
                     unlink($path);

                     $val = $this->db->query($sql);

					if ($val == TRUE) {
						$sql1="select * from jobpost where id='".$job_id."' and job_type=0";
						$val1 = $this->db->query($sql1);
						$check=$val1->num_rows();

						$this->session->set_flashdata('success_message', 'Successfully Updated');
						redirect('admin_jobpost/edit/'.$job_id);

					} else {
						$this->session->set_flashdata('error_message', 'Not Updated');
						redirect('admin_jobpost/edit/'.$job_id);
					}


				} else {
					$alias = $this->input->post('job_alias');
					$str = $this->UrlAlias($alias,'jobpost',$job_id);

					//if ($this->input->post('prefile') != "")  {
                    $line_number_code = 'M'.$this->input->post('lineMonth').$this->input->post('lineYear').'L'.$this->input->post('lineNumber');

                    $sql = "UPDATE `jobpost` SET
                        `name`   = '".$this->input->post('job_title')."',
                        `description`   = '". $this->input->post('job_description') ."',
                        `language`   = '".$this->input->post('language_from')."/". $this->input->post('job_language') ."',
                        `price`    = '". $this->input->post('job_price') ."',
                        `lineNumberCode` = '".$line_number_code."',
                        `lineNumber` = '".$this->input->post('lineNumber')."',
                        `lineMonth` = '".$this->input->post('lineMonth')."',
                        `lineYear` = '".$this->input->post('lineYear')."',
                        `stage`    = '". $this->input->post('job_stage') ."',
                        `dueDate` = '".$this->input->post('dueDate').' '.$this->input->post('hour').':'.$this->input->post('minute').' '.$this->input->post('ampm')."',
                        `status`    = '". $this->input->post('job_status') ."',
                        `job_type`    = '". $this->input->post('type') ."',
                        `proofread_required` = ".$proofread_required.",
                        `proofreadType` = '".$this->input->post('proofreadType')."',
                        `modified`    = '". date('Y-m-d H:i:s') ."'
                         WHERE `id` = '" .$job_id. "'";

                        $val = $this->db->query($sql);

                    $sql1="select * from jobpost where id='".$job_id."' and job_type=0";

                    $val1 = $this->db->query($sql1);
                    $check=$val1->num_rows();

					    //echo '<pre>'; print_r($sql); die;
				            $this->session->set_flashdata('success_message', 'Successfully Updated');
                    			    redirect('admin_jobpost/edit/'.$job_id);
					//} else {
					//    $this->session->set_flashdata('error_message', 'Not Updated');
                    			//    redirect('admin_jobpost/editprofile/'.$job_id);
					//}

				}

	      }

	        $sql="SELECT * FROM `jobpost` where `id`='$job_id' ";
		$qry=$this->db->query($sql);

		if($qry->num_rows()=='1') {
                    $data['fetch']=$qry->row();
		    $this->load->view('admin/vwEditjob', $data);
		}

		}
	}

	function linkdelete() {
		$id=$this->input->post('id');

		$path = './uploads/jobpost/'.$id;
		unlink($path);
		echo "Remove sucessfully";


		}

	public function removefile()
	{
        if ($this->session->userdata('is_admin')) {
    		$id = $this->uri->segment(3);
    		$old=$this->uri->segment(4);
    		$oldf=$this->uri->segment(5);
    		if($oldf == ''){
    		 $oldfile=$old.'##';
    			}else{
    		 $oldfile=$old.'/'.$oldf.'##';}
    		//echo $oldfile;die;
    		$sql = "SELECT * FROM jobpost WHERE id = " . $id . " ";
    		$val = $this->db->query($sql);
    		$row = $val->row_array();
    		$file = $row['file'];
    		//echo"$file";die;
    		$filename = strstr($file, '/',true);
    		if($oldf == ''){
    		 $old=$old;
    			}else{
    		 $old=$old.'/'.$oldf;}
    		//echo './uploads/jobpost/'.$old; die;
    		$path = './uploads/jobpost/'.$old;
    		unlink($path);
    		//echo $file ;
    		$string = str_replace($oldfile, "", $file);
    		//echo $string;die;
    			$sql1 = "UPDATE `jobpost` SET
    					`file`   = '".$string."'
    					 WHERE `id` = '" .$id. "'";

    		$val1 = $this->db->query($sql1);
    		$this->session->set_flashdata('success_message', 'Successfully Removed');
            redirect('admin_jobpost/editprofile/'.$id);
	   }
	}

    public function remove_document_from_job()
	{
        if ($this->session->userdata('is_admin')) {
    		$id  = $this->input->get('id');
            $ref = $this->input->get('ref');

            $this->db->select('file')->from('jobpost');
            $this->db->where('id', $id);
            $query = $this->db->get();

            if ($query->num_rows()) {
                $row = $query->row_array();
                $original_file = $row['file'];

                if (strstr($original_file, $ref)) {
                    $new_file = str_replace($ref, '', $original_file);
                    $new_file = trim($new_file, '##');
                    $new_file = "{$new_file}##";

                    $this->db->update('jobpost', array('file' => $new_file), array('id' => $id));

                    $ref = trim($ref, '##');
                    unlink('./uploads/jobpost/'.$ref);

                    echo json_encode(array('status' => true));
                }
            }
        }
	}

	 public function delete()
    {
	if($this->session->userdata('is_admin')){
       $id = $this->uri->segment(3);
	   //echo $id;die;
		$sql = "SELECT * FROM jobpost WHERE id = " . $id . " ";
		$val = $this->db->query($sql);
		$row = $val->row_array();
		$file = $row['file'];

		$filename=strstr($file, '/',true);
		//echo $filename ;die;
		$path = './uploads/jobpost/'.$filename;
		unlink($path);
		$this->adminjobpost_model->delete_jobpost($id);
		$this->session->set_flashdata('success_message', 'Successfully Deleted');
        redirect('admin/joblist');
      } else {
		$this->session->set_flashdata('error_message', ' Not Deleted');
        redirect('admin/joblist');
      }

    }//delete


    public function deleteall()
    {
        $id = $this->input->post('id');

        $sql1= "SELECT * FROM jobpost WHERE id IN (".$id.")";
        $val1 = $this->db->query($sql1);
        $result = $val1->result();

        foreach($result as $row){
            $file = $row->file;
            $filename=explode("##", $file);
            array_pop($filename);
            $count=count($filename);

            for($i=0;$i<=$count-1;$i++){
                $path[$i] = './uploads/jobpost/'.$filename[$i];
                unlink($path[$i]);
            }
        }

        $sql ="DELETE FROM jobpost WHERE id IN (".$id.")";
        $val = $this->db->query($sql);

        if ($val){
            $sql2 ="DELETE FROM bidjob WHERE job_id IN (".$id.")";
            $this->db->query($sql2);

            $sql3 ="DELETE FROM invoice WHERE job_id IN (".$id.")";
            $this->db->query($sql3);

            $sql4 ="DELETE FROM ajax_chat_messages WHERE job_id IN (".$id.")";
            $this->db->query($sql4);

            $sql3 ="DELETE FROM send_invitation WHERE job_id IN (".$id.")";
            $this->db->query($sql3);

            echo "delete successfully";
        }
    }

   public function bid_job()
   {

        $job_id= $this->uri->segment(2);
        $search_string = $this->input->post('search_string');
        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] =10;
        $config['base_url'] = base_url().'bidjob/'.$job_id.'/';
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

                $order_type = 'Asc';
            }
        }

        $data['order_type_selected'] = $order_type;

        if($search_string !='' || $order !='' || $this->uri->segment(3) == true){

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



            $data['count_bidjob']= $this->adminbidjob_model->count_bidjob($search_string, $order);
            $config['total_rows'] = $data['count_bidjob'];

            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['bidjob'] = $this->adminbidjob_model->get_bidjob($search_string, $order, $order_type, $config['per_page'],$limit_end);
                }else{
                    $data['bidjob'] = $this->adminbidjob_model->get_bidjob($search_string, '', $order_type, $config['per_page'],$limit_end);
                }
            }else{
                if($order){
                    $data['bidjob'] = $this->adminbidjob_model->get_bidjob('', $order, $order_type, $config['per_page'],$limit_end);
                }else{
                    $data['bidjob'] = $this->adminbidjob_model->get_bidjob('', '', $order_type, $config['per_page'],$limit_end);
                }
            }

        }else{

            //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
			$filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays

            $data['count_bidjob']= $this->adminbidjob_model->count_bidjob();
            $data['bidjob'] = $this->adminbidjob_model->get_bidjob('', '', $order_type, $config['per_page'],$limit_end);
            $config['total_rows'] = $data['count_bidjob'];

          }
        $this->pagination->initialize($config);
	    $this->load->view('admin/jobpost/vwBidjob',$data);

 }


    public function bidjobedit()
	{
	if($this->session->userdata('is_admin')){
        $id= $this->uri->segment(3);
		//echo $id;die;
		$sql="SELECT * FROM `bidjob` where `id`='$id' ";
		$qry=$this->db->query($sql);
		if($qry->num_rows()=='1')
		{
        $data['fetch']=$qry->row();
		$this->load->view('admin/vwEditbidjob', $data);
		}

      } else {
        $this->session->set_flashdata('error_message', 'Not Permited');
        redirect('admin/index');
      }
	}


	function editbidjobprofile() {


		if(!$this->session->userdata('is_admin')){
			$this->load->view('admin/vwLogin');
		} else {
			$id= $this->uri->segment(3);
			//echo $job_id;die;
			$this->form_validation->set_rules('time_need', 'Time Needed', 'trim|required');

			if($this->form_validation->run() == FALSE) {
				//$this->session->set_flashdata('flash_error','Error Validation');
				//redirect('admin_jobpost/edit/'.$job_id);
			} else {

				    if($_FILES['userfile']['size'] != 0){
					$upload_dir = './uploads/bidjobpost/';
					if (!is_dir($upload_dir)) {
						 mkdir($upload_dir);
					}
					if(!preg_match('/[^\x20-\x7f]/',$_FILES['userfile']['name'])){
                        $config['file_name'] = $_FILES['userfile']['name'];
                    }else{
					    $config['file_name'] =time();
                    }
					$config['upload_path']   = $upload_dir;
					$config['allowed_types'] = 'jpeg|jpg|png|doc|docx|txt|pdf|xls|zip';
					$config['overwrite']     = false;
					$config['max_size']	 = '20000';

					$this->load->library('upload', $config);
					if (!$this->upload->do_upload('userfile')){
						//$data = array('message_error' => $this->upload->display_errors());
					} else {
						$upload_data = $this->upload->data();
						$filename = $upload_data['file_name'];
					$stage=$this->input->post('stage');
					if($stage==1){
					 $sql = "UPDATE `bidjob` SET
					`time_need` = '".$this->input->post('time_need')."',
				    `file`    = '".$filename."',
					`stage` = '".$this->input->post('stage')."',
					`working_date` ='".date('Y-m-d H:i:s')."'
					 WHERE `id` = '" .$id. "'";
					}else{
						$sql = "UPDATE `bidjob` SET
					`time_need` = '".$this->input->post('time_need')."',
				    `file`    = '".$filename."',
					`stage` = '".$this->input->post('stage')."',
					`completed_date` ='".date('Y-m-d H:i:s')."'
					 WHERE `id` = '" .$id. "'";
						}
					 //delete previous file
					$path = './uploads/bidjobpost/'.$this->input->post('prefile');
					unlink($path);

				$val = $this->db->query($sql);

                if($val == TRUE){
                     $this->session->set_flashdata('success_message', 'Successfully Updated');
                    redirect('admin_jobpost/bidjobedit/'.$id);
                }else{
                   $this->session->set_flashdata('error_message', 'Not Updated');
                    redirect('admin_jobpost/bidjobedit/'.$id);
                }

				}



				}
				else {

					if($this->input->post('prefile') != "")  {
					$stage=$this->input->post('stage');
					if($stage==1){
					  $sql = "UPDATE `bidjob` SET
					`time_need` = '".$this->input->post('time_need')."',
					`stage` = '".$this->input->post('stage')."',
					`working_date` ='".date('Y-m-d H:i:s')."'
				     WHERE `id` = '" .$id. "'";
					 }
					 else{
						 $sql = "UPDATE `bidjob` SET
					`time_need` = '".$this->input->post('time_need')."',
					`stage` = '".$this->input->post('stage')."',
					`complete_date` ='".date('Y-m-d H:i:s')."'
					 WHERE `id` = '" .$id. "'";
						 }

				    $val = $this->db->query($sql);
					//echo '<pre>'; print_r($sql); die;
					$this->session->set_flashdata('success_message', 'Successfully Updated');
                    redirect('admin_jobpost/bidjobedit/'.$id);
					} else {
					$this->session->set_flashdata('error_message', 'Not Updated');
                    redirect('admin_jobpost/bidjobedit/'.$id);
					}

				}

	      }
	    $sql="SELECT * FROM `bidjob` where `id`='$id' ";
		$qry=$this->db->query($sql);
		if($qry->num_rows()=='1')
		{
        $data['fetch']=$qry->row();
		$this->load->view('admin/vwEditbidjob', $data);
		}
		}
	}

    public function bidjobdelete()
    {
        $job_id = $this->uri->segment(3);
        $id = $this->uri->segment(4);

        if ($this->session->userdata('is_admin')) {
            $sql = "SELECT * FROM bidjob WHERE `id` = " . $id . " ";
            $val = $this->db->query($sql);
            $row = $val->row_array();
            $file = $row['file'];
            $path = './uploads/bidjobpost/'.$file;

            if ($path) {
                unlink($path);
            }

            $this->adminbidjob_model->delete_bidjob($id);
            $this->session->set_flashdata('success_message', 'Successfully Deleted');
            redirect('bidjob/'.$job_id);
        } else {
            $this->session->set_flashdata('error_message', ' Not Deleted');
            redirect('bidjob/'.$job_id);
        }
    }

	public function awardupdate()
    {
        if ($this->session->userdata('is_admin') && $this->session->userdata('admin_id') != false) {
            $admin_id = $this->session->userdata('admin_id');
            $data['message_error'] = "";
    		$data['message_success'] = "";

            $id = $this->uri->segment(3);
    		$job_id = $this->uri->segment(4);

    		if ($id!='') {
                $date = date('Y-m-d H:i:s');

    			$val = $this->db->update('bidjob',
                    array(
                        'awarded' => 1,
                        'award_date' => $date,
                        'stage' => 1,
                        'working_date' => $date,
                        'show_notification' => 1,
                        'awarded_admin_id' => $admin_id
                    ),
                    array(
                        'id' => $id
                    )
                );

                $this->db->update('jobpost', array('modified' => date('Y-m-d H:i:s')), array('id' => $job_id));

    			if ($val) {
        		    $transql="select `trans_id` from `bidjob` where `id`='$id'";
        			$tranval=$this->db->query($transql);
        			$tranfetch=$tranval->row();
        			$trans_id=$tranfetch->trans_id;

        			$jobsql="select * from `jobpost` where `id`='$job_id'";
        			$jobval=$this->db->query($jobsql);
        			$jobfetch=$jobval->row();
        			$job_name=$jobfetch->name;
        			$job_description=$jobfetch->description;
        			$job_created=$jobfetch->created;
        			$job_alias=$jobfetch->alias;
                    $job_stage=$jobfetch->stage;

        			$emailsql="select * from `translator` where `id`='$trans_id'";
        			$emailval=$this->db->query($emailsql);
        			$emailfetch=$emailval->row();
        			$trans_email=$emailfetch->email_address;
        			$trans_name=$emailfetch->first_name.'&nbsp;'.$emailfetch->last_name;

        			/* Update by JBP */
        			$jobInfo = $this->awardjob_model->getJobInfo($job_id);

        			$messageInfo = array(
        				'translatorID' => $trans_id,
        				'message' => 'Congratulations! you have been awarded to this Job:<a href = "'.base_url().'job/'.$jobInfo->alias.'">'.$jobInfo->name."</a>.",
        				'created' => date("Y-m-d H:i:s")
        			);

        			$data['name'] = $trans_name;
        			$data['job_id'] = $job_id;
        			$data['bid_id'] = $id;
        			$data['trans_id'] = $trans_id;
        			$data['job_name'] =$job_name;
        			$data['job_description'] =$job_description;
        			$data['job_created'] =$job_created;
        			$data['job_alias'] =$job_alias;

        			$mailTo =$trans_email;
        			$mailName =$trans_name;
        			$this->email->set_mailtype("html");
        			$this->email->from('info@montesinotranslation.com');
        			$this->email->to($mailTo);
        			$this->email->subject('Award Job Confirmation');
        			$html_email = $this->load->view('email/vwTranslatorAwardConfirmation', $data ,true);
        			$this->email->message($html_email);
        			$this->email->send();

                    $post_to_chat_box = [
                        'bid_id' => $id,
                        'job_id' => $jobfetch->id,
                        'trans_id' => $trans_id,
                        'type' => 'admin',
                        'status' => 'unread',
                        'jobname' => $job_name,
                        'userID' => 1,
                        'userName' => 'Guest',
                        'channel' => 1,
                        'dateTime' => date('Y-m-d H:i:s'),
                        'text' => 'You have been awarded this job, please coordinate with the admin to proceed',
                        'ip' => '127.0.0.1'
                    ];

                    $this->db->insert('ajax_chat_messages', $post_to_chat_box);

        			$this->session->set_flashdata('success_message', 'Successfully Awarded');
                    if ($job_stage == 0) {
                        $referrer = $this->agent->referrer().'?prompt=1';
                    } else {
                        $referrer = $this->agent->referrer();
                    }

        		    redirect($referrer);
    			}
    		} else {
                $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
                $referrer=$this->agent->referrer();
                redirect($referrer);
    		}
        } else {
          redirect('admin/index');
        }

    }//update

    public function unaward_check_if_invoiced()
    {
        if ($this->session->userdata('is_admin')) {
            $job_id   = $this->input->get('job_id');
            $bid_id   = $this->input->get('bidjob_id');
            $trans_id = $this->input->get('trans_id');

            $invoice = $this->db->from('invoice')->where(array('bid_id' => $bid_id, 'job_id' => $job_id, 'is_deleted' => 0))->get();

            if ($invoice->num_rows()) {
                $translator = $this->db->from('translator')->where('id', $trans_id)->get();
                $bidjob = $this->db->from('bidjob')->where('id', $bid_id)->get();

                if ($translator->num_rows()) {
                    $data['data']['translator_name'] = $translator->row()->first_name.' '.$translator->row()->last_name;
                }

                if ($bidjob->num_rows()) {
                    $data['data']['invoice_amount'] = $bidjob->row()->price;
                }

                $data['data']['is_invoiced'] = true;
            } else {
                $data['data']['is_invoiced'] = false;
            }

            $data['success'] = true;

        } else {
            $data['success'] = false;
            $data['message'] = "You're not authorized in this page";
        }

        $data['redirect'] = base_url('admin_jobpost/viewsummary/' . $job_id);

        echo json_encode($data); exit;
    }



	public function awardcupdate()
    {
    	if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
    		$data['message_success'] = "";

            $form     = $this->input->get('form');
            $id       = $this->uri->segment(3);
    		$job_id   = $this->uri->segment(4);
            $trans_id = $this->input->get('trans_id');

    		if ($id != '') {
    			$val = $this->db->update('bidjob',
                    array(
                        'awarded' => 0,
                        'stage' => 1,
                        'is_done' => 0,
                        'is_rated' => 0,
                        'show_notification' => 0,
                        'complete_date' => '0000-00-00 00:00:00',
                    ),
                    array(
                        'id' => $id
                    )
                );

    			$this->db->update('jobpost', array('modified' => date('Y-m-d H:i:s')), array('id' => $job_id));

    			if ($val) {
    			    $transql   = "select `trans_id` from `bidjob` where `id`='$id'";
    				$tranval   = $this->db->query($transql);
    				$tranfetch = $tranval->row();
    				$trans_id  = $tranfetch->trans_id;

                    $this->db->where(array('job_id' => $job_id, 'bidjob_id' => $id))->delete('ratings');
                    $this->db->update('invoice', array('is_deleted' => 1), array('job_id' => $job_id, 'bid_id' => $id));

                    $jobpost = $this->db->from('jobpost')->where('id', $job_id)->get();

                    if (!empty($form)) {
                        parse_str($form, $message);

                        $message_str = "{$jobpost->row()->name} cancelled:<br/>Reason: {$message['message']}<br/>Please contact admin for any questions.";

                        $message_info = array(
        					'translatorID' => $trans_id,
        					'message' => $message_str,
        					'created' => date("Y-m-d H:i:s")
        				);

        				$this->db->insert('notifications', $message_info);
                    }

                    // check if job has a review job
                    $has_review_job = $this->db->from('jobpost')->where('parent_id', $jobpost->row()->id)->get();

                    if ($has_review_job->num_rows()) {
                        $review_job_name = $has_review_job->row()->name;
                        $review_line_no  = $has_review_job->row()->lineNumberCode;
                        $hiring_status   = ($has_review_job->row()->status == 0) ? 'Closed' : 'Open';

                        // check if anything has been awarded
                        $check_awarded = $this->db->from('proofread_jobs')
                            ->join('proofread_jobs_docs', 'proofread_jobs_docs.proofread_job_id = proofread_jobs.id')
                            ->where('job_id', $has_review_job->row()->id)
                            ->where('is_awarded', 1)
                            ->get();

                        $data['review_job']['job_name']      = $review_job_name;
                        $data['review_job']['line_number']   = $review_line_no;
                        $data['review_job']['hiring_status'] = $hiring_status;

                        if ($check_awarded->num_rows()) {
                            $data['review_job']['has_awarded'] = 'Yes';
                        } else {
                            $data['review_job']['has_awarded'] = 'No';
                        }
                    }
    			}

    			$data['url'] = base_url('admin_jobpost/viewsummary/' . $job_id);

    			echo json_encode($data);
    		}

            exit;
        }

    }

	public function cancelupdate()
    {

	if($this->session->userdata('is_admin')){
      $data['message_error'] = "";
		$data['message_success'] = "";
        //artist id
        $id = $this->uri->segment(3);
		$job_id = $this->uri->segment(4);
		//echo 'testcanupdate'.$id;die;

		if($id!='')
		{   $date=date('Y-m-d H:i:s');
			$sql = "UPDATE `bidjob` SET `canceled` = '1',`cancel_date`='$date' WHERE `id` = '" . $id . "'";
			$val = $this->db->query($sql);
			$this->session->set_flashdata('success_message', 'Successfully Canceled');
			$referrer=$this->agent->referrer();
		    redirect($referrer);
		}
		else
		{
		   $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
			$referrer=$this->agent->referrer();
		    redirect($referrer);
		}
      } else {
        redirect('admin/index');
      }




    }//update

	public function cancelcupdate()
    {

	if($this->session->userdata('is_admin')){
      $data['message_error'] = "";
		$data['message_success'] = "";
        //artist id
        $id = $this->uri->segment(3);
		$job_id = $this->uri->segment(4);
		//echo 'testcancupdate'.$id;die;
		if($id!='')
		{
			$sql = "UPDATE `bidjob` SET `canceled` = '0' WHERE `id` = '" . $id . "'";
			$val = $this->db->query($sql);
			$this->session->set_flashdata('success_message', 'Successfully Uncanceled');
			$referrer=$this->agent->referrer();
		    redirect($referrer);
		}
		else
		{
		    $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
			$referrer=$this->agent->referrer();
		    redirect($referrer);
		}
      } else {
        redirect('admin/index');
      }



    }
	 public function invoice()
   {
	$data['id'] = $this->uri->segment(3);
	$data['job_id'] = $this->uri->segment(4);
	$data['trans_id'] = $this->uri->segment(5);
	$this->load->view('admin/vwAwardInvoice',$data);

   }

   public function send_invoice()
   {
            $bid_id = $this->input->post('bid_id');
	        $job_id = $this->input->post('job_id');
	        $trans_id = $this->input->post('trans_id');
			$comp_time = $this->input->post('comp_time');
			$invoice_id=$this->input->post('invoice_id');
			$invoice_desc=$this->input->post('invoice_desc');
			$job_title=$this->input->post('job_title');
			$job_price=$this->input->post('job_price');
			$award_date=$this->input->post('award_date');

	        //echo'test';die;
	        $jobsql="select * from `jobpost` where `id`='$job_id'";
			$jobval=$this->db->query($jobsql);
			$jobfetch=$jobval->row();
			$job_name=$jobfetch->name;
			$job_description=$jobfetch->description;
			$job_created=$jobfetch->created;
			$job_alias=$jobfetch->alias;
			$emailsql="select * from `translator` where `id`='$trans_id'";
			$emailval=$this->db->query($emailsql);
			$emailfetch=$emailval->row();
			$trans_email=$emailfetch->email_address;
			$trans_name=$emailfetch->first_name.'&nbsp;'.$emailfetch->last_name;


			//echo $trans_name;die;


			$data['invoice_id'] = $invoice_id;
			$data['name'] = $trans_name;
			$data['job_title'] =$job_title;
			$data['invoice_desc'] =$invoice_desc;
			$data['award_date'] =$award_date;
			$data['job_alias'] =$job_alias;
			$data['job_price'] =$job_price;
			$data['comp_time'] =$comp_time;

			$mailTo =$trans_email;
			$mailName =$trans_name;
			$this->email->set_mailtype("html");
			$this->email->from('info@montesinotranslation.com');
			$this->email->to($mailTo);
			$this->email->subject('Awarded Job Invoice');
			$html_email = $this->load->view('email/vwAwardedJobInvoice', $data ,true);
			$this->email->message($html_email);
			$mail=$this->email->send();
			if($mail){

			        $data_to_store = array(
					'bid_id' =>$bid_id,
                    'invoice_id' =>$invoice_id,
					'job_id'=>$job_id,
					'trans_id' => $trans_id,
                    'description' =>$invoice_desc,
					'created' => date('Y-m-d H:i:s')
                );
				//echo '<pre>'; print_r($data_to_store);die;
                //if the insert has returned true then we show the flash message
                if($this->adminjobpost_model->store_invoice($data_to_store))
				{
				$this->session->set_flashdata('success_message', 'Successfully Send  Awarded Invoice');
			    redirect('bidjob/'.$job_id);
				}
			}

	}

	public function reinvoice()
   {
	$data['id'] = $this->uri->segment(3);
	$data['job_id'] = $this->uri->segment(4);
	$data['trans_id'] = $this->uri->segment(5);
	$this->load->view('admin/vwReAwardInvoice',$data);

   }

   public function resend_invoice()
   {
            $bid_id = $this->input->post('bid_id');
	        $job_id = $this->input->post('job_id');
	        $trans_id = $this->input->post('trans_id');
			$comp_time = $this->input->post('comp_time');
			$invoice_id=$this->input->post('invoice_id');
			$invoice_desc=$this->input->post('invoice_desc');
			$job_title=$this->input->post('job_title');
			$job_price=$this->input->post('job_price');
			$award_date=$this->input->post('award_date');

	        //echo'test';die;
	        $jobsql="select * from `jobpost` where `id`='$job_id'";
			$jobval=$this->db->query($jobsql);
			$jobfetch=$jobval->row();
			$job_name=$jobfetch->name;
			$job_description=$jobfetch->description;
			$job_created=$jobfetch->created;
			$job_alias=$jobfetch->alias;

			$emailsql="select * from `translator` where `id`='$trans_id'";
			$emailval=$this->db->query($emailsql);
			$emailfetch=$emailval->row();
			$trans_email=$emailfetch->email_address;
			$trans_name=$emailfetch->first_name.'&nbsp;'.$emailfetch->last_name;


			$bidsql="select * from `bidjob` where `id`='$bid_id'";
			$bidval=$this->db->query($bidsql);
			$bidfetch=$bidval->row();
			$complete_date=$bidfetch->complete_date;




			//echo $trans_name;die;


			$data['invoice_id'] = $invoice_id;
			$data['name'] = $trans_name;
			$data['job_title'] =$job_title;
			$data['invoice_desc'] =$invoice_desc;
			$data['award_date'] =$award_date;
			$data['complete_date'] =$complete_date;
			$data['job_alias'] =$job_alias;
			$data['job_price'] =$job_price;
			$data['comp_time'] =$comp_time;

			$mailTo =$trans_email;
			$mailName =$trans_name;
			$this->email->set_mailtype("html");
			$this->email->from('info@montesinotranslation.com');
			$this->email->to($mailTo);
			$this->email->subject('Job Completion Invoice');
			$html_email = $this->load->view('email/vwJobCompletionReInvoice', $data ,true);
			$this->email->message($html_email);
			$mail=$this->email->send();
			if($mail){

			        $data_to_store = array(
					'bid_id' =>$bid_id,
                    'invoice_id' =>$invoice_id,
					'job_id'=>$job_id,
					'trans_id' => $trans_id,
                    'description' =>$invoice_desc,
					'modified' => date('m-d-Y H:i:s')
                );
				//echo '<pre>'; print_r($data_to_store);die;
                //if the insert has returned true then we show the flash message
                if($this->adminjobpost_model->store_reinvoice($data_to_store,$bid_id))
				{
				$this->session->set_flashdata('success_message', 'Successfully resent invoice');
			    //redirect('bidjob/'.$job_id);
				$referrer=$this->agent->referrer();
		        redirect($referrer);
				}
			}

	}

		public function viewbid()
   {
	   if($this->session->userdata('is_admin')){
	$data['job_id'] = $this->uri->segment(3);
	$this->load->view('translator/vwdetailsbidjob',$data);
	}

   }
		public function viewbiddetails()
   {
	   if($this->session->userdata('is_admin')){
	 $data['bid_id'] = $this->uri->segment(3);
	$this->load->view('admin/vwdetailsbid',$data);
	}

   }

	public function message()
   {
	$data['id'] = $this->uri->segment(3);
	$data['job_id'] = $this->uri->segment(4);
	$data['trans_id'] = $this->uri->segment(5);
	$this->load->view('admin/vwMessage',$data);

   }

   public function send_message()
   {
            $bid_id = $this->input->post('bid_id');
	        $job_id = $this->input->post('job_id');
	        $trans_id = $this->input->post('trans_id');
			//$comp_time = $this->input->post('comp_time');
			//$invoice_id=$this->input->post('invoice_id');
			$message=$this->input->post('message');
			$job_title=$this->input->post('job_title');
			$job_price=$this->input->post('job_price');
			$award_date=$this->input->post('award_date');

	        //echo'test';die;
	        $jobsql="select * from `jobpost` where `id`='$job_id'";
			$jobval=$this->db->query($jobsql);
			$jobfetch=$jobval->row();
			$job_name=$jobfetch->name;
			$job_description=$jobfetch->description;
			$job_created=$jobfetch->created;
			$job_alias=$jobfetch->alias;

			$emailsql="select * from `translator` where `id`='$trans_id'";
			$emailval=$this->db->query($emailsql);
			$emailfetch=$emailval->row();
			$trans_email=$emailfetch->email_address;
			$trans_name=$emailfetch->first_name.'&nbsp;'.$emailfetch->last_name;
			$upload_dir = './uploads/message';
				if (!is_dir($upload_dir)) {
					 mkdir($upload_dir);
				}
				$config['upload_path']   = $upload_dir;
				$config['allowed_types'] = 'jpeg|jpg|png|doc|docx|txt|pdf|xls|zip';
       if(!preg_match('/[^\x20-\x7f]/',$_FILES['file']['name'])){
           $config['file_name'] = $_FILES['file']['name'];
       }else{
           $config['file_name'] =time();
       }
				$config['overwrite']     = false;
				$config['max_size']	 = '20000';
				$this->load->library('upload', $config);
				$this->upload->do_upload('file');
				$upload_data = $this->upload->data();
				$filename = $upload_data['file_name'];
				$image_config["image_library"] = "GD2";
				$image_config["source_image"] = $upload_data["full_path"];
				$image_config['create_thumb'] = FALSE;
				$image_config['maintain_ratio'] = TRUE;

			//echo $trans_name;die;
		/*	 $data_to_store = array(
					'bid_id' =>$bid_id,
					'job_id'=>$job_id,
					'trans_id' => $trans_id,
                    'text' =>$message,
					'file'=>$filename,
					'created' => date('Y-m-d h:i:s')
                );
		    $this->adminjobpost_model->store_message($data_to_store);die;*/

			$data['name'] = $trans_name;
			$data['job_title'] =$job_title;
			$data['message'] =$message;
			$data['award_date'] =$award_date;
			$data['job_alias'] =$job_alias;
			$data['job_price'] =$job_price;
			$data['file'] =$filename;
			//$data['comp_time'] =$comp_time;

			//$this->adminjobpost_model->store_message($data); die;

			$mailTo =$trans_email;
			$mailName =$trans_name;
			$this->email->set_mailtype("html");
			$this->email->from('info@montesinotranslation.com');
			$this->email->to($mailTo);
			$this->email->subject('Message');
			$html_email = $this->load->view('email/vwMessageMail', $data ,true);
			$this->email->message($html_email);
			if($_FILES['file']['name']!=''){
			$path = set_realpath('uploads/message');
			$this->email->attach($path . $filename);
			}
			$mail=$this->email->send();
			if($mail){
			        $data_to_store = array(
					'bid_id' =>$bid_id,
					'job_id'=>$job_id,
					'trans_id' => $trans_id,
					'type'=>'0',
                    'text' =>$message,
					'file'=>$filename,
					'created' => date('Y-m-d H:i:s')
                );
				//echo '<pre>'; print_r($data_to_store);die;
                //if the insert has returned true then we show the flash message
                if($this->adminjobpost_model->store_message($data_to_store))
				{
				$this->session->set_flashdata('success_message', 'Successfully Send  Message');
			    redirect('bidjob/'.$job_id);
				}
			}

	}



	public function jobupdate()
	{


	if($this->session->userdata('is_admin')){


		    $job_id = $this->uri->segment(3);

		    $sql1= "UPDATE `jobpost` SET `stage` = '1' WHERE `id` = '" . $job_id . "'";
			$val1 = $this->db->query($sql1);
			if($val1)
			{
		    $this->session->set_flashdata('success_message', 'This job was mark as Completed');
			$referrer=$this->agent->referrer();
		    redirect($referrer);
			}

      } else {
        redirect('admin/index');
      }
    }
		public function jobcupdate()
	{


	if($this->session->userdata('is_admin')){


		$job_id = $this->uri->segment(3);

		    $sql1= "UPDATE `jobpost` SET `stage` = '2' WHERE `id` = '" . $job_id . "'";
			$val1 = $this->db->query($sql1);
			if($val1)
			{
		    $this->session->set_flashdata('success_message', 'This job was mark as Working');
			$referrer=$this->agent->referrer();
		    redirect($referrer);
			}

      } else {
        redirect('admin/index');
      }
    }

	public function pending()
    {
		 //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        $search_string = preg_replace('/[^A-Za-z0-9\s\-\:]/', '', $search_string);
        $search_string = trim($search_string);
        
        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] = 10;
        $config['base_url'] = base_url().'admin/pendingjobpost';
        $config['use_page_numbers'] = TRUE;

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

                $order_type = 'Asc';
            }
        }

        $data['order_type_selected'] = $order_type;

        if($search_string !='' || $order !='' || $this->uri->segment(3) == true){

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



            $data['count_jobpost']= $this->adminjobpost_model->count_pendingjobpost($search_string, $order, 0);
            $config['total_rows'] = $data['count_jobpost'];

            $config['num_links'] = floor($config['total_rows']/$config['per_page']);
            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['jobpost'] = $this->adminjobpost_model->get_pendingjobpost($search_string, $order, $order_type, $config['per_page'],$limit_end, 0);
                }else{
                    $data['jobpost'] = $this->adminjobpost_model->get_pendingjobpost($search_string, '', $order_type, $config['per_page'],$limit_end, 0);
                }
            }else{
                if($order){
                    $data['jobpost'] = $this->adminjobpost_model->get_pendingjobpost('', $order, $order_type, $config['per_page'],$limit_end, 0);
                }else{
                    $data['jobpost'] = $this->adminjobpost_model->get_pendingjobpost('', '', $order_type, $config['per_page'],$limit_end, 0);
                }
            }

        }else{
            //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
			$filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays

            $data['count_jobpost']= $this->adminjobpost_model->count_pendingjobpost('', '', 0);
            $data['jobpost'] = $this->adminjobpost_model->get_pendingjobpost('', '', $order_type, $config['per_page'],$limit_end, 0);
            $config['total_rows'] = $data['count_jobpost'];
            $config['num_links'] = floor($config['total_rows']/$config['per_page']);
        }
        $this->pagination->initialize($config);

		$this->load->view('admin/jobpost/pendingJobList',$data);
	}

    public function viewer()
    {
        if ($this->session->userdata('is_admin')) {

            $job_id   = (int) $this->uri->segment(5);
            $document = base64_decode($this->uri->segment(6));
            $data     = null;

            $this->load->helper('file');

            $document_obj = $this->db->select('file')->from('jobpost')->where('id', $job_id)->get();

            if ($document_obj->num_rows()) {

                $file_arr = explode('##', $document_obj->row()->file);

                foreach ($file_arr as $key => $file) {
                    if ($document == $file) {
                        $file_path = "./uploads/jobpost/{$file}";
                        $file_info = get_file_info($file_path);
                        $file_type = get_mime_by_extension($file_path);

                        $data['documents'][$key]['document']  = 'jobpost/'.$file;
                        $data['documents'][$key]['file_info'] = $file_info;
                        $data['documents'][$key]['file_type'] = $file_type;

                        break;
                    }
                }
            }

            $this->load->view('admin/jobpost/vwAdminJobDocumentViewer', $data);

        } else {
            redirect('admin/index');
        }
    }

	public function pendingEditApproval($job_id)
    {
		$data['message_error'] = "";
		$data['message_success'] = "";

		if (!$this->session->userdata('is_admin')) {
			$this->load->view('admin/vwLogin');
		} else {
			if($this->input->post("name")) {
				$sql="SELECT * FROM `jobpost` where `id`='$job_id' ";
				$qry=$this->db->query($sql);
				$data['fetch']=$qry->row();

				$this->form_validation->set_rules('name', 'jobpost Job Name', 'required');
				$this->form_validation->set_rules('clientName', 'jobpost Client Name', 'required');
				$this->form_validation->set_rules('price', 'jobpost Price', 'trim|required|numeric');
				$this->form_validation->set_rules('desc', 'jobpost Description', 'required');
				$this->form_validation->set_rules('language', 'jobpost Language', 'required');
				$this->form_validation->set_rules('lineNumber', 'Line Number', 'trim|numeric|required');

			    if ($this->form_validation->run() == FALSE) {
					$this->load->view('admin/jobpost/vwPostJobForApproval', $data);
				} else {
					$number_of_files = sizeof($_FILES['userfile']['tmp_name']);
					$files = $_FILES['userfile'];

                    $name = $this->input->post('name');
                    $prefile = $this->input->post('prefile');
                    $newfile = $prefile.$this->input->post('totalFile');
                    $str = $this->UrlAlias($name,'jobpost',$job_id);

                    if ($str) {
                        $proofread_required = 0;
                        $proofreadType = "";

                        if ($this->input->post('proofread_required') && !is_null($this->input->post('proofread_required'))) {
                            $proofread_required = $this->input->post("proofread_required");
                        }

                        if ($proofread_required) {
                            $proofread_required = -1;
                        }

                        if($this->input->post('proofreadType') && !is_null($this->input->post('proofreadType'))) {
                            $proofreadType = $this->input->post("proofreadType");
                        }

                        $today = date('Y-m-d H:i:s');

                        $val = $this->db->update('jobpost',
                            array(
                                'name' => $this->input->post('name'),
                                'clientName' => $this->input->post('clientName'),
                                'description' => $this->input->post('desc'),
                                'job_type' => $this->input->post('type'),
                                'language' => $this->input->post('language_from')."/". $this->input->post('language'),
                                'price' => $this->input->post('price'),
                                'alias' => $str,
                                'file' => $newfile,
                                'stage' => 0,
                                'status' => 1,
                                'approval_status' => 1,
                                'modified' => $today,
                                'date_posted' => $today,
                                'dueDate' => $this->input->post('dueDate').' '.$this->input->post('hour').':'.$this->input->post('minute').' '.$this->input->post('ampm'),
                                'lineNumberCode' => 'M'.$this->input->post('lineMonth').$this->input->post('lineYear')."L".$this->input->post('lineNumber'),
                                'lineNumber' => $this->input->post('lineNumber'),
                                'lineMonth' => $this->input->post('lineMonth'),
                                'lineYear' => $this->input->post('lineYear'),
                                'proofread_required' => $proofread_required,
                                'proofreadType' => $proofreadType
                            ),
                            array(
                                'id' => $job_id
                            )
                        );

                        $path = './uploads/jobpost/'.$this->input->post('prefile');
                        unlink($path);

                        $val = $this->db->query($sql);
                    } else {
                        $this->session->set_flashdata('error_message', 'Please try another alias!');
                        redirect('admin_jobpost/pendingEditApproval/'.$job_id);
                    }

                    if ($val == TRUE) {
                        $sql1 = "select * from jobpost where id='".$job_id."' and job_type=0";
                        $val1 = $this->db->query($sql1);
                        $check = $val1->num_rows();

                        // if public
                        if($check == 1) {
                            $rows = $val1->row();
                            $job_name = $rows->name;
                            $job_desc = $rows->description;
                            $job_alias = $rows->alias;
                            $job_type = 'Public';
                            $due_date = $rows->dueDate;

                            $job_language = $rows->language;
                            /*$inIds = "'".str_replace("/", "','", $job_language)."'";
                            $sql_lan = "SELECT name FROM `languages` WHERE `id` IN(".$inIds.")";
                            $val_lan = $this->db->query($sql_lan);
                            $lang = $val_lan->result_array();
                            $lang2 = $lang[0]['name'].' to '.$lang[1]['name'];*/
                            $lang= explode("/",$job_language);
                            $sql_lan0="SELECT name FROM `languages` WHERE `id` =  $lang[0]";
                            $val0 = $this->db->query($sql_lan0);
                            $row_lang0 = $val0->result();
                            $from_lang = $row_lang0[0]->name;

                            $sql_lan1="SELECT name FROM `languages` WHERE `id` =  $lang[1]";
                            $val1 = $this->db->query($sql_lan1);
                            $row_lang1 = $val1->result();
                            $to_lang = $row_lang1[0]->name;

                            $lang2=$from_lang.' to '.$to_lang;

                            $lang = $this->input->post('language_from')."/". $this->input->post('language') ;

                            $sql = "SELECT * FROM translator WHERE language like '%".",".$lang.","."%' ";
                            $val = $this->db->query($sql);
                            $row_email = $val->result();

                            $data = array(
                                'job_name' => $job_name,
                                'description' => $job_desc,
                                'translate_to'=>$lang2,
                                'job_alias' => $job_alias,
                                'job_type' => $job_type,
                                'created' => date('Y-m-d H:i:s'),
                                'due_date' => $due_date,
                                'job_id' => $job_id
                            );


                            foreach ($row_email as $key => $value) {

                                $mailTo = $value->email_address;
                                $mailName = $value->first_name;
                                $mailhash = $value->hash;

                                $mailId=$value->id;
                                $data['name'] = $mailName;
                                $data['hash'] = $mailhash;
                                $data['id'] = $mailId;

                                $this->email->set_mailtype("html");
                                $this->email->from('info@montesinotranslation.com');
                                $this->email->to($mailTo);
                                $this->email->subject('Invitation');
                                $html_email = $this->load->view('email/vwTranslatorSend', $data ,true);
                                $this->email->message($html_email);
                                $this->email->send();
                            }

                        }

                        $this->session->set_flashdata('success_message', 'Job has been successfully approved');
                        redirect('admin_jobpost/pendingEditApproval/'.$job_id);
                    } else {
                        $this->session->set_flashdata('error_message', 'Not Updated');
                        redirect('admin_jobpost/pendingEditApproval/'.$job_id);
                    }
		      	}
			} else {
			    $sql="SELECT * FROM `jobpost` where `id`='$job_id' ";
				$qry=$this->db->query($sql);

				if ($qry->num_rows()=='1') {
                    $data['fetch']=$qry->row();
					$this->load->view('admin/jobpost/vwPostJobForApproval', $data);
				}
			}
		}
	}

	public function postPendingApproval($job_id) {
			$this->form_validation->set_rules('name', 'jobpost Job Name', 'required');
			$this->form_validation->set_rules('clientName', 'jobpost Client Name', 'required');
			$this->form_validation->set_rules('price', 'jobpost Price', 'trim|required|numeric');
			$this->form_validation->set_rules('desc', 'jobpost Description', 'required');
	/*			$this->form_validation->set_rules('language_from', 'jobpost language From', 'required');*/
			$this->form_validation->set_rules('language', 'jobpost Language', 'required');
			$this->form_validation->set_rules('stage', 'jobpost Stage', 'required');
			$this->form_validation->set_rules('totalFile', 'upload file ', 'required');
			$this->form_validation->set_rules('lineNumber', 'Line Number', 'trim|numeric|required');

		    if ($this->form_validation->run() == FALSE)
	        {
				$this->session->set_flashdata('flash_error','Error Validation');
				//redirect('admin_jobpost/edit/'.$job_id);
			} else {
				$number_of_files = sizeof($_FILES['userfile']['tmp_name']);
				$files = $_FILES['userfile'];

				if($this->input->post('totalFile') != "") {
					// $a = $this->input->post('stage');
					// $alias = $this->input->post('job_alias');

					$name = $this->input->post('name');
					$prefile = $this->input->post('prefile');
					$newfile = $prefile.$this->input->post('totalFile');
					$str = $this->UrlAlias($name,'jobpost',$job_id);
					if($str) {
						$proofread_required = 0;
						$proofreadType = 0;
						if($this->input->post('proofread_required') && !is_null($this->input->post('proofread_required'))) {
							$proofread_required = $this->input->post("proofread_required");
						}

						if($this->input->post('proofreadType') && !is_null($this->input->post('proofreadType'))) {
							$proofreadType = $this->input->post("proofreadType");
						}

						$sql = "UPDATE `jobpost` SET
						`name`   = '".$this->input->post('job_title')."',
						`description`   = '". $this->input->post('job_description') ."',
						`job_type`    = '". $this->input->post('type') ."',
						`language`   = '".$this->input->post('language_from')."/". $this->input->post('job_language') ."',
						`price`    = '". $this->input->post('job_price') ."',
						`alias`    = '".$str."',
						`file`    = '".$newfile."',
						`stage`    = 0,
						`status`    = '". 1 ."',
						`modified`    = '". date('Y-m-d H:i:s') ."',

						lineNumberCode = 'M".$this->input->post('lineMonth').$this->input->post('lineYear')."L".$this->input->post('lineNumber').",
						lineNumber = ".$this->input->post('lineNumber').",
						lineMonth = '".$this->input->post('lineMonth')."',
						lineYear = '".$this->input->post('lineYear')."',
						approval_status = 0,
						dueDate = ".$this->input->post('dueDate').",
						clientName = ".$this->input->post('clientName').",
						proofread_required = '".$proofread_required."',
						proofreadType = '".$proofreadType."',

						approval_status = 1

						WHERE `id` = '" .$job_id. "'";

						// csCreator = '".$this->session->userdata('admin_id')."',
						print_r($sql);
						exit();
						$path = './uploads/jobpost/'.$this->input->post('prefile');
						unlink($path);

						$val = $this->db->query($sql);
					} else {
						$this->session->set_flashdata('error_message', 'Please try another alias!');
						redirect('admin_jobpost/pendingEditApproval/'.$job_id);
					}

					if($val == TRUE) {
						$sql1 = "select * from jobpost where id='".$job_id."' and job_type=0";
						$val1 = $this->db->query($sql1);
						$check = $val1->num_rows();

						if($check == 1) { // if public
							$rows = $val1->row();
							$job_name = $rows->name;
							$job_desc = $rows->description;
							$job_alias = $rows->alias;

							$job_language = $rows->language;
							/*$inIds = "'".str_replace("/", "','", $job_language)."'";
							$sql_lan = "SELECT name FROM `languages` WHERE `id` IN(".$inIds.")";
							$val_lan = $this->db->query($sql_lan);
							$lang = $val_lan->result_array();
							$lang2 = $lang[0]['name'].' to '.$lang[1]['name'];*/
                            $lang= explode("/",$job_language);
                            $sql_lan0="SELECT name FROM `languages` WHERE `id` =  $lang[0]";
                            $val0 = $this->db->query($sql_lan0);
                            $row_lang0 = $val0->result();
                            $from_lang = $row_lang0[0]->name;

                            $sql_lan1="SELECT name FROM `languages` WHERE `id` =  $lang[1]";
                            $val1 = $this->db->query($sql_lan1);
                            $row_lang1 = $val1->result();
                            $to_lang = $row_lang1[0]->name;

                            $lang2=$from_lang.' to '.$to_lang;
							$lang = $this->input->post('language_from')."/". $this->input->post('job_language') ;

							$sql = "SELECT * FROM translator WHERE language like '%".",".$lang.","."%' ";
							$val = $this->db->query($sql);
							$row_email = $val->result();

							$data = array(
								'job_name' => $job_name,
								'description' => $job_desc,
								'translate_to'=>$lang2,
								'job_alias' => $job_alias,
								'created' => date('Y-m-d H:i:s')
							);

							foreach ($row_email as $key => $value)
							{
								$mailTo = $value->email_address;
								$mailName = $value->first_name;
								$mailhash = $value->hash;

								$mailId=$value->id;
								$data['name'] = $mailName;
								$data['hash'] = $mailhash;
								$data['id'] = $mailId;
								$this->email->set_mailtype("html");
								$this->email->from('info@montesinotranslation.com');
								$this->email->to($mailTo);
								$this->email->subject('Invitation');
								$html_email = $this->load->view('email/vwTranslatorSend', $data ,true);
								$this->email->message($html_email);
								$this->email->send();
							}
						}

						$this->session->set_flashdata('success_message', 'Successfully Updated');
						redirect('admin_jobpost/pendingEditApproval/'.$job_id);
					}
					else {
						$this->session->set_flashdata('error_message', 'Not Updated');
						redirect('admin_jobpost/pendingEditApproval/'.$job_id);
					}


				}
				// else {
				// 	 $alias=$this->input->post('job_alias');
				// 	$str=$this->UrlAlias ($alias,'jobpost',$job_id);
				// 	//***//

				// 	if($this->input->post('prefile') != "")  {
				// 	if($str){
				// 	$sql = "UPDATE `jobpost` SET
				// 	`name`   = '".$this->input->post('job_title')."',
				// 	`description`   = '". $this->input->post('job_description') ."',

				// 	`language`   = '".$this->input->post('language_from')."/". $this->input->post('job_language') ."',
				// 	`price`    = '". $this->input->post('job_price') ."',
				// 	`alias`    = '".$str."',
				// 	`stage`    = '". $this->input->post('job_stage') ."',
				// 	`status`    = '". $this->input->post('job_status') ."',
				// 	`job_type`    = '". $this->input->post('type') ."',
				// 	`modified`    = '". date('Y-m-d h:i:s') ."'
				// 	 WHERE `id` = '" .$job_id. "'";

				//     $val = $this->db->query($sql);

				// 		$sql1="select * from jobpost where id='".$job_id."' and job_type=0";

				// 		$val1 = $this->db->query($sql1);
				// 		$check=$val1->num_rows();
				// 		if($check==1){
				// 		$rows = $val1->row();
				// 		$job_name=$rows->name;
				// 		$job_desc=$rows->description;
				// 		$job_alias=$rows->alias;

				// 		$job_language=$rows->language;
				// 		$inIds = "'".str_replace("/", "','", $job_language)."'";
				// 		$sql_lan="SELECT name FROM `languages` WHERE `id` IN(".$inIds.")";
				// 		//echo $sql_lan;
				// 		$val_lan=$this->db->query($sql_lan);
				// 		$lang=$val_lan->result_array();
				// 		$lang2=$lang[0]['name'].' to '.$lang[1]['name'];

				// 		//echo'<pre>';print_r($row_name); die();
				// 		$lang= $this->input->post('language_from')."/". $this->input->post('job_language') ;

				// 		$sql = "SELECT * FROM translator WHERE language like '%".",".$lang.","."%' ";

				// 		$val = $this->db->query($sql);
				// 		$row_email = $val->result();

				// 			$data = array(
				// 				'job_name' => $job_name,
				// 				'description' => $job_desc,
				// 				'translate_to'=>$lang2,
				// 				'job_alias' => $job_alias,
				// 				'created' => date('Y-m-d h:i:s')
				// 			);
				// 				foreach ($row_email as $key => $value)
				// 				{
				// 				$mailTo = $value->email_address;
				// 				$mailName = $value->first_name;
				// 				$mailhash = $value->hash;

				// 				$mailId=$value->id;
				// 				$data['name'] = $mailName;
				// 				$data['hash'] = $mailhash;
				// 				$data['id'] = $mailId;
				// 				$this->email->set_mailtype("html");
				// 				$this->email->from('info@montesinotranslation.com');
				// 				$this->email->to($mailTo);
				// 				$this->email->subject('Invitation');
				// 				$html_email = $this->load->view('email/vwTranslatorSend', $data ,true);
				// 				$this->email->message($html_email);
				// 				// $this->email->send();
				// 				}
				// 				}
				// 	            }else
				// 				{
				// 				$this->session->set_flashdata('error_message', 'Please try another alias!');
    //                             redirect('admin_jobpost/pendingEditApproval/'.$job_id);
				// 				}

				// 	//echo '<pre>'; print_r($sql); die;
				// 	$this->session->set_flashdata('success_message', 'Successfully Updated');
    //                 redirect('admin_jobpost/pendingEditApproval/'.$job_id);
				// 	} else {
				// 	$this->session->set_flashdata('error_message', 'Not Updated');
    //                 redirect('admin_jobpost/pendingEditApproval/'.$job_id);
				// 	}

				// }

	      	}
	}

}
