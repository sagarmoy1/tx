<?php

//error_reporting(0);
class Admin extends CI_Controller
{

    /**
     * Check if the admin is logged in, if he's not,
     * send him to the login page
     * @return void
     */

    function CheckDbUpdates()
    {

        $date = new DateTime();
        $date->sub(new DateInterval('P15D'));
        $curdate = $date->format('Y-m-d');
//        $query = $this->db->query("SELECT `stage`,`id`,`dueDate` FROM `jobpost` WHERE date_format(str_to_date(`dueDate`, '%m-%d-%Y'), '%Y-%m-%d') < str_to_date('".$curdate."', '%Y-%m-%d') AND `stage` = 0 ");
        $query = $this->db->query("UPDATE `jobpost` SET `stage` = 2 WHERE date_format(str_to_date(`dueDate`, '%m-%d-%Y'), '%Y-%m-%d') < str_to_date('" . $curdate . "', '%Y-%m-%d') AND `stage` = 0 ");
    }

    function index()
    {
        if ($this->session->userdata('is_admin') && $this->session->userdata('admin_type') != 3) {
            redirect('dashboard/index');
        } else {
            $this->load->view('admin/vwLogin');
        }
    }

    /**
     * encript the password
     * @return mixed
     */
    function __encrip_password($password)
    {
        return md5($password);
    }

    function test()
    {
        echo 'test';
    }

    /**
     * check the username and the password with the database
     * @return void
     */
    function validate_credentials()
    {
        $this->load->model('Admins_model');

        $user_name = $this->input->post('user_name');
        $password = $this->__encrip_password($this->input->post('password'));

        //$is_valid = $this->Admins_model->validate($user_name, $password);

        $sql = "SELECT * FROM admin WHERE user_name = ? AND pass_word = ?";
        $val = $this->db->query($sql, array($user_name, $password));
        $is_valid = $val->num_rows;
        if ($is_valid == 1) {

            $sql2 = "SELECT * FROM admin WHERE user_name = ? AND pass_word = ? AND status = 1";
            $val2 = $this->db->query($sql2, array($user_name, $password));
            $is_active = $val2->num_rows;
            if ($is_active == 1) {

                foreach ($val->result_array() as $res) {
                    $data = array(
                        'admin_id' => $res['id'],
                        'user_name' => $res['user_name'],
                        'email' => $res['email_addres'],
                        'admin_type' => $res['admin_type'],
                        'is_logged_in' => true,
                        'is_admin' => ($res['admin_type'] == 3 ? false : true)
                    );

                    $this->session->set_userdata($data);
                    $this->session->set_flashdata('success_message', 'Successfully Loged In');
                    if ($res['admin_type'] == 3) {
                        redirect("cs_admin/index");
                    } else {
                        redirect('dashboard/index');
                    }
                }

            } else {
                //$data['message_error'] = "Your account is inactive";
                //$this->load->view('admin/vwLogin', $data);

                $this->session->set_flashdata('error_message', 'Your account is inactive');
                redirect('admin/index');

            }


        } else // incorrect username or password
        {
            //$data['message_error'] = "Invalid username/password";
            //$this->load->view('admin/vwLogin', $data);

            $this->session->set_flashdata('error_message', 'Invalid username/password');
            redirect('admin/index');
        }
    }

    /**
     * The method just loads the signup view
     * @return void
     */
    function signup()
    {
        $this->load->view('admin/signup_form');
    }


    /**
     * Create new admin and store it in the database
     * @return void
     */
    function create_member()
    {

        // field name, error message, validation rules
        $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/signup_form');
        } else {
            $this->load->model('Admins_model');

            if ($query = $this->Admins_model->create_member()) {
                $this->load->view('admin/signup_successful');
            } else {
                $this->load->view('admin/signup_form');
            }
        }

    }

    /**
     * Destroy the session, and logout the admin.
     * @return void
     */
    function logout()
    {
        $this->session->sess_destroy();
        redirect('admin');
    }


    /*public function dashboard() {
        $arr['page']='dash';
        $this->load->view('admin/admins/vwDashboard',$arr);
    }
    */

    function changepass()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";

            $ladmin_id = $this->session->userdata('admin_id');
            //die;
            $this->form_validation->set_rules('old_word', 'Password', 'trim|required|min_length[4]|max_length[32]');
            $this->form_validation->set_rules('pass_word', 'Password', 'trim|required|min_length[4]|max_length[32]');
            $this->form_validation->set_rules('con_pass_word', 'Password Confirmation', 'trim|required|matches[pass_word]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('flash_error', 'errorValidation');
                $this->load->view('admin/vwChangepass', $data);
            } else {
                $check_sql = "SELECT * FROM admin WHERE pass_word = ? AND id = ?";
                $check_val = $this->db->query($check_sql, array($this->__encrip_password($this->input->post('old_word')), $ladmin_id));
                $current = $check_val->num_rows();

                if ($current > 0) {
                    if ($this->input->post('pass_word') == $this->input->post('con_pass_word')) {
                        $sql = "UPDATE admin SET `pass_word` = ? WHERE id = ?";
                        $val = $this->db->query($sql, array($this->__encrip_password($this->input->post('pass_word')), $ladmin_id));
                        $this->session->set_flashdata('success_message', 'Successfully Change your password..');
                        redirect('admin/changepass');
                    }

                } else {

                    $this->session->set_flashdata('error_message', 'Your Old password does not match.');
                    redirect('admin/changepass');
                }
            }
        } else {
            $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
            redirect('admin/index');
        }


    }


    function changeprofile()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";

            $ladmin_id = $this->session->userdata('admin_id');

            $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
            //$this->form_validation->set_rules('alter_email', 'Email Address', 'trim|required|valid_email');
            //$this->form_validation->set_rules('phone', 'Phone', 'required|regex_match[/^[0-9]+$/]|xss_clean');
            $this->form_validation->set_rules('email_addres', 'Admin Email', 'trim|required');
            //$this->form_validation->set_rules('site_name', 'Site Title', 'trim|required');
            $sql = "SELECT * FROM `admin` WHERE id = '" . $ladmin_id . "'";
            $val = $this->db->query($sql);
            foreach ($val->result_array() as $recs => $res) {
                $data['user_name'] = $res['user_name'];
                $data['first_name'] = $res['first_name'];
                $data['last_name'] = $res['last_name'];
                $data['email_addres'] = $res['email_addres'];
                $data['phone'] = $res['phone'];
                $data['alter_email'] = $res['alter_email'];

            }
            //$content['user_id']= $insert_id;
            //echo $current;
            //echo $this->__encrip_password($this->input->post('pass_word'));

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('flash_error', 'errorValidation');
                $this->load->view('admin/vwChangeprofile', $data);
            } else {
                //echo $ladmin_id;die;
                $sql = "UPDATE admin SET `first_name` = ?, `last_name` = ?, `email_addres` = ?, `phone` = ?, `alter_email` = ? WHERE id = ?";
                $val = $this->db->query($sql, array($this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('email_addres'), $this->input->post('phone'), $this->input->post('alter_email'), $ladmin_id));
                $this->session->set_flashdata('success_message', 'Successfully Change your Profile.');
                redirect('admin/changeprofile');

            }
        } else {
            $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
            redirect('admin/index');
        }

    }


    public function sitesettings()
    {

        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";

            $this->form_validation->set_rules('title', 'title', 'trim|required');
            $this->form_validation->set_rules('tag_line', 'tag_line', 'trim|required');
            $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
            $this->form_validation->set_rules('paypal_id', 'paypal_id', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone', 'required|regex_match[/^[0-9]+$/]|xss_clean');
            $this->form_validation->set_rules('address', 'address', 'trim|required');
            $this->form_validation->set_rules('facebook', 'facebook', 'trim|required|prep_url');
            $this->form_validation->set_rules('twitter', 'twitter', 'trim|required');
            $this->form_validation->set_rules('googlep', 'googlep', 'trim|required');
            $this->form_validation->set_rules('instagram', 'instagram', 'trim|required');
            $this->form_validation->set_rules('youtube', 'youtube', 'trim|required');
            $this->form_validation->set_rules('printerus', 'printerus', 'trim|required');
            //$this->form_validation->set_rules('created', 'created', 'trim|required');
            //$this->form_validation->set_rules('modified', 'modified', 'trim|required');

            //$content['user_id']= $insert_id;
            //echo $current;
            //echo $this->__encrip_password($this->input->post('pass_word'));

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('flash_error', 'errorValidation');

            } else {
                $sql = "UPDATE `settings` SET `title` = ?, `tag_line` = ?, `email` = ?, `paypal_id` = ?, `phone` = ?, `address` = ?, `facebook` = ?, `twitter` = ?, `googlep` = ?, `instagram` = ?, `youtube` = ?, `printerus` = ? WHERE id = '1'";
                $val = $this->db->query($sql, array(
                    $this->input->post('title'), $this->input->post('tag_line'), $this->input->post('email'),
                    $this->input->post('paypal_id'), $this->input->post('phone'), $this->input->post('address'),
                    $this->input->post('facebook'), $this->input->post('twitter'), $this->input->post('googlep'),
                    $this->input->post('instagram'), $this->input->post('youtube'), $this->input->post('printerus')
                ));
                $data['message_success'] = "Successfully Change Your Profile";
                $this->session->set_flashdata('success_message', 'Successfully Change your Profile.');
                redirect('admin/sitesettings');

            }

            $sql = "SELECT * FROM `settings` WHERE id = '1'";
            $val = $this->db->query($sql);
            $data['fetch'] = $val->row();
            //echo '<pre>'; print_r($fetch); die;
            $this->load->view('admin/vwSettings', $data);
        } else {
            $this->session->set_flashdata('error_message', 'Sorry, some problem occured. Please try again');
            redirect('admin/index');
        }


    }


    public function forgotpass()
    {
        $data['message_error'] = "";
        $data['message_success'] = "";
        $this->form_validation->set_rules('email_addres', 'Email Address', 'trim|required|valid_email');
        //$newpass=$this->form_validation->set_rules('pass_word', 'Password', 'trim|required|min_length[4]|max_length[32]');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('flash_error', 'errorValidation');
        } else {

            $sql = "SELECT * FROM admin WHERE email_addres = ?";

            $val = $this->db->query($sql, array($this->input->post('email_addres')));
            $check = $val->num_rows();
            if ($check > 0) {
                $row = $val->row();
                $Emaildata['first_name'] = $row->first_name;
                $Emaildata['last_name'] = $row->last_name;
                $Emaildata['username'] = $this->input->post('email_addres');
                $Emaildata['password'] = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);


                $pass_word = md5($Emaildata['password']);


                $sql = "UPDATE admin SET `pass_word` = ? WHERE email_addres = ?";
                $val = $this->db->query($sql, array($pass_word, $this->input->post('email_addres')));
                if ($val) {
                    $this->email->set_mailtype("html");
                    $this->email->from('info@translation.co.uk');
                    $this->email->to($this->input->post('email_addres')); //$user_insert_data['email_address']
                    $this->email->subject('Forgot Password');

                    //echo '<pre>'; print_r($Emaildata); die;

                    $html_email = $this->load->view('email/vwForgotPasswordAdmin', $Emaildata, true);
                    $this->email->message($html_email);
                    $this->email->send();

                    //$data['success_message'] = "Please check your email for username and password.";
                    $this->session->set_flashdata('success_message', 'Please check your email for username and password.');
                    redirect('admin/forgotpass');
                } else {
                    $this->session->set_flashdata('error_message', 'Password Not updated please try again');
                    redirect('admin/forgotpass');
                }
            } else {
                // $data['error_message'] = "This email address is not registered.";
                $this->session->set_flashdata('error_message', 'This email address is not registered.');
                redirect('admin/forgotpass');
            }
        }

        $this->load->view('admin/vwForgotpassword', $data);
    }


    public function addadmin()
    {
        $data['message_error'] = "";
        $data['message_success'] = "";
        // field name, error message, validation rules
        $this->form_validation->set_rules('admin_type', 'Admin Type', 'trim|required');
        $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
        //$this->form_validation->set_rules('user_name', 'User Name', 'trim|required');
        //$this->form_validation->set_rules('phone_no', 'Phone No', 'trim|required');
        $this->form_validation->set_rules('pass_word', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('con_pass_word', 'Password Confirmation', 'trim|required|matches[pass_word]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('flash_error', 'errorValidation');
            $this->load->view('admin/vwRegister', $data);
        } else {

            $admin_insert_data = array(
                'admin_type' => $this->input->post('admin_type'),
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email_addres' => $this->input->post('email_address'),
                'user_name' => $this->input->post('email_address'),
                'phone' => $this->input->post('phone_no'),
                'alter_email' => $this->input->post('alter_email'),
                'pass_word' => md5($this->input->post('pass_word')),
                'status' => $this->input->post('status'),
                'created' => date("Y-m-d H:i:s")

            );
            $sql = "SELECT * FROM admin WHERE email_addres = ?";
            $val = $this->db->query($sql, array($this->input->post('email_address')));
            if ($val->num_rows) {
                $data['message_error'] = "Email Address/Adminname already taken.";
                $this->load->view('admin/vwRegister', $data);
            } else {
                $query = $this->db->insert('admin', $admin_insert_data);
                if ($query) {

                    $data['message_success'] = "Registered Successfully";
                }

                $this->load->view('admin/vwRegister', $data);
            }
        }


    }

    public function adminlist()
    {
        $admin_id = $this->session->userdata('admin_id'); // Current admin id
        if ($admin_id) {
            $sql = "SELECT * FROM `admin` ";
            $val = $this->db->query($sql);
            $admin_type = $this->db->select('admin_type')->get_where('admin', ['id' => $admin_id])->first_row()->admin_type;
            if ($val->num_rows >= 1) {
                $data['results'] = $val->result();
                $data['admin_type'] = $admin_type;
                $this->load->view('admin/vwAdminList', $data);

            }
        } else {
            redirect(base_url() . 'admin');
            exit();
        }

    }


    public function update()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";
            //user id
            $id = $this->uri->segment(3);
            if ($id != '') {
                $sql = "UPDATE `admin` SET `status` = '1' WHERE id = '" . $id . "'";
                $val = $this->db->query($sql);
                $this->session->set_flashdata('msg', 'Successfully Updated Status');
                redirect('admin/adminlist');
            } else {
                $this->session->set_flashdata('msg', 'Sorry, some problem occured. Please try again');
                redirect('admin/adminlist');
            }

        } else {
            redirect('http://www.demand-ingtalent.co.uk/admin/');
        }


    }

    public function cupdate()
    {
        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";
            //user id
            $id = $this->uri->segment(3);
            if ($id != '') {
                $sql = "UPDATE `admin` SET `status` = '0' WHERE id = '" . $id . "'";
                $val = $this->db->query($sql);
                $this->session->set_flashdata('msg', 'Successfully Updated Payment Status');
                redirect('admin/adminlist');
            } else {
                $this->session->set_flashdata('msg', 'Sorry, some problem occured. Please try again');
                redirect('admin/adminlist');
            }
        } else {
            redirect('http://www.demand-ingtalent.co.uk/admin/');
        }


    }


    public function edit()
    {
        if ($this->session->userdata('is_admin')) {
            $id = $this->uri->segment(3);
            $sql = "SELECT * FROM `admin` where `id`='$id' ";
            $qry = $this->db->query($sql);
            if ($qry->num_rows() == '1') {
                $data['fetch'] = $qry->row();
                $this->load->view('admin/vwEditAdmin', $data);
            }

        } else {
            redirect('http://www.demand-ingtalent.co.uk/admin/');
        }
    }

    function editprofile()
    {

        if (!$this->session->userdata('is_admin')) {
            $this->load->view('admin/vwLogin');
        } else {
            $data['message_error'] = "";
            $data['message_success'] = "";

            $admin_id = $this->uri->segment(3);
            $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
            //$this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone_no', 'Phone No', 'trim|required|number');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|number');
            if ($this->input->post('alter_email') != "") {
                $this->form_validation->set_rules('alter_email', 'Email Address', 'trim|required|valid_email');
            }

            $this->form_validation->set_rules('alter_password', 'Password', 'trim|matches[alter_passwordConfirm]');
            $this->form_validation->set_rules('alter_passwordConfirm', 'Confirm Password', 'trim');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('flash_error', 'errorValidation');
                //$this->load->view('artist/artists/vwChangeprofile', $data);
            } else {


                $update_data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'alter_email' => $this->input->post('alter_email'),
                    'status' => $this->input->post('status'),
                    'phone' => $this->input->post('phone_no'),
                    'modified' => date("Y-m-d H:i:s")
                );

                if ($this->input->post('alter_password')) {
                    $update_data['pass_word'] = md5($this->input->post('alter_password'));
                }

                $this->db->where('id', $admin_id);
                $val = $this->db->update('admin', $update_data);
                //echo '<pre>'; print_r($sql); die;
                $data['message_success'] = "Successfully Changed Admin Profile";


            }

            $id = $this->uri->segment(3);
            $sql = "SELECT * FROM `admin` where `id`='$id' ";
            $qry = $this->db->query($sql);
            if ($qry->num_rows() == '1') {
                $data['fetch'] = $qry->row();
                $this->load->view('admin/vwEditAdmin', $data);
            }
        }
    }


    public function delete()
    {

        if ($this->session->userdata('is_admin')) {
            $data['message_error'] = "";
            $data['message_success'] = "";
            //user id
            $id = $this->uri->segment(3);
            if ($id != '') {
                $sql = "DELETE FROM `admin`  WHERE id = '" . $id . "'";
                $val = $this->db->query($sql);
                $this->session->set_flashdata('msg', 'Successfully Deleted');
                redirect('admin/adminlist');
            } else {
                $this->session->set_flashdata('wmsg', 'Sorry, some problem occured. Please try again');
                redirect('admin/adminlist');
            }
        } else {
            redirect('http://www.demand-ingtalent.co.uk/admin/');
        }

    }

    public function testmail()
    {
        //$to      = $_GET['email'];
        $to = 'john.diegor@gmail.com';
        $subject = 'Test email ONLY';
        $message = 'hello world';
        $headers = 'From: nobody@translatorexchange.com' . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $success = mail($to, $subject, $message, $headers);
        if ($success) {
            echo 'mail sent';
        } else {
            echo 'failed';
        }

        exit(0);
    }


    public function TechIssue()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->is_ajax_request() == true) {
            $data['subject'] = $this->input->post('subject',true);
            $data['page'] = $this->input->post('page',true);
            $data['link'] = $this->input->post('url',true);
            $data['content'] = $this->input->post('details',true);
            if($this->session->userdata('admin_id')){
                $id = $this->session->userdata('admin_id');
            $data['user_name'] = $this->db->get_where('admin',['id'=>$id])->first_row();
            $data['user_name'] = $data['user_name']->first_name.' '.$data['user_name']->last_name;
            }else{
                $data['user_name'] = 'Translator Exchange Admin';
            }
            $this->load->library('email');
            $this->email->set_mailtype("html");
            $this->email->from('info@translatorexchange.com', 'Translator Exchange');
            $this->email->to('Pedro@montesinotranslation.com');
            $this->email->subject('Technical Issue');
            $this->email->message($this->load->view('email/vwReportTechIssue', $data, true));
            $this->email->send();
            echo "Success";
            exit();
        }
    }
}

	