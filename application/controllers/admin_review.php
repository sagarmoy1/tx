<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_Review extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_review_model');
        // $this->load->model('adminbidjob_model');
        // $this->load->model('awardjob_model');
        $this->load->model('adminhiringjob_model');
        $this->load->model('adminworkingjob_model');
        $this->load->model('adminjobpost_model');
        $this->load->model('admincompletedjob_model');
        $this->load->model('dashboard_model');
        $this->load->helper('path');
//        echo $this->router->fetch_class();
//        echo $this->router->fetch_method();
//        exit();
        if (!$this->session->userdata('is_admin')) {
            redirect('admin/login');
        }

        date_default_timezone_set('America/New_York');
    }

    function UrlAlias($string, $table, $id = NULL)
    {
        //remove any '-' from the string they will be used as concatonater
        $str = str_replace('-', ' ', $string);
        $str = str_replace('_', ' ', $string);
        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $str);

        // lowercase and trim
        $str = trim(strtolower($str));

        // checking if in db or not
        if ($id == NULL) {
            $sql = "SELECT * FROM " . $table . " WHERE 1 AND alias ='" . $str . "'";
        } else {
            $sql = "SELECT * FROM " . $table . " WHERE 1 AND alias ='" . $str . "' AND id <> '" . $id . "'";
        }
        $res = mysql_query($sql);
        $rowcount = mysql_num_rows($res);

        if ($rowcount == 0) {
            return $str;
        } else {
            $number = mt_rand(100, 999);
            return $str . $number;
            //return false;
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
        $config['per_page'] = 10;
        $config['base_url'] = base_url() . 'admin_review/joblist';
        $config['use_page_numbers'] = TRUE;

        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $page = $this->uri->segment(3);

        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }


        if ($order_type) {
            $filter_session_data['order_type'] = $order_type;
        } else {
            if ($this->session->userdata('order_type')) {
                $order_type = $this->session->userdata('order_type');
            } else {
                $order_type = 'Asc';
            }
        }

        $data['order_type_selected'] = $order_type;

        if ($search_string != '' || $order != '' || $this->uri->segment(3) == true) {

            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string = $this->session->userdata('search_string_selected');
                $filter_session_data['search_string_selected'] = $search_string;
            }

            $data['search_string_selected'] = $search_string;

            if ($order) {
                $filter_session_data['order'] = $order;
            } else {
                $order = $this->session->userdata('order');
            }

            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);

            $data['count_jobpost'] = $this->admin_review_model->count_jobpost($search_string, $order);
            $config['total_rows'] = $data['count_jobpost'];

            $config['num_links'] = floor($config['total_rows'] / $config['per_page']);

            //fetch sql data into arrays
            if ($search_string) {
                if ($order) {
                    $data['jobpost'] = $this->admin_review_model->get_jobpost($search_string, $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['jobpost'] = $this->admin_review_model->get_jobpost($search_string, '', $order_type, $config['per_page'], $limit_end);
                }
            } else {
                if ($order) {
                    $data['jobpost'] = $this->admin_review_model->get_jobpost('', $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['jobpost'] = $this->admin_review_model->get_jobpost('', '', $order_type, $config['per_page'], $limit_end);
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

            $data['count_jobpost'] = $this->admin_review_model->count_jobpost('', '');
            $data['jobpost'] = $this->admin_review_model->get_jobpost('', '', $order_type, $config['per_page'], $limit_end);
            $config['total_rows'] = $data['count_jobpost'];
            $config['num_links'] = floor($config['total_rows'] / $config['per_page']);
        }

        $this->pagination->initialize($config);

        $this->load->view('admin/jobpost/proofreadList', $data);
    }

    public function joblist()
    {
        if (!$this->session->userdata('is_admin')) {
            $this->load->view('admin/vwLogin');
        } else {
            //all the posts sent by the view
            $search_string = $this->input->post('search_string');
            $search_string = preg_replace('/[^A-Za-z0-9\s\-\:]/', '', $search_string);
            $search_string = trim($search_string);

            $page = $this->uri->segment(4);

            //pagination settings
            $config['per_page'] = 10;
            $config['base_url'] = base_url('admin_review/review/joblist');
            $config['use_page_numbers'] = TRUE;

            $config['full_tag_open'] = '<ul>';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="active"><a>';
            $config['cur_tag_close'] = '</a></li>';
            $config["uri_segment"] = 4;

            $limit_end = ($page * $config['per_page']) - $config['per_page'];

            if ($limit_end < 0) {
                $limit_end = 0;
            }

            if ($search_string != '' || $this->uri->segment(4) == true) {
                if ($search_string) {
                    $filter_session_data['search_string_selected'] = $search_string;
                } else {
                    $search_string = $this->session->userdata('search_string_selected');
                    $filter_session_data['search_string_selected'] = $search_string;
                }

                $data['search_string_selected'] = $search_string;

                //save session data into the session
                $this->session->set_userdata($filter_session_data);

                $data['joblists'] = $this->admin_review_model->get_joblist($search_string, $limit_end, $config['per_page']);
                $data['count_joblist'] = $this->admin_review_model->count_joblist($search_string);
                $config['total_rows'] = $data['count_joblist'];
            } else {
                //clean filter data inside section
                $filter_session_data['search_string_selected'] = null;
                $this->session->set_userdata($filter_session_data);

                //pre selected options
                $data['search_string_selected'] = '';
                $data['order'] = 'id';

                $data['count_joblist'] = $this->admin_review_model->count_joblist();
                $data['joblists'] = $this->admin_review_model->get_joblist();
                $config['total_rows'] = $data['count_joblist'];
            }

            $config['num_links'] = floor($config['total_rows'] / $config['per_page']);

//            echo '<pre>'; print_r($config); exit;

            $this->pagination->initialize($config);

            $this->load->view('admin/jobpost/vwReviewJoblist', $data);
        }
    }

    public function showReviewDetails($job_id)
    {
        $data['message_error'] = "";
        $data['message_success'] = "";

        if (!$this->session->userdata('is_admin')) {
            $this->load->view('admin/vwLogin');
        } else {
            if ($this->input->post("name")) {
                $sql = "SELECT * FROM jobpost where id='$job_id' ";
                $qry = $this->db->query($sql);
                $data['fetch'] = $qry->row();

                $this->form_validation->set_rules('name', 'jobpost Job Name', 'required');
                $this->form_validation->set_rules('clientName', 'jobpost Client Name', 'required');
                $this->form_validation->set_rules('price', 'jobpost Price', 'trim|required|numeric');
                $this->form_validation->set_rules('desc', 'jobpost Description', 'required');
                $this->form_validation->set_rules('language', 'jobpost Language', 'required');
                $this->form_validation->set_rules('lineNumber', 'Line Number', 'trim|numeric|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->load->view('admin/jobpost/vwPostJobForApproval', $data);
                } else {
                    if (isset($_FILES['documents'])) {
                        $number_of_files = sizeof($_FILES['documents']['tmp_name']);
                        $files = $_FILES['documents'];
                    }

                    $name = $this->input->post('name');
                    $prefile = $this->input->post('prefile');
                    $newfile = $prefile . $this->input->post('totalFile');
                    $str = $this->UrlAlias($name, 'jobpost', $job_id);

                    if ($str) {
                        $proofread_required = 0;
                        $proofreadType = "";

                        if ($this->input->post('proofread_required') && !is_null($this->input->post('proofread_required'))) {
                            $proofread_required = $this->input->post("proofread_required");
                        }

                        if ($this->input->post('proofreadType') && !is_null($this->input->post('proofreadType'))) {
                            $proofreadType = $this->input->post("proofreadType");
                        }

                        $due_date = $this->input->post('dueDate') . ' ' . $this->input->post('hour') . ':' . $this->input->post('minute') . ' ' . $this->input->post('ampm');

                        $sql = "UPDATE jobpost SET
                            name   = '" . $this->input->post('name') . "',
                            clientName   = '" . $this->input->post('clientName') . "',
                            description   = '" . $this->input->post('desc') . "',
                            job_type    = '" . $this->input->post('type') . "',
                            LANGUAGE   = '" . $this->input->post('language_from') . "/" . $this->input->post('language') . "',
                            price    = '" . $this->input->post('price') . "',
                            alias    = '" . $str . "',
                            FILE    = '" . $newfile . "',
                            stage    = 0,
                            STATUS    = '" . 1 . "',
                            modified    = '" . date('Y-m-d H:i:s') . "',
                            lineNumberCode = 'M" . $this->input->post('lineMonth') . $this->input->post('lineYear') . "L" . $this->input->post('lineNumber') . "',
                            lineNumber = " . $this->input->post('lineNumber') . ",
                            lineMonth = '" . $this->input->post('lineMonth') . "',
                            lineYear = '" . $this->input->post('lineYear') . "',
                            approval_status = 0,
                            dueDate = '" . $due_date . "',
                            clientName = '" . $this->input->post('clientName') . "',
                            proofread_required = '" . $proofread_required . "',
                            proofreadType = '" . $proofreadType . "'
                            WHERE id = '" . $job_id . "'";

                        // csCreator = '".$this->session->userdata('admin_id')."',

                        $path = './uploads/jobpost/' . $this->input->post('prefile');
                        unlink($path);

                        $val = $this->db->query($sql);

                    } else {
                        $this->session->set_flashdata('error_message', 'Please try another alias!');
                        redirect('admin_jobpost/pendingEditApproval/' . $job_id);
                    }

                    if ($val == TRUE) {
                        $sql1 = "SELECT * FROM jobpost WHERE id='" . $job_id . "' AND job_type=0";
                        $val1 = $this->db->query($sql1);
                        $check = $val1->num_rows();

                        if ($check == 1) { // if public
                            $rows = $val1->row();
                            $job_name = $rows->name;
                            $job_desc = $rows->description;
                            $job_alias = $rows->alias;

                            $job_language = $rows->language;
                           /* $inIds = "'" . str_replace("/", "','", $job_language) . "'";
                            $sql_lan = "SELECT name FROM languages WHERE id IN(" . $inIds . ")";
                            $val_lan = $this->db->query($sql_lan);
                            $lang = $val_lan->result_array();
                            $lang2 = $lang[0]['name'] . ' to ' . $lang[1]['name'];*/
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



                            $lang = $this->input->post('language_from') . "/" . $this->input->post('job_language');

                            if($this->input->post("proofreadType") == 'editing'){
                                $sql = "SELECT * FROM translator WHERE language LIKE '%".$this->input->post('job_language',true)."%'";
                            }else {
                                $lang = $this->input->post('language_from') . "/" . $this->input->post('job_language');
                                $lang_reverse = $this->input->post('job_language').'/'.$this->input->post('language_from');
                                //echo $this->input->post('job_language');exit;
                                $sql = "SELECT * FROM translator WHERE (language LIKE '%," . $lang . ",%' OR language LIKE '%".$lang_reverse."%')";
                            }
                            $val = $this->db->query($sql);
                            $row_email = $val->result();

                            $data = array(
                                'job_name' => $job_name,
                                'description' => $job_desc,
                                'translate_to' => $lang2,
                                'lang_from' => $from_lang,
                                'lang_to' => $to_lang,
                                'job_alias' => $job_alias,
                                'created' => date('Y-m-d H:i:s')
                            );

                            foreach ($row_email as $key => $value) {
                                $mailTo = $value->email_address;
                                $mailName = $value->first_name;
                                $mailhash = $value->hash;

                                $mailId = $value->id;
                                $data['name'] = $mailName;
                                $data['hash'] = $mailhash;
                                $data['id'] = $mailId;
                                $this->email->set_mailtype("html");
                                $this->email->from('info@montesinotranslation.com');
                                $this->email->to($mailTo);
                                $this->email->subject('Invitation');
                                $html_email = $this->load->view('email/vwTranslatorSend', $data, true);
                                $this->email->message($html_email);
                                $this->email->send();
                            }
                        }

                        $this->session->set_flashdata('success_message', 'Job has been successfully approved');
                        redirect('admin_jobpost/pendingEditApproval/' . $job_id);
                    } else {
                        $this->session->set_flashdata('error_message', 'Not Updated');
                        redirect('admin_jobpost/pendingEditApproval/' . $job_id);
                    }
                }
            } else {
                $this->db->join("translator", 'translator.id = proofread_jobs.translator_id', 'left');
                $this->db->join("jobpost", 'jobpost.id = proofread_jobs.job_id', 'left');
                $qry = $this->db->get_where("proofread_jobs", array("proofread_jobs.id" => $job_id));

                if ($qry->num_rows() != 0) {
                    $data['fetch'] = $qry->row();
                    $this->load->view('admin/jobpost/proofreaddetails', $data);
                }
            }
        }
    }

    public function hiring()
    {
        //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        $search_string = preg_replace('/[^A-Za-z0-9\s\-\:]/', '', $search_string);
        $search_string = trim($search_string);

        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] = 10;
        $config['base_url'] = base_url() . 'admin_review/hiring';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $page = $this->uri->segment(3);

        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }


        if ($order_type) {
            $filter_session_data['order_type'] = $order_type;
        } else {
            if ($this->session->userdata('order_type')) {
                $order_type = $this->session->userdata('order_type');
            } else {
                $order_type = 'Asc';
            }
        }

        $data['order_type_selected'] = $order_type;

        if ($search_string != '' || $order != '' || $this->uri->segment(3) == true) {

            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string = $this->session->userdata('search_string_selected');
                $filter_session_data['search_string_selected'] = $search_string;
            }
            $data['search_string_selected'] = $search_string;

            if ($order) {
                $filter_session_data['order'] = $order;
            } else {
                $order = $this->session->userdata('order');
            }
            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);


            $data['count_hiringjob'] = $this->admin_review_model->count_hiringjob($search_string, $order);
            $config['total_rows'] = $data['count_hiringjob'];

            //fetch sql data into arrays
            if ($search_string) {
                if ($order) {
                    $data['hiringjob'] = $this->admin_review_model->get_hiringjob($search_string, $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['hiringjob'] = $this->admin_review_model->get_hiringjob($search_string, '', $order_type, $config['per_page'], $limit_end);
                }
            } else {
                if ($order) {
                    $data['hiringjob'] = $this->admin_review_model->get_hiringjob('', $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['hiringjob'] = $this->admin_review_model->get_hiringjob('', '', $order_type, $config['per_page'], $limit_end);
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

            $data['count_hiringjob'] = $this->admin_review_model->count_hiringjob();
            $data['hiringjob'] = $this->admin_review_model->get_hiringjob('', '', $order_type, $config['per_page'], $limit_end);
            $config['total_rows'] = $data['count_hiringjob'];
        }

        // echo '<pre>'; print_r($data['hiringjob']); exit;

        $this->pagination->initialize($config);
        $this->load->view('admin/jobpost/vwReviewHiringJob', $data);
    }

    public function working()
    {
        if ($this->session->userdata('is_admin')) {
            $filter_session_data = "";
            //all the posts sent by the view
            $search_string = $this->input->post('search_string');
            $search_string = preg_replace('/[^A-Za-z0-9\s\-\:]/', '', $search_string);
            $search_string = trim($search_string);

            $stage = $this->input->post('job_stage');
            $order = $this->input->post('order');
            $order_type = $this->input->post('order_type');

            //pagination settings
            $config['per_page'] = 10;
            $config['base_url'] = base_url() . 'admin_review/working/';
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

            if ($limit_end < 0) {
                $limit_end = 0;
            }

            //if order type was changed
            if ($order_type) {
                $filter_session_data['order_type'] = $order_type;
            } else {
                //we have something stored in the session?
                if ($this->session->userdata('order_type')) {
                    $order_type = $this->session->userdata('order_type');
                } else {
                    //if we have nothing inside session, so it's the default "Asc"
                    $order_type = 'Asc';
                }
            }

            //make the data type var avaible to our view
            $data['order_type_selected'] = $order_type;


            //we must avoid a page reload with the previous session data
            //if any filter post was sent, then it's the first time we load the content
            //in this case we clean the session filter data
            //if any filter post was sent but we are in some page, we must load the session data

            //filtered && || paginated
            if (($search_string !== false || $stage != false || $this->uri->segment(4) == true) && $order !== false) {
                if ($search_string) {
                    $filter_session_data['search_string_selected'] = $search_string;
                } else {
                    $search_string = $this->session->userdata('search_string_selected');
                }

                $data['search_string_selected'] = $search_string;

                if ($stage) {
                    $filter_session_data['stage_selected'] = $stage;
                } else {
                    $stage = $this->session->userdata('stage_selected');
                }

                $data['stage_selected'] = $stage;

                if ($order) {
                    $filter_session_data['order'] = $order;
                } else {
                    $order = $this->session->userdata('order');
                }

                $data['order'] = $order;

                //save session data into the session
                $this->session->set_userdata($filter_session_data);

                $data['count_workingjob'] = $this->adminworkingjob_model->count_workingjob($search_string, $stage, $order);
                $config['total_rows'] = $data['count_workingjob'];

                // echo "search string: {$search_string}<br/>";
                // echo "stage: {$stage}<br/>";
                // echo "order: {$order}<br/>";
                // exit;

                //fetch sql data into arrays
                if ($search_string && $stage == '' && $order == '') {
                    $data['workingjob'] = $this->adminworkingjob_model->get_workingjob($search_string, '', '', $order_type, $config['per_page'], $limit_end);
                }

                if ($order && $search_string == '' && $stage == '') {
                    $data['workingjob'] = $this->adminworkingjob_model->get_workingjob('', '', $order, $order_type, $config['per_page'], $limit_end);
                }

                if ($stage && $order == '' && $search_string == '') {
                    $data['workingjob'] = $this->adminworkingjob_model->get_workingjob('', $stage, '', $order_type, $config['per_page'], $limit_end);
                }

                if ($search_string && $stage && $order == '') {
                    $data['workingjob'] = $this->adminworkingjob_model->get_workingjob($search_string, $stage, '', $order_type, $config['per_page'], $limit_end);
                }

                if ($search_string && $stage == '' && $order) {
                    $data['workingjob'] = $this->adminworkingjob_model->get_workingjob($search_string, $order, $order_type, $config['per_page'], $limit_end);
                }

                if ($search_string == '' && $stage && $order) {
                    $data['workingjob'] = $this->adminworkingjob_model->get_workingjob('', $stage, $order, $order_type, $config['per_page'], $limit_end);
                }

                if ($search_string && $order && $stage) {
                    $data['workingjob'] = $this->adminworkingjob_model->get_workingjob($search_string, $stage, $order, $order_type, $config['per_page'], $limit_end);
                }
            } else {
                //clean filter data inside section;
                $filter_session_data['search_string_selected'] = null;
                $filter_session_data['stage_selected'] = null;
                $filter_session_data['order'] = null;
                $filter_session_data['order_type'] = null;

                $this->session->set_userdata($filter_session_data);

                //pre selected options
                $data['search_string_selected'] = '';
                $data['stage_selected'] = '';
                $data['order'] = 'id';

                //fetch sql data into arrays
                $data['count_workingjob'] = $this->adminworkingjob_model->count_workingjob();

                $data['workingjob'] = $this->adminworkingjob_model->get_workingjob(null, null, $order_type, $config['per_page'], $limit_end);

                $config['total_rows'] = $data['count_workingjob'];

            }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

            //initializate the panination helper
            $this->pagination->initialize($config);

            // echo "DEBUG: <pre>"; print_r($data); exit;

            //load the view
            $this->load->view('admin/jobpost/vwWorkingJob', $data);
        } else {
            $this->load->view('admin/vwLogin');
        }
    }

    public function completed()
    {
        //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        $search_string = preg_replace('/[^A-Za-z0-9\s\-\:]/', '', $search_string);
        $search_string = trim($search_string);

        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] = 10;
        $config['base_url'] = base_url() . 'admin_review/completed';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $page = $this->uri->segment(3);

        $limit_end = ($page * $config['per_page']) - $config['per_page'];

        if ($limit_end < 0) {
            $limit_end = 0;
        }


        if ($order_type) {
            $filter_session_data['order_type'] = $order_type;
        } else {
            if ($this->session->userdata('order_type')) {
                $order_type = $this->session->userdata('order_type');
            } else {
                $order_type = 'DESC';
            }
        }

        $data['order_type_selected'] = $order_type;

        if ($search_string != '' || $order != '' || $this->uri->segment(3) == true) {
            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string = $this->session->userdata('search_string_selected');
                $filter_session_data['search_string_selected'] = $search_string;
            }

            $data['search_string_selected'] = $search_string;

            if ($order) {
                $filter_session_data['order'] = $order;
            } else {
                // $order = $this->session->userdata('order');
                $order = 'complete_date';
            }

            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);

            $data['count_completedjob'] = $this->admincompletedjob_model->count_completedjob($search_string, $order);
            $config['total_rows'] = $data['count_completedjob'];

            //fetch sql data into arrays
            if ($search_string) {
                if ($order) {
                    $data['completedjob'] = $this->admincompletedjob_model->get_completedjob($search_string, $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['completedjob'] = $this->admincompletedjob_model->get_completedjob($search_string, '', $order_type, $config['per_page'], $limit_end);
                }
            } else {
                if ($order) {
                    $data['completedjob'] = $this->admincompletedjob_model->get_completedjob('', $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['completedjob'] = $this->admincompletedjob_model->get_completedjob('', '', $order_type, $config['per_page'], $limit_end);
                }
            }

        } else {
            //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = 'complete_date';
            $filter_session_data['order_type'] = 'desc';
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            $data['count_completedjob'] = $this->admincompletedjob_model->count_completedjob();
            $data['completedjob'] = $this->admincompletedjob_model->get_completedjob('', 'b.complete_date', $order_type, $config['per_page'], $limit_end);
            $config['total_rows'] = $data['count_completedjob'];
        }

        $this->pagination->initialize($config);
        $this->load->view('admin/jobpost/vwReviewCompletedJob', $data);
    }

    public function add_review_job()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";

            //if save button was clicked, get the data sent via post
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                $this->form_validation->set_rules('name', 'jobpost name', 'required');
                $this->form_validation->set_rules('lineNumber', 'jobpost price', 'trim|numeric');
                $this->form_validation->set_rules('price', 'jobpost price', 'trim|required|numeric');
                $this->form_validation->set_rules('desc', 'jobpost Description', 'required');
                $this->form_validation->set_rules('language_from', 'jobpost language from', 'required');
                $this->form_validation->set_rules('language', 'jobpost language to', 'required');
                $this->form_validation->set_rules('stage', 'jobpost stage', 'required');
                $this->form_validation->set_rules('dueDate', 'due date', 'required');

                if (empty($_FILES['document']['name']['original'][0])) {
                    $this->form_validation->set_rules('original', 'original file', 'required');
                }

                if (empty($_FILES['document']['name']['translated'][0])) {
                    $this->form_validation->set_rules('translated', 'translated file', 'required');
                }

                $this->form_validation->set_rules('translator', 'translator', 'required');

                if ($this->form_validation->run()) {
                    $alias = $this->input->post('alias');
                    $name = $this->input->post('name');

                    if ($alias == '') {
                        $str = $this->UrlAlias($name, 'jobpost');
                    } else {
                        $str = $this->UrlAlias($alias, 'jobpost');
                    }

                    if ($str) {
                        $line_month = $this->input->post('lineMonth') ? $this->input->post('lineMonth') : $this->input->post('_lineMonth');
                        $line_year = $this->input->post('lineYear') ? $this->input->post('lineYear') : $this->input->post('_lineYear');
                        $line_number = $this->input->post('lineNumber') ? $this->input->post('lineNumber') : $this->input->post('_lineNumber');

                        $line_number_code = 'M' . $line_month . $line_year . 'L' . $line_number;
                        // $line_number_code = 'M'.$this->input->post('lineMonth').$this->input->post('lineYear').'L'.$this->input->post('lineNumber');

                        if ($this->input->post('remaining_balance') != '') {
                            $price = $this->input->post('remaining_balance');
                        } else {
                            $price = $this->input->post('price');
                        }

                        $today = date('Y-m-d H:i:s');

                        $data_to_store = array(
                            'name' => 'Review Job: ' . $this->input->post('name'),
                            'alias' => $str,
                            'file' => $this->input->post('totalFile'),
                            'description' => $this->input->post('desc'),
                            'language' => $this->input->post('language_from') . "/" . $this->input->post('language'),
                            'price' => $this->input->post('price'),
                            'status' => 1,
                            'stage' => $this->input->post('stage'),
                            'job_type' => $this->input->post('type'),
                            'created' => $today,
                            'date_posted' => $today,
                            'dueDate' => $this->input->post('dueDate') . ' ' . $this->input->post('hour') . ':' . $this->input->post('minute') . ' ' . $this->input->post('ampm'),
                            'proofread_required' => $this->input->post("proofread_required"),
                            'proofreadType' => $this->input->post("proofreadType"),
                            'lineNumberCode' => $line_number_code,
                            'lineNumber' => $this->input->post('lineNumber'),
                            'lineMonth' => $this->input->post('lineMonth'),
                            'lineYear' => $this->input->post('lineYear'),
                            'approval_status' => 1,
                            'jobDone' => 0
                        );
                        if ($this->adminjobpost_model->store_jobpost($data_to_store)) {
                            $job_id = $this->db->insert_id();
                            $sql1 = "select * from jobpost where id=$job_id and job_type=0";
                            $val1 = $this->db->query($sql1);
                            $check = $val1->num_rows();

                            // store details in proofread_jobs table
                            $proofread_jobs_data = array(
                                'job_id' => $job_id,
                                'translation_completed' => 0,
                                'review_price' => 0,
                                'review_stage' => 1,  // hiring
                                'review_type' => 1,
                                'totalfiles' => $this->input->post('totalFile')
                            );

                            $proofread_job_id = $this->adminjobpost_model->store_proofread_jobs($proofread_jobs_data);
                            $this->upload_documents($proofread_job_id, $_POST['document']['translator']);
                            if ($check == 1) {

                                // uploads
                                //$this->upload_documents($proofread_job_id, $_POST['document']['translator']);

                                $rows = $val1->row();

                                $job_name = $rows->name;
                                $job_desc = $rows->description;
                                $job_alias = $rows->alias;

                                $job_language = $rows->language;
                               /* $inIds = "'" . str_replace("/", "','", $job_language) . "'";
                                $sql_lan = "SELECT name FROM languages WHERE id IN(" . $inIds . ")";

                                $val_lan = $this->db->query($sql_lan);
                                $lang = $val_lan->result_array();
                                $lang2 = $lang[0]['name'] . ' to ' . $lang[1]['name'];*/

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

                               
                                
                                // populate list of translator who will be receiving an invitation
                                if ($this->input->post('proofreadType') == 'editing') {
                                    $lang_1 = $this->input->post('language_from');
                                    $lang_2 = $this->input->post('language');
                                    
                                    //$sql = "SELECT * FROM translator WHERE language LIKE '%{$lang_2}%'";
                                    $sql = "SELECT * FROM translator WHERE language LIKE '%/".$lang_2.",%'";
                                    $job_type = 'Proof Reading / Editing';

                                    $job_type_description = 'Proof Reading / Editing</span>: means that English Speaking linguists can bid and review the final translation for grammar and accuracy in English.';
                                } else {
                                    $lang = $this->input->post('language_from') . "/" . $this->input->post('language');
                                    $lang_reverse = $this->input->post('language',true).'/'.$this->input->post('language_from');

                                    $sql = "SELECT * FROM translator WHERE ( language LIKE '%{$lang}%' OR language LIKE '%".$lang_reverse."%')";

                                    $job_type = 'Proof Reading / Comparison';

                                    $job_type_description = 'Proof Reading / Comparison</span>: means you can only bid if you have the same language pair listed on the job. Because you are required to compare the original doc and translation for accuracy.';
                                }
                                //echo $sql;die;
                                $val = $this->db->query($sql);
                                $row_email = $val->result();

                                // get due date
                                $sql = "SELECT dueDate FROM jobpost WHERE id = '{$job_id}'";
                                $query = $this->db->query($sql);
                                $row = $query->row();

                                if ($row->dueDate == '') {
                                    $due_date = 'N/A';
                                } else {
                                    $due_date = $row->dueDate;
                                }

                                // echo '<pre>'; print_r($row); exit;
                               $proofread_required=$this->input->post("proofread_required");
				               $proofreadType=$this->input->post("proofreadType");

                                $data = array(
                                    'job_name' => $job_name,
                                    'job_type' => $job_type,
                                    'job_type_description' => $job_type_description,
                                    'description' => $job_desc,
                                    'job_alias' => $job_alias,
                                    'translate_to' => $lang2,
									 'lang_from' => $from_lang,
                                    'lang_to' => $to_lang,
                                    'due_date' => $due_date,
                                     'proofread_required' => $proofread_required,
				                     'proofreadType' => $proofreadType,
                                    'created' => $data_to_store['created']
                                );

                                // echo '<pre>'; print_r($data); exit;

                                foreach ($row_email as $key => $value) {
                                    $mailTo = $value->email_address;
                                    $mailName = $value->first_name;
                                    $mailhash = $value->hash;

                                    $mailId = $value->id;
                                    $data['name'] = $mailName;
                                    $data['hash'] = $mailhash;
                                    $data['id'] = $mailId;
                                    $data['job_id'] = $job_id;

                                    $this->email->set_mailtype("html");
                                    $this->email->from('info@montesinotranslation.com');
                                    $this->email->to($mailTo);
                                    $this->email->subject('Invitation');
                                    $html_email = $this->load->view('email/vwTranslatorSend', $data, true);

                                    // echo $html_email; exit;

                                    $this->email->message($html_email);
                                    $this->email->send();
                                }

                            }

                            $data['message_success'] = "Successfully Added Review Job";
                        }
                    } else {
                        $data['message_error'] = "Please try another alias!";
                    }
                }
            }

            $sql = "SELECT lineNumberCode AS lineNumber FROM jobpost WHERE lineNumber != '' GROUP BY lineNumber ORDER BY lineNumberCode ASC";
            $query = $this->db->query($sql);
            $data['line_numbers'] = $query->result_array();

            // echo '<pre>'; print_r($data['line_numbers']); exit;

            //load the view
            $this->load->view('admin/jobpost/vwAddReviewJob', $data);
        } else {
            redirect('admin_review/add');
        }
    }

    public function workcomplete()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";

            $bidjob_details_id = $this->uri->segment(3);
            $job_id = $this->uri->segment(4);

            if ($bidjob_details_id != '') {
                $sql = "UPDATE bidjob_details SET is_completed = 1, date_completed=NOW() WHERE id = {$bidjob_details_id}";
                $val = $this->db->query($sql);

                if ($val) {
                    $sql = "SELECT bidjob_id FROM bidjob_details WHERE id = {$bidjob_details_id}";
                    $query = $this->db->query($sql);
                    $row = $query->row();
                    $bidjob_id = $row->bidjob_id;

                    $sql = "UPDATE bidjob SET stage = 2, is_done = 1, complete_date = NOW() WHERE id = {$bidjob_id}";
                    $this->db->query($sql);

                    $sql = "UPDATE invoice SET is_deleted = 0 WHERE bid_id = {$bidjob_id} AND job_id = {$job_id}";
                    $this->db->query($sql);

                    $transql = "select * from bidjob where id='$bidjob_id'";
                    $tranval = $this->db->query($transql);
                    $tranfetch = $tranval->row();
                    $trans_id = $tranfetch->trans_id;


                    $tranlator = $this->db->get_where('translator', ['id' => $trans_id])->first_row();
                    $job = $this->db->get_where('jobpost', ['id' => $job_id])->first_row();
                    $data['name'] = $tranlator->first_name . ' ' . $tranlator->last_name;
                    $data['job_name'] = $job->name;
                    $data['job_description'] = $job->desc;
                    $data['job_created'] = $job->created;
                    $data['job_alias'] = $job->alias;
                    //$data['invoice'] =$invoice;

                    $mailTo = $tranlator->email_address;
                    $this->email->set_mailtype("html");
                    $this->email->from('info@montesinotranslation.com');
                    $this->email->to($mailTo);
                    $this->email->subject('Award Job Completion');
                    $html_email = $this->load->view('email/vwTranslatorAwardCompletion', $data, true);
                    $this->email->message($html_email);
                    $this->email->send();
                    // $bid_price=$tranfetch->price;
                    // $awarded_date=$tranfetch->award_date;
                    // $complete_date=$tranfetch->complete_date;
                    // $comp_time=$tranfetch->time_need;
                    //
                    // $jobsql="select * from jobpost where id='$job_id'";
                    // $jobval=$this->db->query($jobsql);
                    // $jobfetch=$jobval->row();
                    // $job_name=$jobfetch->name;
                    // $job_description=$jobfetch->description;
                    // $job_created=$jobfetch->created;
                    // $job_alias=$jobfetch->alias;
                    //
                    // $emailsql="select * from translator where id='$trans_id'";
                    // $emailval=$this->db->query($emailsql);
                    // $emailfetch=$emailval->row();
                    // $trans_email=$emailfetch->email_address;
                    // $trans_name=$emailfetch->first_name.'&nbsp;'.$emailfetch->last_name;
                    //
                    // $data['name'] = $trans_name;
                    // $data['job_name'] =$job_name;
                    // $data['job_description'] =$job_description;
                    // $data['job_created'] =$job_created;
                    // $data['job_alias'] =$job_alias;
                    //
                    // $mailTo =$trans_email;
                    // $mailName =$trans_name;
                    // $this->email->set_mailtype("html");
                    // $this->email->from('info@montesinotranslation.com');
                    // $this->email->to($mailTo);
                    // $this->email->subject('Award Job Completion');
                    // $html_email = $this->load->view('email/vwTranslatorAwardCompletion', $data ,true);
                    // $this->email->message($html_email);
                    // $this->email->send();

                    $invoice_id = time();
                    // $data['invoice_id'] =$invoice_id;
                    // $data['name'] =$trans_name;
                    // $data['job_title'] =$job_name;
                    // $data['award_date'] =$awarded_date;
                    // $data['complete_date'] =$complete_date;
                    // $data['job_alias'] =$job_alias;
                    // $data['job_price'] =$bid_price;
                    // $data['comp_time'] =$comp_time;

                    // $mailTo =$trans_email;
                    // $mailName =$trans_name;
                    // $this->email->set_mailtype("html");
                    // $this->email->from('info@montesinotranslation.com');
                    // $this->email->to($mailTo);
                    // $this->email->subject('Job Completion Invoice');
                    // $html_email = $this->load->view('email/vwJobCompletionInvoice', $data ,true);
                    // $this->email->message($html_email);
                    // $mail=$this->email->send();

                    // $this->load->model("admin_review_model");
                    // if ($jobfetch->proofread_required == 1) {
                    // $data['translator_id'] = $trans_id;
                    // $data['job_id'] = $job_id;
                    // $data['totalfiles'] = $jobfetch->totalfile;
                    // $this->admin_review_model->createPendingJob($data);
                    // }

                    // $sql = "UPDATE proofread_jobs SET translator_id=".$trans_id.", review_stage=4, modified='$date' WHERE job_id = '" . $job_id . "'";
                    // $this->db->query($sql);

                    // check if all documents are already completed
                    // $sql = "SELECT COUNT(*) AS total_num FROM bidjob WHERE job_id = {$job_id};";
                    // $q_total_num = $this->db->query($sql);
                    // $total_num = $q_total_num->row();
                    //
                    // $sql = "SELECT COUNT(*) AS total_completed FROM bidjob b JOIN bidjob_details bd ON bd.bidjob_id = b.id WHERE b.job_id = {$job_id} AND bd.is_completed = 1";
                    // $q_total_completed = $this->db->query($sql);
                    // $total_completed = $q_total_completed->row();
                    //
                    // if ($total_num->total_num == $total_completed->total_completed) {
                    //     $sql = "UPDATE bidjob SET  stage = 2, complete_date=NOW() WHERE id = '" . $bidjob_id . "'";
                    //     $this->db->query($sql);
                    // } else {
                    //     $sql = "UPDATE jobpost SET jobDone = 0 WHERE id = {$job_id}";
                    //     $this->db->query($sql);
                    // }


                    // marked awarded job as completed
                    $this->db->select('p2.*')->from('proofread_jobs_docs as p1');
                    $this->db->join('proofread_jobs_awarded as p2', 'p2.proofread_doc_id = p1.id');
                    $this->db->join('proofread_jobs as p3', 'p3.id = p1.proofread_job_id');
                    $this->db->where(array(
                        'p2.proofreader_id' => $trans_id,
                        'p3.job_id' => $job_id
                    ));

                    $query = $this->db->get();

                    if ($query->num_rows()) {
                        foreach ($query->result_array() as $row) {
                            $this->db->update('proofread_jobs_awarded',
                                array('review_stage' => 3, 'is_completed' => 1, 'complete_date' => date('Y-m-d H:i:s')),
                                array('id' => $row['id'])
                            );
                        }
                    }

                    // check if all proof reading job is completed
                    $sql = "SELECT COUNT(*) total_awarded, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) AS num_completed
                            FROM proofread_jobs_docs p1
                             JOIN proofread_jobs_awarded p2 ON p2.proofread_doc_id = p1.id
                             JOIN proofread_jobs p3 ON p3.id = p1.proofread_job_id
                            WHERE p3.job_id = {$job_id}";

                    $completed_obj = $this->db->query($sql);

                    if ($completed_obj->num_rows()) {
                        $completed = $completed_obj->row();

                        if ($completed->total_awarded == $completed->num_completed) {
                            $this->db->update('proofread_jobs', array('review_stage' => 3, 'translation_completed' => date('Y-m-d H:i:s')), array('job_id' => $job_id));

                            $this->db->update('bidjob', array('is_rated' => 1), array('job_id' => $job_id));
                        }
                    }


                    $invoice_update_sql = "SELECT * FROM invoice WHERE bid_id='" . $bidjob_id . "'  AND job_id='" . $job_id . "' AND trans_id='" . $trans_id . "' ";
                    $invoice_update_query = $this->db->query($invoice_update_sql);
                    $invoice_update_num = $invoice_update_query->num_rows();

                    if ($invoice_update_num > 0) {
                        $invoice_update_fetch = $invoice_update_query->row();
                        $invoice_update_id = $invoice_update_fetch->id;

                        $data_to_update = array(
                            'bid_id' => $bidjob_id,
                            'job_id' => $job_id,
                            'trans_id' => $trans_id,
                            'description' => '',
                            'created' => date('Y-m-d H:i:s')
                        );

                        $this->db->where('id', $invoice_update_id);
                        $this->db->update('invoice', $data_to_update);
                    } else {
                        $data_to_store = array(
                            'bid_id' => $bidjob_id,
                            'invoice_id' => $invoice_id,
                            'job_id' => $job_id,
                            'trans_id' => $trans_id,
                            'description' => '',
                            'created' => date('Y-m-d H:i:s')
                        );

                        $this->load->model('adminjobpost_model');
                        $this->adminjobpost_model->store_invoice($data_to_store);
                    }

                    $this->session->set_flashdata('success_message', 'Working job Completed');
                    redirect('admin_review/working/');
                }
            } else {
                $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
                redirect('admin_review/working/');
            }
        } else {
            redirect('admin_review/working/');
        }
    }

    public function viewsummary()
    {
        if ($this->session->userdata('is_admin')) {
            $data['prompt'] = $this->input->get('prompt');
            $this->load->view('admin/vwReviewSummary', $data);
        }
    }

    public function get_translator()
    {
        if ($this->session->userdata('is_admin')) {
            try {
                $q = $this->input->get('term');

                $sql = "SELECT id, CONCAT(last_name, ', ', first_name) AS translator_name FROM translator ";

                if ($q) {
                    $sql .= "WHERE first_name LIKE '%{$q}%' or last_name LIKE '%{$q}%'";
                }

                $sql .= "ORDER BY last_name ASC";

                $query = $this->db->query($sql);

                $data = null;

                foreach ($query->result_array() as $i => $translator) {
                    $data[$i]['id'] = $translator['id'];
                    $data[$i]['value'] = $translator['translator_name'];
                }

            } catch (Exception $e) {
                $this->session->set_flashdata('error_message', $e->getMessage());
                redirect('admin_review/working/');
            }

            echo json_encode($data);
            exit;
        } else {
            $this->session->set_flashdata('error_message', "You're not authorized in this page");
            redirect('admin_review/working/');
        }
    }

    public function get_review_job_details()
    {
        if ($this->session->userdata('is_admin')) {
            try {

                $id = (int)$this->input->get('id');
                $jobid = (int)$this->input->get('jobid');
                $transid = (int)$this->input->get('transid');
                $bidjob_id = (int)$this->input->get('bidjob');

                $sql = "SELECT pd.*, p.id AS proofread_job_id, pd.id AS proofread_job_doc_id, p.job_id FROM proofread_jobs p ";
                $sql .= " JOIN proofread_jobs_docs pd ON pd.proofread_job_id = p.id ";
                $sql .= "WHERE p.job_id = {$jobid}";
                $sql .= " AND pd.is_awarded = 0";
                $sql .= " AND pd.is_active = 1 ";
                $sql .= "ORDER BY pd.doc_order ASC;";

                $query = $this->db->query($sql);

                $str = '<form id="form-docs" method="post" action="' . base_url() . 'admin_review/award_review_doc">';
                $str .= '<table id="dynamic-table" class="table table-striped table-bordered table-hover">';

                $str .= '<thead>';
                $str .= '  <tr>';
                $str .= '    <td style="text-align: center;"><input type="checkbox" id="toggle-select-all"></td>';
                $str .= '    <td style="text-align: center;">#</td>';
                $str .= '    <td>Original File</td>';
                $str .= '    <td>Translated File</td>';
                $str .= '  </tr>';
                $str .= '</thead>';
                $str .= '<tbody>';

                if ($query->num_rows()) {
                    foreach ($query->result_array() as $i => $qry) {
                        $original = explode('/', $qry['original_file']);
                        $translated = explode('/', $qry['translated_file']);

                        $str .= '<tr>';
                        $str .= '  <td style="text-align: center"><input type="checkbox" id="" name="doc[]" value="' . $qry['proofread_job_id'] . ',' . $transid . ',' . $bidjob_id . ',' . $qry['proofread_job_doc_id'] . '"></td>';
                        $str .= '  <td style="text-align: center">' . $qry['doc_order'] . '</td>';
                        $str .= '  <td><label for="' . $qry['id'] . '">' . $original[1] . '</label></td>';
                        $str .= '  <td><label for="' . $qry['id'] . '">' . $translated[1] . '</label></td>';
                        $str .= '</tr>';
                    }
                } else {
                    $str .= '<tr>';
                    $str .= '  <td colspan="4">No documents to award</td>';
                    $str .= '</tr>';
                }

                $str .= '</tbody>';
                $str .= '</table>';
                $str .= '<input type="hidden" name="job_id" value="' . $jobid . '">';
                $str .= '</form>';

                echo $str;
                exit;

            } catch (Exception $e) {
                $this->session->set_flashdata('error_message', $e->getMessage());
                redirect('admin_review/viewsummary/' . $jobid);
            }
        } else {
            $this->session->set_flashdata('error_message', "You're not authorized in this page");
            redirect('admin_review/working/');
        }

    }

    public function unaward_check_if_invoiced()
    {
        if ($this->session->userdata('is_admin')) {
            $job_id = $this->input->get('job_id');
            $bid_id = $this->input->get('bidjob_id');
            $trans_id = $this->input->get('trans_id');

            $invoice = $this->db->from('invoice')->where(array('bid_id' => $bid_id, 'job_id' => $job_id, 'is_deleted' => 0))->get();

            if ($invoice->num_rows()) {
                $translator = $this->db->from('translator')->where('id', $trans_id)->get();
                $bidjob = $this->db->from('bidjob')->where('id', $bid_id)->get();

                if ($translator->num_rows()) {
                    $data['data']['translator_name'] = $translator->row()->first_name . ' ' . $translator->row()->last_name;
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

        $data['redirect'] = base_url('admin_review/viewsummary/' . $job_id);

        echo json_encode($data);
        exit;
    }

    public function unaward_review_doc()
    {
        if ($this->session->userdata('is_admin')) {
            try {
                $form = $this->input->get('form');
                $job_id = $this->input->get('job_id');
                $bid_id = $this->input->get('bidjob_id');
                $trans_id = $this->input->get('trans_id');
                $proofread_job_id = $this->input->get('proofread_job_id');
                $proofread_doc_id = $this->input->get('proofread_doc_id');

                $this->db->update('bidjob', array('is_done' => 0, 'is_rated' => 0, 'show_notification' => 0, 'stage' => 1, 'awarded' => 0, 'award_date' => null, 'working_date' => null), array('id' => $bid_id));
                $this->db->update('proofread_jobs', array('review_stage' => 1), array('id' => $proofread_job_id, 'job_id' => $job_id));
                $this->db->update('proofread_jobs_docs', array('is_awarded' => 0), array('proofread_job_id' => $proofread_job_id));
                $this->db->update('jobpost', array('modified' => date('Y-m-d H:i:s')), array('id' => $job_id));

                $this->db->where('bidjob_id', $bid_id)->delete('bidjob_details');
                $this->db->where(array('proofread_doc_id' => $proofread_doc_id))->delete('proofread_jobs_awarded');
                $this->db->where(array('job_id' => $job_id, 'bidjob_id' => $bid_id))->delete('ratings');
                $this->db->update('invoice', array('is_deleted' => 1), array('job_id' => $job_id, 'bid_id' => $bid_id));

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


            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        } else {
            echo "You're not authorized in this page";
            exit;
        }

        $redirect_url = base_url('admin_review/viewsummary/' . $job_id);
        echo $redirect_url;
    }

    public function award_review_doc()
    {
        if ($this->session->userdata('is_admin')) {
            try {
                $data = $this->input->post('doc');
                $job_id = $this->input->post('job_id');

                if (!is_array($data)) {
                    redirect('admin_review/viewsummary/' . $job_id);
                }

                // echo '<pre>'; print_r($data); echo '</pre>'; exit;

                if (isset($data[0])) {
                    foreach ($data as $key => $value) {
                        $ids = explode(',', $value);
                        $today = date('Y-m-d H:i:s');

                        $this->db->insert('bidjob_details',
                            array(
                                'bidjob_id' => $ids[2],
                                'translator_id' => $ids['1'],
                                'proofread_doc_id' => $ids[0],
                                'is_active' => 1
                            )
                        );

                        $this->db->update('bidjob',
                            array(
                                'show_notification' => 1,
                                'stage' => 1,
                                'awarded' => 1,
                                'award_date' => $today,
                                'working_date' => $today,
                                'awarded_admin_id' => ($this->session->userdata('admin_id')) ? $this->session->userdata('admin_id') : 1,
                            ),
                            array(
                                'id' => $ids[2]
                            )
                        );

                        $this->db->update('jobpost', array('modified' => date('Y-m-d H:i:s')), array('id' => $job_id));

                        // update proofreading detials
                        $this->db->update('proofread_jobs_docs', array('is_awarded' => 1), array('id' => $ids[3]));

                        $this->db->update('proofread_jobs', array('review_stage' => 2), array('id' => $ids[0]));

                        $this->db->insert('proofread_jobs_awarded', array(
                            'proofread_doc_id' => $ids[3],
                            'proofreader_id' => $ids[1],
                            'review_stage' => 2
                        ));

                        // post message in chat box
                        $sql = "SELECT b.job_id, j.name, j.stage,j.description,j.created,j.alias FROM bidjob b JOIN jobpost j ON j.id = b.job_id WHERE b.id = {$ids[2]}";
                        $query = $this->db->query($sql);
                        $row = $query->row();
                        $mailTo = $this->db->get_where('translator', ['id' => $ids['1']])->first_row();
                        $data['name'] = $mailTo->first_name . ' ' . $mailTo->last_name;
                        $data['job_id'] = $job_id;
                        $data['bid_id'] = $ids[2];
                        $data['trans_id'] = $ids[1];
                        $data['job_name'] = $row->name;
                        $data['job_description'] = $row->description;
                        $data['job_created'] = $row->created;
                        $data['job_alias'] = $row->alias;
                        $this->email->set_mailtype("html");
                        $this->email->from('info@montesinotranslation.com');
                        $this->email->to($mailTo->email_address);
                        $this->email->subject('Review Job Award Confirmation');
                        $html_email = $this->load->view('email/vwTranslatorAwardConfirmation', $data, true);
                        $this->email->message($html_email);
                        $this->email->send();

                        $post_to_chat_box = [
                            'bid_id' => $ids[2],
                            'job_id' => $row->job_id,
                            'trans_id' => $ids[1],
                            'type' => 'admin',
                            'status' => 'unread',
                            'jobname' => $row->name,
                            'userID' => 1,
                            'userName' => 'Guest',
                            'channel' => 1,
                            'dateTime' => date('Y-m-d H:i:s'),
                            'text' => 'You have been awarded this job, please coordinate with the admin to proceed',
                            'ip' => '127.0.0.1'
                        ];

                        $this->db->insert('ajax_chat_messages', $post_to_chat_box);
                    }

                    $this->session->set_flashdata('success_message', 'Review Job Awarded');

                    if ($row->stage == 0) {
                        $redirect_url = 'admin_review/viewsummary/' . $job_id . '?prompt=1';
                    } else {
                        $redirect_url = 'admin_review/viewsummary/' . $job_id;
                    }

                    redirect($redirect_url);
                }

            } catch (Exception $e) {
                $this->session->set_flashdata('error_message', $e->getMessage());
                redirect('admin_review/working/');
            }
        } else {
            $this->session->set_flashdata('error_message', "You're not authorized in this page");
            redirect('admin_review/working/');
        }

    }

    public function delete()
    {
        if ($this->session->userdata('is_admin')) {
            $id = $this->uri->segment(3);
            $page = $this->uri->segment(4) ? $this->uri->segment(4) : 'hiring';

            $sql = "SELECT * FROM jobpost j";
            $sql .= " LEFT JOIN proofread_jobs p1 ON p1.job_id = j.id";
            $sql .= " LEFT JOIN proofread_jobs_docs p2 ON p2.proofread_job_id = p1.id ";
            $sql .= "WHERE j.id = {$id}";

            $val = $this->db->query($sql);
            $row = $val->row_array();

            if ($row['original_file']) {
                $filename = explode('/', $row['original_file']);
                $path = './uploads/review/' . $filename[0];
                unlink($path);
            }

            $this->adminjobpost_model->delete_jobpost($id);
            $this->session->set_flashdata('success_message', 'Successfully Deleted');
            redirect('admin_review/' . $page);
        } else {
            $this->session->set_flashdata('error_message', ' Not Deleted');
            redirect('admin_review/hiring');
        }
    }

    public function delete_document_from_job()
    {
        if ($this->session->userdata('is_admin')) {
            $id = $this->input->get('id');

            $return = $this->db->update('proofread_jobs_docs', array('is_active' => 0), array('id' => $id));

            if ($return) {
                echo json_encode(array('status' => true));
            }
        }
    }

    public function get_job_price()
    {
        $line_month = $this->input->get('line_month');
        $line_year = $this->input->get('line_year');
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

        echo $data;
        exit;
    }

    public function check_line_numbers()
    {
        $line_month = $this->input->get('line_month');
        $line_year = $this->input->get('line_year');
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
                    'job_name' => ($check_obj->name != '') ? $check_obj->name : 'Job Manually Entered',
                    'language_from' => $from_lang->name,
                    'language_to' => $to_lang->name,
                    'price' => $check_obj->price,
                    'date_added' => date('jS F Y', strtotime($check_obj->created))
                ];

                $data = json_encode($data_string);
            } else {
                $data = null;
            }
        } else {
            $data = null;
        }


        echo $data;
        exit;
    }

    private function upload_documents($review_job_id, $translator)
    {
        try {
            $this->load->library('upload');
            $files = $_FILES;
            $cpt = count($_FILES['document']['name']['original']);
            $ref = array('original', 'translated');

            $db_data = null;

            $folder_name = time();

            foreach ($ref as $_ref) {
                for ($i = 0; $i < $cpt; $i++) {
                    if (strpos($files['document']['name'][$_ref][$i], '.') != false) {
                        $ext = '.' . end(explode('.', $files['document']['name'][$_ref][$i]));
                    } else {
                        $ext = '';
                    }
                    if (!preg_match('/[^\x20-\x7f]/', $files['document']['name'][$_ref][$i])) {
                        $_FILES['documents']['name'] = $files['document']['name'][$_ref][$i];
                    } else {
                        $_FILES['documents']['name'] = time() . rand(111, 999) . $ext;
                    }
                    $_FILES['documents']['type'] = $files['document']['type'][$_ref][$i];
                    $_FILES['documents']['tmp_name'] = $files['document']['tmp_name'][$_ref][$i];
                    $_FILES['documents']['error'] = $files['document']['error'][$_ref][$i];
                    $_FILES['documents']['size'] = $files['document']['size'][$_ref][$i];
                    $this->upload->initialize($this->set_upload_options($folder_name));

                    if (!$this->upload->do_upload('documents')) {
                        $this->session->set_flashdata('error_message', $this->upload->display_errors());
                        redirect('admin_review/add');
                    }

                    // populate data to save in database
                    $db_data[$i][] = $_FILES['documents']['name'];
                }
            }
            // link documents to review job
            if (!is_null($db_data)) {

                $sql = "SELECT doc_order FROM proofread_jobs_docs WHERE proofread_job_id = {$review_job_id} ORDER BY doc_order DESC LIMIT 1;";
                $query = $this->db->query($sql);

                if ($query->num_rows()) {
                    $proofread_job_docs_obj = $query->row();
                    $doc_order = $proofread_job_docs_obj->doc_order + 1;
                } else {
                    $doc_order = 1;
                }

                foreach ($db_data as $i => $data) {
//                    $value_string = '';

                    $original_file = "{$folder_name}/{$data[0]}";
                    $translated_file = "{$folder_name}/{$data[1]}";

//                    foreach ($data as $d) {
//                        $value_string .= "'{$folder_name}/{$d}', ";
//                    }

//                    $value_string = trim($value_string, ', ');
//                    $sql = "INSERT INTO proofread_jobs_docs (proofread_job_id, original_file, translated_file, translator_id, doc_order) VALUES ({$review_job_id}, {$value_string}, ".$translator[$i].",".$doc_order.");";
//
//                    $this->db->query($sql);

                    $this->db->insert('proofread_jobs_docs', array(
                        'proofread_job_id' => $review_job_id,
                        'original_file' => $original_file,
                        'translated_file' => $translated_file,
                        'translator_id' => $translator[$i],
                        'doc_order' => $doc_order
                    ));

                    $doc_order++;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Error Processing uploads", 1);
        }
    }

    private function set_upload_options($folder_name)
    {
        $upload_dir = './uploads/review/' . $folder_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, '755', true);
        }

        //upload an image options
        $config = array();

        $config['upload_path'] = $upload_dir . '/';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
//        $config['allowed_types'] = 'jpg|jpeg|ai|docx|xls|xlsx|ppt|pptx|png|gif|doc|pdf|zip|tar|txt';
//        $config['max_size']      = 0;


        return $config;
    }

    public function edit()
    {
        if ($this->session->userdata('is_admin')) {
            $id = $this->uri->segment(3);

            $sql = "SELECT * FROM `jobpost` where `id`='$id' ";
            $qry = $this->db->query($sql);

            if ($qry->num_rows() == '1') {
                $data['fetch'] = $qry->row();
                $this->load->view('admin/vwReviewJobDetails', $data);
            }
        } else {
            $this->session->set_flashdata('error_message', 'Not Permitted');
            redirect('admin_review/index');
        }
    }

    public function editprofile()
    {
        if (!$this->session->userdata('is_admin')) {
            $this->load->view('admin/vwLogin');
        } else {
            $job_id = $this->uri->segment(3);



$sql1="select * from jobpost where id='$job_id'";
                            $val1 = $this->db->query($sql1);
                            $check=$val1->num_rows();

                            if ($check==1) {
								$rows = $val1->row();

                                $job_name=$rows->name;
                                $job_desc=$rows->description;
                                $job_alias=$rows->alias;

                                $job_language=$rows->language;
                                /*$inIds = "'".str_replace("/", "','", $job_language)."'";
                                $sql_lan="SELECT name FROM `languages` WHERE `id` IN(".$inIds.")";

                                $val_lan=$this->db->query($sql_lan);
                                $lang=$val_lan->result_array();
                                $lang2=$lang[0]['name'].' to '.$lang[1]['name'];*/
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
                                if($this->input->post("proofreadType") == 'editing'){
                                    $sql = "SELECT * FROM translator WHERE language LIKE '%".$this->input->post('job_language',true)."%'";
                                }else {
                                    $lang = $this->input->post('language_from') . "/" . $this->input->post('job_language');
                                    $lang_reverse = $this->input->post('job_language').'/'.$this->input->post('language_from');
                                    //echo $this->input->post('job_language');exit;
                                    $sql = "SELECT * FROM translator WHERE (language LIKE '%," . $lang . ",%' OR language LIKE '%".$lang_reverse."%')";
                                }
                               // echo $sql;exit;
                                $val = $this->db->query($sql);
                                $row_email = $val->result();
//var_dump($row_email);exit;
								
								$proofread_required=$this->input->post("proofread_required");
								$proofreadType=$this->input->post("proofreadType");

                                $data = array(
                                    'job_name' => $job_name,
                                    'job_id'  => $job_id,
                                    'description' => $job_desc,
                                    'lang_from' => $from_lang,
                                    'lang_to' => $to_lang,
									'proofread_required' => $proofread_required,
									'proofreadType' => $proofreadType,
                                    'job_alias' => $job_alias,
									
                                    'translate_to'=>$lang2,
                                    'due_date' => $this->input->post('dueDate').' '.$this->input->post('hour').':'.$this->input->post('minute').' '.$this->input->post('ampm'),
                                    'created' => date('Y-m-d H:i:s')
                                );
								//print_r($row_email);exit;

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







            $this->form_validation->set_rules('job_title', 'Job Title', 'trim|required');
            $this->form_validation->set_rules('lineNumber', 'Line Number', 'trim|required');
            $this->form_validation->set_rules('job_price', 'Job Price', 'trim|required|numeric');
            $this->form_validation->set_rules('job_description', 'Job Description', 'trim|required');
            $this->form_validation->set_rules('dueDate', 'Due Date', 'trim|required');

            if (empty($_FILES['document']['name']['original'][0])) {
                $this->form_validation->set_rules('original', 'original file', 'trim');
            }

            if (empty($_FILES['document']['name']['translated'][0])) {
                $this->form_validation->set_rules('translated', 'translated file', 'trim');
            }

            $this->form_validation->set_rules('translator', 'translator', 'trim');


            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('flash_error', 'Error Validation');
            } else {
                $a = $this->input->post('stage');
                $alias = $this->input->post('job_alias');
                $prefile = $this->input->post('prefile');
                $newfile = $prefile . $this->input->post('totalFile');
                $str = $this->UrlAlias($alias, 'jobpost', $job_id);

                $line_month = $this->input->post('lineMonth') ? $this->input->post('lineMonth') : $this->input->post('_lineMonth');
                $line_year = $this->input->post('lineYear') ? $this->input->post('lineYear') : $this->input->post('_lineYear');
                $line_number = $this->input->post('lineNumber') ? $this->input->post('lineNumber') : $this->input->post('_lineNumber');

                $line_number_code = 'M' . $line_month . $line_year . 'L' . $line_number;

//                if ($this->input->post('remaining_balance') != '') {
//                    $price = $this->input->post('remaining_balance');
//                } else {
//                    $price = $this->input->post('price');
//                }

                $is_posted = $this->db->from('jobpost')->where('id', $job_id)->get();
                $date_posted_value = '';

                $today = date('Y-m-d H:i:s');

//                if ($is_posted->num_rows()) {
//                    if ($is_posted->row()->date_posted == '0000-00-00 00:00:00') {
//                        $date_posted_value = "`date_posted` = '{$today}', ";
//                    }
//                }

                if (strpos($this->input->post('job_title'), 'Review Job: ') === false) {
                    $job_name = 'Review Job: ' . $this->input->post('job_title');
                } else {
                    $job_name = $this->input->post('job_title');
                }

                $val = $this->db->update('jobpost',
                    array(
                        'name' => $job_name,
                        'description' => $this->input->post('job_description'),
                        'job_type' => $this->input->post('type'),
                        'language' => $this->input->post('language_from') . "/" . $this->input->post('job_language'),
                        'lineNumber' => $line_number,
                        'lineMonth' => $line_month,
                        'lineYear' => $line_year,
                        'lineNumberCode' => $line_number_code,
                        'dueDate' => $this->input->post('dueDate') . ' ' . $this->input->post('hour') . ':' . $this->input->post('minute') . ' ' . $this->input->post('ampm'),
                        'price' => $this->input->post('job_price'),
                        'file' => $newfile,
                        'proofread_required' => $this->input->post("proofread_required"),
                        'proofreadType' => $this->input->post("proofreadType"),
                        'modified' => $today,
                        'approval_status' => 1,
                        'date_posted' => ($is_posted->row()->date_posted == '0000-00-00 00:00:00') ? $today : ''
                    ),
                    array(
                        'id' => $job_id
                    )
                );

//                $sql = "UPDATE `jobpost` SET
//                    `name`   = '".$job_name."',
//                    `description`   = '". $this->input->post('job_description') ."',
//                    `job_type`    = '". $this->input->post('type') ."',
//                    `language`   = '".$this->input->post('language_from')."/". $this->input->post('job_language') ."',
//                    `lineNumber` = ".$line_number.",
//                    `lineMonth` = '".$line_month."',
//                    `lineYear` = '".$line_year."',
//                    `lineNumberCode` = '".$line_number_code."',
//                    `dueDate` = '".$this->input->post('dueDate').' '.$this->input->post('hour').':'.$this->input->post('minute').' '.$this->input->post('ampm')."',
//                    `price`    = '". $this->input->post('job_price') ."',
//                    `file`    = '".$newfile."',
//                    `proofread_required` = ".$this->input->post("proofread_required").",
//                    `proofreadType` = '".$this->input->post("proofreadType")."',
//                    {$date_posted_value}
//                    `modified`    = '". $today ."'
//                    WHERE `id` = '" .$job_id. "'";
//
//                    // print_r($sql); exit;
//
//                $val = $this->db->query($sql);

                if ($val == TRUE) {
                    $sql1 = "SELECT * FROM jobpost WHERE id='" . $job_id . "' AND job_type=0";
                    $val1 = $this->db->query($sql1);
                    $check = $val1->num_rows();

                    if (isset($_FILES['document']['name']['original'][0]) and $_FILES['document']['name']['original'][0] != '') {

                        $this->db->update('jobpost', array('proofread_required' => 1), array('id' => $val1->row()->id));

                        $sql = "SELECT * FROM proofread_jobs WHERE job_id = {$job_id}";
                        $query = $this->db->query($sql);

                        if ($query->num_rows()) {
                            $this->db->update('proofread_jobs', array('review_stage' => 2, 'review_type' => 1, 'totalfiles' => $this->input->post('totalFile')), array('job_id' => $job_id));
                            $proofread_job = $query->row();

                            $proofread_job_id = $proofread_job->id;
                        } else {
                            // store details in proofread_jobs table
                            $proofread_jobs_data = array(
                                'job_id' => $job_id,
                                'translator_id' => '',
                                'translation_completed' => 0,
                                'review_price' => 0,
//                                'review_stage' => 2,
                                'review_type' => 1,
                                'totalfiles' => $this->input->post('totalFile')
                            );

                            $proofread_job_id = $this->adminjobpost_model->store_proofread_jobs($proofread_jobs_data);








				


                        }

                        $this->upload_documents($proofread_job_id, $_POST['document']['translator']);
                    }

                    $this->session->set_flashdata('success_message', 'Successfully Updated');
                } else {
                    $this->session->set_flashdata('error_message', 'Not Updated');
                }
            }

            redirect('admin_review/edit/' . $job_id);

        }
    }

    public function undo_complete()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";

            $bid_id = $this->uri->segment(3);
            $job_id = $this->uri->segment(4);
            $proofread_job_award_id = $this->uri->segment(5);

            if ($bid_id != '' and $job_id != '' and $proofread_job_award_id != '') {
                $this->db->trans_start(true);
                $this->db->update('jobpost', array('stage' => 0, 'approval_status' => 1), array('id' => $job_id));
                $this->db->update('invoice', array('is_deleted' => 1), array('job_id' => $job_id, 'bid_id' => $bid_id));
//                $this->db->update('bidjob',['awarded' => 1,'stage' => 1, 'is_done' => 0],['id' => $bid_id]);
                $this->db->update('proofread_jobs', array('review_stage' => 2, 'modified' => date('Y-m-d H:i:s')), array('job_id' => $job_id));
                $this->db->update('proofread_jobs_awarded', array('review_stage' => 2, 'is_completed' => 0), array('id' => $proofread_job_award_id));
                $this->db->trans_complete();
                if ($this->db->trans_status() != false) {
                    $this->session->set_flashdata('success_message', 'Awarded Job marked as Not Compleated');
                } else {
                    $this->session->set_flashdata('error_messgae', $this->db->_error_message());
                }
                $referrer = $this->agent->referrer();
                redirect($referrer);
            } else {
                $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
                $referrer = $this->agent->referrer();
                redirect($referrer);
            }
        } else {
            redirect('admin/index');
        }
    }

    public function award_complete()
    {
        if ($this->session->userdata('is_admin')) {
            $admin_id = $this->session->userdata('admin_id');
            $data['message_error'] = "";
            $data['message_success'] = "";
            //artist id
            $id = $this->uri->segment(3);
            $job_id = $this->uri->segment(4);

            if ($id != '') {
                $date = date('Y-m-d H:i:s');
                $sql = "UPDATE `bidjob` SET  `awarded`='1', is_done = 1, `stage` = '2',completed_admin_id= " . $admin_id . ", `complete_date`='$date' WHERE `id` = '" . $id . "'";
                $val = $this->db->query($sql);

                if ($val) {
                    $transql = "select `trans_id`,`price` from `bidjob` where `id`='$id'";
                    $tranval = $this->db->query($transql);
                    $tranfetch = $tranval->row();
                    $trans_id = $tranfetch->trans_id;
                    $price = $tranfetch->price;
                    //echo $trans_id;die;

                    $jobsql = "select * from `jobpost` where `id`='$job_id'";
                    $jobval = $this->db->query($jobsql);
                    $jobfetch = $jobval->row();
                    $job_name = $jobfetch->name;
                    $job_description = $jobfetch->description;
                    $job_created = $jobfetch->created;
                    $job_alias = $jobfetch->alias;

                    $emailsql = "select * from `translator` where `id`='$trans_id'";
                    $emailval = $this->db->query($emailsql);
                    $emailfetch = $emailval->row();
                    $trans_email = $emailfetch->email_address;
                    $trans_name = $emailfetch->first_name . '&nbsp;' . $emailfetch->last_name;

                    $data['name'] = $trans_name;
                    $data['job_name'] = $job_name;
                    $data['job_description'] = $job_description;
                    $data['job_created'] = $job_created;
                    $data['job_alias'] = $job_alias;
                    //$data['invoice'] =$invoice;

                    $mailTo = $trans_email;
                    $mailName = $trans_name;
                    $this->email->set_mailtype("html");
                    $this->email->from('info@montesinotranslation.com');
                    $this->email->to($mailTo);
                    $this->email->subject('Award Job Completion');
                    $html_email = $this->load->view('email/vwTranslatorAwardCompletion', $data, true);
                    $this->email->message($html_email);
                    $this->email->send();

                    $invoice_id = time();
                    $data['invoice'] = $invoice_id;
                    $data['price'] = $price;
                    $invoicesql = "select * from  invoice where bid_id=$id";
                    $query = $this->db->query($invoicesql);
                    $inv = $query->num_rows();

                    if ($inv > 1) {
                        $data_to_store = array(
                            'invoice_id' => $invoice_id,
                            'job_id' => $job_id,
                            'trans_id' => $trans_id,
                            'modified' => date('Y-m-d H:i:s')
                        );
                        $this->db->update('invoice', $data_to_store, array('bid_id' => $id));

                    } else {
                        $data_to_store = array(
                            'bid_id' => $id,
                            'invoice_id' => $invoice_id,
                            'job_id' => $job_id,
                            'trans_id' => $trans_id,
                            'created' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('invoice', $data_to_store);
                    }

                    $mailTo = $trans_email;
                    $mailName = $trans_name;
                    $this->email->set_mailtype("html");
                    $this->email->from('info@montesinotranslation.com');
                    $this->email->to($mailTo);
                    $this->email->subject('Award Job Completion Invoice');
                    $html_email = $this->load->view('email/vwTranslatorAwardCompletioninvoice', $data, true);
                    $this->email->message($html_email);
                    $this->email->send();


                    $this->session->set_flashdata('success_message', ' Awarded job Completed');
                    $referrer = $this->agent->referrer();
                    redirect($referrer);
                }
            } else {
                $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
                $referrer = $this->agent->referrer();
                redirect($referrer);
            }
        } else {
            redirect('admin/index');
        }
    }

    public function admin_notif()
    {
        if ($this->session->userdata('is_admin') && $this->session->userdata('admin_id') != false) {
            $admin_id = $this->session->userdata('admin_id');
            $job_id = $this->input->post('job_id');
            $bidjob_id = $this->input->post('bidjob_id');
            $trans_id = $this->input->post('trans_id');

            $jobpost_obj = $this->db->from('jobpost')->where('id', $job_id)->get();
            $translator_obj = $this->db->from('translator')->where('id', $trans_id)->get();

            if ($jobpost_obj->num_rows() and $translator_obj->num_rows()) {

                // marked awarded job as completed
                $this->db->select('p2.*')->from('proofread_jobs_docs as p1');
                $this->db->join('proofread_jobs_awarded as p2', 'p2.proofread_doc_id = p1.id');
                $this->db->join('proofread_jobs as p3', 'p3.id = p1.proofread_job_id');
                $this->db->where(array(
                    'p2.proofreader_id' => $trans_id,
                    'p3.job_id' => $job_id
                ));

                $query = $this->db->get();

                if ($query->num_rows()) {
                    foreach ($query->result_array() as $row) {
                        $this->db->update('proofread_jobs_awarded',
                            array('review_stage' => 3, 'is_completed' => 1, 'complete_date' => date('Y-m-d H:i:s')),
                            array('id' => $row['id'])
                        );
                    }
                }

                // check if all proof reading job is completed
                $sql = "SELECT COUNT(*) total_awarded, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) AS num_completed
                        FROM proofread_jobs_docs p1
                         JOIN proofread_jobs_awarded p2 ON p2.proofread_doc_id = p1.id
                         JOIN proofread_jobs p3 ON p3.id = p1.proofread_job_id
                        WHERE p3.job_id = {$job_id}";

                $completed_obj = $this->db->query($sql);

                if ($completed_obj->num_rows()) {
                    $completed = $completed_obj->row();

                    if ($completed->total_awarded == $completed->num_completed) {
                        $this->db->update('proofread_jobs', array('review_stage' => 3, 'translation_completed' => date('Y-m-d H:i:s')), array('job_id' => $job_id));

                        $this->db->update('bidjob', array('is_rated' => 1, 'completed_admin_id' => $admin_id), array('job_id' => $job_id));
                    }
                }

                // check if job already has a rating
                $is_rated = $this->db->from('ratings')->where('job_id', $job_id)->get();

                if ($is_rated->num_rows()) {
                    $this->db->update('bidjob', array('stage' => 2, 'is_done' => 1, 'is_rated' => 1, 'complete_date' => date('Y-m-d H:i:s'), 'completed_admin_id' => $admin_id), array('id' => $bidjob_id));
                } else {
                    $this->db->update('bidjob', array('stage' => 2, 'is_done' => 1, 'is_rated' => 0, 'complete_date' => date('Y-m-d H:i:s'), 'completed_admin_id' => $admin_id), array('id' => $bidjob_id));
                }
                $invoice_id = time();
                $this->db->insert('invoice', array(
                    'bid_id' => $bidjob_id,
                    'invoice_id' => $invoice_id,
                    'job_id' => $job_id,
                    'trans_id' => $trans_id,
                    'payment_date' => date('Y-m-d', strtotime('+30 days'))
                ));

                $trans_email = $this->db->get_where('translator',['id'=> $trans_id])->first_row();
                $mailName = $trans_email->first_name.' '.$trans_email->last_name;
                $job_data = $this->db->get_where('jobpost',['id' =>$job_id])->first_row();
                $bid_data = $this->db->get_where('bidjob',['id' => $bidjob_id])->first_row();
                $mailTo = $trans_email->email_address;
                $data['name'] = $mailName;
                $data['job_name'] = $job_data->name;
                $data['job_description'] = $job_data->desc;
                $data['job_id'] = $job_id;
                $data['job_created'] = $job_data->created;
                $data['job_alias'] = $job_data->alias;

                $this->email->set_mailtype("html");
                $this->email->from('info@montesinotranslation.com');
                $this->email->to($mailTo);
                $this->email->subject('Award Job Completion');
                $html_email = $this->load->view('email/vwTranslatorAwardCompletion', $data, true);
                $this->email->message($html_email);
                // $this->email->send();

                $data['invoice_id'] = $invoice_id;
                $data['name'] = $mailName;
                $data['job_title'] = $job_data->name;
                //$data['invoice_desc'] =$invoice_desc;
                $data['award_date'] = date('jS F, Y',strtotime($bid_data->award_date));
                $data['complete_date'] = date('jS F, Y',strtotime($bid_data->complete_date));
                $data['job_alias'] = $job_data->alias;
                $data['job_price'] = $job_data->price;


                $this->email->set_mailtype("html");
                $this->email->from('info@montesinotranslation.com');
                $this->email->to($mailTo);
                $this->email->subject('Job Completion Invoice');
                $html_email = $this->load->view('email/vwJobCompletionInvoice', $data, true);
                $this->email->message($html_email);
                $mail = $this->email->send();

                /********************************************/

                $job_post = $jobpost_obj->row();
                $translator = $translator_obj->row();

                $translator_name = "{$translator->first_name} {$translator->last_name}";

                $data = array(
                    'translator_name' => $translator_name,
                    'job_name' => $job_post->name,
                    'url' => $this->config->base_url() . "chat-box/?bid_id={$bidjob_id}&job_id={$job_id}&trans_id={$trans_id}&type=user&auto={$trans_id}"
                );

//                $mailTo = $translator->email_address;
//                $mailName = $translator_name;
//                $this->email->set_mailtype("html");
//                $this->email->from('info@montesinotranslation.com');
//                $this->email->to($mailTo);
//                $this->email->subject('Proofread Reminder');
//                $html_email = $this->load->view('email/vwRequestProofreadJob', $data, true);
//                $this->email->message($html_email);
//                $this->email->send();

                $this->db->update('bidjob', array('is_done' => 1, 'admin_notif' => 1, 'completed_admin_id' => $admin_id), array('id' => $bidjob_id));

                $message = "Hello {$translator_name}, I want to mark this {$job_post->name} job completed. However you need to rate the translation quality before we proceed. Please click on the link below the chat to proceed. Without your rating this job is incomplete and we cant proceed to issue your payment. let me know if you have any questions.";

                $post_to_chat_box = [
                    'bid_id' => $bidjob_id,
                    'job_id' => $job_id,
                    'trans_id' => $trans_id,
                    'type' => 'admin',
                    'status' => 'unread',
                    'jobname' => $job_post->name,
                    'userID' => 1,
                    'userName' => 'Guest',
                    'channel' => 1,
                    'dateTime' => date('Y-m-d H:i:s'),
                    'text' => $message,
                    'ip' => '127.0.0.1'
                ];

                $this->db->insert('ajax_chat_messages', $post_to_chat_box);
            }
        }
    }

    public function notify_proofreader()
    {
        $notifications = $this->db->from('bidjob')->where('admin_notif', 1)->get();

        if ($notifications->num_rows()) {

            foreach ($notifications->result_array() as $row) {
                $jobpost_obj = $this->db->from('jobpost')->where('id', $row['job_id'])->get();
                $proofreader_obj = $this->db->from('proofread_jobs')->where('job_id', $row['job_id'])->get();

                if ($jobpost_obj->num_rows() and $proofreader_obj->num_rows()) {
                    $jobpost = $jobpost_obj->row();
                    $proofreader = $proofreader_obj->row();
                    $translator_obj = $this->db->from('translator')->where('id', $proofreader->translator_id)->get();

                    if ($translator_obj->num_rows()) {
                        $translator = $translator_obj->row();
                        $translator_name = "{$translator->first_name} {$translator->last_name}";

                        $data = array(
                            'translator_name' => $translator_name,
                            'job_name' => $jobpost->name,
                            'url' => $this->config->base_url() . "chat-box/?bid_id={$row['id']}&job_id={$row['job_id']}&trans_id={$translator->id}&type=user&auto={$translator->id}"
                        );

                        $mailTo = $translator->email_address;
                        $mailName = $translator_name;
                        $this->email->set_mailtype("html");
                        $this->email->from('info@montesinotranslation.com');
                        $this->email->to($mailTo);
                        $this->email->subject('Proofread Reminder');
                        $html_email = $this->load->view('email/vwRequestProofreadJob', $data, true);
                        $this->email->message($html_email);
                        $this->email->send();
                    }

                }

            }

        }
    }

    public function check_document_to_award()
    {
        if ($this->session->userdata('is_admin')) {

            $job_id = $this->input->get('job_id');

            echo $this->admin_review_model->get_awarded_document_summary($job_id);
        }
    }

    public function viewer()
    {
        if ($this->session->userdata('is_admin')) {

            $proofread_job_doc_id = (int)$this->uri->segment(5);
            $doc_reference = $this->uri->segment(6);

            if ($doc_reference != 'original_file' and $doc_reference != 'translated_file') {
                $this->session->set_flashdata('error', 'Incorrect document reference');
                redirect($this->agent->referrer());
            }

            $this->load->helper('file');

            $document_obj = $this->db->select($doc_reference)->from('proofread_jobs_docs')->where('id', $proofread_job_doc_id)->get();

            if ($document_obj->num_rows()) {

                $file_path = './uploads/review/' . str_replace(' ', '_', $document_obj->row()->$doc_reference);
                $file_info = get_file_info($file_path);
                $file_type = get_mime_by_extension($file_path);

                $data['document'] = 'review/' . str_replace(' ', '_', $document_obj->row()->$doc_reference);
                $data['file_info'] = $file_info;
                $data['file_type'] = $file_type;

                $this->load->view('admin/jobpost/vwDocumentViewer', $data);
            }

        } else {
            redirect('admin/index');
        }
    }
}
