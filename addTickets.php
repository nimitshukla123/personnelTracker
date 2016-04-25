<?php
include_once 'admin-class.php';
$admin = new itg_admin();
if (!$_SESSION['admin_login']) {
    header('Location: index.php');
}

if ($_POST) {
    if (!empty($_POST['ticket'])) {
        $admin->saveTicket($_POST['name'], $_POST['email'], $_POST['company'], $_POST['ticket']);
    }
}
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Tracker | Users</title>
        <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="dist/css/sb-admin-2.css" rel="stylesheet">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
            <script src="bower_components/datePicker/jquery.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="bower_components/raphael/raphael-min.js"></script>
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
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Send Email</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="contact-us" style="height:100%;width: 100%">
                                        <h4>Please provide info on anything that would improve the Personnel
                                            Tracker experience</h4>
                                        <form id="contact-us-form" method="post" action="addTickets.php" style="height: 100%;width: 100%">
                                            <table  style="line-height: 30px; float: left;width: 100%;height: 50%">
                                                <tr>
                                                    <td><label>Name:</label></td>
                                                    <td><input style="width: 30%" type="text" name="name"  value="<?php echo $_SESSION['data']['name'] ?>"/></td>
                                                </tr>
                                                <tr>
                                                    <td><label>From:</label></td>
                                                    <td><input style="width: 30%" type="text" name="email" readonly="" value="<?php echo $_SESSION['data']['email'] ?>"/></td>
                                                </tr>
                                                <tr>
                                                    <td><label>Company:</label></td>
                                                    <td><input style="width: 30%" type="text" name="company"  value="<?php echo $_SESSION['data']['company'] ?>"/></td>
                                                </tr>
                                                <tr>
                                                    <td><label>Feedback:</label></td>
                                                    <td><textarea style="width: 30%" name="ticket" cols="20" rows="3"></textarea></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="padding-left: 200px"><input type="button" onclick="submitTicket();" value="submit"></input></td> 
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
            <div id="dialog-ticket" title="Personnel Tracker" style="display: none">
                <p>
                    <span  style="float:left; margin:0 0px 0px 0;">

                    </span><span style="float:left;" id="track-msg-sessn">Your email has been sent</span></p>
            </div>
        </div>
    </body>
    <script type="text/javascript">
        function submitTicket() {
            var data = $('#contact-us-form').serialize();
            $.ajax({
                url: 'addTickets.php',
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (result) {
                    if (result.status == true) {
                        $("#dialog-ticket").dialog({
                            resizable: false,
                            height: 180,
                            modal: true,
                            dialogClass: "noOverlayDialog",
                            buttons: {
                                "Ok": function () {
                                    $(this).dialog("close");
                                    window.location.reload();
                                },
                            },
                            open: function (event, ui) {
                                $('.noOverlayDialog').next('div').css({'opacity': 0.0});
                            }
                        });
                    }
                }
            });
        }
    </script>
</html>

