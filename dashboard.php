<?php
include_once 'admin-class.php';
$admin = new itg_admin();
if (!$_SESSION['admin_login']) {
    header('Location: index.php');
}
if ($_POST['saveTime'] == 1) {
    $admin->saveTimezone($_POST);
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
        <title>Personnel Tracker</title>
        <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="dist/css/sb-admin-2.css" rel="stylesheet">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="bower_components/datePicker/jquery.js"></script>
        <script src="js/strophe.js" type="text/javascript"></script>
        <script src="js/strophe-openfire.js" type="text/javascript"></script>
        <script src="js/ServerManager.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="dist/js/jstz.min.js"/>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
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
                        <h1 class="page-header">Dashboard</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-user fa-5x"></i>
                                    </div>
                                    <?php if ($_SESSION['is_super'] == 1) { ?>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"><?php echo $admin->getCountAdmin(); ?></div>
                                            <div>Admin Users</div>
                                        </div>
                                    <?php } elseif ($_SESSION['is_super'] == 0) { ?>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"><?php echo $admin->getCountUsers(); ?></div>
                                            <div><a style="color: white" href="users.php" >Total Users</a></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if ($_SESSION['is_super'] == 1) { ?>
                                <a href="addAdminUsers.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">Add Admin</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            <?php } elseif ($_SESSION['is_super'] == 0) { ?>
                                <a href="addAdminUsers.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">Add Users</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($_SESSION['is_super'] == 1) { ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-green">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-tasks fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"><?php echo $admin->getAllUsersCount() ?></div>
                                            <div>Users</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="users.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($_SESSION['is_super'] == 0) { ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-yellow">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-mobile fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"><?php echo $admin->getRunningSessionCount() ?></div>
                                            <div>Active sessions</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="runningSession.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-red">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-question-circle fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <?php if ($_SESSION['is_super'] == 1) { ?>
                                            <div class="huge"><?php echo $admin->getTotalTicketCounts() ?></div>
                                        <?php } else { ?>
                                            <div class="huge" style="visibility: hidden">0</div>
                                        <?php } ?>
                                        <div>Feedback</div>
                                    </div>
                                </div> 
                            </div>
                            <?php if ($_SESSION['is_super'] == 1) { ?>
                                <a href="viewTickets.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            <?php } elseif ($_SESSION['is_super'] == 0) { ?>
                                <a href="addTickets.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">Add Feedback</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            <?php } ?>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php if ($_SESSION['is_super'] == 1) { ?>
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Recent Admins
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Admin database</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $data = $admin->getAdminUsers();
                                                $i = 1;
                                                foreach ($data as $value) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $value['name']; ?></td>
                                                        <td><?php echo $value['email']; ?></td>
                                                        <td><?php echo $value['admindatabase']; ?></td>
                                                    </tr>
                                                    <?php
                                                    ++$i;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($_SESSION['is_super'] == 0) { ?>
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Recently Added User's (24hrs)
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Company</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $data = $admin->getRecentUsers();
                                                $i = 1;
                                                foreach ($data as $value) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $value['name']; ?></td>
                                                        <td><?php echo $value['email']; ?></td>
                                                        <td><?php echo $value['company']; ?></td>
                                                    </tr>
                                                    <?php
                                                    ++$i;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($_SESSION['is_super'] == 0) { ?>
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Recent Expired Sessions (24hrs)
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Id</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Company</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $data = $admin->getRecentSession();
                                                $i = 1;
                                                foreach ($data as $value) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $value['grcid']; ?></td>
                                                        <td><?php echo $value['name']; ?></td>
                                                        <td><?php echo $value['email']; ?></td>
                                                        <td><?php echo $value['company']; ?></td>
                                                    </tr>
                                                    <?php
                                                    ++$i;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if ($_SESSION['is_super'] == 0) { ?>
            <script>
                ServerManager.connect('http://54.191.56.95:7070', 'personneltracker', '<?php echo $_SESSION['data']['admindatabase'] . '@personneltracker' ?>', '<?php echo $_SESSION['data']['databasepassword'] ?>', 'roster_entry');
            </script>

        <?php } ?>
        <script type="text/javascript">
            var timezone = jstz.determine();
            var localetimezone = (timezone.name());
            $.ajax({
                url: 'dashboard.php',
                type: 'post',
                dataType: 'json',
                data: {saveTime: 1, timezone: localetimezone,email:'<?php echo $_SESSION['data']['email']?>'},
                success: function (result) {
                    cosole.log('timexone updated');
                }
            });
        </script>
    </body>

</html>