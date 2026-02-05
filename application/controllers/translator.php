<?php error_reporting(0);

class Translator extends CI_Controller
{

    function __construct()

    {

        parent::__construct();

        $this->load->helper(array('form', 'url'));

        $this->load->model('front_award_model');
        $this->load->model('common_model');
        $this->load->model('front_working_model');

        $this->load->model('front_message_model');

        $this->load->model('front_bid_model');
        $this->load->model('front_review_model');
        $this->load->model('translators_model');
        $this->load->model('adminreview_model');

        $this->load->helper('path');

//        echo $this->router->fetch_class();
//        echo $this->router->fetch_method();
//        exit();

        $this->load->library('user_agent');

    }

    function UrlAlias($string, $table, $id = NULL)
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

    /**
     * Check if thetranslator is logged in, if he's not,
     * send him to the login page
     * @return void
     */

    function index()
    {
        $data['message_error'] = "";

        $data['message_success'] = "";

        if ($this->session->userdata('is_translator')) {

            redirect('translator/dashboard');

        } else {

            $this->load->view('translator/vwLogin');

        }

    }

    function newlogin()
    {
        $this->load->view('translator/vwLoginNew');
    }


    /**
     * encript the password
     * @return mixed
     */

    function __encrip_password($password)
    {

        return md5($password);

    }


    function notification()
    {

        $adminID = $this->session->userdata('admin_id');

        //echo $adminID;

        //die;

        $trans_id = $this->session->userdata('translator_id');

        /*$sql="SELECT * FROM `message` WHERE `type`= 'admin' and `read`='0'";*/

        if ($adminID != '') {

            $sql = "SELECT * FROM `ajax_chat_messages` WHERE `status` = 'unread' AND `type`= 'user'";
            $query = $this->db->query($sql);

        } else {

            $sql = "SELECT * FROM `ajax_chat_messages` WHERE `status` = 'unread' AND `type`= 'admin' AND `trans_id` = ?";
            $query = $this->db->query($sql, array($trans_id));

        }


        $data = $query->result();

        $num = $query->num_rows();


        echo $num;


    }

    function notification1()
    {

        $trans_id = $this->session->userdata('translator_id');
        if (isset($trans_id) && $trans_id != '') {
            $sql = "SELECT * FROM ajax_chat_messages WHERE ajax_chat_messages.status = 'unread' AND ajax_chat_messages.type = 'admin' AND ajax_chat_messages.trans_id = ? AND ajax_chat_messages.bid_id IN (SELECT bidjob.id FROM bidjob WHERE bidjob.trans_id = ?)";
            $query = $this->db->query($sql, array($trans_id, $trans_id));

            $data = $query->result();

            $num = $query->num_rows();

            echo $num;
        } else {
            echo 0;
        }

    }


    /**
     * check the username and the password with the database
     * @return void
     */

    function autologin(){
        $id=$this->uri->segment(3);
        if($id!=''){
            $check=$this->translators_model->check_data(['id'=>$id]);
            if(!empty($check) && $check->verified!=1){
                $update=$this->translators_model->update_data(['verified'=>1,'status'=>1],['id'=>$id]);
                if($update){
                    $data = array(
                                'translator_id' => $id,
                                'user_name' => $check->user_name,
                                'email_addres' => $check->email_address,
                                'is_logged_in' => true,
                                'is_translator' => true
                            );
                    $this->session->set_userdata($data);
                    $return['redirect']=base_url().'translator/dashboard';
                } else{
                   $return['redirect']=$this->session->userdata('last_url'); 
                }
            } else{
                   $return['redirect']=$this->session->userdata('last_url'); 
            }

            echo json_encode($return);
        }
        
    }

    function login()
    {


//$data = array('last_url' => 'test');


//echo 'hii'; echo'<pre>';print_r($this->session->userdata);die;

        if ($this->session->userdata('is_translator')) {

            redirect('translator/dashboard');

        } else {
            $data['message_error'] = "";

            $data['message_success'] = "";

            $this->load->model('Translators_model');

            // field name, error message, validation rules

            $this->form_validation->set_rules('user_name', 'Username', 'trim|required');

            $this->form_validation->set_rules('pass_word', 'Password', 'trim|required|min_length[4]|max_length[32]');


            if ($this->form_validation->run() == FALSE) {

                $this->session->set_flashdata('flash_error', 'errorValidation');

                $this->load->view('translator/vwLogin', $data);

            } else {

                $user_name = $this->input->post('user_name');

                $password = $this->__encrip_password($this->input->post('pass_word'));

                $sql = "SELECT * FROM translator WHERE user_name = ? AND pass_word = ?";


                $val = $this->db->query($sql, array($user_name, $password));

                $is_valid = $val->num_rows;

                if ($is_valid == 1) {


                    $sql2 = "SELECT * FROM translator WHERE user_name = ? AND pass_word = ? AND (verified = 0 OR verified = 2)";

                    $val2 = $this->db->query($sql2, array($user_name, $password));

                    $verified = $val2->num_rows;

                    if ($verified > 0) {

                        $data['message_error'] = "Your account is not verified or inactive.";

                        $this->load->view('translator/vwLogin', $data);

                    } else {


                        foreach ($val->result_array() as $recs => $res) {

                            $data = array(

                                'translator_id' => $res['id'],

                                'user_name' => $res['user_name'],

                                'email_addres' => $res['email_address'],

                                'is_logged_in' => true,

                                'is_translator' => true

                            );


                            $this->session->set_userdata($data);

                            $referrer_url = $this->session->userdata('last_url');

                            //echo $this->session->userdata['last_url'];

                            if ($referrer_url != '') {

                                redirect($referrer_url);

                            } else {
                                $this->load->library('user_agent');
                                //echo $this->agent->referrer();exit();

                                redirect($this->agent->referrer());

                            }


                        }

                    }


                } else // incorrect username or password

                {

                    $data['message_error'] = "Invalid Username/Password";

                    $this->load->view('translator/vwLogin', $data);

                }

            }

        }

    }


    /**
     * Create new user and store it in the database
     * @return void
     */

    function registration()

    {
        if ($this->session->userdata('is_translator')) {

            redirect('translator/dashboard');

        } else {

            $data['message_error'] = "";

            $data['message_success'] = "";

            // field name, error message, validation rules


            $this->form_validation->set_rules('first_name', 'Name', 'trim|required');

            $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');

            $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');

            $this->form_validation->set_rules('pass_word', 'Password', 'trim|required|min_length[4]|max_length[32]');

            $this->form_validation->set_rules('con_pass_word', 'Password Confirmation', 'trim|required|matches[pass_word]');


            if ($this->form_validation->run() == FALSE) {//echo "hello";

                $this->session->set_flashdata('flash_error', 'errorValidation');

                //redirect('translator/registration');

                $this->load->view('translator/vwRegister', $data);

            } else {

                $alias = $this->input->post('alias');

                $title = $this->input->post('title');


                if ($alias == '') {

                    $str = $this->UrlAlias($title, 'translator');

                } else {

                    $str = $this->UrlAlias($alias, 'translator');

                }


                //if($str){
                $lang = "";
                $language_from1 = $this->input->post('language_from1');
                $language1 = $this->input->post('language1');
                if ($language_from1 != "" && $language1 != "") {
                    $lang .= $language_from1 . "/" . $language1 . ",";
                }

                $language_from2 = $this->input->post('language_from2');
                $language2 = $this->input->post('language2');
                if ($language_from2 != "" && $language2 != "") {
                    $lang .= $language_from2 . "/" . $language2 . ",";
                }

                $language_from3 = $this->input->post('language_from3');
                $language3 = $this->input->post('language3');
                if ($language_from3 != "" && $language3 != "") {
                    $lang .= $language_from3 . "/" . $language3 . ",";
                }

                $language_from4 = $this->input->post('language_from4');
                $language4 = $this->input->post('language4');
                if ($language_from4 != "" && $language4 != "") {
                    $lang .= $language_from4 . "/" . $language4 . ",";
                }

                $language_from5 = $this->input->post('language_from5');
                $language5 = $this->input->post('language5');
                if ($language_from5 != "" && $language5 != "") {
                    $lang .= $language_from5 . "/" . $language5 . ",";
                }

                $language_from6 = $this->input->post('language_from6');
                $language6 = $this->input->post('language6');
                if ($language_from6 != "" && $language6 != "") {
                    $lang .= $language_from6 . "/" . $language6 . ",";
                }


                $num = count($languageArr);

                if ($num > 6) {

                    $data['message_error'] = 'You cannot select  more than six Languages!';

                    $this->load->view('translator/vwRegister', $data);

                } else {

                    //$language = implode(",",$languageArr);

                    $language = "," . $lang;

                    $hash = md5(uniqid(rand()));

                    $translator_insert_data = array(

                        //'title' => $this->input->post('title'),

                        //'alias' => $str,

                        'first_name' => $this->input->post('first_name'),

                        'last_name' => $this->input->post('last_name'),

                        'email_address' => $this->input->post('email_address'),

                        'user_name' => $this->input->post('email_address'),

                        'pass_word' => $this->__encrip_password($this->input->post('pass_word')),

                        'location' => $this->input->post('location'),

                        'language' => $language,


                        'status' => 0,

                        'hash' => $hash,

                        'verified' => 0,

                        'created' => date('Y-m-d h:i:s')


                    );

                    $this->load->model('Translators_model');

                    $sql = "SELECT * FROM translator WHERE email_address = '" . $this->input->post('email_address') . "'  AND verified = '1' ";

                    //echo $sql; die;

                    $val = $this->db->query($sql);

                    if ($val->num_rows) {//echo "error";

                        $data['message_error'] = "Email Address/Username already taken. ";

                        $this->load->view('translator/vwRegister', $data);

                    } else {


                        $query = $this->db->insert('translator', $translator_insert_data);

                        if ($query) {

                            $translator_id = $this->db->insert_id();


                            //Email

                            $data = array(

                                //'title' => $this->input->post('title'),

                                //'alias' => $str,

                                'first_name' => $this->input->post('first_name'),

                                'last_name' => $this->input->post('last_name'),

                                'email_address' => $this->input->post('email_address'),

                                'user_name' => $this->input->post('email_address'),

                                'location' => $this->input->post('location'),

                                'category' => $category,

                                'status' => 0,

                                'hash' => $hash,

                                'verified' => 0,

                                'created' => date('Y-m-d h:i:s'),

                                'translator_id' => $translator_id


                            );

                            $this->email->set_mailtype("html");

                            $this->email->from('info@montesinotranslation.com');

                            $this->email->to($data['email_address']);

                            $this->email->subject('Email verification');

                            $html_email = $this->load->view('email/vwTransEmailActivation', $data, true);

                            $this->email->message($html_email);

                            $this->email->send();


                            $data['message_success'] = "Thank you for registering with us. Please check your email and activate your account.";
                        }

                    }

                    $this->load->view('translator/vwRegister', $data);

                }
            }

        }

    }


    function resendactivation()
    {

        $data['message_error'] = "";

        $data['message_success'] = "";


        $translator_id = $this->uri->segment(3);

        $hash = $this->uri->segment(4);


        $sql = "SELECT * FROM translator WHERE id = '" . $translator_id . "' AND hash = '" . $hash . "' ";

        //echo $sql; die;

        $val = $this->db->query($sql);


        if ($val->num_rows) {//echo "a";


            $sql2 = $sql . " AND verified = '1' ";

            //echo $sql2;die;

            $val2 = $this->db->query($sql2);


            if ($val2->num_rows) {

                $this->session->set_flashdata('message_error', 'Your account already activated');

                redirect('translator/login');

            } else {

                $results = $val->result_array();

                $data = array(

                    'first_name' => $results[0]['first_name'],

                    'last_name' => $results[0]['last_name'],

                    'email_address' => $results[0]['email_address'],

                    'user_name' => $results[0]['email_address'],

                    'location' => $results[0]['location'],

                    'status' => 0,

                    'hash' => $hash,

                    'verified' => 0,

                    'created' => date('Y-m-d h:i:s'),

                    'translator_id' => $translator_id

                );

                //echo'<pre>';print_r($data);die;

                $this->email->set_mailtype("html");

                $this->email->from('info@montesinotranslation.com');

                $this->email->to($data['email_address']); //$user_insert_data['email_address']

                $this->email->subject('Email verification');

                $html_email = $this->load->view('email/vwTransEmailActivation', $data, true);

                $this->email->message($html_email);

                $this->email->send();


                $data['message_success'] = "Please check your email and active your account.";

            }

        } else { //echo "b";die;

            $this->session->set_flashdata('message_error', 'Invalid activation key.');

            redirect('translator/login');

        }

        $this->load->view('translator/vwRegister', $data);

    }


    function logout()

    {

        if (!$this->session->userdata('is_translator')) {

            redirect('translator/login');
            exit();

        } else {

            $this->session->sess_destroy();
            session_destroy();
            redirect('translator/logout');

        }

    }

    function dashboard($start = 0)
    {
        $this->load->library('pagination');
        $this->load->model('translators_model');

        $data['message_error'] = "";

        $data['message_success'] = "";

        if ($this->session->userdata('is_translator')) {

            if ($_POST) {
                $dateFrom = $this->input->post('bidDateFrom');
                $dateTo = $this->input->post('bidDateTo');
                $search_string = $this->input->post('search_string');
                $job_status = $this->input->post('jobStatus');

                $this->session->set_userdata('dateFromDash', $this->input->post('bidDateFrom'));
                $this->session->set_userdata('dateToDash', $this->input->post('bidDateTo'));
                $this->session->set_userdata('search_stringDash', $this->input->post('search_string'));
                $this->session->set_userdata('job_statusDash', $this->input->post('jobStatus'));
            }

            $arr['myJobs'] = $this->translators_model->fetchMyBids(10, $start, $this->session->userdata('translator_id'), $this->session->userdata('search_stringDash'), $this->session->userdata('job_statusDash'), $this->session->userdata('dateFromDash'), $this->session->userdata('dateToDash'));

            $config['total_rows'] = count($this->translators_model->fetchMyTotalBids($this->session->userdata('translator_id'), $this->session->userdata('search_stringDash'), $this->session->userdata('job_statusDash')));

            $config['base_url'] = base_url() . 'translator/dashboard/';
            $config['per_page'] = 10;
            $config['uri_segment'] = 3;

            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';

            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a>';
            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $this->pagination->initialize($config);
            $arr['pages'] = $this->pagination->create_links();

            $arr['totalnotifications'] = $this->translators_model->getCountUnreadNotification($this->session->userdata('translator_id'));
            $arr['totalUnreadMessages'] = $this->translators_model->fetchUnReadMessage($this->session->userdata('translator_id'));

            $arr['page'] = 'dash';
            $this->load->view('translator/translators/vwDashboard', $arr);
        } else {
            $this->load->view('translator/vwLogin');
        }
    }

    function clearFilters()
    {
        $this->session->unset_userdata('dateFromDash');
        $this->session->unset_userdata('dateToDash');
        $this->session->unset_userdata('search_stringDash');
        $this->session->unset_userdata('job_statusDash');
    }

    function notifications($start = 0)
    {
        $this->load->library('pagination');
        $this->load->model('translators_model');

        $data['message_error'] = "";

        $data['message_success'] = "";

        if ($this->session->userdata('is_translator')) {

            $arr['notifications'] = $this->translators_model->fetchMyNotifications(10, $start, $this->session->userdata('translator_id'));
            $config['total_rows'] = count($this->translators_model->fetchMyTotalNotifications($this->session->userdata('translator_id')));

            $config['base_url'] = base_url() . 'translator/notifications/';
            $config['per_page'] = 10;
            $config['uri_segment'] = 3;

            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';

            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a>';
            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $this->pagination->initialize($config);
            $arr['pages'] = $this->pagination->create_links();

            $arr['totalnotifications'] = $this->translators_model->getCountUnreadNotification($this->session->userdata('translator_id'));
            $arr['totalUnreadMessages'] = $this->translators_model->fetchUnReadMessage($this->session->userdata('translator_id'));

            $this->db->limit((10 + $start))->update('notifications', ['isRead' => 1], ['translatorID' => $this->session->userdata('translator_id')]);

            $this->load->view('translator/translators/vwNotifications', $arr);
        } else {
            $this->load->view('translator/vwLogin');
        }
    }

    function verify()
    {

        $data['message_error'] = "";

        $data['message_success'] = "";

        $translator_id = $this->uri->segment(3);

        $hash = $this->uri->segment(4);

        $sql = "SELECT * FROM translator WHERE id = '" . $translator_id . "' AND hash = '" . $hash . "' ";

        //echo $sql;die;

        $val = $this->db->query($sql);

        $results = $val->result_array();

        $email_address = $results[0]['email_address'];

        if ($val->num_rows) {

            $sql2 = $sql . " AND verified = '1' ";

            $val2 = $this->db->query($sql2);

            if ($val2->num_rows) {

                $this->session->set_flashdata('message_error', 'Your account already activated');

            } else {

                $sql3 = "UPDATE translator SET `verified` = '1' WHERE id = '" . $translator_id . "' AND hash = '" . $hash . "' ";

                $val3 = $this->db->query($sql3);

                $sql4 = "DELETE FROM translator WHERE email_address = '" . $email_address . "' AND verified = '0' AND  hash != '" . $hash . "'  ";

                $val4 = $this->db->query($sql4);

                $this->session->set_flashdata('message_success', 'Thank you for activating your account, please log in to proceed.');

            }

        } else {

            $this->session->set_flashdata('message_error', 'Invalid activation key.');

        }

        redirect('translator/login');

    }


    function changepass()
    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $data['message_error'] = "";

            $data['message_success'] = "";


            $luser_id = $this->session->userdata('translator_id');

            $this->form_validation->set_rules('old_word', 'Password', 'trim|required|min_length[4]|max_length[32]');

            $this->form_validation->set_rules('pass_word', 'Password', 'trim|required|min_length[4]|max_length[32]');

            $this->form_validation->set_rules('con_pass_word', 'Password Confirmation', 'trim|required|matches[pass_word]');


            if ($this->form_validation->run() == FALSE) {

                $this->session->set_flashdata('flash_error', 'errorValidation');

                $this->load->view('translator/translators/vwChangepass', $data);

            } else {

                $check_sql = "SELECT * FROM translator WHERE pass_word = ? AND id = ?";
                $check_val = $this->db->query($check_sql, array($this->__encrip_password($this->input->post('old_word')), $luser_id));
                $current = $check_val->num_rows();

                if ($current > 0) {

                    if ($this->input->post('pass_word') == $this->input->post('con_pass_word')) {

                        $sql = "UPDATE translator SET `pass_word` = ? WHERE id = ?";

                        $val = $this->db->query($sql, array($this->__encrip_password($this->input->post('pass_word')), $luser_id));

                        $data['message_success'] = "Successfully Change your password.";

                        $this->load->view('translator/translators/vwChangepass', $data);

                    }

                } else {

                    $data['message_error'] = "Your Old password does not match.";

                    $this->load->view('translator/translators/vwChangepass', $data);

                }

            }

        }

    }


    function changeprofile()
    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $data['message_error'] = "";
            $data['message_success'] = "";
            $luser_id = $this->session->userdata('translator_id');
            $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
            $sql = "SELECT * FROM translator WHERE id = '" . $luser_id . "'";
            $val = $this->db->query($sql);
            $data['results'] = $val->result_array();

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('flash_error', 'errorValidation');
            } else {


                $languageFrom = $this->input->post('languageFrom');
                $languageTo = $this->input->post('languageTo');
                $lang = "";
                for ($x = 0; $x < count($languageFrom); $x++) {
                    $lang .= $languageFrom[$x] . "/" . $languageTo[$x] . ",";
                }

                $language = "," . $lang;

                if ($this->input->post('totalFile') != "") {

                    $filename = $this->input->post('totalFile');

                    $sql = "UPDATE translator SET
					`first_name`   = '" . $this->input->post('first_name') . "',
					`last_name`   = '" . $this->input->post('last_name') . "',
					`language`    = '" . $language . "',
					`file`    = '" . $this->input->post('prefile') . $filename . "',
					`modified` = '" . date('Y-m-d h:i:s') . "' ,
					`location`    = '" . $this->input->post('location') .
                        "' WHERE id = '" . $luser_id . "'";

                    $val = $this->db->query($sql);
                    $path = './uploads/user/' . $this->input->post('prefile');
                    unlink($path);
                    $val = $this->db->query($sql);
                    if ($val == TRUE) {
                        $this->session->set_flashdata('success_message', 'Successfully change your profile');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    } else {
                        $this->session->set_flashdata('error_message', 'Your profile is not updated');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    }

                } else {

                    $sql = "UPDATE translator SET
					`first_name`   = '" . $this->input->post('first_name') . "',
					`last_name`   = '" . $this->input->post('last_name') . "',
					`language`    = '" . $language . "',
					`file`    = '" . $this->input->post('prefile') . "',
					`modified` = '" . date('Y-m-d h:i:s') . "' ,
					`location`    = '" . $this->input->post('location') .
                        "' WHERE id = '" . $luser_id . "'";

                    $val = $this->db->query($sql);

                    if ($val == TRUE) {
                        $this->session->set_flashdata('success_message', 'Successfully change your profile');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    } else {
                        $this->session->set_flashdata('error_message', 'Your profile is not updated ');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    }

                }

            }

            $this->load->model('translators_model');

            $data['allLanguages'] = $this->translators_model->getAllLanguages();

            $this->load->view('translator/translators/vwChangeprofile', $data);

        }

    }


    function paypal()
    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $data['message_error'] = "";

            $data['message_success'] = "";

            $luser_id = $this->session->userdata('translator_id');

            $this->form_validation->set_rules('paypal_id', 'Paypal Id', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->session->set_flashdata('flash_error', 'errorValidation');

            } else {

                $sql = "UPDATE translator SET

					`paypal_id`   = '" . $this->input->post('paypal_id') . "',

					`modified` = '" . date('Y-m-d h:i:s') . "'

					 WHERE id = '" . $luser_id . "'";

                $val = $this->db->query($sql);

                $data['message_success'] = "PayPal ID Successfully updated.";

            }

            $this->load->view('translator/translators/vwPaypalInfo', $data);
        }

    }


    function contactinfo()
    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $luser_id = $this->session->userdata('translator_id');

            $sql = "SELECT * FROM translator WHERE id = '" . $luser_id . "'";

            $val = $this->db->query($sql);

            $data['results'] = $val->result_array();

            //$this->session->set_flashdata('flash_error', 'errorValidation');

            $this->load->view('translator/translators/vwContactInfo', $data);
        }

    }


    public function request()
    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $data['error'] = "";

            $data['success'] = "";

            $this->form_validation->set_rules('request', 'Request', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->session->set_flashdata('flash_error', 'errorValidation');

            }

            $data['request'] = $this->input->post('request');

            //echo $data['request'];die;

            $data['trans_id'] = $this->session->userdata('translator_id');

            $sql = "SELECT * FROM translator WHERE id = '" . $data['trans_id'] . "'";

            $val = $this->db->query($sql);

            $fetch = $val->row();

            //echo'<pre>'; print_r($fetch); die;

            $data['name'] = $fetch->first_name;

            $data['email_address'] = $fetch->email_address;

            $data['job_id'] = $this->input->post('job_id');

            //echo $data['job_id'];die;

            $sql3 = "SELECT * FROM bidjob WHERE job_id ='" . $data['job_id'] . "' AND trans_id= '" . $data['trans_id'] . "'";

            $val3 = $this->db->query($sql3);

            $fetch3 = $val3->row();

            $data['bid_id'] = $fetch3->id;

            //echo $data['bid_id'];die;


            $sql1 = "SELECT * FROM jobpost WHERE id ='" . $data['job_id'] . "'";

            $val1 = $this->db->query($sql1);

            $fetch1 = $val1->row();

            $data['jobname'] = $fetch1->name;


            $this->email->set_mailtype("html");

            $this->email->from($data['email_address']);

            $sql2 = "SELECT * FROM settings WHERE id = '1'";

            $val2 = $this->db->query($sql2);

            $fetch2 = $val2->row();

            $email = $fetch2->email;


            $this->email->to($email);

            $this->email->subject('Payment Request');

            $html_email = $this->load->view('email/vwRequestmail', $data, true);

            $this->email->message($html_email);

            $mail = $this->email->send();

            if ($mail) {

                $this->session->set_flashdata('success_message', 'Your Payment request is Send.');

                redirect('translator/invoice');

            } else {

                $this->session->set_flashdata('error_message', 'Something happend,Please try again.');

                redirect('translator/invoice');

            }


        }

    }


    function earning()
    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $luser_id = $this->session->userdata('translator_id');


            $sql = "SELECT SUM(b.price) as `earning` FROM invoice as a LEFT join bidjob AS b ON a.trans_id = b.trans_id WHERE  a.payment='1' AND a.trans_id ='" . $luser_id . "' AND  b.id =a.bid_id ";//AND  a.id =b.bid_id


            //echo $sql; die;

            $val = $this->db->query($sql);


            if ($val->num_rows() == '1') {

                $data['results'] = $val->result_array();

                //echo '<pre>'; print_r($data['results']); die;

            } else {

                $this->session->set_flashdata('error', 'Yet Not Paid.');

            }

            //$this->session->set_flashdata('flash_error', 'errorValidation');

            $this->load->view('translator/translators/vwEarning', $data);
        }

    }


    public function upload()

    {
        error_reporting(0);

        if (isset($_FILES["myfile"])) {

            $newRet = "";
            $ret = array();
            $error = $_FILES["myfile"]["error"];
            if (!is_array($_FILES["myfile"]['name'])) //single file
            {
                $newdir = time();
                $output_dir = "./uploads/bidjobpost/" . $newdir . "/";
                $dir = $newdir . "/";
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
                $NewImageName = $ImageName;
                move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $NewImageName);
                //echo "<br> Error: ".$_FILES["myfile"]["error"];
                //$ret[$fileName]= $output_dir.$NewImageName;
                $newRet .= $dir . $NewImageName . "##";

                echo $newRet;
            }
        }
    }


    public function uploads()

    {
        error_reporting(0);

        if (isset($_FILES["myfile"])) {

            $newRet = "";
            $ret = array();
            $error = $_FILES["myfile"]["error"];
            if (!is_array($_FILES["myfile"]['name'])) //single file
            {
                $newdir = time();
                $output_dir = "./uploads/user/" . $newdir . "/";
                $dir = $newdir . "/";
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
                $NewImageName = $ImageName;
                move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $NewImageName);
                //echo "<br> Error: ".$_FILES["myfile"]["error"];
                //$ret[$fileName]= $output_dir.$NewImageName;
                $newRet .= $dir . $NewImageName . "##";

                echo $newRet;
            }
        }
    }

    function linkdelete()
    {
        $id = $this->input->post('id');
        $id = rtrim($id, "##");
        //echo $id;die;
        $path = './uploads/bidjobpost/' . $id;
        unlink($path);
        echo "Remove sucessfully";

    }

    function linkdelete1()
    {
        $id = $this->input->post('id');
        $id = rtrim($id, "##");

        $path = './uploads/user/' . $id;

        unlink($path);
        echo "Remove sucessfully";

    }

    function bidjob()
    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $data['message_error'] = "";

            $data['message_success'] = "";


            $this->form_validation->set_rules('time_need', 'Time Needed', 'trim|required');
            $this->form_validation->set_rules('price', 'Job Quotation', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Your bid is invalid');

                $referrer = $this->agent->referrer();

                redirect($referrer);
            } else {

                if ($this->input->post('totalFile') != "") {

                    $filename = $this->input->post('totalFile');

                } else {

                    $filename = "";

                }

                $job_id = $this->input->post('id');

                $trans_id = $this->session->userdata('translator_id');

                $time_need = $this->input->post('time_need');

                $price = preg_replace('/[^\d,\.]/', '', $this->input->post('price'));
                $price = preg_replace('/,(\d{2})$/', '.$1', $price);

                $translator_bid_data = array(

                    'price' => $price,

                    'time_need' => $time_need,

                    'proposal' => $this->input->post('proposal'),

                    'file' => $filename,

                    'trans_id' => $trans_id,

                    'job_id' => $job_id,

                    'created' => date('Y-m-d h:i:s')

                );

                // echo '<pre>';
                // print_r($_POST);
                // print_r($translator_bid_data); exit;

                $query = $this->db->insert('bidjob', $translator_bid_data);

                $this->session->set_flashdata('success_message', 'Your bid is posted sucessfully');

                $referrer = $this->agent->referrer();

                redirect($referrer);

            }

        }

    }

    public function reviewlist()
    {
        if (!$this->session->userdata('is_translator')) {
            $this->load->view('translator/vwLogin');
        } else {
            $filter_session_data = "";

            //all the posts sent by the view
            $search_string = $this->input->post('search_string');
            $order = $this->input->post('order');
            $order_type = $this->input->post('order_type');

            //pagination settings
            $config['per_page'] = 10;
            $config['base_url'] = base_url() . 'translator/reviewlist';
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

            if ($search_string !== false && $order !== false || $this->uri->segment(3) == true) {
                if ($search_string) {
                    $filter_session_data['search_string_selected'] = $search_string;
                } else {
                    $search_string = $this->session->userdata('search_string_selected');
                }

                $data['search_string_selected'] = $search_string;

                if ($order) {
                    $filter_session_data['order'] = $order;
                } else {
                    $order = $this->session->userdata('order');
                }

                $data['order'] = $order;

                $this->session->set_userdata($filter_session_data);

                $data['count_review'] = $this->front_review_model->count_review($search_string, $order);

                $config['total_rows'] = $data['count_review'];

                if ($search_string) {
                    if ($order) {
                        $data['review'] = $this->front_review_model->get_review($search_string, $order, $order_type, $config['per_page'], $limit_end);
                    } else {
                        $data['review'] = $this->front_review_model->get_review($search_string, '', $order_type, $config['per_page'], $limit_end);
                    }
                } else {
                    if ($order) {
                        $data['review'] = $this->front_review_model->get_review('', $order, $order_type, $config['per_page'], $limit_end);
                    } else {
                        $data['review'] = $this->front_review_model->get_review('', '', $order_type, $config['per_page'], $limit_end);
                    }
                }
            } else {
                $filter_session_data['search_string_selected'] = null;
                $filter_session_data['order'] = null;
                $filter_session_data['order_type'] = null;

                $this->session->set_userdata($filter_session_data);

                $data['search_string_selected'] = '';
                $data['order'] = 'id';
                $data['count_review'] = $this->front_review_model->count_review();
                $data['review'] = $this->front_review_model->get_review(null, null, $order_type, $config['per_page'], $limit_end);
                $config['total_rows'] = $data['count_review'];
            }

            // echo '<pre>'; print_r($data['review']); exit;

            $this->pagination->initialize($config);

            $dat = array(
                'is_awarded' => true
            );

            $trans_id = $this->session->userdata('translator_id');

            $data['no_awarded_jobs'] = $this->adminreview_model->get_jobs_awarded($trans_id);
            $data['rating'] = $this->adminreview_model->get_average_rating($trans_id);
            $data['translator'] = $this->adminreview_model->get_translator_name($trans_id);

            $this->session->set_userdata($dat);
            $this->load->view('translator/translators/vwReviewList', $data);
        }

    }


    function bidjobedit()
    {
        if (!$this->session->userdata('is_translator')) {
            $this->load->view('translator/vwLogin');
        } else {
            $job_id = $this->input->post('job_id');
            $trans_id = $this->session->userdata('translator_id');
            $this->form_validation->set_rules('time_need', 'Time Needed', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
            } else {
                if ($this->input->post('totalFile') != "") {
                    $filename = $this->input->post('totalFile');
                    $sql = "UPDATE `bidjob` SET
						`time_need` = '" . $this->input->post('time_need') . "',
						`file`    = '" . $this->input->post('prefile') . $filename . "',
						`proposal` = '" . $this->input->post('proposal') . "',
						`price` ='" . $this->input->post('price') . "'
						 WHERE `job_id` = '" . $job_id . "' AND `trans_id` = '" . $trans_id . "'";
                    $path = './uploads/bidjobpost/' . $this->input->post('prefile');
                    unlink($path);
                    $val = $this->db->query($sql);
                    if ($val == TRUE) {
                        $this->session->set_flashdata('success_message', 'Your bid is updated sucessfully');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    } else {
                        $this->session->set_flashdata('error_message', 'Your bid is not updated');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    }
                } else {
                    $sql1 = "UPDATE `bidjob` SET
					`time_need` = '" . $this->input->post('time_need') . "',
					`file`    = '" . $this->input->post('prefile') . "',
					`proposal` = '" . $this->input->post('proposal') . "',
					`price` ='" . $this->input->post('price') . "'
					 WHERE `job_id` = '" . $job_id . "' AND `trans_id` = '" . $trans_id . "'";
                    $val1 = $this->db->query($sql1);
                    if ($val1 == TRUE) {
                        $this->session->set_flashdata('success_message', 'Your bid is updated sucessfully');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    } else {
                        $this->session->set_flashdata('error_message', 'Your bid is not updated ');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    }
                }
            }
            $sql = "SELECT * FROM `bidjob` where `id`='$id' ";
            $qry = $this->db->query($sql);
            if ($qry->num_rows() == '1') {
                $data['fetch'] = $qry->row();
                $this->load->view('admin/vwEditbidjob', $data);
            }
        }
    }

    public function removefile()

    {
        if ($this->session->userdata('is_translator')) {
            $id = $this->uri->segment(3);
            $old = $this->uri->segment(4);
            $oldf = $this->uri->segment(5);
            if ($oldf == '') {
                $oldfile = $old . '##';
            } else {
                $oldfile = $old . '/' . $oldf . '##';
            }
            //$oldfile=$old.'##';
            //echo $oldfile;
            $sql = "SELECT * FROM bidjob WHERE id = " . $id . " ";
            $val = $this->db->query($sql);
            $row = $val->row_array();
            $file = $row['file'];
            $filename = strstr($file, '/', true);
            //echo $oldf;die;
            if ($oldf == '') {
                $old = $old;
            } else {
                $old = $old . '/' . $oldf;
            }
            $path = './uploads/bidjobpost/' . $old;
            unlink($path);
            //echo $file ;
            $string = str_replace($oldfile, "", $file);
            //echo $string;die;
            $sql1 = "UPDATE `bidjob` SET
			`file`   = '" . $string . "'
			WHERE `id` = '" . $id . "'";
            $val1 = $this->db->query($sql1);
            $this->session->set_flashdata('success_message', 'Your file is removed sucessfully');
            $referrer = $this->agent->referrer();
            redirect($referrer);

        }

    }


    public function removefile1()

    {
        if ($this->session->userdata('is_translator')) {
            $id = $this->uri->segment(3);
            $old = $this->uri->segment(4);
            $oldf = $this->uri->segment(5);
            if ($oldf == '') {
                $oldfile = $old . '##';
            } else {
                $oldfile = $old . '/' . $oldf . '##';
            }
            //$oldfile=$old.'##';
            //echo $oldfile;
            $sql = "SELECT * FROM translator WHERE id = " . $id . " ";
            $val = $this->db->query($sql);
            $row = $val->row_array();
            $file = $row['file'];
            $filename = strstr($file, '/', true);
            //echo $oldf;die;
            if ($oldf == '') {
                $old = $old;
            } else {
                $old = $old . '/' . $oldf;
            }
            $path = './uploads/user/' . $old;
            unlink($path);
            //echo $file ;
            $string = str_replace($oldfile, "", $file);
            //echo $string;die;
            $sql1 = "UPDATE `translator` SET
			`file`   = '" . $string . "'
			WHERE `id` = '" . $id . "'";
            $val1 = $this->db->query($sql1);
            $this->session->set_flashdata('success_message', 'Your file is removed sucessfully');
            $referrer = $this->agent->referrer();
            redirect($referrer);

        }

    }


    public function award()

    {


        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $filter_session_data = "";

            //all the posts sent by the view

            $search_string = $this->input->post('search_string');


            $order = $this->input->post('order');

            $order_type = $this->input->post('order_type');


            //pagination settings

            $config['per_page'] = 20;

            $config['base_url'] = base_url() . 'translator/award';

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

                    $order_type = 'DESC';

                }

            }

            //make the data type var avaible to our view

            $data['order_type_selected'] = $order_type;


            //we must avoid a page reload with the previous session data

            //if any filter post was sent, then it's the first time we load the content

            //in this case we clean the session filter data

            //if any filter post was sent but we are in some page, we must load the session data


            //filtered && || paginated

            if ($search_string !== false && $order !== false || $this->uri->segment(3) == true) {


                /*

                The comments here are the same for line 79 until 99



                if post is not null, we store it in session data array

                if is null, we use the session data already stored

                we save order into the the var to load the view with the param already selected

                */


                if ($search_string) {


                    $filter_session_data['search_string_selected'] = $search_string;

                } else {

                    $search_string = $this->session->userdata('search_string_selected');

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


                $data['count_bidjob'] = $this->front_award_model->count_bidjob($search_string, $order);

                $config['total_rows'] = $data['count_bidjob'];


                //fetch sql data into arrays

                if ($search_string) {

                    //echo $search_string;die;

                    if ($order) {

                        $data['bidjob'] = $this->front_award_model->get_bidjob($search_string, $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_award_model->get_bidjob($search_string, '', $order_type, $config['per_page'], $limit_end);

                    }

                } else {

                    if ($order) {

                        $data['bidjob'] = $this->front_award_model->get_bidjob('', $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_award_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                    }

                }


            } else {


                //clean filter data inside section;

                $filter_session_data['search_string_selected'] = null;

                $filter_session_data['order'] = null;

                $filter_session_data['order_type'] = null;

                $this->session->set_userdata($filter_session_data);


                //pre selected options

                $data['search_string_selected'] = '';

                $data['order'] = 'id';


                //fetch sql data into arrays

                $data['count_bidjob'] = $this->front_award_model->count_bidjob();

                $data['bidjob'] = $this->front_award_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                $config['total_rows'] = $data['count_bidjob'];


            }//!isset($manufacture_id) && !isset($search_string) && !isset($order)


            //initializate the panination helper

            $this->pagination->initialize($config);

            $dat = array(

                'is_awarded' => true

            );

            //echo'<pre>';print_r($dat);die;

            $this->session->set_userdata($dat);


            $this->load->view('translator/translators/vwAwardedJobs', $data);

        }

    }


    public function proposal()

    {


        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $filter_session_data = "";

            //all the posts sent by the view

            $search_string = $this->input->post('search_string');


            $order = $this->input->post('order');

            $order_type = $this->input->post('order_type');


            //pagination settings

            $config['per_page'] = 10;

            $config['base_url'] = base_url() . 'translator/proposal';

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

                    $order_type = 'DESC';

                }

            }

            //make the data type var avaible to our view

            $data['order_type_selected'] = $order_type;


            //we must avoid a page reload with the previous session data

            //if any filter post was sent, then it's the first time we load the content

            //in this case we clean the session filter data

            //if any filter post was sent but we are in some page, we must load the session data


            //filtered && || paginated

            if ($search_string !== false && $order !== false || $this->uri->segment(3) == true) {


                /*

                The comments here are the same for line 79 until 99



                if post is not null, we store it in session data array

                if is null, we use the session data already stored

                we save order into the the var to load the view with the param already selected

                */


                if ($search_string) {


                    $filter_session_data['search_string_selected'] = $search_string;

                } else {

                    $search_string = $this->session->userdata('search_string_selected');

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


                $data['count_bidjob'] = $this->front_bid_model->count_bidjob($search_string, $order);

                $config['total_rows'] = $data['count_bidjob'];


                //fetch sql data into arrays

                if ($search_string) {

                    //echo $search_string;die;

                    if ($order) {

                        $data['bidjob'] = $this->front_bid_model->get_bidjob($search_string, $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_bid_model->get_bidjob($search_string, '', $order_type, $config['per_page'], $limit_end);

                    }

                } else {

                    if ($order) {

                        $data['bidjob'] = $this->front_bid_model->get_bidjob('', $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_bid_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                    }

                }


            } else {


                //clean filter data inside section;

                $filter_session_data['search_string_selected'] = null;

                $filter_session_data['order'] = null;

                $filter_session_data['order_type'] = null;

                $this->session->set_userdata($filter_session_data);


                //pre selected options

                $data['search_string_selected'] = '';

                $data['order'] = 'id';


                //fetch sql data into arrays

                $data['count_bidjob'] = $this->front_bid_model->count_bidjob();

                $data['bidjob'] = $this->front_bid_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                $config['total_rows'] = $data['count_bidjob'];


            }//!isset($manufacture_id) && !isset($search_string) && !isset($order)


            //initializate the panination helper

            $this->pagination->initialize($config);

            $dat = array(

                'is_awarded' => true

            );

            //echo'<pre>';print_r($dat);die;

            $this->session->set_userdata($dat);


            $this->load->view('translator/translators/vwProposal', $data);

        }

    }


    public function working()

    {


        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $filter_session_data = "";

            //all the posts sent by the view

            $search_string = $this->input->post('search_string');


            $order = $this->input->post('order');

            $order_type = $this->input->post('order_type');


            //pagination settings

            $config['per_page'] = 20;

            $config['base_url'] = base_url() . 'translator/working';

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

                    $order_type = 'DESC';

                }

            }

            //make the data type var avaible to our view

            $data['order_type_selected'] = $order_type;


            //we must avoid a page reload with the previous session data

            //if any filter post was sent, then it's the first time we load the content

            //in this case we clean the session filter data

            //if any filter post was sent but we are in some page, we must load the session data


            //filtered && || paginated

            if ($search_string !== false && $order !== false || $this->uri->segment(3) == true) {


                /*

                The comments here are the same for line 79 until 99



                if post is not null, we store it in session data array

                if is null, we use the session data already stored

                we save order into the the var to load the view with the param already selected

                */


                if ($search_string) {


                    $filter_session_data['search_string_selected'] = $search_string;

                } else {

                    $search_string = $this->session->userdata('search_string_selected');

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


                $data['count_bidjob'] = $this->front_working_model->count_bidjob($search_string, $order);

                $config['total_rows'] = $data['count_bidjob'];


                //fetch sql data into arrays

                if ($search_string) {

                    //echo $search_string;die;

                    if ($order) {

                        $data['bidjob'] = $this->front_working_model->get_bidjob($search_string, $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_working_model->get_bidjob($search_string, '', $order_type, $config['per_page'], $limit_end);

                    }

                } else {

                    if ($order) {

                        $data['bidjob'] = $this->front_working_model->get_bidjob('', $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_working_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                    }

                }


            } else {


                //clean filter data inside section;

                $filter_session_data['search_string_selected'] = null;

                $filter_session_data['order'] = null;

                $filter_session_data['order_type'] = null;

                $this->session->set_userdata($filter_session_data);


                //pre selected options

                $data['search_string_selected'] = '';

                $data['order'] = 'id';


                //fetch sql data into arrays

                $data['count_bidjob'] = $this->front_working_model->count_bidjob();

                $data['bidjob'] = $this->front_working_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                $config['total_rows'] = $data['count_bidjob'];


            }//!isset($manufacture_id) && !isset($search_string) && !isset($order)


            //initializate the panination helper

            $this->pagination->initialize($config);

            $dat = array(

                'is_working' => true

            );

            //echo'<pre>';print_r($dat);die;

            $this->session->set_userdata($dat);


            $this->load->view('translator/translators/vwWorkingJobs', $data);

        }

    }


    function message_index()

    {

        //echo 'test';die;

        $filter_session_data = "";

        //all the posts sent by the view

        $search_string = $this->input->post('search_string');

        $order = $this->input->post('order');

        $order_type = $this->input->post('order_type');


        //pagination settings

        $config['per_page'] = 10;

        $config['base_url'] = base_url() . 'translator/message_index/';

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

        if ($search_string !== false && $order !== false || $this->uri->segment(3) == true) {


            //echo $search_string;

            //echo $order;

            //echo $order_type;

            //echo $this->uri->segment(3); die;

            if ($search_string) {

                $filter_session_data['search_string_selected'] = $search_string;

            } else {

                $search_string = $this->session->userdata('search_string_selected');

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


            $data['count_messages'] = $this->front_message_model->count_messages($search_string, $order);

            $config['total_rows'] = $data['count_messages'];


            //fetch sql data into arrays

            if ($search_string) {

                if ($order) {

                    $data['messages'] = $this->front_message_model->get_messages($search_string, $order, $order_type, $config['per_page'], $limit_end);

                } else {

                    $data['messages'] = $this->front_message_model->get_messages($search_string, '', $order_type, $config['per_page'], $limit_end);

                }

            } else {

                if ($order) {

                    $data['messages'] = $this->front_message_model->get_messages('', $order, $order_type, $config['per_page'], $limit_end);

                } else {

                    $data['messages'] = $this->front_message_model->get_messages('', '', $order_type, $config['per_page'], $limit_end);

                }

            }


        } else {


            //clean filter data inside section;

            $filter_session_data['search_string_selected'] = null;

            $filter_session_data['order'] = null;

            $filter_session_data['order_type'] = null;

            $this->session->set_userdata($filter_session_data);


            //pre selected options

            $data['search_string_selected'] = '';

            $data['order'] = 'id';


            //fetch sql data into arrays

            $data['count_messages'] = $this->front_message_model->count_messages();

            $data['messages'] = $this->front_message_model->get_messages('', '', $order_type, $config['per_page'], $limit_end);

            $config['total_rows'] = $data['count_messages'];


        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)


        //initializate the panination helper

        $this->pagination->initialize($config);


        //load the view

        $this->load->view('translator/translators/vwMessageIndex', $data);


    }

    public function message()

    {


        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $filter_session_data = "";

            //all the posts sent by the view

            $search_string = $this->input->post('search_string');


            $order = $this->input->post('order');

            $order_type = $this->input->post('order_type');


            //pagination settings

            $config['per_page'] = 20;

            $config['base_url'] = base_url() . 'translator/message';

            $config['use_page_numbers'] = TRUE;

            $config['num_links'] = 20;

            $config['full_tag_open'] = '<ul>';

            $config['full_tag_close'] = '</ul>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a>';

            $config['cur_tag_close'] = '</a></li>';


            //limit end

            $page = $this->uri->segment(4);


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

                    $order_type = 'DESC';

                }

            }

            //make the data type var avaible to our view

            $data['order_type_selected'] = $order_type;


            //we must avoid a page reload with the previous session data

            //if any filter post was sent, then it's the first time we load the content

            //in this case we clean the session filter data

            //if any filter post was sent but we are in some page, we must load the session data


            //filtered && || paginated

            if ($search_string !== false && $order !== false || $this->uri->segment(4) == true) {


                /*

                The comments here are the same for line 79 until 99



                if post is not null, we store it in session data array

                if is null, we use the session data already stored

                we save order into the the var to load the view with the param already selected

                */


                if ($search_string) {


                    $filter_session_data['search_string_selected'] = $search_string;

                } else {

                    $search_string = $this->session->userdata('search_string_selected');

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


                $data['count_bidjob'] = $this->front_working_model->count_bidjob($search_string, $order);

                $config['total_rows'] = $data['count_bidjob'];


                //fetch sql data into arrays

                if ($search_string) {

                    //echo $search_string;die;

                    if ($order) {

                        $data['bidjob'] = $this->front_message_model->get_bidjob($search_string, $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_message_model->get_bidjob($search_string, '', $order_type, $config['per_page'], $limit_end);

                    }

                } else {

                    if ($order) {

                        $data['bidjob'] = $this->front_message_model->get_bidjob('', $order, $order_type, $config['per_page'], $limit_end);

                    } else {

                        $data['bidjob'] = $this->front_message_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                    }

                }


            } else {


                //clean filter data inside section;

                $filter_session_data['search_string_selected'] = null;

                $filter_session_data['order'] = null;

                $filter_session_data['order_type'] = null;

                $this->session->set_userdata($filter_session_data);


                //pre selected options

                $data['search_string_selected'] = '';

                $data['order'] = 'id';


                //fetch sql data into arrays

                $data['count_bidjob'] = $this->front_message_model->count_bidjob();

                $data['bidjob'] = $this->front_message_model->get_bidjob('', '', $order_type, $config['per_page'], $limit_end);

                $config['total_rows'] = $data['count_bidjob'];


            }//!isset($manufacture_id) && !isset($search_string) && !isset($order)


            //initializate the panination helper

            $this->pagination->initialize($config);

            $dat = array(

                'is_working' => true

            );

            //echo'<pre>';print_r($dat);die;

            $this->session->set_userdata($dat);


            $this->load->view('translator/translators/vwMessage', $data);

        }

    }


    public function chat()

    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {
            $trans_id = $this->session->userdata('translator_id');
            $jobs = $this->db->select('jobpost.name AS job_name, bidjob.id AS bid_id, jobpost.lineNumberCode AS  lineNumberCode, jobpost.id AS job_id')->join('jobpost', 'jobpost.id = bidjob.job_id', 'left')->get_where('bidjob', ['bidjob.trans_id' => $trans_id]);

            $this->load->view('translator/translators/vwChat', ['jobs' => $jobs]);

        }


    }


    public function reply()

    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $data['reply_id'] = $this->uri->segment(3);

            $data['job_id'] = $this->uri->segment(4);

            $data['trans_id'] = $this->uri->segment(5);


            $this->load->view('translator/translators/vwReply', $data);

        }

    }


    public function reply_message()

    {

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $reply_id = $this->input->post('reply_id');

            $job_id = $this->input->post('job_id');

            $trans_id = $this->input->post('trans_id');

            //$comp_time = $this->input->post('comp_time');

            //$invoice_id=$this->input->post('invoice_id');

            $reply = $this->input->post('reply');


            //echo'test';die;

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

            $upload_dir = './uploads/reply';

            if (!is_dir($upload_dir)) {

                mkdir($upload_dir);

            }

            $config['upload_path'] = $upload_dir;

            $config['allowed_types'] = 'jpeg|jpg|png|doc|docx|txt|pdf|xls|zip';

            if(!preg_match('/[^\x20-\x7f]/',$_FILES['file']['name'])){
                $config['file_name'] = $_FILES['file']['name'];
            }else{
                $config['file_name'] =time();
            }

            $config['overwrite'] = false;

            $config['max_size'] = '20000';

            $this->load->library('upload', $config);

            $this->upload->do_upload('file');

            $upload_data = $this->upload->data();

            $filename = $upload_data['file_name'];

            $image_config["image_library"] = "GD2";

            $image_config["source_image"] = $upload_data["full_path"];

            $image_config['create_thumb'] = FALSE;

            $image_config['maintain_ratio'] = TRUE;


            //echo $trans_name;die;


            $data['name'] = $trans_name;

            $data['job_title'] = $job_name;

            $data['reply'] = $reply;

            $data['file'] = $filename;

            $data['email'] = $trans_email;


            //$this->adminjobpost_model->store_message($data); die;

            //echo $trans_email;

            //$mailTo =$trans_email;

            $mailName = $trans_name;

            $this->email->set_mailtype("html");

            $this->email->from($trans_email);

            $sql = "SELECT * FROM settings WHERE id = '1'";

            $val = $this->db->query($sql);

            $fetch = $val->row();

            $email = $fetch->email;

            //echo $email;

            $this->email->to($email);

            $this->email->subject('Message');

            $html_email = $this->load->view('email/vwReplyMail', $data, true);

            $this->email->message($html_email);

            if ($_FILES['file']['name'] != '') {

                $path = set_realpath('uploads/reply');

                $this->email->attach($path . $filename);

            }

            $mail = $this->email->send();

            //echo $mail;die;

            if ($mail) {

                $sql1 = "SELECT * FROM message WHERE id = $reply_id";

                $val1 = $this->db->query($sql1);

                $fetch1 = $val1->row();

                $bid_id = $fetch1->bid_id;


                $data_to_store = array(

                    'reply_id' => $reply_id,

                    'type' => '1',

                    'job_id' => $job_id,

                    'trans_id' => $trans_id,

                    'bid_id' => $bid_id,

                    'text' => $reply,

                    'file' => $filename,

                    'modified' => date('Y-m-d h:i:s')

                );

                //echo '<pre>'; print_r($data_to_store);die;

                //if the insert has returned true then we show the flash message

                if ($this->front_message_model->store_reply($data_to_store)) {

                    $this->session->set_flashdata('success_message', 'Successfully Reply  Message');

                    redirect('translator/message/' . $job_id);

                }

            }

        }

    }


    function changeprofilepicture()
    {

        error_reporting(0);

        if (!$this->session->userdata('is_translator')) {

            $this->load->view('translator/vwLogin');

        } else {

            $data['message_error'] = "";

            $data['message_success'] = "";

            $luser_id = $this->session->userdata('translator_id');

            //$this->form_validation->set_rules('images', 'Image', 'trim|required');


            /*if($this->form_validation->run() == FALSE) {

                $this->session->set_flashdata('flash_error', 'errorValidation');

            } else*/
            {


                if ($_FILES['images']['size'] != 0) {

                    $timestamp = time();

                    $config['upload_path'] = './uploads/translator/profile';

                    $config['allowed_types'] = 'gif|jpg|png';

                    if(!preg_match('/[^\x20-\x7f]/',$_FILES['images']['name'])){
                     $config['file_name'] =  $_FILES['images']['name'];
                    }else {

                        $config['file_name'] = $timestamp;
                    }

                    $config['max_size'] = '1024';

                    $config['max_width'] = '2024';

                    $config['max_height'] = '2024';


                    $this->load->library('upload', $config);

                    if (!is_dir($config['upload_path'])) {

                        mkdir($config['upload_path'], 0755, TRUE);

                    }


                    if (!$this->upload->do_upload('images')) {

                        $data['message_error'] = $this->upload->display_errors();

                    } else {


                        //$this->load->library('image_lib');


//                        $this->upload->do_upload('images');

                        $upload_data = $this->upload->data();


                        $this->load->library('image_lib');


                        $imagesname = $upload_data['file_name'];

                        $image_config["image_library"] = "gd2";

                        $image_config["source_image"] = $upload_data["full_path"];

                        $image_config['create_thumb'] = FALSE;

                        $image_config['maintain_ratio'] = TRUE;

                        $image_config['quality'] = "100%";


                        $sql = "UPDATE translator SET

					`images`    = '" . $upload_data['file_name'] . "',

					 `modified` = '" . date('Y-m-d h:i:s') . "'



					WHERE id = '" . $luser_id . "'";

                        $val = $this->db->query($sql);

                        $path = './uploads/translator/profile/' . $this->input->post('preimage');

                        unlink($path);

                        $data['message_success'] = "Successfully Change Your Profile";

                        $this->session->set_flashdata('message_success', 'Profile image updated successfully');

                        redirect('translator/changeprofilepicture');

                    }

                }

            }

            $sql = "SELECT * FROM translator WHERE id = '" . $luser_id . "'";

            $data['results'] = array();

            $val = $this->db->query($sql);

            if ($val->num_rows) {

                $data['results'] = $val->result_array();

                //echo '<pre>'; print_r($data['results']);


            }

            $this->load->view('translator/translators/vwChangeprofilePicture', $data);

        }

    }


    public function forgotpassconfirm()
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');

            if ($this->form_validation->run() == FALSE) {
                $validation_errors = validation_errors();
                $this->session->set_flashdata('error_message', $validation_errors);
                $referrer = $this->agent->referrer();
                redirect($referrer);
            } else {
                $sql = "SELECT * FROM `translator` WHERE `email_address` = '" . $this->input->post('email_address') . "'";
                $val = $this->db->query($sql);
                $check = $val->num_rows();
                if ($check > 0) {

                    //echo 'test1'.$this->input->post('email_address');die;
                    $row = $val->row();
                    $Emaildata['first_name'] = $row->first_name;
                    $Emaildata['last_name'] = $row->last_name;
                    if($row->hash != '') {
                        $Emaildata['hash'] = $row->hash;
                    }else{
                        $Emaildata['hash'] = implode( array_map( function() { return dechex( mt_rand( 0, 15 ) ); }, array_fill( 0, 40, null ) ) );
                    }
                    $Emaildata['email'] = $this->input->post('email_address');


                    $this->email->set_mailtype("html");
                    $this->email->from(CONTACT_FROM, 'Translator Exchange Contact');
                    $this->email->to($Emaildata['email']);
                    $this->email->subject('Forgot Password Confirmation');


                    $html_email = $this->load->view('email/vwForgotPasswordConfirm', $Emaildata, true);
                    $this->email->message($html_email);
                    $mail = $this->email->send();
                    if ($mail) {

                        $data = array(
                            'reset_pass_con_time' => date('Y-m-d h-i-s'),
                            'hash' => $Emaildata['hash']
                        );
                        $this->db->where('email_address', $this->input->post('email_address'));
                        $this->db->update('translator', $data);

                        $this->session->set_flashdata('success_message', 'An email is sent with password reset link..');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    } else {
                        $this->session->set_flashdata('error_message', 'Some error occour please try again');
                        $referrer = $this->agent->referrer();
                        redirect($referrer);
                    }
                } else {
                    // $data['error_message'] = "This email address is not registered.";
                    $this->session->set_flashdata('error_message', 'This email address is not registered.');
                    $referrer = $this->agent->referrer();
                    redirect($referrer);
                }
            }
        }
        $this->load->view('translator/vwForgotPassTranslator');


    }


    public function forgotpass()
    {

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //echo 'test';die;
            $this->form_validation->set_rules('new_password', 'New password', 'trim|required');
            $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required');


            if ($this->form_validation->run() == FALSE) {
                $validation_errors = validation_errors();
                $this->session->set_flashdata('error_message', $validation_errors);
                $referrer = $this->agent->referrer();
                redirect($referrer);
            } else {
                $hash = $this->input->post('hash');
                $sql_translator = "select `email_address` from `translator` where `hash`='" . $hash . "' ";
                $val_translator = $this->db->query($sql_translator);
                $fetch_translator = $val_translator->row();
                $email = $fetch_translator->email_address;

                $sql = "SELECT * FROM `translator` WHERE `email_address` = '" . $email . "'";
                $val = $this->db->query($sql);
                $check = $val->num_rows();
                if ($check > 0) {
                    $row = $val->row();
                    $Emaildata['first_name'] = $row->first_name;
                    $Emaildata['last_name'] = $row->last_name;
                    $Emaildata['email'] = $email;


                    $sql1 = "select `reset_pass_con_time` from `translator` where `email_address`='" . $email . "' ";
                    $val1 = $this->db->query($sql1);
                    $fetch1 = $val1->row();
                    $reset_pass_con_time = $fetch1->reset_pass_con_time;

                    $expire = strtotime('+1 day',strtotime($reset_pass_con_time));
                    $now = strtotime(date('Y-m-d H:i:s'));
                    if ($now <= $expire) {
                        $new_password = $this->input->post('new_password');
                        $confirm_password = $this->input->post('confirm_password');

                        if ($new_password == $confirm_password) {
                            $password = md5($confirm_password);
                            $data = array(
                                'pass_word' => $password
                            );

                            $this->db->where('email_address', $email);
                            $val2 = $this->db->update('translator', $data);


                        }

                        if ($val2) {
                            $this->email->set_mailtype("html");
                            $this->email->from(CONTACT_FROM);
                            $this->email->to($email);
                            $this->email->subject('Forgot Password');

                            //echo '<pre>'; print_r($Emaildata); die;

                            $html_email = $this->load->view('email/vwForgotPasswordTranslator', $Emaildata, true);
                            $this->email->message($html_email);
                            $this->email->send();


                            $this->session->set_flashdata('success_message', 'Your password is successfully changed.');

                            redirect('translator/forgotpass');
                        } else {
                            $this->session->set_flashdata('error_message', 'Password Not updated please try again');

                            redirect('translator/forgotpass/'.$hash);
                        }

                    } else {
                        $this->session->set_flashdata('error_message', 'Your reset password request was invalid');

                        redirect('translator/forgotpassconfirm');
                    }
                } else {
                    // $data['error_message'] = "This email address is not registered.";
                    $this->session->set_flashdata('error_message', 'This email address is not registered.');

                    redirect('translator/forgotpassconfirm');
                }

            }
        }
        $this->load->view('translator/vwResetPassTranslator');
    }

    function jobIsDone()
    {
        $job_id = (int)$this->input->post('data');
        $sql = "SELECT * FROM jobpost WHERE id = " . $job_id;
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            $jobpost = $query->row();

            $proofread_query = $this->db->from('proofread_jobs')->where('job_id', $job_id)->get();
            $proofread_job = $proofread_query->row();

            if ($jobpost->proofread_required and $proofread_job->review_stage > 0) {

                // save ratings
                $sql = "SELECT * FROM ajax_chat_messages WHERE bid_id = '" . $_POST['bidjob_id'] . "' AND job_id = '" . $_POST['data'] . "' AND text LIKE 'Rating%' ORDER BY dateTime DESC LIMIT " . $_POST['count'];
                $query = $this->db->query($sql);

                if ($query->num_rows()) {
                    $translators = explode(',', $_POST['translators']);

                    $ids = array_reverse($translators);

                    foreach ($query->result_array() as $i => $result) {
                        $rating_str = trim($result['text'], 'Rating: ');
                        $rating_arr = explode(',', $rating_str);
                        $rating = explode('/', $rating_arr[1]);

                        $rating_val = (int)$rating[0];

                        $this->db->insert('ratings', array(
                            'translator_id' => $ids[$i],
                            'job_id' => $_POST['data'],
                            'bidjob_id' => $_POST['bidjob_id'],
                            'rating' => $rating_val,
                            'date_rated' => date('Y-m-d H:i:s')
                        ));
                    }
                }
                $admin_id = $this->session->userdata('admin_id');
                if ($admin_id) {
                    $this->db->update('bidjob', array('admin_notif' => 0, 'is_rated' => 1, 'stage' => 2, 'completed_admin_id' => $admin_id), array('id' => $_POST['bidjob_id']));
                } else {
                    $this->db->update('bidjob', array('admin_notif' => 0, 'is_rated' => 1, 'stage' => 2), array('id' => $_POST['bidjob_id']));
                }
            } else {
                $admin_id = $this->session->userdata('admin_id');
                if ($admin_id) {
                    $this->db->update("bidjob", array("is_done" => 1, 'complete_date' => date('Y-m-d H:i:s'), 'completed_admin_id' => $admin_id), array("id" => $_POST['bidjob_id']));
                } else {
                    $this->db->update("bidjob", array("is_done" => 1, 'complete_date' => date('Y-m-d H:i:s')), array("id" => $_POST['bidjob_id']));
                }
            }
        }

        echo "true";
    }

    function admin_rating()
    {
        if ($this->session->userdata('admin_id') != false) {
            $job_id = $this->input->post('job_id');
            $bidjob_id = $this->input->post('bidjob_id');
            $trans_id = $this->input->post('trans_id');
            $rating = $this->input->post('rating');
            $admin_id = $this->session->userdata('admin_id');

            $ratings = [
                'translator_id' => $trans_id,
                'job_id' => $job_id,
                'bidjob_id' => $bidjob_id,
                'rating' => $rating,
                'date_rated' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('ratings', $ratings);

            $this->db->update('bidjob', array('is_rated' => 1, 'stage' => 2, 'is_done' => 1, 'complete_date' => date('Y-m-d H:i:s'), 'completed_admin_id' => $admin_id), array('id' => $bidjob_id));
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
            $data['job_created'] = $job_data->created;
            $data['job_id'] = $job_id;
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
			$data['bid_price'] = $bid_data->price;


            $this->email->set_mailtype("html");
            $this->email->from('info@montesinotranslation.com');
            $this->email->to($mailTo);
            $this->email->subject('Job Completion Invoice');
            $html_email = $this->load->view('email/vwJobCompletionInvoice', $data, true);
            $this->email->message($html_email);
            $mail = $this->email->send();

            $jobpost = $this->db->from('jobpost')->where('id', $job_id)->get();

            $post_to_chat_box = [
                'bid_id' => $bidjob_id,
                'job_id' => $job_id,
                'trans_id' => $trans_id,
                'type' => 'admin',
                'status' => 'unread',
                'jobname' => $jobpost->row()->name,
                'userID' => 1,
                'userName' => 'Guest',
                'channel' => 1,
                'dateTime' => date('Y-m-d H:i:s'),
                'text' => 'Admin has verified completion',
                'ip' => '127.0.0.1'
            ];

            $this->db->insert('ajax_chat_messages', $post_to_chat_box);

            $check_proofread = $this->db->from('proofread_jobs')->where(['job_id' => $job_id, 'review_stage' => 0])->get();

            if ($check_proofread->num_rows()) {
                $this->db->update('proofread_jobs', array('review_stage' => 0, 'translator_id' => $trans_id), array('job_id' => $job_id));
            }
            $trans_email = $this->db->get_where('translator',['id'=> $trans_id])->first_row();
            $mailName = $trans_email->first_name.' '.$trans_email->last_name;
            $job_data = $this->db->get_where('jobpost',['id' =>$job_id])->first_row();
            $bid_data = $this->db->get_where('bidjob',['id' => $bidjob_id])->first_row();
                $mailTo = $trans_email->email_address;
                $data['name'] = $mailName;
                $data['job_name'] = $job_data->name;
                $data['job_description'] = $job_data->desc;
                $data['job_created'] = $job_data->created;
                $data['job_alias'] = $job_data->alias;
                $data['job_id'] = $job_id;

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
				$data['bid_price'] = $bid_data->price;


                $this->email->set_mailtype("html");
                $this->email->from('info@montesinotranslation.com');
                $this->email->to($mailTo);
                $this->email->subject('Job Completion Invoice');
                $html_email = $this->load->view('email/vwJobCompletionInvoice', $data, true);
                $this->email->message($html_email);
                $mail = $this->email->send();

            echo "true";
        }
    }

    function admin_marked_completed()
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $job_id = $this->input->post('job_id');

            $bidjob_id = $this->input->post('bidjob_id');
            $trans_id = $this->input->post('trans_id');
            $admin_id= $this->session->userdata('admin_id');

            if($admin_id){
                $this->db->update('bidjob', array('is_rated' => 0, 'stage' => 3, 'is_done' => 1, 'complete_date' => date('Y-m-d H:i:s'),'completed_admin_id' => $admin_id), array('id' => $bidjob_id));
            }else{
                $this->db->update('bidjob', array('is_rated' => 0, 'stage' => 3, 'is_done' => 1, 'complete_date' => date('Y-m-d H:i:s')), array('id' => $bidjob_id));
            }
            $check_proofread = $this->db->from('jobpost')->where(array('id' => $job_id, 'proofread_required' => -1))->get();

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
            $data['job_id'] = $job_id;
            $data['job_description'] = $job_data->desc;
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
			$data['bid_price'] = $bid_data->price;


            $this->email->set_mailtype("html");
            $this->email->from('info@montesinotranslation.com');
            $this->email->to($mailTo);
            $this->email->subject('Job Completion Invoice');
            $html_email = $this->load->view('email/vwJobCompletionInvoice', $data, true);
            $this->email->message($html_email);
            $mail = $this->email->send();

            if ($check_proofread->num_rows()) {

                $check = $this->db->from('jobpost')->where('parent_id', $job_id)->get();

                if ($check->num_rows()) {
                } else {
                    $row = $check_proofread->row();

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
                        'created' => date('Y-m-d H:i:s'),
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
                        $job_id = $this->db->insert_id();
                        $sql1 = "select * from jobpost where id=$job_id and job_type=0";
                        $val1 = $this->db->query($sql1);
                        $check = $val1->num_rows();

                        // store details in proofread_jobs table
                        $proofread_jobs_data = array(
                            'job_id' => $job_id,
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




            // $this->db->update('jobpost', array('proofread_required' => 1, 'stage' => 0), array('id' => $job_id));
            //
            // $query = $this->db->from('jobpost')->where(array('id' => $job_id))->get();
            //
            // if ($query->num_rows()) {
            //     $proofread_jobs_data = array(
            //         'job_id' => $job_id,
            //         'translator_id' => '',
            //         'translation_completed' => 0,
            //         'review_price' => 0,
            //         'review_stage' => 0,
            //         'review_type' => 1
            //     );
            //
            //     $this->db->insert('proofread_jobs', $proofread_jobs_data);
            // }

            // $check_proofread = $this->db->from('proofread_jobs')->where(['job_id' => $job_id, 'review_stage' => 0])->get();
            //
            // if ($check_proofread->num_rows()) {
            //     $this->db->update('proofread_jobs', array('review_stage' => 0, 'translator_id' => $trans_id), array('job_id' => $job_id));
            // }

            echo 'true';
        }
    }

    function admin_mark_proofread_job_complete()
    {
        if ($this->session->userdata('admin_id') != false) {
            $admin_id = $this->session->userdata('admin_id');
            $job_id = $this->input->post('job_id');
            $bidjob_id = $this->input->post('bidjob_id');
            $trans_id = $this->input->post('trans_id');
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
                $invoice_id = time();
                $this->db->insert('invoice', array(
                    'bid_id' => $bidjob_id,
                    'invoice_id' => $invoice_id,
                    'job_id' => $job_id,
                    'trans_id' => $trans_id,
                    'payment_date' => date('Y-m-d', strtotime('+30 days'))
                ));
            } else {
                $this->db->update('bidjob', array('stage' => 2, 'is_done' => 1, 'is_rated' => 0, 'complete_date' => date('Y-m-d H:i:s'), 'completed_admin_id' => $admin_id), array('id' => $bidjob_id));
            }

            $trans_email = $this->db->get_where('translator',['id'=> $trans_id])->first_row();
            $mailName = $trans_email->first_name.' '.$trans_email->last_name;
            $job_data = $this->db->get_where('jobpost',['id' =>$job_id])->first_row();
            $bid_data = $this->db->get_where('bidjob',['id' => $bidjob_id])->first_row();
            $mailTo = $trans_email->email_address;
            $data['name'] = $mailName;
            $data['job_name'] = $job_data->name;
            $data['job_description'] = $job_data->desc;
            $data['job_created'] = $job_data->created;
            $data['job_alias'] = $job_data->alias;
            $data['job_id'] = $job_id;

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
			$data['bid_price'] = $bid_data->price;


            $this->email->set_mailtype("html");
            $this->email->from('info@montesinotranslation.com');
            $this->email->to($mailTo);
            $this->email->subject('Job Completion Invoice');
            $html_email = $this->load->view('email/vwJobCompletionInvoice', $data, true);
            $this->email->message($html_email);
            $mail = $this->email->send();


            echo "true";
        }
    }

    function close_award_notification()
    {
        $bidjob_id = $this->input->post('bidjob_id');

        $this->db->update('bidjob', ['show_notification' => 0], ['id' => $bidjob_id]);

        echo "true";
    }

    function add_action()
    {
        $pid_selected=$this->input->post('pid_selected');
        $p=($pid_selected==0)?$this->input->post('languageTo'):$this->input->post('languageTo2');
        $temporary_password = random_string('alnum', 8);
        $checkProofread = $this->input->post('proofreader');
      //  echo $checkProofread;exit;
        if($checkProofread==1)
        {
            $languageFrom = "P";
        }else{
            $languageFrom = $this->input->post('languageFrom');
        }

        $languageTo =$p;
        $lang = '';
        for ($x = 0; $x < count($languageFrom); $x++) {
            $lang.= $languageFrom[$x] . "/" . $languageTo[$x] . ",";
        }

        //$return['tolang']=$l;
       // $return['lang']=serialize($lang);
        $language = "," . $lang;
        $data = array("first_name" => $this->input->post('first_name', true),
            "last_name" => $this->input->post('last_name', TRUE),
            "pass_word" => md5($temporary_password),
            "user_name" => $this->input->post('email', TRUE),
            "email_address" => $this->input->post('email', TRUE),
            "location" => $this->input->post('location', TRUE),
            "language" => $language,
            "verified" => '1'
        );
        $this->db->insert('translator', $data);
        $mailTo = $this->input->post('email', TRUE);
        $first_name = $this->input->post('first_name', true);
        $last_name = $this->input->post('last_name', TRUE);
        $name = $this->input->post('first_name', true) . ' ' . $this->input->post('last_name', true);
        $mailId = $this->db->insert_id();
        $date = date('Y-m-d H:i:s');

        $data['language'] = $language;
        $data['name'] = $name;
        $data['first_name'] = $first_name;
        $data['last_name'] = $last_name;
        $data['user_name'] = $this->input->post('email', TRUE);
        $data['temp_password'] = $temporary_password;
        $data['id'] = $this->db->insert_id();
        $data['created'] = $date;

        $this->email->set_mailtype("html");
        $this->email->from('info@montesinotranslation.com');
        $this->email->to($mailTo);
        $this->email->subject('Invitation Email');
        $html_email = $this->load->view('email/email_invitation', $data, true);

        $this->email->message($html_email);
        $this->email->send();

       // echo json_encode($return);
    }

    function add_translator()
    {
        $data['from'] = $this->common_model->get_all('languages', '*', 'id', 'asc');
        $this->load->view('admin/add_translator', $data);
    }

    function check()
    {

        $query = $this->db->get_where('translator', array('email_address' => $this->input->post('email')));

        if ($query->num_rows() == '0') {
            $is_available = true;
        } else {
            $is_available = false;
        }

        echo json_encode(array('valid' => $is_available));
    }

    function update_inbox()
    {
        $data_msg['res'] = 0;
        if ($this->input->server("REQUEST_METHOD") == "POST" && $this->input->is_ajax_request() == true) {
            $trans_id = $this->session->userdata('translator_id');
            if (isset($trans_id) && $trans_id != '') {
                $data_msg['messages'] = $this->db->query("SELECT * FROM ajax_chat_messages  WHERE ajax_chat_messages.status = 'unread' AND ajax_chat_messages.type = 'admin' AND ajax_chat_messages.type= 'admin' AND ajax_chat_messages.trans_id= " . $trans_id . " AND ajax_chat_messages.bid_id IN (SELECT bidjob.id FROM bidjob WHERE bidjob.trans_id = " . $trans_id . ")")->num_rows;
                $data_msg['notifications'] = $this->translators_model->getCountUnreadNotification($trans_id);
                $data_msg['res'] = 1;
                echo json_encode($data_msg);
            } else {
                echo json_encode($data_msg);
                exit();
            }
        } else {
            echo json_encode($data_msg);
            exit();
        }
    }

    function getimages(){
        if($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->is_ajax_request() == true){
            $id = $this->input->post('id',true);
            $data_msg['res'] = 0;
            if($id != ''){
                $imges = $this->db->get_where('proofread_jobs_docs',['id' => $id]);

                if($imges->num_rows() > 0){
                    $this->load->helper('file');
                    $imges = $imges->first_row();
                    $data['file_path_org']   = './uploads/review/' . str_replace(' ', '_',  $imges->original_file);
                    $data['file_path_trans'] = './uploads/review/' . str_replace(' ', '_',  $imges->translated_file);
                    $data['file_path_org_type'] = get_mime_by_extension($data['file_path_org']);
                    $data['file_path_org'] = base_url().'uploads/review/' . str_replace(' ', '_',  $imges->original_file);
                    $data['file_path_trans_type'] = get_mime_by_extension($data['file_path_trans']);
                    $data['file_path_trans'] = base_url().'uploads/review/' . str_replace(' ', '_',  $imges->translated_file);
                   $data_msg['html'] = $this->load->view('ajax_modals/modal_body',$data,true);
                    $data_msg['res'] = 1;
                }
            }
            //echo 'dasdasdas';exit();
            echo json_encode($data_msg);
            exit();
        }
    }

    function delete_chat_file(){
        if($this->input->is_ajax_request() == true && $this->input->server('REQUEST_METHOD') == 'POST'){
                $file = $this->input->post('file');
                $file_dir = $this->input->post('file_dir',true);
//                $date = date('Y-m-d G:i:s',$this->input->post('date'));

                $query = $this->db->delete('ajax_chat_messages',['text LIKE "%'.$file_dir.'/'.$file.'%"' => NULL]);
                if($this->db->affected_rows() > 0){
                    unlink('./chat-box/uploads/'.$file_dir.'/'.$file);
                    echo 'success';
                    exit();
                }else{
                    echo 'failure';
                    exit();
                }
        }
    }
    function translateTo()
    {
        $valu=$this->input->post("valu");
       // $from=$this->input->post("from");
        if($valu==1) {
            echo '<td>
                        <select class = "form-control selectpicker" name = "languageFrom[]" data-live-search="true">
   						<option>Proofreader</option>
   						</select>
                    </td>
                    <td><select class = "form-control selectpicker" name = "languageTo2[]" id="lang_to"  data-live-search="true">                     
                <option value="1">English</option>
                </select>
                </td>';
        }else{
            echo '<select class = "form-control selectpicker" name = "languageTo[]" id="lang_to"  data-live-search="true">
<option>Select Language</option>'.
            $languages=$this->db->get('languages')->result();
            foreach($languages as $row){
   							echo '<option value="'.$row->id.'">'.$row->name.'</option>';
   						}
   						echo '</select>';

             }

    }
function new_registration()
{

$id=$this->input->post("id");
$con_pass_word=md5($this->input->post("pass_word"));


$lang = "";
                $language_from1 = $this->input->post('language_from1');
                $language1 = $this->input->post('language1');
                if ($language_from1 != "" && $language1 != "") {
                    $lang .= $language_from1 . "/" . $language1 . ",";
                }

                $language_from2 = $this->input->post('language_from2');
                $language2 = $this->input->post('language2');
                if ($language_from2 != "" && $language2 != "") {
                    $lang .= $language_from2 . "/" . $language2 . ",";
                }

                $language_from3 = $this->input->post('language_from3');
                $language3 = $this->input->post('language3');
                if ($language_from3 != "" && $language3 != "") {
                    $lang .= $language_from3 . "/" . $language3 . ",";
                }

                $language_from4 = $this->input->post('language_from4');
                $language4 = $this->input->post('language4');
                if ($language_from4 != "" && $language4 != "") {
                    $lang .= $language_from4 . "/" . $language4 . ",";
                }

                $language_from5 = $this->input->post('language_from5');
                $language5 = $this->input->post('language5');
                if ($language_from5 != "" && $language5 != "") {
                    $lang .= $language_from5 . "/" . $language5 . ",";
                }

                $language_from6 = $this->input->post('language_from6');
                $language6 = $this->input->post('language6');
                if ($language_from6 != "" && $language6 != "") {
                    $lang .= $language_from6 . "/" . $language6 . ",";
                }
               $language = "," . $lang;
                $data1=array(

                    "pass_word" => $con_pass_word,
                    "language" => $language
                );
                $data2=array(

                    "pass_word" => $con_pass_word
                );


    $sql="select language from translator where id='$id'";
    $val = $this->db->query($sql);
    $language=$val->result();
//print_r($language);exit;
    $language1=$language[0]->language;
    // echo $language1;die;


    $findme   = 'P';
    $pos = strpos($language1, $findme);
    if ($pos === false) {

      //  echo '1'; die;
        $this->translators_model->Update_data1($id,$data1);
        redirect('translator/dashboard');


    } else {
        //echo '2'; die;
        $this->translators_model->Update_data2($id,$data2);
        redirect('translator/dashboard');
    }
}

    function block_me(){
        $id=$this->session->userdata('translator_id');
        if(isset($id) && $id!=''){
            $archive_data_check=$this->translators_model->check_data(['id'=>$id]);
           $mailTo=$archive_data_check[0]->email_address;
           $data['name']=ucwords($archive_data_check[0]->first_name.' '.$archive_data_check[0]->last_name);
            $data['msg']='You have deactivated your account by your own on '.date('d-m-Y H:i:s A');

            $this->email->set_mailtype("html");
            $this->email->from('info@montesinotranslation.com');
            $this->email->to($mailTo);
            $this->email->subject('Account Deactivation Confirmation Email');
            $html_email = $this->load->view('email/email_account_deactivation', $data, true);

            $this->email->message($html_email);
            $this->email->send();
            $this->translators_model->copy_table($id);
            $update=$this->translators_model->update_data(['verified'=>2,'status'=>2,'de_act_status'=>1,'language'=>''],['id'=>$id]);
            if($update){
                $this->session->sess_destroy();
                $return['redirect']=base_url();
            } else{
                $return['error']='Error';
            } 
        } else{
            $return['warning']='Please login to deactivate your account';
        }

         echo json_encode($return);
    }



}

