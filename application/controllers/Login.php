<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{

    function __construct()
    {
        // Construct our parent class
        parent::__construct();
        $this->load->helper('url');
        $autoload['helper'] = array('url', 'utility', 'form');
        $this->load->helper(array('form', 'url'));
        $this->load->database();
        $this->load->library('session');
    }

    public function index()
    {
        $data['response'] = '';
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $sql = "SELECT e.*,r.* FROM employee e,roles r WHERE e.email = '$email' AND e.password = '$password' AND e.role_id = r.role_id ";
            $query = $this->db->query($sql);
            if ($query->num_rows() == 0) {
                $data['response'] = 'Invalid email or password.';
            } else {
                $arrays = $query->row_array();
                $role = $arrays['role_designation'];

                if (strcasecmp($role, "C.E.O") == 0) {
                    $name = $arrays['emp_name'];
                    $pic = $arrays['emp_dp'];
                    $type = $arrays['role_designation'];
                    $id = $arrays['emp_id'];

                    $this->session->set_userdata('login', 'true');
                    $this->session->set_userdata('name', $name);
                    $this->session->set_userdata('profile_pic', $pic);
                    $this->session->set_userdata('desig', $type);
                    $this->session->set_userdata('emp_id', $id);

                    redirect('http://localhost/ets1/index.php/dashboardhigh/index');
                } else if (strcasecmp($role, "Region Manager") == 0) {
                    $name = $arrays['emp_name'];
                    $pic = $arrays['emp_dp'];
                    $type = $arrays['role_designation'];
                    $id = $arrays['emp_id'];

                    $this->session->set_userdata('login', 'true');
                    $this->session->set_userdata('name', $name);
                    $this->session->set_userdata('profile_pic', $pic);
                    $this->session->set_userdata('desig', $type);
                    $this->session->set_userdata('emp_id', $id);

                    redirect('http://localhost/ets1/index.php/dashboardmed/index');

                } else if (strcasecmp($role, "Area Manager") == 0) {
                    $name = $arrays['emp_name'];
                    $pic = $arrays['emp_dp'];
                    $type = $arrays['role_designation'];
                    $id = $arrays['emp_id'];

                    $this->session->set_userdata('login', 'true');
                    $this->session->set_userdata('name', $name);
                    $this->session->set_userdata('profile_pic', $pic);
                    $this->session->set_userdata('desig', $type);
                    $this->session->set_userdata('emp_id', $id);

                    redirect('http://localhost/ets1/index.php/dashboardlow/index');

                } else
                    $data['response'] = 'You are not authorized user.';
            }
        }
        $this->load->view('login', $data);
    }

    public function forgot_pass()
    {
        $data['response'] = '';
        $this->load->view('forgot_pass', $data);
    }

    public function attempt_forgot_pass()
    {
        $data['response'] = '';
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $sql = "SELECT * FROM employee WHERE email = '$email'";
            $query = $this->db->query($sql);
            if ($query->num_rows() <= 0) {
                $data['response'] = 'Please Enter your valid Email';
            } else {
                $info = $query->result_array();
                $password = $info[0]['admin_password'];

                $this->load->library('email');
                $config['mailpath'] = '/usr/sbin/sendmail';
                $config['useragent'] = 'CodeIgniter';
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'ssl://smtp.googlemail.com';
                $config['smtp_user'] = 'etsteamnoreply@gmail.com';
                $config['smtp_pass'] = 'ets12345';
                $config['smtp_port'] = 465;
                $config['smtp_timeout'] = 5;
                $config['wordwrap'] = TRUE;
                $config['wrapchars'] = 76;
                $config['mailtype'] = 'html';
                $config['charset'] = 'utf-8';
                $config['validate'] = FALSE;
                $config['priority'] = 3;
                $config['crlf'] = "\r\n";
                $config['newline'] = "\r\n";
                $config['bcc_batch_mode'] = FALSE;
                $config['bcc_batch_size'] = 200;

                $this->email->initialize($config);
                $message = "Dear Admin! Your account password is ' $password ' .";
                $this->email->from('Support@ets.com', 'no-reply@ets.com');
                $this->email->to($email);
                $this->email->subject('Password Recovery');
                $this->email->message($message);

                if ($this->email->send()) {
                    $data['response'] = 'Your Password has been Retrieved...Please check your mail now.';
                } else {
                    $data['response'] = 'Your request is unsuccessfull.';
                    show_error($this->email->print_debugger());
                }
            }
        } else {
            $data['response'] = '';
        }
        $this->load->view('forgot_pass', $data);
    }

}
