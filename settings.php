<?php
include_once 'admin-class.php';
$admin = new itg_admin();
if (!$_SESSION['admin_login']) {
    header('Location: index.php');
}

if(!empty($_POST)){
    $admin->changeAdminPassword($_POST);
}
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Personnel Tracker</title>
        <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="dist/css/timeline.css" rel="stylesheet">
        <link href="dist/css/sb-admin-2.css" rel="stylesheet">
        <link href="bower_components/morrisjs/morris.css" rel="stylesheet">
        <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="js/jQuery-v1.9.1.js" type="text/javascript"></script>
        <script src="js/jquery.ui.widget.min.js" type="text/javascript"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="bower_components/raphael/raphael-min.js"></script>
        <script src="bower_components/morrisjs/morris.min.js"></script>
        <script src="js/morris-data.js"></script>
        <script src="dist/js/sb-admin-2.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="dashboard.php">Personnel Tracker</a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="adminProfile.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                            </li>
                            <li><a href="settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a>
                            </li>
                            <li class="divider"></li>
                            <li><a onclick="javascript:window.location.href = 'logout.php'"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li>
                                <a href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i>Dashboard</a>
                            </li>
                            <?php if ($_SESSION['is_super'] == 1) { ?>
                                <li>
                                    <a href="admins.php"><i class="fa fa-users"></i> Admin Users</a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="users.php"><i class="fa fa-users"></i> Users</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="cust-message">
                <?php echo $_GET['msg']; ?>
            </div>
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Settings</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="dataTable_wrapper">
                                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                            <div class="row">
                                                <div class="change-password" style="padding-left: 30px">
                                                    <a onclick="showChangePasswordForm();" href="javascript:void(0)">Click here to change your password</a>
                                                    <div class="password-form" style="display: none;">
                                                        <form id="password-change" method="post" action="settings.php" onsubmit="return checkValidation()">
                                                            <table style="height: 150px">
                                                                <tr style="padding-bottom: 5px">
                                                                    <td><label>Old Password:</label></td>
                                                                    <td> <input type="text" name="oldPassword"/></td>
                                                                </tr>
                                                                <tr style="padding-bottom: 5px">
                                                                    <td><label>New Password:</label></td>
                                                                    <td><input type="text" name="password"/></td>
                                                                </tr>
                                                                <tr style="padding-bottom: 5px">
                                                                    <td> <label>Confirm Password:</label></td>
                                                                    <td> <input type="text" name="cpassword"/></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center" colspan="2"> <button type="submit" name="submit">submit</button></td>
                                                                </tr>
                                                            </table>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function showChangePasswordForm() {
                $('.password-form').show();
            }
            
            function checkValidation(){
              
            }
        </script>
    </body>
</html>
