<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardMed extends CI_Controller
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
        $data['orders'] = array();

        if ($res == 'true' && $name) {

            $sql = "SELECT * FROM employee WHERE sup_id= $adminid";
            $query = $this->db->query($sql);
            $data['users'] = $query->result_array();

            if (isset($_GET['month'])) {
                $month = $_GET['month'];
                $now = new \DateTime('now');
                $year = $now->format('Y');

                $query1 = $this->db->query("SELECT * FROM employee WHERE sup_id='$adminid'");
                foreach ($query1->result_array() as $row1) {
                    $tid = $row1['emp_id'];
                    $query2 = $this->db->query("SELECT * FROM employee WHERE sup_id='$tid'");
                    foreach ($query2->result_array() as $row2) {
                        $tid2 = $row2['emp_id'];
                        $sql2 = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE o.emp_id = e.emp_id AND o.emp_id = '$tid2' AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC LIMIT 20 ";
                        $query3 = $this->db->query($sql2);
                        $result = $query3->result_array();
                        $old = array();
                        $old = $data['orders'];
                        $data['orders'] = array_merge($old, $result);
                    }
                }
                $data['month'] = $month;
            } else {
                $now = new \DateTime('now');
                $month = $now->format('m');
                $year = $now->format('Y');

                $query1 = $this->db->query("SELECT * FROM employee WHERE sup_id='$adminid'");
                foreach ($query1->result_array() as $row1) {
                    $tid = $row1['emp_id'];
                    $query2 = $this->db->query("SELECT * FROM employee WHERE sup_id='$tid'");
                    foreach ($query2->result_array() as $row2) {
                        $tid2 = $row2['emp_id'];
                        $sql2 = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE o.emp_id = e.emp_id AND o.emp_id = '$tid2' AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC LIMIT 20 ";
                        $query3 = $this->db->query($sql2);
                        $result = $query3->result_array();
                        $old = array();
                        $old = $data['orders'];
                        $data['orders'] = array_merge($old, $result);
                    }
                }
                $data['month'] = $month;
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
            $this->load->view('med_pages/dashboard', $data);
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
                    $this->load->view('med_pages/view_profile', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardmed/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function view_employee_area()
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

                    $sql2 = "SELECT * FROM employee WHERE sup_id='$id'";
                    $query2 = $this->db->query($sql2);
                    $data['users_emp'] = $query2->result_array();

                    $data['admin_name'] = $name;
                    $data['admin_pic'] = $pic;
                    $data['admin_desig'] = $type;
                    $this->load->view('med_pages/view_employee_area', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardmed/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
        } else
            redirect('http://localhost/ets1/index.php/pages/index?invalid=true');
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

            $this->load->view('med_pages/map', $data);
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function view_orders()
    {
        $res = $this->session->userdata('login');
        $name = $this->session->userdata('name');
        $adminid = $this->session->userdata('emp_id');
        $pic = $this->session->userdata('profile_pic');
        $type = $this->session->userdata('desig');
        $data['orders'] = array();

        if ($res == 'true') {

            if (isset($_POST['month']) &&
                isset($_POST['year'])
            ) {
                $month = $_POST['month'];
                $year = $_POST['year'];

                if ($month == 0) {

                    $query1 = $this->db->query("SELECT * FROM employee WHERE sup_id='$adminid'");
                    foreach ($query1->result_array() as $row1) {
                        $tid = $row1['emp_id'];
                        $query2 = $this->db->query("SELECT * FROM employee WHERE sup_id='$tid'");
                        foreach ($query2->result_array() as $row2) {
                            $tid2 = $row2['emp_id'];
                            $sql = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE o.emp_id = e.emp_id AND o.emp_id = '$tid2' AND YEAR(Date) = '$year' ORDER BY order_id DESC";
                            $query3 = $this->db->query($sql);
                            $result = $query3->result_array();
                            $old = array();
                            $old = $data['orders'];
                            $data['orders'] = array_merge($old, $result);
                        }
                    }

                    $now = new \DateTime('now');
                    $month = $now->format('M');

                    $data['month'] = $month;
                    $data['year'] = $year;
                } else if ($year == 0) {

                    $query1 = $this->db->query("SELECT * FROM employee WHERE sup_id='$adminid'");
                    foreach ($query1->result_array() as $row1) {
                        $tid = $row1['emp_id'];
                        $query2 = $this->db->query("SELECT * FROM employee WHERE sup_id='$tid'");
                        foreach ($query2->result_array() as $row2) {
                            $tid2 = $row2['emp_id'];
                            $sql = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE o.emp_id = e.emp_id AND o.emp_id = '$tid2' AND MONTH(Date) = '$month' ORDER BY order_id DESC";
                            $query3 = $this->db->query($sql);
                            $result = $query3->result_array();
                            $old = array();
                            $old = $data['orders'];
                            $data['orders'] = array_merge($old, $result);
                        }
                    }

                    $now = new \DateTime('now');
                    $year = $now->format('Y');

                    $data['month'] = $month;
                    $data['year'] = $year;
                } else {

                    $query1 = $this->db->query("SELECT * FROM employee WHERE sup_id='$adminid'");
                    foreach ($query1->result_array() as $row1) {
                        $tid = $row1['emp_id'];
                        $query2 = $this->db->query("SELECT * FROM employee WHERE sup_id='$tid'");
                        foreach ($query2->result_array() as $row2) {
                            $tid2 = $row2['emp_id'];
                            $sql = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE o.emp_id = e.emp_id AND o.emp_id = '$tid2' AND YEAR(Date) = '$year' AND MONTH(Date) = '$month' ORDER BY order_id DESC";
                            $query3 = $this->db->query($sql);
                            $result = $query3->result_array();
                            $old = array();
                            $old = $data['orders'];
                            $data['orders'] = array_merge($old, $result);
                        }
                    }

                    $data['month'] = $month;
                    $data['year'] = $year;
                }
            } else {
                $now = new \DateTime('now');
                $month = $now->format('m');
                $year = $now->format('Y');

                $query1 = $this->db->query("SELECT * FROM employee WHERE sup_id='$adminid'");
                foreach ($query1->result_array() as $row1) {
                    $tid = $row1['emp_id'];
                    $query2 = $this->db->query("SELECT * FROM employee WHERE sup_id='$tid'");
                    foreach ($query2->result_array() as $row2) {
                        $tid2 = $row2['emp_id'];
                        $sql = "SELECT o.*,e.emp_name FROM order_detail o,employee e WHERE o.emp_id = e.emp_id AND o.emp_id = '$tid2' ORDER BY order_id DESC LIMIT 50 ";
                        $query3 = $this->db->query($sql);
                        $result = $query3->result_array();
                        $old = array();
                        $old = $data['orders'];
                        $data['orders'] = array_merge($old, $result);
                    }
                }

                $data['month'] = $month;
                $data['year'] = $year;
            }

            $data['admin_name'] = $name;
            $data['admin_pic'] = $pic;
            $data['admin_desig'] = $type;
            $this->load->view('med_pages/view_orders', $data);
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
                    redirect('http://localhost/ets1/index.php/dashboardmed/index');
                } else
                    redirect('http://localhost/ets1/index.php/dashboardmed/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    public function edit_admin()
    {
        $res = $this->session->userdata('login');
        if ($res == 'true') {
            // simple message to console
            $isPicUploaded = false;
            $file_name = "";
            if (isset($_FILES['image'])) {
                $errors = array();
                $width = 500;
                $height = 500;
                $file_name = $_FILES['image']['name'];
                $file_size = $_FILES['image']['size'];
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_type = $_FILES['image']['type'];
                /* Get original image x y*/
                list($w, $h) = getimagesize($file_tmp);
                /* calculate new image size with ratio */
                $ratio = max($width / $w, $height / $h);
                $h = ceil($height / $ratio);
                $x = ($w - $width / $ratio) / 2;
                $w = ceil($width / $ratio);
                /* read binary data from image file */
                $imgString = file_get_contents($file_tmp);
                /* create image from string */
                $image = imagecreatefromstring($imgString);
                $tmp = imagecreatetruecolor($width, $height);
                imagecopyresampled($tmp, $image,
                    0, 0,
                    $x, 0,
                    $width, $height,
                    $w, $h);

                $file_ext = strtolower(end(explode('.', $file_name)));

                $expensions = array("jpeg", "jpg", "png");

                if (in_array($file_ext, $expensions) === false) {
                    $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
                }

                if ($file_size > 2097152) {
                    $errors[] = 'File size must be excately 2 MB';
                }

                if (empty($errors) == true) {
                    move_uploaded_file($file_tmp, "images/" . $file_name);
                    $isPicUploaded = true;
                } else {
                    print_r($errors);
                }
            }

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

                if ($isPicUploaded) {
                    $profile_pic = "http://localhost/ets1/images/" . $file_name;
                    $info = array(
                        'emp_name' => $name,
                        'email' => $email,
                        'phone_no' => $phone,
                        'password' => $pass,
                        'emp_dp' => $profile_pic
                    );
                } else
                    $info = array(
                        'emp_name' => $name,
                        'email' => $email,
                        'phone_no' => $phone,
                        'password' => $pass
                    );
                $this->db->where('emp_id', intval($id));
                $this->db->update('employee', $info);
                redirect('http://localhost/ets1/index.php/dashboardmed/view_me');

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    function resize($width, $height)
    {
        /* Get original image x y*/
        list($w, $h) = getimagesize($_FILES['image']['tmp_name']);
        /* calculate new image size with ratio */
        $ratio = max($width / $w, $height / $h);
        $h = ceil($height / $ratio);
        $x = ($w - $width / $ratio) / 2;
        $w = ceil($width / $ratio);
        /* new file name */
        $path = 'uploads/' . $width . 'x' . $height . '_' . $_FILES['image']['name'];
        /* read binary data from image file */
        $imgString = file_get_contents($_FILES['image']['tmp_name']);
        /* create image from string */
        $image = imagecreatefromstring($imgString);
        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $image,
            0, 0,
            $x, 0,
            $width, $height,
            $w, $h);
        /* Save image */
        switch ($_FILES['image']['type']) {
            case 'image/jpeg':
                imagejpeg($tmp, $path, 100);
                break;
            case 'image/png':
                imagepng($tmp, $path, 0);
                break;
            case 'image/gif':
                imagegif($tmp, $path);
                break;
            default:
                exit;
                break;
        }
        return $path;
        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($tmp);
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
                    $this->load->view('med_pages/view_me', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardmed/index');
            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');

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
                    $this->load->view('med_pages/edit_me', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardmed/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }

    private function save_image_to_server($image)
    {

    }

    public function upload_profile_pic()
    {
        $debug = 5; // All lower and equal priority logs will be displayed
        console('func called', 1, $debug);
        $res = $this->session->userdata('login');
        if ($res == 'true') {
            if (isset($_FILES['image'])) {
                $errors = array();
                $file_name = $_FILES['image']['name'];
                $file_size = $_FILES['image']['size'];
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_type = $_FILES['image']['type'];
                $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

                $expensions = array("jpeg", "jpg", "png");

                if (in_array($file_ext, $expensions) === false) {
                    $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
                }

                if ($file_size > 2097152) {
                    $errors[] = 'File size must be excately 2 MB';
                }

                if (empty($errors) == true) {
                    move_uploaded_file($file_tmp, "images/" . $file_name);
                    echo "Success";
                } else {
                    print_r($errors);
                }
            }
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
                redirect('http://localhost/ets1/index.php/dashboardmed/view_profile?id=' . $id);

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
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
                    $this->load->view('med_pages/edit_profile', $data);
                } else
                    redirect('http://localhost/ets1/index.php/dashboardmed/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
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
            $this->load->view('med_pages/add_user', $data);
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

                $sql = "SELECT * FROM roles WHERE role_designation = 'Region Manager'";
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

                redirect('http://localhost/ets1/index.php/dashboardmed/index');

            } else
                redirect('http://localhost/ets1/index.php/dashboardmed/index');
        } else
            redirect('http://localhost/ets1/index.php/login/index?invalid=true');
    }
}


