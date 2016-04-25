<?php

error_reporting(false);
/** Include the database file */
include_once realpath(dirname(__FILE__) . '/db.php');
include_once realpath(dirname(__FILE__) . '/smtp/PHPMailerAutoload.php');

/**
 * The main class of login
 * All the necesary system functions are prefixed with _
 * examples, _login_action - to be used in the login-action.php file
 * _authenticate - to be used in every file where admin restriction is to be inherited etc...
 * @author Swashata <swashata@intechgrity.com>
 */
class itg_admin {

    /**
     * Holds the script directory absolute path
     * @staticvar
     */
    static $abs_path;

    const isSuperAdmin = 1;

    /**
     * Store the sanitized and slash escaped value of post variables
     * @var array
     */
    var $post = array();

    /**
     * Stores the sanitized and decoded value of get variables
     * @var array
     */
    var $get = array();
    var $urlToHIt;

    /**
     * The constructor function of admin class
     * We do just the session start
     * It is necessary to start the session before actually storing any value
     * to the super global $_SESSION variable
     */
    public function __construct() {
        session_start();

//store the absolute script directory
//note that this is not the admin directory
        self::$abs_path = dirname(dirname(__FILE__));

//initialize the post variable
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->post = $_POST;
            if (get_magic_quotes_gpc()) {
//get rid of magic quotes and slashes if present
                array_walk_recursive($this->post, array($this, 'stripslash_gpc'));
            }
        }

//initialize the get variable
        $this->get = $_GET;
//decode the url
        array_walk_recursive($this->get, array($this, 'urldecode'));
    }

    /**
     * Sample function to return the nicename of currently logged in admin
     * @global ezSQL_mysql $db
     * @return string The nice name of the user
     */
    public function get_nicename() {
        $username = $_SESSION['admin_login'];
        global $db;
        $info = $db->get_row("SELECT `nicename` FROM `user` WHERE `username` = '" . $db->escape($username) . "'");
        if (is_object($info))
            return $info->nicename;
        else
            return '';
    }

    /**
     * Sample function to return the email of currently logged in admin user
     * @global ezSQL_mysql $db
     * @return string The email of the user
     */
    public function get_email() {
        $username = $_SESSION['admin_login'];
        global $db;
        $info = $db->get_row("SELECT `email` FROM `user` WHERE `username` = '" . $db->escape($username) . "'");
        if (is_object($info))
            return $info->email;
        else
            return '';
    }

    /**
     * Checks whether the user is authenticated
     * to access the admin page or not.
     *
     * Redirects to the login.php page, if not authenticates
     * otherwise continues to the page
     *
     * @access public
     * @return void
     */
    public function _authenticate() {
//first check whether session is set or not
        if (!isset($_SESSION['admin_login'])) {
//check the cookie
            if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
//cookie found, is it really someone from the
                if ($this->_check_db($_COOKIE['username'], $_COOKIE['password'])) {
                    global $db;
                    $user_row = $db->get_row("SELECT * FROM `admin_database_info` WHERE `email`='" . $db->escape($_COOKIE['username']) . "' AND `is_active`=1");
                    if ($user_row->admin_type == self::isSuperAdmin) {
                        $_SESSION['admin_login'] = $username;
                        $_SESSION['is_super'] = 1;
                    } elseif ($user_row->admin_type == 0) {
                        $_SESSION['admin_login'] = $username;
                        $_SESSION['is_super'] = 0;
                    }
                    header("location: dashboard.php");
                    die();
                }
            }
        } else {
            header("location: dashboard.php");
            die();
        }
    }

    public function _createAdmin() {
        try {
            global $db;
            global $dbhost;
            $response = array();
            $name = $this->post['username'];
            $email = $this->post['email'];
            $contact = $this->post['contactno'];
            $adminpassword = md5($this->post['password']);
            if (($name == '') || ($email == '') || ($contact == '') || ($adminpassword == '')) {
                $redirectUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                header("Location: " . $redirectUrl . "?msg=Invalid values! please submit again. ");
                die();
            }
            $count = "SELECT * FROM `admin_database_info` WHERE `email` = '" . $email . "' AND `is_active`=1";
            $result = $db->query($count);
            if ($result > 0) {
                header("Location: " . $redirectUrl . "?msg=Email already exist. ");
                die();
            }
            if (strlen($this->post['username']) > 12) {
                $username = substr($this->post['username'], 0, 12);
            } else {
                $username = $this->post['username'];
            }
            $characters = 'ACEFHJKMNPRTUVWXY4937';
            $string = '';
            for ($i = 0; $i < 5; $i++) {
                $string .= $characters[rand(0, strlen($characters) - 1)];
            }
            $dbname = $string;
            $sql = "CREATE DATABASE " . $dbname;
            $db->query($sql);
            $this->createPredefindedUserTables($dbname);
            $password = $this->randomPassword();
            $createUser = "CREATE USER " . $dbname . "@'" . $dbhost . "' IDENTIFIED BY '" . $password . "'";
            $db->query($createUser);
            $privilage = "grant all privileges on " . $dbname . ".* to '" . $dbname . "'@'" . $dbhost . "' identified by '" . $password . "'";
            $db->query($privilage);
            $addAdmininfo = "INSERT INTO `admin_database_info`(`name`, `email`,`contactno`, `admindatabase`, `databasepassword`, `created_at`, `password`, `admin_type`,`is_active`,`locale`) 
		VALUES ('" . $name . "','" . $email . "','" . $contact . "','" . $dbname . "','" . $password . "','" . date('Y-m-d H:i:s') . "','" . $adminpassword . "','0','1','')";
            $db->query($addAdmininfo);
            $url = 'http://54.191.56.95:9090/plugins/userService/userservice?type=add&secret=toLa16o7&username=' . $dbname . '&password=' . $password . '&name=' . $name . '&email=' . $email . '&groups=PersonnelTrackerGroup';
            $check = 1;
            $this->sendEmail($email, $this->post['password'], $name, $check, null);
            $response['status'] = true;
            $response['url'] = $url;
        } catch (exception $e) {
            $response['status'] = FALSE;
            $response['msg'] = $e->getMessage();
        }
        echo json_encode($response);
        exit(0);
    }

    public function adminEmailTemplate() {
        $message = '<html><body>';
        $message .= '<table width="100%"; rules="all" style="border:1px solid #3A5896;" cellpadding="10">';
        $message .= "<tr><td><img src='https://www.google.co.in/url?sa=i&rct=j&q=&esrc=s&source=images&cd=&cad=rja&uact=8&ved=0CAcQjRxqFQoTCO_DiqDM88gCFYiMlAodRfMEnw&url=http%3A%2F%2Fwww.act.is%2Fportfolio%2FTracker&psig=AFQjCNEp7_PIZgpRGuu8eGGil5g4wWQicw&ust=1446617488324832' alt='Personnel Tracker' /></td></tr>";
        $message .= "<tr><td colspan=2>Dear \$name,<br /><br />You account has been created.Use the below details to login.</td></tr>";
        $message .= "<tr><td colspan=2 font='colr:#999999;'><I>Username: \$uname<br>Password: \$password </I></td></tr>";
        $message .= "<tr><td colspan=2 font='colr:#999999;'><I><a href='http://52.24.255.248/index.php'>Click here to go to login page</a></I></td></tr>";
        $message .= "</table>";
        $message .= "</body></html>";
        return $message;
    }

    public function createPredefindedUserTables($dbname) {
        global $dbhost;
        global $dbuser;
        global $dbpassword;
        $userDbQuery = "CREATE TABLE IF NOT EXISTS `user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp,
  `is_on_track` tinyint(1) NOT NULL,
  `trackStart` timestamp,
  `trackEnd` timestamp,
  `trackInterval` varchar(255) ,
  `uniqueCode` varchar(255),
  `deviceId` varchar(255) ,
  `token` varchar(255),
  `devicetype` varchar(255),
  `cronTrackStatus` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";
        $userLocationQuery = "CREATE TABLE IF NOT EXISTS `user_tracking_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `locationtime` timestamp NOT NULL,
  `timestamp` timestamp,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        $userSessionQuery = "CREATE TABLE IF NOT EXISTS `user_session_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grcid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `data` text NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $dbname, $dbhost);
        $dbUser->query($userDbQuery);
        $dbUser->query($userLocationQuery);
        $dbUser->query($userSessionQuery);
        unset($dbUser);
    }

    public function getCountAdmin() {
        global $db;
        $count = "SELECT * FROM `admin_database_info` WHERE `admin_type`= 0 and is_active=1";
        $result = $db->query($count);
        return $result;
    }

    public function isSuperAdmin() {
        if ($_SESSION['is_super'] != 1) {
            header("Location: dashboard.php?msg=You do not have privillage to view this page");
        }
    }

    public function deleteAdmin() {
        global $db;
        $id = $this->get['id'];
        $query = "update `admin_database_info` SET `is_active`=0 WHERE id = " . $id;
        $db->query($query);
        header("Location: dashboard.php?msg='admin successfully deleted'");
    }

    public function getAdminUsers() {
        global $db;
        $count = "SELECT * FROM `admin_database_info` WHERE `admin_type`= 0 ORDER BY created_at DESC
      LIMIT 3";
        $result = $db->get_results($count, ARRAY_A);
        return $result;
    }

    public function getCompleteAdminUsers() {
        global $db;
        $count = "SELECT * FROM `admin_database_info` WHERE `admin_type`= 0 and is_active=1";
        $result = $db->get_results($count, ARRAY_A);
        return $result;
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Check for login in the action file
     */
    public function _login_action() {
//insufficient data provided
        if (!isset($this->post['username']) || $this->post['username'] == '' || !isset($this->post['password']) || $this->post['password'] == '') {
            header("location: index.php");
        }
//get the username and password
        $username = $this->post['username'];
        $password = md5(($this->post['password']));
//check the database for username
        if ($this->_check_db($username, $password)) {
//ready to login
            global $db;
            $user_row = $db->get_row("SELECT * FROM `admin_database_info` WHERE `email`='" . $db->escape($username) . "' and `is_active`=1");
            if ($user_row->admin_type == self::isSuperAdmin) {
                $_SESSION['admin_login'] = $username;
                $userData = (array) $user_row;
                unset($userData['password']);
//                unset($userData['databasepassword']);
                $_SESSION['data'] = $userData;
                $_SESSION['is_super'] = 1;
            } elseif ($user_row->admin_type == 0) {
                $_SESSION['admin_login'] = $username;
                $_SESSION['is_super'] = 0;
                $userData = (array) $user_row;
                unset($userData['password']);
//                unset($userData['databasepassword']);
                $_SESSION['data'] = $userData;
            }

//check to see if remember, ie if cookie
            if (isset($this->post['remember'])) {
//set the cookies for 1 day, ie, 1*24*60*60 secs
//change it to something like 30*24*60*60 to remember user for 30 days
                setcookie('username', $username, time() + 1 * 24 * 60 * 60);
                setcookie('password', $password, time() + 1 * 24 * 60 * 60);
            } else {
//destroy any previously set cookie
                setcookie('username', '', time() - 1 * 24 * 60 * 60);
                setcookie('password', '', time() - 1 * 24 * 60 * 60);
            }

            header("location: dashboard.php");
        } else {
            header("location: index.php");
        }

        die();
    }

    /**
     * Check the database for login user
     * Get the password for the user
     * compare md5 hash over sha1
     * @param string $username Raw username
     * @param string $password expected to be md5 over sha1
     * @return bool TRUE on success FALSE otherwise
     */
    private function _check_db($username, $password) {
        global $db;

        $user_row = $db->get_row("SELECT * FROM `admin_database_info` WHERE `email`='" . $db->escape($username) . "' and `is_active`=1");
//general return
        if (is_object($user_row) && ($user_row->password) == $password)
            return true;
        else
            return false;
    }

    /**
     * stripslash gpc
     * Strip the slashes from a string added by the magic quote gpc thingy
     * @access protected
     * @param string $value
     */
    private function stripslash_gpc(&$value) {
        $value = stripslashes($value);
    }

    /**
     * htmlspecialcarfy
     * Encodes string's special html characters
     * @access protected
     * @param string $value
     */
    private function htmlspecialcarfy(&$value) {
        $value = htmlspecialchars($value);
    }

    /**
     * URL Decode
     * Decodes a URL Encoded string
     * @access protected
     * @param string $value
     */
    protected function urldecode(&$value) {
        $value = urldecode($value);
    }

    public function _checkAdmin($email) {
        global $db;
        $count = "SELECT * FROM `admin_database_info` WHERE `email` = '" . $email . "' AND `is_active`=1";
        $result = $db->query($count);
        if ($result > 0) {
            $response['status'] = true;
        } else {
            $response['status'] = FALSE;
        }
        echo json_encode($response);
        die();
    }

    public function _checkUser($email) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $adminemail = $admin = $_SESSION['admin_login'];
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
        $daName = $db->get_col($getDbName);
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
        $count = "SELECT * FROM `user_info` WHERE `email` = '" . $email . "' AND `is_active` = 1";
        $result = $dbUser->query($count);
        if ($result > 0) {
            $response['status'] = true;
        } else {
            $response['status'] = FALSE;
        }
        echo json_encode($response);
        die();
    }

    public function getCountUsers() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $adminemail = $admin = $_SESSION['admin_login'];
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
        $daName = $db->get_col($getDbName);
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
        $count = "SELECT * FROM `user_info` WHERE `is_active`= 1";
        $result = $dbUser->query($count);
        return $result;
    }

    public function getRecentUsers() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $adminemail = $admin = $_SESSION['admin_login'];
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
        $daName = $db->get_col($getDbName);
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
        $count = "SELECT * FROM `user_info` WHERE `created_at` >= now() - INTERVAL 1 DAY ORDER BY id DESC";
        $result = $dbUser->get_results($count, ARRAY_A);
        return $result;
    }

    public function getActiveUsers() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $adminemail = $admin = $_SESSION['admin_login'];
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
        $daName = $db->get_col($getDbName);
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
        $count = "SELECT * FROM `user_info` WHERE `is_active`= 1 ORDER BY name";
        $result = $dbUser->get_results($count, ARRAY_A);
        return $result;
    }

    public function _createUser() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $result = array();
        $adminemail = $_SESSION['admin_login'];
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
        $daName = $db->get_col($getDbName);
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
        $name = $this->post['username'];
        $email = $this->post['email'];
        $contact = $this->post['contact'];
        $company = $this->post['company'];
        if (($name == '') || ($email == '')) {
            $redirectUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: " . $redirectUrl . "?msg=Invalid values! please submit again. ");
            die();
        }
        $digits = 3;
        try {
            $secretCode = $daName[0] . '_' . rand(pow(10, $digits - 1), pow(10, $digits) - 1);
            $userInsert = "INSERT INTO `user_info`(`name`, `email`,`uniqueCode`,`created_at`,`is_on_track`,`is_active`,`company`,`contact`,`deviceId`,`token`,`devicetype`,`cronTrackStatus`)"
                    . "VALUES ('" . $name . "','" . $email . "','" . $secretCode . "','" . (date('Y-m-d H:i:s')) . "',0,1,'" . $company . "','" . $contact . "','','','','')";
            $dbUser->query($userInsert);
            $check = 0;
            $result['status'] = true;
        } catch (Exception $e) {
            $result['status'] = FALSE;
            echo $e->getMessage();
        }
        $this->sendEmail($email, NULL, $name, $check, $secretCode);
        print_r(json_encode($result));
        exit;
    }

    public function deleteUser() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $adminemail = $admin = $_SESSION['admin_login'];
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
        $daName = $db->get_col($getDbName);
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
        $id = $this->get['id'];
        $userEmail = "SELECT `email` FROM `user_info` WHERE `id`=" . $id;
        $data = $dbUser->get_results($userEmail, ARRAY_A);
        $deleteSession = "DELETE  FROM `user_tracking_info` WHERE `email`= '" . $data[0]['email'] . "'";
        $result = $dbUser->query($deleteSession);
        $deleteRecordedSession = "DELETE  FROM `user_session_details` WHERE `email`= '" . $data[0]['email'] . "'";
        $result = $dbUser->query($deleteRecordedSession);
        $deleteopenfireuser = "DELETE  FROM `ofUser` WHERE `email`= '" . $data[0]['email'] . "'";
        $result = $dbUser->query($deleteopenfireuser);
        $count = "DELETE  FROM `user_info` WHERE `id`=" . $id;
        $result = $dbUser->query($count);
        if ($result) {
            $data['success'] = TRUE;
            $data['msg'] = 'User successfully deleted';
        } else {
            $data['success'] = FALSE;
            $data['msg'] = 'Some error occured. Try again later.';
        }
        echo json_encode($data);
        exit;
    }

    public function getAllUsers() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `is_active`=1";
        $daName = $db->get_results($getDbName, ARRAY_A);
        foreach ($daName as $value) {
            if ($value['admindatabase'] != '') {
                $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $value['admindatabase'], $dbhost);
                $count = "SELECT * FROM `user_info` WHERE `is_active`= 1";
                $result[] = $dbUser->get_results($count, ARRAY_A);
            }
        }
        foreach ($result as $value) {
            if (is_array($value)) {
                foreach ($value as $key => $value1) {
                    $final[] = $value1;
                }
            }
        }
        return $final;
    }

    public function getAllUsersCount() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `is_active`=1";
        $daName = $db->get_results($getDbName, ARRAY_A);
        foreach ($daName as $value) {
            if ($value['admindatabase'] != '') {
                $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $value['admindatabase'], $dbhost);
                $count = "SELECT * FROM `user_info` WHERE `is_active`= 1";
                $result[] = $dbUser->get_results($count, ARRAY_A);
            }
        }
        foreach ($result as $value) {
            if (is_array($value)) {
                foreach ($value as $key => $value1) {
                    $final[] = $value1;
                }
            }
        }
        return count($final);
    }

    public function addMultipleSession($details) {
        $data = $details['data'];
        $users = $details['users'];
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $timezone = (string) $data['default_locale'];
        $utc_date = DateTime::createFromFormat(
                        'Y-m-d H:i:s', $data['sdate'], new DateTimeZone($timezone));
        $utc_date->setTimeZone(new DateTimeZone('UTC'));
        $utc_date1 = DateTime::createFromFormat(
                        'Y-m-d H:i:s', $data['edate'], new DateTimeZone($timezone));
        $utc_date1->setTimeZone(new DateTimeZone('UTC'));
        $data['sdate'] = $utc_date->format('Y-m-d H:i:s');
        $data['edate'] = $utc_date1->format('Y-m-d H:i:s');
        try {
            if (!is_numeric($data['interval'])) {
                $result['status'] = FALSE;
                $result['message'] = 'you have provided wrong values';
            } else {
                $adminemail = $admin = $_SESSION['admin_login'];
                $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
                $daName = $db->get_col($getDbName);
                $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
                foreach ($users as $userId) {
                    $query = 'UPDATE `user_info` SET `is_on_track` = 1,`trackStart` ="' . ($data['sdate']) . '",`trackEnd` = "' . ($data['edate']) . '",`cronTrackStatus` = 1,`trackInterval` =' . $data['interval'] . ' where id =' . $userId;
                    $dbUser->query($query);
                }
                $result['message'] = 'Your tracking session is about to start.';
                $result['status'] = TRUE;
                $result['data'] = json_encode($data);
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $result['status'] = FALSE;
        }

        echo json_encode($result);
        exit();
    }

    public function addUserTrackingDetails($data) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $timezone = (string) $data['default_locale'];
        $utc_date = DateTime::createFromFormat(
                        'Y-m-d H:i:s', $data['sdate'], new DateTimeZone($timezone));
        $utc_date->setTimeZone(new DateTimeZone('UTC'));
        $utc_date1 = DateTime::createFromFormat(
                        'Y-m-d H:i:s', $data['edate'], new DateTimeZone($timezone));
        $utc_date1->setTimeZone(new DateTimeZone('UTC'));
        $data['sdate'] = $utc_date->format('Y-m-d H:i:s');
        $data['edate'] = $utc_date1->format('Y-m-d H:i:s');
        try {
            if (!is_numeric($data['interval'])) {
                $result['status'] = FALSE;
                $result['message'] = 'you have provided wrong values';
            } else {
                $adminemail = $admin = $_SESSION['admin_login'];
                $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
                $daName = $db->get_col($getDbName);
                $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
                $query = 'UPDATE `user_info` SET `is_on_track` =' . $data['tracking-status'] . ',`trackStart` ="' . ($data['sdate']) . '",`trackEnd` = "' . ($data['edate']) . '",`trackInterval` =' . $data['interval'] . ' ,`cronTrackStatus` = 1 where id =' . $data['user_id'];
                $dbUser->query($query);
                $result['message'] = 'Your tracking session is about to start.';
                if ($data['save_session'] == 1) {
                    $selectData = 'SELECT * from `user_tracking_info` WHERE `email`=(SELECT `email` FROM `user_info` WHERE `id`= "' . $data['user_id'] . '" and (`locationtime` >= "' . ($data['sdate']) . '" and `locationtime` <= "' . ($data['edate']) . '"))';
                    $locationdata = $dbUser->get_results($selectData, ARRAY_A);
                    if (!empty($locationdata)) {
                        foreach ($locationdata as $value) {
                            $savedLocationData[] = array(date('dS M Y g:i A', strtotime($this->getLocaleDate($value['locationtime']))) => array($value['latitude'], $value['longitude']));
                        }
                        $userqry = 'SELECT `email`,`uniqueCode` FROM `user_info` WHERE `id`= "' . $data['user_id'] . '"';
                        $resultset = $dbUser->get_results($userqry, ARRAY_A);
                        $insertSession = "INSERT INTO `user_session_details` (`grcid`, `email`,`data`) "
                                . "VALUES ('" . $resultset[0]['uniqueCode'] . "','" . $resultset[0]['email'] . "','" . json_encode($savedLocationData) . "')";
                        $dbUser->query($insertSession);
                        $deleteData = 'DELETE  from `user_tracking_info` WHERE `email`=(SELECT `email` FROM `user_info` WHERE `id`= "' . $data['user_id'] . '" )';
                        $dbUser->query($deleteData);
                    }
                    $result['message'] = 'Tracking session saved.';
                } else {
                    $deleteData = 'DELETE  from `user_tracking_info` WHERE `email`=(SELECT `email` FROM `user_info` WHERE `id`= "' . $data['user_id'] . '" and (`locationtime` >= "' . ($data['sdate']) . '" and `locationtime` <= "' . ($data['edate']) . '"))';
                    $dbUser->query($deleteData);
                }
                $result['status'] = TRUE;
                $result['data'] = json_encode($data);
                if ($data['save_session'] !== '' && isset($data['save_session']) && $data['save_session'] == 0) {
                    $result['popup'] = FALSE;
                } else {
                    $result['popup'] = TRUE;
                }
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $result['status'] = FALSE;
        }

        echo json_encode($result);
        exit();
    }

    public function getUserTrackingDetalis($id) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $adminemail = $admin = $_SESSION['admin_login'];
            $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $daName = $db->get_col($getDbName);
            $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
            $query = 'SELECT * FROM `user_info` WHERE id=' . $id;
            $result = $dbUser->get_results($query, ARRAY_A);
            return $result[0];
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return FALSE;
        }
    }

    public function getLocaleDate($date) {
        global $db;
        $adminemail = $admin = $_SESSION['admin_login'];
        if (empty($date)) {
            return '';
        }
//        $getlocale = "select `locale` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
//        $locale = $db->get_col($getlocale);
        $utc_date = DateTime::createFromFormat(
                        'Y-m-d H:i:s', $date, new DateTimeZone('UTC'));
        $utc_date->setTimeZone(new DateTimeZone($_SESSION['locale']));
        return $utc_date->format('Y-m-d H:i:s');
    }

    public function createOpenfireUser($url) {

        $this->urlToHIt = $url;
    }

    public function generateOpenFireUrl() {
        return $this->urlToHIt;
    }

    function sendEmail($email, $adminpassword, $name, $check, $secretCode) {
        $username = 'personneltrackerapp@gmail.com';
        $password = 'nexus211';
        $senderEmail = 'personneltrackerapp@gmail.com';
        $senderName = 'Personnel Tracker Team';
        if ($check == 1) {
            $message = $this->adminEmailTemplate();
            $message = str_replace("\$uname", $email, $message);
            $message = str_replace("\$password", $adminpassword, $message);
            $message = str_replace("\$name", $name, $message);
        }
        if ($check == 0) {
            $message = $this->userEmailTemplate();
            $message = str_replace("\$uname", $name, $message);
            $message = str_replace("\$code", $secretCode, $message);
        }
        $subject = 'Personnel Tracker : Account Successfully Created';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->setFrom($senderEmail, $senderName);
        $mail->addReplyTo($senderEmail, $senderName);
        $mail->addAddress($email, $name);
        $mail->Subject = $subject;
        $mail->msgHTML($message);
        $mail->AltBody = 'This is a plain-text message body';
        if (!$mail->send()) {
            $result['status'] = FALSE;
        } else {
            $result['status'] = TRUE;
            $result['msg'] = 'Email has been successfully sent';
        }
        return;
    }

    public function userEmailTemplate() {
        $message = '<html><body>';
        $message .= '<table width="100%"; rules="all" style="border:1px solid #3A5896;" cellpadding="10">';
        $message .= "<tr><td><img src='http://54.191.56.95/images/logo.png' alt='Personnel Tracker' /></td></tr>";
        $message .= "<tr><td colspan=2> Dear \$uname, Your account has been created. Please open the Personnel Tracker App on your
phone and input the Account Code seen below</td></tr>";
        $message .= "<tr><td colspan=2 font='colr:#999999;'><I>Account Code: \$code </I></td></tr>";
        $message .= "</table>";
        $message .= "</body></html>";
        return $message;
    }

    public function getUserSessionData($id) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $adminemail = $admin = $_SESSION['admin_login'];
            $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $daName = $db->get_col($getDbName);
            $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
            $query = 'SELECT `uniqueCode` FROM `user_info` WHERE id=' . $id;
            $result = $dbUser->get_col($query);
            $getSession = "SELECT * FROM `user_session_details` WHERE `grcid` ='" . $result[0] . "'";
            $sessionList = $dbUser->get_results($getSession, ARRAY_A);
            return $sessionList;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return FALSE;
        }
    }

    public function changeAdminPassword($data) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $result = array();
        try {
            $adminemail = $_SESSION['admin_login'];
            $getPassword = "select `password` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $oldPassword = $db->get_col($getPassword);
            if (md5($data['oldPassword']) == $oldPassword[0]) {
                if ($data['password'] == $data['cpassword']) {
                    $updatePassword = 'UPDATE `admin_database_info` SET `password` = "' . md5($data['password']) . '" WHERE `email` = "' . $adminemail . '"';
                    $db->query($updatePassword);
                    $msg = 'Your password has been updated';
                    $result['msg'] = $msg;
                    $result['status'] = TRUE;
                } else {
                    $msg = 'Password miss matched.Try again.';
                    $result['msg'] = $msg;
                    $result['status'] = FALSE;
                }
            } else {
                $msg = 'Your password did not matched.Please try again!';
                $result['msg'] = $msg;
                $result['status'] = FALSE;
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $result['msg'] = $msg;
            $result['status'] = FALSE;
        }
        echo json_encode($result);
        exit(0);
    }

    function getUtcDate($date) {
        global $db;
        $adminemail = $admin = $_SESSION['admin_login'];
        if (empty($date)) {
            return '';
        }
        $utc_date = DateTime::createFromFormat(
                        'Y-m-d H:i:s', $date, new DateTimeZone($_SESSION['locale']));
        $utc_date->setTimeZone(new DateTimeZone('UTC'));
        return $utc_date->format('Y-m-d H:i:s');
    }

    public function getTrackedData($data) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            if ($data['tracStat'] != 0) {
                $locale = (string) $data['locale'];
                $utc_date = DateTime::createFromFormat(
                                'Y-m-d H:i:s', $data['trackEnd'], new DateTimeZone($locale));
                $utc_date->setTimeZone(new DateTimeZone('UTC'));
                $data['edate'] = strtotime($utc_date->format('Y-m-d H:i:s'));
                if (strtotime(date('Y-m-d H:i:s')) > $data['edate']) {
                    $tracking = 0;
                } else {
                    $tracking = 1;
                }
            } else {
                $tracking = 1;
            }
            $adminemail = $admin = $_SESSION['admin_login'];
            $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $daName = $db->get_col($getDbName);
            $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
            $selectData = 'SELECT * from `user_tracking_info` WHERE (`email`= "' . $data['email'] . '"  and (`locationtime` BETWEEN ' . "'" . $this->getUtcDate($data['trackStart']) . "'" . ' AND  ' . "'" . $this->getUtcDate($data['trackEnd']) . "'" . '))';
            $result = $dbUser->get_results($selectData, ARRAY_A);
            $pin = 1;
            if (!empty($result)) {
                foreach ($result as $value) {
                    $date = $this->getLocaleDate($value['locationtime']);
                    $arrayLatLng[] = array(date('dS M Y g:i A', strtotime($date)), $value['latitude'], $value['longitude'], $pin);
                    ++$pin;
                }
            } else {
                $arrayLatLng = array();
            }
            $final = array('session_expire' => $tracking, 'data' => $arrayLatLng);
            echo json_encode($final);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return FALSE;
        }
    }

    function getSavedSession($sessionId) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $adminemail = $admin = $_SESSION['admin_login'];
            $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $daName = $db->get_col($getDbName);
            $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
            $selectData = 'SELECT `data` from `user_session_details` WHERE  `id`="' . $sessionId . '"';
            $result = $dbUser->get_results($selectData, ARRAY_A);
            $data = json_decode($result[0]['data'], TRUE);
            $pin = 1;
            foreach ($data as $value) {
                foreach ($value as $key1 => $value1) {
                    $arrayLatLng[] = array($key1, $value1[0], $value1[1], $pin);
                    ++$pin;
                }
            }


            if (!empty($result)) {

                echo json_encode($arrayLatLng);
            } else {
                return FALSE;
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return FALSE;
        }
    }

    function deleteSavedSession($sessionId) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $adminemail = $admin = $_SESSION['admin_login'];
            $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $daName = $db->get_col($getDbName);
            $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
            $selectData = 'DELETE from `user_session_details` WHERE  `id`="' . $sessionId . '"';
            $dbUser->query($selectData);
            $result['success'] = TRUE;
            echo json_encode($result);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return FALSE;
        }
    }

    function getCurrentTimeFormat($date) {

        if ($date) {
            $utc_date = DateTime::createFromFormat(
                            'Y-m-d H:i:s', $date, new DateTimeZone('UTC'));
            $nyc_date = $utc_date;
            $nyc_date->setTimeZone(new DateTimeZone(date_default_timezone_get()));
            return $nyc_date->format('Y-m-d H:i:s');
        } else {
            return '';
        }
    }

    function getUtcTimeFormat($date, $default) {

        if ($date) {
            $date = new DateTime($date, new DateTimeZone($default));
            $date->setTimezone(new DateTimeZone('UTC'));
            return $date->format('Y-m-d H:i:s');
        } else {
            return '';
        }
    }

    function saveTicket($name, $email, $company, $ticket) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $query = 'INSERT INTO `supportTicket` (`name`,`email`,`company`,`ticket`) VALUES ("' . $name . '","' . $email . '","' . $company . '","' . $ticket . '")';
            if ($db->query($query)) {
                $username = 'personneltrackerapp@gmail.com';
                $password = 'nexus211';
                $subject = 'Personnel Tracker Feedback';
                $message = file_get_contents('./mail.html');
                $message = str_replace("\$msg", $ticket, $message);
                $message = str_replace("\$name", $name, $message);
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'html';
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $username;
                $mail->Password = $password;
                $mail->setFrom($email, $name);
                $mail->addReplyTo($email, $name);
                $mail->addAddress($username, 'Personnel Tracker Team');
                $mail->Subject = $subject;
                $mail->msgHTML($message);
                if (!$mail->send()) {
                    $result['status'] = FALSE;
                } else {
                    $result['status'] = TRUE;
                    $result['msg'] = 'Email has been successfully sent';
                }
                $result['status'] = TRUE;
            } else {
                $result['status'] = false;
            }
        } catch (Exception $e) {
            $result['status'] = FALSE;
        }
        echo json_encode($result);
        exit(0);
    }

    function getTotalTicketCounts() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $query = 'SELECT count(*) FROM `supportTicket` WHERE `status` = 0';
            $res = $db->get_results($query, ARRAY_A);
            return $res[0]['count(*)'];
        } catch (Exception $e) {
            
        }
    }

    function getTicketInfo() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $query = 'SELECT * FROM `supportTicket` WHERE `status` = 0';
            $res = $db->get_results($query, ARRAY_A);
            return $res;
        } catch (Exception $e) {
            
        }
    }

    function closeTicket($id) {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $query = 'UPDATE `supportTicket` SET `status` = 1 WHERE id=' . $id;
            if ($db->query($query)) {
                $msg = 'Ticket has been updated!';
                header("Location: viewTickets.php?msg=" . $msg);
                exit(0);
            } else {
                $msg = 'Some error occurred! Try again  !';
                header("Location: viewTickets.php?msg=" . $msg);
                exit(0);
            }
        } catch (Exception $e) {
            header("Location: viewTickets.php?msg=" . $e->getMessage());
            exit(0);
        }
    }

    public function getRunningSessionCount() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $adminemail = $admin = $_SESSION['admin_login'];
            $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $daName = $db->get_col($getDbName);
            $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
            $selectData = 'SELECT count(*) from `user_info` WHERE  `is_on_track`=1';
            $res = $dbUser->get_results($selectData, ARRAY_A);
            return $res[0]['count(*)'];
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return $msg;
        }
    }

    public function getRunningSession() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        try {
            $adminemail = $admin = $_SESSION['admin_login'];
            $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
            $daName = $db->get_col($getDbName);
            $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
            $selectData = 'SELECT * from `user_info` WHERE  `is_on_track`=1';
            $res = $dbUser->get_results($selectData, ARRAY_A);
            return $res;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return FALSE;
        }
    }

    public function getRecentSession() {
        global $db;
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $adminemail = $admin = $_SESSION['admin_login'];
        $getDbName = "select `admindatabase` from `admin_database_info` WHERE `email` = '" . $adminemail . "' AND `is_active`=1";
        $daName = $db->get_col($getDbName);
        $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $daName[0], $dbhost);
        $count = "SELECT user_session_details.email,user_session_details.id,user_session_details.grcid,user_info.name,user_info.company FROM `user_session_details` INNER JOIN `user_info` ON user_session_details.grcid = user_info.uniqueCode ORDER BY id DESC";
        $result = $dbUser->get_results($count, ARRAY_A);
        return $result;
    }

    public function getOfflineUsers() {
        global $db;
        $selectOffline = "select `username` from `ofPresence` ";
        $result = $db->get_results($selectOffline, ARRAY_A);
        return $result;
    }

    public function getUserOnline($id) {
        global $db;

        $userExist = "select count(*) from `ofUser` WHERE `username`='" . strtolower($id) . "' ";
        $res1 = $db->get_results($userExist, ARRAY_A);
        if ($res1[0]['count(*)']) {
            $selectOffline = "select count(*) from `ofPresence` WHERE `username`='" . strtolower($id) . "' ";
            $res = $db->get_results($selectOffline, ARRAY_A);
            return $res[0]['count(*)'];
        } else {
            echo 1;
            exit();
        }
    }

    public function saveTimezone($data) {
        global $db;
        $query = "UPDATE `admin_database_info` SET `locale`= '" . $data['timezone'] . "' WHERE `email` = '" . $data['email'] . "' AND `is_active`=1";
        $_SESSION['locale'] = $data['timezone'];
        $db->query($query);
        return true;
        exit;
    }

    function setTrackingUpdate($usr) {
        global $dbhost;
        global $dbpassword;
        global $dbuser;
        $currentDate = date('Y-m-d H:i:s');
        foreach ($usr as $value) {
            if (strtotime($value['trackEnd']) < strtotime($currentDate)) {
                $dbDetalis = explode('_', $value['uniqueCode']);
                $dbUser = new ezSQL_mysql($dbuser, $dbpassword, $dbDetalis[0], $dbhost);
                $trackingStatus = 'UPDATE `user_info` SET `cronTrackStatus` = 0 WHERE `uniqueCode` = "' . $value['uniqueCode'] . '"';
                $dbUser->query($trackingStatus);
                unset($dbUser);
            }
        }
        return TRUE;
    }

}
