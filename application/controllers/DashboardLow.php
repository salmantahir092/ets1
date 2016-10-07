<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardLow extends CI_Controller
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
        $this->load->library('googlemaps');
    }

    public function index()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $adminid = $this->session->userdata('emp_id');
        $type = $this->session->userdata('desig');

        if ($res == 'true' && $name) {

            $sql = "SELECT * FROM employee WHERE sup_id= $adminid";
            $query = $this->db->query($sql);
            $data['users'] = $query->result_array();

            if (isset($_GET['month'])) {
                $month = $_GET['month'];
                $now = new \DateTime('now');
                $year = $now->format('Y');

                $sql2 = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE e.sup_id= $adminid AND o.emp_id = e.emp_id AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC LIMIT 20 ";
                $query2 = $this->db->query($sql2);
                $data['month'] = $month;
                $data['orders'] = $query2->result_array();
            } else {
                $now = new \DateTime('now');
                $month = $now->format('m');
                $year = $now->format('Y');

                $sql2 = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE e.sup_id= $adminid AND o.emp_id = e.emp_id AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC LIMIT 20 ";
                $query2 = $this->db->query($sql2);
                $data['month'] = $month;
                $data['orders'] = $query2->result_array();
            }

            $marker = array();
            $config = array();
            $config['center'] = 'lahore, pakistan';
            $config['zoom'] = 15;
            $this->googlemaps->initialize($config);


            foreach ($query->result_array() as $row) {
                $long = $row['emp_long'];
                $lat = $row['emp_lat'];
                if ($lat && $long) {
                    $marker['position'] = $lat . ',' . $long;
                    $this->googlemaps->add_marker($marker);
                }
            }
            $data['map'] = $this->googlemaps->create_map();

            $data['admin_name'] = $name;
            $data['admin_pic'] = $pic;
            $data['admin_desig'] = $type;
            $this->load->view('low_pages/dashboard', $data);
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');

    }

    public function signout()
    {
        $this->session->set_userdata('login', 'false');
        redirect('http://localhost/ets1/index.php/login/index');
    }

    public function view_profile()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $type = $this->session->userdata('desig');

        if ($res == 'true') {

            if (isset($_GET['id'])) {

                $id = $_GET['id'];
                $sql = "SELECT e.*,r.*,CONCAT(address_area,', ',address_city)  AS address FROM employee e,roles r,address a WHERE e.emp_id = '$id' AND e.role_id=r.role_id AND e.address_id=a.address_id";
                $query = $this->db->query($sql);
                if ($query->num_rows() > 0) {
                    $data['user_info'] = $query->row_array();

                    if (isset($_GET['month'])) {
                        $month = $_GET['month'];
                        $id = $_GET['id'];
                        $now = new \DateTime('now');
                        $year = $now->format('Y');

                        $sql2 = "SELECT * FROM order_detail WHERE emp_id = '$id' AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC LIMIT 15 ";
                        $query2 = $this->db->query($sql2);
                        $data['month'] = $month;
                        $data['orders'] = $query2->result_array();
                    } else {
                        $now = new \DateTime('now');
                        $month = $now->format('m');
                        $year = $now->format('Y');

                        $sql2 = "SELECT * FROM order_detail WHERE emp_id = '$id' AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC LIMIT 15 ";
                        $query2 = $this->db->query($sql2);
                        $data['month'] = $month;
                        $data['orders'] = $query2->result_array();
                    }

                    $marker = array();
                    $config = array();
                    $config['center'] = 'lahore, pakistan';
                    $config['zoom'] = 15;
                    $this->googlemaps->initialize($config);


                    foreach ($query->result_array() as $row) {
                        $long = $row['emp_long'];
                        $lat = $row['emp_lat'];
                        if ($lat && $long) {
                            $marker['position'] = $lat . ',' . $long;
                            $this->googlemaps->add_marker($marker);
                        }
                    }
                    $data['map'] = $this->googlemaps->create_map();
                    $data['admin_name'] = $name;
                    $data['admin_pic'] = $pic;
                    $data['admin_desig'] = $type;
                    $this->load->view('low_pages/view_profile', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardlow/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function view_map()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $type = $this->session->userdata('desig');

        if ($res == 'true') {
            $data['admin_name'] = $name;
            $data['admin_pic'] = $pic;
            $data['admin_desig'] = $type;

            $config = array();
            $config['center'] = 'lahore, pakistan';
            $config['zoom'] = 13;
            $this->googlemaps->initialize($config);


            $data['map'] = $this->googlemaps->create_map();

            $this->load->view('low_pages/map', $data);
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function view_orders()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $adminid = $this->session->userdata('emp_id');
        $type = $this->session->userdata('desig');

        if ($res == 'true') {

            if (isset($_POST['month']) &&
                isset($_POST['year'])
            ) {
                $month = $_POST['month'];
                $year = $_POST['year'];

                if ($month == 0) {
                    $sql = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE o.emp_id = e.emp_id  AND e.sup_id= $adminid AND YEAR(Date) = '$year' ORDER BY order_id DESC";
                    $query = $this->db->query($sql);

                    $now = new \DateTime('now');
                    $month = $now->format('M');

                    $data['month'] = $month;
                    $data['year'] = $year;
                    $data['orders'] = $query->result_array();
                } else if ($year == 0) {
                    $sql = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE e.sup_id= $adminid AND o.emp_id = e.emp_id AND MONTH(Date) = '$month' ORDER BY order_id DESC";
                    $query = $this->db->query($sql);

                    $now = new \DateTime('now');
                    $year = $now->format('Y');

                    $data['month'] = $month;
                    $data['year'] = $year;
                    $data['orders'] = $query->result_array();
                } else {
                    $sql = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE e.sup_id= $adminid AND o.emp_id = e.emp_id AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC";
                    $query = $this->db->query($sql);
                    $data['month'] = $month;
                    $data['year'] = $year;
                    $data['orders'] = $query->result_array();
                }
            } else {
                $now = new \DateTime('now');
                $month = $now->format('m');
                $year = $now->format('Y');

                $sql = "SELECT o.*,e.* FROM order_detail o,employee e WHERE e.sup_id= $adminid AND o.emp_id = e.emp_id ORDER BY order_id DESC LIMIT 50 ";
                $query = $this->db->query($sql);
                $data['month'] = $month;
                $data['year'] = $year;
                $data['orders'] = $query->result_array();
            }

            $data['admin_name'] = $name;
            $data['admin_pic'] = $pic;
            $data['admin_desig'] = $type;
            $this->load->view('low_pages/view_orders', $data);
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function about_us()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $type = $this->session->userdata('desig');

        if ($res == 'true') {


            $data['admin_name'] = $name;
            $data['admin_pic'] = $pic;
            $data['admin_desig'] = $type;
            $this->load->view('about_us', $data);
        } else
            redirect('http://localhost/ets1/index.php/pages/index?invalid=true');
    }

    public function delete_profile()
    {
        $res = $this->session->userdata('login');
        if ($res == 'true') {

            if (isset($_GET['id'])) {

                $id = $_GET['id'];
                $sql = "SELECT * FROM employee WHERE emp_id = '$id'";
                $query = $this->db->query($sql);
                if ($query->num_rows() > 0) {
                    $this->db->where('emp_id', $id);
                    $this->db->delete('employee');
                    redirect('http://localhost/ets1/index.php/dashboardlow/index');
                } else
                    redirect('http://localhost/ets1/index.php/dashboardlow/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function edit_admin()
    {
        $res = $this->session->userdata('login');
        if ($res == 'true') {

            if (isset($_POST['id']) &&
                isset($_POST['name']) &&
                isset($_POST['email']) &&
                isset($_POST['password']) &&
                isset($_POST['phone']) &&
                isset($_POST['add_id']) &&
                isset($_POST['add_city']) &&
                isset($_POST['add_area'])
            ) {

                $id = $_POST['id'];
                $name = $_POST['name'];
                $email = $_POST['email'];
                $pass = $_POST['password'];
                $phone = $_POST['phone'];
                $add_id = $_POST['add_id'];
                $add_city = $_POST['add_city'];
                $add_area = $_POST['add_area'];

                $info = array(
                    'address_area' => $add_area,
                    'address_city' => $add_city
                );
                $this->db->where('address_id', intval($add_id));
                $this->db->update('address', $info);

                $info = array(
                    'emp_name' => $name,
                    'email' => $email,
                    'phone_no' => $phone,
                    'password' => $pass
                );
                $this->db->where('emp_id', intval($id));
                $this->db->update('employee', $info);
                redirect('http://localhost/ets1/index.php/dashboardlow/view_me');

            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function view_me()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $admin = $this->session->userdata('emp_id');
        $type = $this->session->userdata('desig');

        if ($res == 'true') {

            if ($admin) {
                $sql = "SELECT e.*,r.*,CONCAT(address_area,', ',address_city)  AS address  FROM employee e,roles r,address a WHERE e.emp_id = '$admin' AND e.role_id=r.role_id AND e.address_id=a.address_id";
                $query = $this->db->query($sql);
                if ($query->num_rows() > 0) {
                    $data['admin_info'] = $query->row_array();
                    $data['admin_name'] = $name;
                    $data['admin_pic'] = $pic;
                    $data['admin_desig'] = $type;
                    $this->load->view('low_pages/view_me', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardlow/index');
            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');

        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');

    }

    public function edit_me()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $adminid = $this->session->userdata('emp_id');
        $type = $this->session->userdata('desig');

        if ($res == 'true') {

            if ($adminid) {

                $sql = "SELECT e.*,r.*,a.* FROM employee e,roles r,address a WHERE e.emp_id = '$adminid' AND e.role_id=r.role_id AND e.address_id=a.address_id";
                $query = $this->db->query($sql);
                if ($query->num_rows() > 0) {
                    $data['admin_info'] = $query->row_array();
                    $data['admin_name'] = $name;
                    $data['admin_pic'] = $pic;
                    $data['admin_desig'] = $type;
                    $this->load->view('low_pages/edit_me', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardlow/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function edit_employee()
    {
        $res = $this->session->userdata('login');
        if ($res == 'true') {

            if (isset($_POST['id']) &&
                isset($_POST['name']) &&
                isset($_POST['email']) &&
                isset($_POST['password']) &&
                isset($_POST['phone']) &&
                isset($_POST['designation']) &&
                isset($_POST['add_id']) &&
                isset($_POST['add_city']) &&
                isset($_POST['add_area'])
            ) {

                $id = $_POST['id'];
                $name = $_POST['name'];
                $email = $_POST['email'];
                $pass = $_POST['password'];
                $phone = $_POST['phone'];
                $desig = $_POST['designation'];
                $add_id = $_POST['add_id'];
                $add_city = $_POST['add_city'];
                $add_area = $_POST['add_area'];

                $info = array(
                    'address_area' => $add_area,
                    'address_city' => $add_city
                );
                $this->db->where('address_id', intval($add_id));
                $this->db->update('address', $info);

                $info = array(
                    'emp_name' => $name,
                    'email' => $email,
                    'phone_no' => $phone,
                    'password' => $pass
                );
                $this->db->where('emp_id', intval($id));
                $this->db->update('employee', $info);
                redirect('http://localhost/ets1/index.php/dashboardlow/view_profile?id=' . $id);

            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function edit_profile()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $type = $this->session->userdata('desig');

        if ($res == 'true') {

            if (isset($_GET['id'])) {

                $id = $_GET['id'];
                $sql = "SELECT e.*,r.*,a.* FROM employee e,roles r,address a WHERE e.emp_id = '$id' AND e.role_id=r.role_id AND e.address_id=a.address_id";
                $query = $this->db->query($sql);
                if ($query->num_rows() > 0) {
                    $data['user_info'] = $query->row_array();
                    $data['admin_name'] = $name;
                    $data['admin_pic'] = $pic;
                    $data['admin_desig'] = $type;
                    $this->load->view('low_pages/edit_profile', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardlow/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function add_user()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $pic = $this->session->userdata('profile_pic');
        $type = $this->session->userdata('desig');
        $adminid = $this->session->userdata('emp_id');

        if ($res == 'true') {
            $data['admin_name'] = $name;
            $data['admin_pic'] = $pic;
            $data['admin_desig'] = $type;
            $data['admin_id'] = $adminid;
            $this->load->view('low_pages/add_user', $data);
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function add_user_db()
    {
        $res = $this->session->userdata('login');
        if ($res == 'true') {

            if (isset($_POST['sup_id']) &&
                isset($_POST['name']) &&
                isset($_POST['email']) &&
                isset($_POST['password']) &&
                isset($_POST['phone']) &&
                isset($_POST['add_area']) &&
                isset($_POST['add_city'])
            ) {

                $id = $_POST['sup_id'];
                $name = $_POST['name'];
                $email = $_POST['email'];
                $pass = $_POST['password'];
                $phone = $_POST['phone'];
                $area = $_POST['add_area'];
                $city = $_POST['add_city'];
                $date = date('Y-m-d');

                $addinfo = array(
                    'address_area' => $area,
                    'address_city' => $city
                );
                $this->db->insert('address', $addinfo);
                $add = $this->db->insert_id();

                $sql = "SELECT * FROM roles WHERE role_designation = 'Employee'";
                $query = $this->db->query($sql);
                $roll = $query->row_array();

                $info = array(
                    'emp_name' => $name,
                    'address_id' => $add,
                    'phone_no' => $phone,
                    'role_id' => $roll['role_id'],
                    'sup_id' => $id,
                    'email' => $email,
                    'password' => $pass,
                    'join_date' => $date,
                    'emp_dp' => "http://localhost/ets1/images/user.jpg",
                    'emp_status' => 'off duty'
                );
                $this->db->insert('employee', $info);

                redirect('http://localhost/ets1/index.php/dashboardlow/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardlow/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }
}
