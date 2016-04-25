<?php
include_once 'admin-class.php';
$admin = new itg_admin();
$admin->isSuperAdmin();
$adminUser = $admin->getCompleteAdminUsers();
if (count($adminUser) <= 0) {
    header("Location: dashboard.php?msg=No admin found");
}
if ($_GET['op'] == "delete") {
    $admin->deleteAdmin();
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
        <title>Tracker | Admins</title>
        <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="dist/css/sb-admin-2.css" rel="stylesheet">
        <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
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
                            <h1 class="page-header">Admin Users</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Showing List Of Admins
                                </div>
                                <div class="panel-body">
                                    <div class="dataTable_wrapper">
                                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
<!--                                            <div class="row"><div class="col-sm-6"><div class="dataTables_length" id="dataTables-example_length"><label>Show <select name="dataTables-example_length" aria-controls="dataTables-example" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> entries</label></div></div><div class="col-sm-6">
                                                </div>
                                            </div>-->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table id="dataTables-example" class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="dataTables-example_info">
                                                        <thead>
                                                            <tr role="row">
                                                                <th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 175px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Name</th>
                                                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 203px;" aria-label="Browser: activate to sort column ascending">Email</th>
                                                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 184px;" aria-label="Platform(s): activate to sort column ascending">Database</th>
                                                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 150px;" aria-label="Engine version: activate to sort column ascending">Registration Date</th>
                                                                <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 108px;" aria-label="CSS grade: activate to sort column ascending">Operation</th></tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($adminUser as $value) { ?>
                                                                <tr class="gradeA odd" role="row">
                                                                    <td class="sorting_1"><?php echo $value['name']; ?></td>
                                                                    <td><?php echo $value['email']; ?></td>
                                                                    <td><?php echo $value['admindatabase']; ?></td>
                                                                    <?php $date = date_create($value['created_at']);?>
                                                                    <td class="center"><?php echo date_format($date, 'jS F Y'); ?></td>
                                                                    <td class="center"><a href="admins.php?op=delete&id=<?php echo $value['id']; ?>">Delete</a></td>
                                                                </tr>                                                                
                                                            <?php } ?>
                                                        </tbody>
                                                    </table></div></div>
<!--                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="dataTables_info" id="dataTables-example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div>

                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                                        <ul class="pagination">
                                                            <li class="paginate_button previous disabled" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous">
                                                                <a href="#">Previous</a></li>
                                                            <li class="paginate_button active" aria-controls="dataTables-example" tabindex="0">
                                                                <a href="#">1</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
                                                                <a href="#">2</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
                                                                <a href="#">3</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
                                                                <a href="#">4</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
                                                                <a href="#">5</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
                                                                <a href="#">6</a></li><li class="paginate_button next" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next">
                                                                <a href="#">Next</a></li></ul></div>
                                                </div>
                                            </div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="dist/js/sb-admin-2.js"></script>
    </body>
</html>
