<?php

class Dashboard extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('dashboard_model');

    }

    public function UrlAlias($string, $table, $id = NULL)
    {
        //remove any '-' and '_' from the string they will be used as concatonater
        $str = str_replace(array('-', '_'), ' ', $string);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $str);

        // lowercase and trim
        $str = trim(strtolower($str));

        // checking if in db or not
        if ($id == NULL) {
            $sql = "SELECT * FROM `" . $table . "` WHERE 1 AND `alias` = ?";
            $res = $this->db->query($sql, array($str));
        } else {
            $sql = "SELECT * FROM `" . $table . "` WHERE 1 AND `alias` = ? AND `id` <> ?";
            $res = $this->db->query($sql, array($str, $id));
        }

        $rowcount = $res->num_rows();

        if ($rowcount == 0) {
            return $str;
        } else {
            return false;
        }

    }


    function index()
    {
        if ($this->session->userdata('is_admin')) {

            $admin_id = $this->session->userdata('admin_id');
            if ($admin_id) {
                $admin_type = $this->db->select('admin_type')->get_where('admin', ['id' => $admin_id])->first_row()->admin_type;
            } else {
                $admin_type = '';
            }
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
            $config['base_url'] = base_url() . 'dashboard/index/';
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
            // echo 'seg='.$page;
            //die;
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

                $data['count_workingjob'] = $this->dashboard_model->count_workingjob($search_string, $stage, $order);
                $config['total_rows'] = $data['count_workingjob'];

                /*echo 'search_string= '.$search_string; echo '<br/>';
                echo 'stage= '.$stage; echo '<br/>';
                echo 'order= '.$order; echo '<br/>';
    */

                //fetch sql data into arrays
                if ($search_string && $stage == '' && $order == '') {
                    $data['workingjob'] = $this->dashboard_model->get_workingjob($search_string, '', '', $order_type, $config['per_page'], $limit_end);
                }

                if ($order && $search_string == '' && $stage == '') {
                    $data['workingjob'] = $this->dashboard_model->get_workingjob('', '', $order, $order_type, $config['per_page'], $limit_end);
                }


                if ($stage && $order == '' && $search_string == '') {
                    $data['workingjob'] = $this->dashboard_model->get_workingjob('', $stage, '', $order_type, $config['per_page'], $limit_end);
                }


                if ($search_string && $stage && $order == '') {
                    $data['workingjob'] = $this->dashboard_model->get_workingjob($search_string, $stage, '', $order_type, $config['per_page'], $limit_end);
                }

                if ($search_string && $stage == '' && $order) {
                    $data['workingjob'] = $this->dashboard_model->get_workingjob($search_string, '', $order, $order_type, $config['per_page'], $limit_end);
                }

                if ($search_string == '' && $stage && $order) {
                    $data['workingjob'] = $this->dashboard_model->get_workingjob('', $stage, $order, $order_type, $config['per_page'], $limit_end);
                }


                if ($search_string && $order && $stage) {
                    $data['workingjob'] = $this->dashboard_model->get_workingjob($search_string, $stage, $order, $order_type, $config['per_page'], $limit_end);
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
                $data['count_workingjob'] = $this->dashboard_model->count_workingjob();
                $data['workingjob'] = $this->dashboard_model->get_workingjob('', '', '', $order_type, $config['per_page'], $limit_end);
                $config['total_rows'] = $data['count_workingjob'];

            }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

            //initializate the panination helper
            $this->pagination->initialize($config);

//		 echo '<pre>'; print_r($data); exit;
            $data['admin_type'] = $admin_type;
            //load the view
            // $this->load->view('admin/jobpost/vwAwardJob',$data);
            $this->load->view('admin/vwDashboard', $data);
        } else {
            $this->load->view('admin/vwLogin');
        }
    }


    public function viewworkingjob()
    {
        $id = $this->uri->segment(3);
        $sql = "select * from `bidjob` where `id`='$id'";
        $query = $this->db->query($sql);
        $data['fetch'] = $query->row();
        $this->load->view('admin/jobpost/vwWorkingJobDetails', $data);

    }


    public function workcomplete()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";
            $admin_id = $this->session->userdata('admin_id');
            $id = $this->uri->segment(3);
            $job_id = $this->uri->segment(4);

            if ($id != '') {
                $date = date('Y-m-d H:i:s');

                $val = $this->db->update('bidjob', array('is_rated' => 0, 'stage' => 3, 'is_done' => 1, 'complete_date' => $date, 'completed_admin_id' => $admin_id), array('id' => $id));

                if ($val) {
                    $payment_date = date('Y-m-d', strtotime('+30 days'));
                    $sql = "UPDATE invoice SET payment_date='{$payment_date}', is_deleted = 0 WHERE bid_id = {$id}";
                    $this->db->query($sql);

                    $sql = "SELECT * FROM jobpost WHERE id = {$job_id} AND proofread_required = -1";
                    $query = $this->db->query($sql);

                    $transql = "select * from `bidjob` where `id`='$id'";
                    $tranval = $this->db->query($transql);
                    $tranfetch = $tranval->row();
                    $trans_id = $tranfetch->trans_id;

                    $bid_price = $tranfetch->price;
                    $awarded_date = $tranfetch->award_date;
                    $complete_date = $tranfetch->complete_date;
                    $comp_time = $tranfetch->time_need;

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
                        'text' => 'Admin has verified completion',
                        'ip' => '127.0.0.1'
                    ];

                    $this->db->insert('ajax_chat_messages', $post_to_chat_box);

                    if ($query->num_rows()) {
                        $row = $query->row();

                        $check = $this->db->from('jobpost')->where('parent_id', $job_id)->get();
                        if ($check->num_rows()) {

                        } else {
                            $alias = $this->UrlAlias('Review Job: ' . $row->name, 'jobpost');

                            $data_to_store = array(
                                'name' => 'Review Job: ' . $row->name,
                                'parent_id' => $job_id,
                                'alias' => $alias,
                                'description' => $row->description,
                                'language' => $row->language,
                                'price' => $row->price,
                                'status' => 1,
                                'stage' => 0,
                                'job_type' => $row->job_type,
                                'created' => date('Y-m-d h:i:s'),
                                'dueDate' => $row->dueDate,

                                'lineNumberCode' => $row->lineNumberCode,
                                'lineNumber' => $row->lineNumber,
                                'lineMonth' => $row->lineMonth,
                                'lineYear' => $row->lineYear,

                                'proofread_required' => 1,
                                'proofreadType' => $row->proofreadType,
                                'approval_status' => 0
                            );

                            if ($this->db->insert('jobpost', $data_to_store)) {
                                $new_job_id = $this->db->insert_id();
                                $sql1 = "select * from jobpost where id=$job_id and job_type=0";
                                $val1 = $this->db->query($sql1);
                                $check = $val1->num_rows();

                                $proofread_jobs_data = array(
                                    'job_id' => $new_job_id,
                                    'translator_id' => '',
                                    'translation_completed' => 0,
                                    'review_price' => 0,
                                    'review_stage' => 0,
                                    'review_type' => 1
                                );

                                $this->db->insert('proofread_jobs', $proofread_jobs_data);
                            }
                        }
                    }

                    $data['name'] = $trans_name;
                    $data['job_name'] = $job_name;
                    $data['job_description'] = $job_description;
                    $data['job_created'] = $job_created;
                    $data['job_id'] = $job_id;
                    $data['job_alias'] = $job_alias;

                    $mailTo = $trans_email;
                    $mailName = $trans_name;
                    $this->email->set_mailtype("html");
                    $this->email->from('info@montesinotranslation.com');
                    $this->email->to($mailTo);
                    $this->email->subject('Award Job Completion');
                    $html_email = $this->load->view('email/vwTranslatorAwardCompletion', $data, true);
                    $this->email->message($html_email);
                    // $this->email->send();

                    $invoice_id = time();
                    $data['invoice_id'] = $invoice_id;
                    $data['name'] = $trans_name;
                    $data['job_title'] = $job_name;
                    //$data['invoice_desc'] =$invoice_desc;
                    $data['award_date'] = $awarded_date;
                    $data['complete_date'] = $complete_date;
                    $data['job_alias'] = $job_alias;
                   // $data['job_price'] = $bid_price;
                    $data['bid_price'] = $bid_price;
                    $data['comp_time'] = $comp_time;


                    $mailTo = $trans_email;
                    $mailName = $trans_name;
                    $this->email->set_mailtype("html");
                    $this->email->from('info@montesinotranslation.com');
                    $this->email->to($mailTo);
                    $this->email->subject('Job Completion Invoice');
                    $html_email = $this->load->view('email/vwJobCompletionInvoice', $data, true);
                    $this->email->message($html_email);
                    $mail = $this->email->send();

                    $invoice_update_sql = "SELECT * FROM `invoice` WHERE `bid_id`='" . $id . "'  AND `job_id`='" . $job_id . "' AND `trans_id`='" . $trans_id . "' ";
                    $invoice_update_query = $this->db->query($invoice_update_sql);
                    $invoice_update_num = $invoice_update_query->num_rows();

                    if ($invoice_update_num > 0) {
                        $invoice_update_fetch = $invoice_update_query->row();
                        $invoice_update_id = $invoice_update_fetch->id;

                        $data_to_update = array(
                            'bid_id' => $id,
                            'job_id' => $job_id,
                            'trans_id' => $trans_id,
                            'description' => '',
                            'is_deleted' => 0,
                            'created' => date('Y-m-d h:i:s')
                        );

                        $this->db->where('id', $invoice_update_id);
                        $this->db->update('invoice', $data_to_update);
                    } else {
                        $data_to_store = array(
                            'bid_id' => $id,
                            'invoice_id' => $invoice_id,
                            'job_id' => $job_id,
                            'trans_id' => $trans_id,
                            'description' => '',
                            'created' => date('Y-m-d h:i:s')
                        );

                        $this->load->model('adminjobpost_model');
                        $this->adminjobpost_model->store_invoice($data_to_store);
                    }

                    $this->session->set_flashdata('success_message', 'Working job Completed');
                    redirect('dashboard/index/');
                }
            } else {
                $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
                redirect('dashboard/index/');
            }
        } else {
            redirect('admin/index');
        }
    }

    /*public function awarduncomplete()
    {

    if($this->session->userdata('is_admin')){
      $data['message_error'] = "";
        $data['message_success'] = "";
        //artist id
        $id = $this->uri->segment(3);
        $job_id = $this->uri->segment(4);

        if($id!='')
        {
            $sql = "UPDATE `bidjob` SET `awarded` = '1',`stage` = '1' WHERE `id` = '" . $id . "'";
            $val = $this->db->query($sql);
            $this->session->set_flashdata('success_message', 'Awarded Job Not Compleated');
            redirect('admin/awardjob/');
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
            redirect('admin/awardjob/');
        }
      } else {
        redirect('admin/index');
      }



    }
    */


}
