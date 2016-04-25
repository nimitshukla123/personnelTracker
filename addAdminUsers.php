<?php
include_once dirname(__FILE__) . '/admin-class.php';
session_start();

$admin = new itg_admin();
if ($_SESSION['is_super'] == 1) {
    if ($_POST) {
        if ($_POST['createadmin'] == 1) {
            $admin->_createAdmin();
        }
    }
    if ($_GET['check'] == 1) {
        $admin->_checkAdmin($_GET['email']);
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">
            <title>Tracker | Add Admin</title>
            <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
            <link href="dist/css/sb-admin-2.css" rel="stylesheet">
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
            <script src="bower_components/datePicker/jquery.js"></script>
            <script src="bower_components/datePicker/jquery.datetimepicker.full.js"></script>
            <link rel="stylesheet" href="bower_components/datePicker/jquery.datetimepicker.css">
            <script src="bower_components/datePicker/formatDate.js"></script>
            <script src="js/strophe.js" type="text/javascript"></script>
            <script src="js/strophe-openfire.js" type="text/javascript"></script>
            <script src="js/ServerManager.js"></script>
            <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
            <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
            <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
            <script src="bower_components/raphael/raphael-min.js"></script>
            <script src="bower_components/morrisjs/morris.min.js"></script>
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
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
                                <i class="fa fa-bars fa-fw"></i>  <i class="fa fa-caret-down"></i>
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
                    <div class="navigation">
                        <div class="sidebar-nav navbar-collapse">
                            <ul class="nav" style="float: left;line-height: 30px">
                                <li style="float: left">
                                    <a href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                                </li>
                                <?php if ($_SESSION['is_super'] == 1) { ?>
                                    <li style="float: left">
                                        <a href="admins.php"><i class="fa fa-users"></i> Admin Users</a>
                                    </li>
                                <?php } ?>
                                <li style="float: left">
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
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Add Admin </h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <form role="form" id="addUserForm" action="addAdminUsers.php" method="post">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input name="username" class="form-control" placeholder="Enter Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input id="emailvalidate" name="email" class="form-control" placeholder="Enter Email">
                                                    <span id="email-error" style="color:red;display: none">This Email already exist.</span>
                                                </div>
                                                <div class="form-group">
                                                    <label>Contact</label>
                                                    <input id="contact" name="contactno" class="form-control" placeholder="Enter Contact">
                                                </div>
                                                <div class="form-group">
                                                    <label>password</label>
                                                    <input id="password" name="password" class="form-control" placeholder="Enter Password">
                                                </div>
                                                <div class="form-group">
                                                    <label>confirm password</label>
                                                    <input id="cpassword" name="cpassword" class="form-control" placeholder="Confirm Password">
                                                </div>
                                                <input type="hidden" name="createadmin" value="1"/>
                                                <button id="adminButton" type="button" disabled="" onclick="submitForm();" class="btn btn-default">Submit</button>
                                                <button type="reset" class="btn btn-default">Reset</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="dialog-email" title="Action" style="display: none">
                    <p>
                        <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">

                        </span>Invalid Email!</p>
                </div>
                <div class="windows8 loader-on-submit" style="margin: 0 auto;overflow: auto;z-index: 99999;display: none">
                    <div class="wBall" id="wBall_1">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_2">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_3">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_4">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_5">
                        <div class="wInnerBall"></div>
                    </div>
                </div>
                <div id="dialog-user-added" title="Personnel Tracker" style="display: none">
                    <p>
                        <span  style="float:left; margin:0 7px 20px 0;">

                        </span>Admin added successfully</p>
                </div>
            </div>
            <iframe style="width: 200px;height: 200px;display: none" id="addAdminOpenfire" src="#"></iframe> 
        </body>
        <script type="text/javascript">
            function submitForm() {
                var pass1 = document.getElementById("password").value;
                var pass2 = document.getElementById("cpassword").value;
                if (pass1 != pass2) {
                    document.getElementById("password").style.borderColor = "#E34234";
                    document.getElementById("cpassword").style.borderColor = "#E34234";
                    return false;
                }
                else {
                    var addUserForm = jQuery('#addUserForm').serialize();
                    $('.loader-on-submit').show();
                    jQuery.ajax({
                        url: 'addAdminUsers.php',
                        type: 'post',
                        dataType: 'json',
                        data: addUserForm, success: function (data) {
                            if (data.status) {
                                $('.loader-on-submit').hide();
                                var url = data.url;
                                var urlToHIt = JSON.stringify(url);
                                urlToHIt = urlToHIt.replace(/^"/, "");
                                urlToHIt = urlToHIt.replace(/"$/, "");
                                window.open(urlToHIt, '_blank');
                                $('.loader-on-submit').hide();
                                jQuery("#dialog-user-added").dialog({
                                    resizable: false,
                                    height: 180,
                                    modal: true,
                                    dialogClass: "noOverlayDialog",
                                    buttons: {
                                        "Ok": function () {
                                            $(this).dialog("close");
                                            jQuery('#addUserForm').trigger('reset');
                                            jQuery('#adminButton').prop('disabled', 'disabled');
                                        },
                                    }, open: function (event, ui) {
                                        $('.noOverlayDialog').next('div').css({'opacity': 0.0});
                                    }
                                });
                            } else {
                                $('.loader-on-submit').hide();
                                alert('Unable to create admin. Please try again!');
                            }
                        }

                    });
                }
            }
            function validateEmail(email) {
                var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                return re.test(email);
            }

            jQuery(document).ready(function () {
                jQuery('#emailvalidate').focusout(function () {
                    var email = jQuery(this).val();
                    if (email !== '') {
                        var result = validateEmail(email);
                        if (result) {
                            jQuery.ajax({
                                url: 'addAdminUsers.php', dataType: "json",
                                data: {email: email, check: 1},
                                success: function (data) {
                                    if (data.status == false) {
                                        jQuery('#adminButton').removeAttr('disabled');
                                    }
                                    else if (data.status == true) {
                                        document.getElementById("emailvalidate").style.borderColor = "#E34234";
                                        jQuery('span#email-error').show();
                                        setTimeout(function () {
                                            jQuery('span#email-error').hide(300);
                                        }, 3000);
                                        jQuery('#adminButton').prop('disabled', "disabled");
                                    }
                                }
                            });
                        } else {
                            jQuery("#dialog-email").dialog({
                                resizable: false,
                                height: 180,
                                modal: true,
                                dialogClass: "noOverlayDialog",
                                buttons: {"Ok": function () {
                                        $(this).dialog("close");
                                    },
                                },
                                open: function (event, ui) {
                                    $('.noOverlayDialog').next('div').css({'opacity': 0.0});
                                }
                            });
                        }
                    } else {
                        jQuery('#adminButton').addAttr("disabled", "disabled");
                    }
                });
            });
        </script>

    </html>
    <?php
} elseif ($_SESSION['is_super'] == 0) {
    if ($_POST['createuser'] == 1) {
        $admin->_createUser();
    }
    if ($_GET['check'] == 1) {
        $admin->_checkUser($_GET['email']);
    }
    ?>
    <!DOCTYPE html>
    <html lang = "en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">
            <title>Tracker | Add Admin</title>
            <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
            <link href="dist/css/sb-admin-2.css" rel="stylesheet">
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
            <script src="bower_components/datePicker/jquery.js"></script>
            <script src="bower_components/datePicker/jquery.datetimepicker.full.js"></script>
            <link rel="stylesheet" href="bower_components/datePicker/jquery.datetimepicker.css">
            <script src="bower_components/datePicker/formatDate.js"></script>
            <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
            <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
            <script src="bower_components/morrisjs/morris.min.js"></script>
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        </head>
        <body>
            <div id = "wrapper">
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
                                <i class="fa fa-bars fa-fw"></i>  <i class="fa fa-caret-down"></i>
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
                    <div class="navigation">
                        <div class="sidebar-nav navbar-collapse">
                            <ul class="nav" style="float: left;line-height: 30px">
                                <li style="float: left">
                                    <a href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                                </li>
                                <?php if ($_SESSION['is_super'] == 1) { ?>
                                    <li style="float: left">
                                        <a href="admins.php"><i class="fa fa-users"></i> Admin Users</a>
                                    </li>
                                <?php } ?>
                                <li style="float: left">
                                    <a href="users.php"><i class="fa fa-users"></i> Users</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </nav>
                <div class = "cust-message">
                    <?php echo $_GET['msg'];
                    ?>
                </div>
                <div id="page-wrapper">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Add Users</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <form role="form" id="addUserForm" action="addAdminUsers.php" method="post">
                                                <div class="form-group">
                                                    <label class="required">Name</label>
                                                    <input id="username" name="username" class="form-control" placeholder="Enter Name">
                                                </div>
                                                <div class="form-group">
                                                    <label class="required">Email</label>
                                                    <input id="emailvalidate" name="email" class="form-control" placeholder="Enter Email" required="">
                                                </div>
                                                <div class="form-group">
                                                    <label>Company Name </label>
                                                    <input name="company" class="form-control" placeholder="Enter Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Mobile Number</label>
                                                    <input name="contact" class="form-control" placeholder="Enter Name">
                                                </div>
                                                <input type="hidden" name="createuser" value="1"/>
                                                <button id="adminButton" type="button" disabled="" onclick="submitForm();" class="btn btn-default">Submit</button>
                                                <button type="reset" class="btn btn-default">Reset</button>
                                            </form>
                                        </div>
                                        <div class="error-message">
                                            <span id="email-error" style="color:red;display: none;">Email already exist.</span>
                                            <div class="windows8 loader-on-submit" style="margin: 0 auto;overflow: auto;z-index: 99999;display: none">
                                                <div class="wBall" id="wBall_1">
                                                    <div class="wInnerBall"></div>
                                                </div>
                                                <div class="wBall" id="wBall_2">
                                                    <div class="wInnerBall"></div>
                                                </div>
                                                <div class="wBall" id="wBall_3">
                                                    <div class="wInnerBall"></div>
                                                </div>
                                                <div class="wBall" id="wBall_4">
                                                    <div class="wInnerBall"></div>
                                                </div>
                                                <div class="wBall" id="wBall_5">
                                                    <div class="wInnerBall"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="dialog-email" title="Personnel Tracker" style="display: none">
                    <p>
                        <span  style="float:left; margin:0 7px 20px 0;">

                        </span>Invalid Email!</p>
                </div>
                <div id="dialog-user-added" title="Personnel Tracker" style="display: none">
                    <p>
                        <span  style="float:left; margin:0 7px 20px 0;">

                        </span>User added</p>
                </div>

            </div>
        </body>
        <script type="text/javascript">
                function submitForm() {
                    if ($('#emailvalidate').val() != '' && $('#username').val() != '') {
                        $('.loader-on-submit').show();
                        var formData = jQuery('#addUserForm').serialize();
                        jQuery.ajax({
                            url: 'addAdminUsers.php',
                            type: 'post',
                            dataType: 'json',
                            data: formData,
                            success: function (data) {
                                if (data.status) {
                                    $('.loader-on-submit').hide();
                                    jQuery("#dialog-user-added").dialog({
                                        resizable: false,
                                        height: 180,
                                        modal: true,
                                        dialogClass: "noOverlayDialog",
                                        buttons: {
                                            "Ok": function () {
                                                $(this).dialog("close");
                                                jQuery('#addUserForm').trigger('reset');
                                                jQuery('#adminButton').prop('disabled', 'disabled');
                                            },
                                        }, open: function (event, ui) {
                                            $('.noOverlayDialog').next('div').css({'opacity': 0.0});
                                        }
                                    });
                                } else {
                                    $('.loader-on-submit').hide();
                                    alert('can not create user!Try again');
                                }
                            }
                        });
                    } else {
                        alert('email and name is a required field');
                    }
                }
            function validateEmail(email) {
                var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                return re.test(email);
            }

            jQuery(document).ready(function () {
                jQuery('#emailvalidate').focusout(function () {
                    var email = jQuery(this).val();
                    if (email !== '') {
                        var result = validateEmail(email);
                        if (result) {
                            jQuery.ajax({
                                url: 'addAdminUsers.php', dataType: "json",
                                data: {email: email, check: 1},
                                success: function (data) {
                                    if (data.status == false) {
                                        jQuery('#adminButton').removeAttr('disabled');
                                    }
                                    else if (data.status == true) {
                                        jQuery('span#email-error').show();
                                        setTimeout(function () {
                                            jQuery('span#email-error').hide(300);
                                        }, 3000);
                                    }
                                }
                            });
                        } else {
                            jQuery("#dialog-email").dialog({
                                resizable: false,
                                height: 180,
                                modal: true,
                                dialogClass: "noOverlayDialog",
                                buttons: {"Ok": function () {
                                        $(this).dialog("close");
                                    },
                                },
                                open: function (event, ui) {
                                    $('.noOverlayDialog').next('div').css({'opacity': 0.0});
                                }
                            });
                        }
                    } else {
                        jQuery('#adminButton').prop("disabled", "disabled");
                    }
                });
            });
        </script>

    </html>
    <?php
} else {
    header("Location: dashboard.php");
    die();
}
?>






