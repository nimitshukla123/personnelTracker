<?php
include_once 'admin-class.php';
$admin = new itg_admin();
if ($_SESSION['is_super'] == 0) {
    $adminUser = $admin->getActiveUsers();
} elseif ($_SESSION['is_super'] == 1) {
    $adminUser = $admin->getAllUsers();
} else {
    header("Location: dashboard.php?msg=Not authorized to view users");
}
if (count($adminUser) <= 0) {
    header("Location: dashboard.php?msg=No users found");
}
if ($_GET['op'] == "delete") {
    $admin->deleteUser();
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
        <title>Tracker | Users</title>
       <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="dist/css/sb-admin-2.css" rel="stylesheet">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="bower_components/datePicker/jquery.js"></script>
        <script src="js/strophe.js" type="text/javascript"></script>
        <script src="js/strophe-openfire.js" type="text/javascript"></script>
        <script src="js/ServerManager.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="bower_components/raphael/raphael-min.js"></script>
        <script src="bower_components/morrisjs/morris.min.js"></script>
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
                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li>
                                <a href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
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
                            <h1 class="page-header">Users</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                   Showing List of Personnel 
                                </div>
                                <div class="panel-body">
                                    <div class="dataTable_wrapper">
                                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table id="dataTables-example" class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="dataTables-example_info">
                                                        <thead>
                                                            <tr role="row">
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 175px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Name</th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 203px;" aria-label="Browser: activate to sort column ascending">Email</th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 150px;" aria-label="Engine version: activate to sort column ascending">Registration Date</th>
                                                                 <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 175px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Contact Number</th>
                                                                 <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 175px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Company Name</th>
                                                                <?php if ($_SESSION['is_super'] == 0) { ?> <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 108px;" aria-label="CSS grade: activate to sort column ascending">Operation</th><?php } ?>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 100px;" aria-label="Engine version: activate to sort column ascending">Current Status</th></tr>
                                                        </thead> 
                                                        <tbody>
                                                            <?php foreach ($adminUser as $value) { ?>
                                                                <tr class="gradeA odd" role="row">
                                                                    <td class="sorting_1"><?php echo $value['name']; ?></td>
                                                                    <td><?php echo $value['email']; ?></td>
                                                                    <?php $date = date_create($value['created_at']); ?>
                                                                    <td class="center"><?php echo date_format($date, 'jS F Y'); ?></td>
                                                                    <td><?php echo $value['contact']; ?></td>
                                                                    <td><?php echo $value['company']; ?></td>
                                                                    <?php if ($_SESSION['is_super'] == 0) { ?>   <td class="center">
                                                                            <a href="users.php?op=delete&id=<?php echo $value['id']; ?>">Delete</a> | 
                                                                            <a href="userProfile.php?id=<?php echo base64_encode($value['id']); ?>">View</a>
                                                                        </td><?php } ?>
                                                                    <td>
                                                                        <span class="show-user-status" id="<?php echo $value['uniqueCode'] ?>">Offline</span>
                                                                    </td>
                                                                </tr>                                                                
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
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
        <script type="text/javascript">
              ServerManager.connect('http://52.24.255.248:7070', 'personneltracker', '<?php echo $_SESSION['data']['admindatabase'] . '@personneltracker' ?>', '<?php echo $_SESSION['data']['databasepassword'] ?>', 'roster_entry');
            function userStatus(uid,status){
                var user = uid.substr(0,9).toUpperCase();
                $('.show-user-status').each(function(){
                if($(this).attr('id') == user){
                    $(this).html(status);
                }
            })
            }
        </script>
    </body>
</html>
