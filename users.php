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
if ($_POST) {
    $admin->addMultipleSession($_POST);
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
        <script src="bower_components/datePicker/jquery.datetimepicker.full.js"></script>
        <link rel="stylesheet" href="bower_components/datePicker/jquery.datetimepicker.css">
        <script src="bower_components/datePicker/formatDate.js"></script>
        <script src="js/strophe.js" type="text/javascript"></script>
        <script src="js/strophe-openfire.js" type="text/javascript"></script>
        <script src="js/ServerManager.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="dist/js/jstz.min.js"/>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="bower_components/raphael/raphael-min.js"></script>
        <script src="bower_components/morrisjs/morris.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;z-index: 999">
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
                            <li style="float: left">
                                <a href="javascript:void(0)" onclick="showOptions(this)"><i class="fa fa-users"></i> Start Tracking</a>
                            </li>

                        </ul>

                    </div>
                </div>
                <div id="user-form-values" style="display: none;float: left;">
                    <form class="configure-input" method="post" action="userProfile.php">
                        <input type="hidden" name="default_locale" value="" class="def-local"/>
                        <div class="tracking-status">
                            <div style="float: left;width: 130px">
                                <button class="startbtn" onclick="showForm();" type="button" name="Start Tracking">Start Tracking</button>
                            </div>
                            <div class="tracking-details-form" style="display: none;margin-top: 0px">
                                <table style="padding: 10px">
                                    <tr>
                                        <td><p> Enter Start Date/Time:</p></td>
                                        <td><input class="startdatetimepicker" autocomplete="off" type="text" name="sdate" value="<?php echo $admin->getCurrentTimeFormat($userTrackData['trackStart']) ?>"/></td>
                                    </tr>
                                    <tr>
                                        <td><p> Enter End Date/Time:</p></td>
                                        <td><input class="startdatetimepicker" autocomplete="off" type="text" value="<?php echo $admin->getCurrentTimeFormat($userTrackData['trackEnd']) ?>" name="edate"/></td>
                                    </tr>
                                    <tr>
                                        <td><p> Enter Tracking Interval:</p></td>
                                        <td> <input id="interval" type="text" value="<?php echo $userTrackData['trackInterval'] ?>" name="interval"/>
                                            <p style="float: right;padding-left: 15px;color:red;">(value in minutes)</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center">
                                            <input type="button" onclick="submitForm();" value="submit"/>                                                                    
                                        </td>
                                    </tr>
                                     <input type="hidden" name="tracking-status" id="tracking-status" value="1"/>
                                     <input type="hidden" name="save_session" value=""  id="save-user-session"/>
                                      <input type="hidden" value="1" name="user_id"/>
                                </table>
                            </div>
                        </div>

                    </form>
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
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending"><input type="checkbox" id="select_all"/></th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Name</th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-label="Browser: activate to sort column ascending">Email</th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-label="Engine version: activate to sort column ascending">Registration Date</th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Contact Number</th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Company Name</th>
                                                                <?php if ($_SESSION['is_super'] == 0) { ?> <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-label="CSS grade: activate to sort column ascending">Operation</th><?php } ?>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-label="Engine version: activate to sort column ascending">Tracking Status</th>
                                                                <th  tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: auto;" aria-label="Engine version: activate to sort column ascending">App Status</th></tr>
                                                        </thead> 
                                                        <tbody>
                                                            <?php
                                                            $i = 1;
                                                            foreach ($adminUser as $value) {
                                                                ?>
                                                                <tr class="gradeA odd" role="row">
                                                                    <td class="sorting_1">
                                                                        <input type="checkbox" <?php
                                                                        if ($value['is_on_track'] == 1) {
                                                                            echo 'disabled checked=checked';
                                                                        }
                                                                        ?> id="checkbox-<?php echo $i; ?>" name="<?php echo $value['uniqueCode'] ?>" value="<?php echo $value['id'] ?>"/></td>
                                                                    <td class="sorting_1"><?php echo $value['name']; ?></td>
                                                                    <td><?php echo $value['email']; ?></td>
                                                                    <?php $date = date_create($value['created_at']); ?>
                                                                    <td class="center"><?php echo date_format($date, 'jS F Y'); ?></td>
                                                                    <td><?php echo $value['contact']; ?></td>
                                                                    <td><?php echo $value['company']; ?></td>
                                                                    <?php if ($_SESSION['is_super'] == 0) { ?>   <td class="center">
                                                                            <a href="javascript:void(0)" onclick="deleteUser('<?php echo $value['id']; ?>', '<?php echo $value['uniqueCode']; ?>');">Delete</a> | 
                                                                            <a href="userProfile.php?id=<?php echo base64_encode($value['id']); ?>">View</a>
                                                                        </td><?php } ?>
                                                                    <td><?php
                                                                        if ($value['is_on_track'] == 1 && $value['cronTrackStatus'] == 1 ) {
                                                                            echo "<span style='color:green'>On<span>";
                                                                        } else {
                                                                            echo "<span style='color:red'>Off<span>";
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <span class="show-user-status" style="color: red" id="<?php echo $value['uniqueCode'] ?>">Offline</span>
                                                                    </td>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="dialog-confirm" title="Personnel Tracker" style="display: none;z-index: 99999">
            <p>
                <span  style="float:left; margin:0 0px 0px 0;">

                </span><span style="float:left;" id="track-msg-sucss"></span></p>
        </div>
        <div id="dialog-error" title="Personnel Tracker" style="display: none;z-index: 99999">
            <p>
                <span  style="float:left; margin:0 0px 0px 0;">

                </span><span style="float:left;" id="track-msg-err"></span></p>
        </div>
        <div id="dialog-delete" title="Personnel Tracker" style="display: none;z-index: 99999">
            <p>
                <span  style="float:left; margin:0 0px 0px 0;">

                </span><span style="float:left;" id="delete-user-msg"></span></p>
        </div>
        <div id="dialog-deleteconfirm" title="Personnel Tracker" style="display: none;z-index: 99999">
            <p>
                <span  style="float:left; margin:0 0px 0px 0;">

                </span><span style="float:left;" id="delete-cnfrm">Do you want to delete the user?</span></p>
        </div>
        <script type="text/javascript">
            ServerManager.connect('http://54.191.56.95:7070', 'personneltracker', '<?php echo $_SESSION['data']['admindatabase'] . '@personneltracker' ?>', '<?php echo $_SESSION['data']['databasepassword'] ?>', 'roster_entry');
            function userStatus(uid, status) {
                var user = uid.substr(0, 9).toUpperCase();
                $('.show-user-status').each(function () {
                    if ($(this).attr('id') == user) {
                        if (status == 'Online') {
                            $(this).html('Online').css('color', 'green');
                        } else if (status == '') {
                            $(this).html('Offline').css('color', 'red');
                        } else {
                            $(this).html(status).css('color', 'red');
                        }
                    }
                });
            }

            var timezone = jstz.determine();
            $('.def-local').val(timezone.name());

            function showOptions(obj) {
                $(obj).toggleClass('active');
                $('#user-form-values').toggle('show');
            }
            function showForm() {
                $('.tracking-details-form').stop(true).toggle('show');
            }

            var today = new Date().format("%Y-%m-%d");
            $('.startdatetimepicker').datetimepicker({
                format: 'Y-m-d H:i:s',
                startDate: today,
            });

            $(document).ready(function () {
                $('#select_all').change(function () {
                    $('.gradeA').each(function () {
                        if ($('#select_all').is(":checked")) {
                            if (!$(this).find('input[type="checkbox"]').is(':disabled')) {
                                $(this).find('input[type="checkbox"]').prop('checked', true);
                            }
                        } else {
                            if (!$(this).find('input[type="checkbox"]').is(':disabled')) {
                                $(this).find('input[type="checkbox"]').prop('checked', false);
                            }
                        }
                    });
                });
            });

            (function ($) {
                $.fn.serializeFormJSON = function () {

                    var o = {};
                    var a = this.serializeArray();
                    $.each(a, function () {
                        if (o[this.name]) {
                            if (!o[this.name].push) {
                                o[this.name] = [o[this.name]];
                            }
                            o[this.name].push(this.value || '');
                        } else {
                            o[this.name] = this.value || '';
                        }
                    });
                    return o;
                };
            })($);

            function submitForm() {
                var users = [];
                var UsrOpenfire = [];
                var data = $('.configure-input').serializeFormJSON();
                $('.gradeA').each(function () {
                    if ($(this).find('input[type="checkbox"]').is(":checked")) {
                        users.push($(this).find('input[type="checkbox"]').val());
                        if (!$(this).find('input[type="checkbox"]').is(":disabled")) {
                            UsrOpenfire.push($(this).find('input[type="checkbox"]').attr('name'));
                        }
                    }
                });
                $.ajax({
                    url: 'users.php',
                    type: 'post',
                    dataType: 'json',
                    data: {'users': users, 'data': data},
                    success: function (result) {
                        console.log(result.data);
                        if (result.status == true) {
                            $('#track-msg-sucss').html(result.message);
                            $("#dialog-confirm").dialog({
                                resizable: false,
                                height: 180,
                                top: 350,
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
                            for (i = 0; i < UsrOpenfire.length; i++) {
                                ServerManager.sendChatMessage(UsrOpenfire[i] + '@personneltracker', result.data);
                            }

                        } else {
                            $('#track-msg-err').html(result.message);
                            $("#dialog-error").dialog({
                                resizable: false,
                                height: 180,
                                modal: true,
                                dialogClass: "noOverlayDialog",
                                buttons: {
                                    "Ok": function () {
                                        $(this).dialog("close");
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

            function deleteUser(id, code) {

                $("#dialog-deleteconfirm").dialog({
                    resizable: false,
                    height: 180,
                    top: 350,
                    modal: true,
                    dialogClass: "noOverlayDialog",
                    buttons: {
                        "Ok": function () {
                            var op = 'delete';
                            $.ajax({
                                url: 'users.php',
                                type: 'get',
                                dataType: 'json',
                                data: {op: op, id: id},
                                success: function (data) {
                                    if (data.success == true) {
                                        var dataServer = {"default_locale": "Asia\/Kolkata", "tracking-status": "0", "sdate": "2016-04-09 04:00:53", "edate": "2016-04-11 03:13:58", "interval": "3", "user_id": "120", "save_session": "1"};
                                        console.log(code + '@personneltracker');
                                        console.log(dataServer);
                                        ServerManager.sendChatMessage(code + '@personneltracker', dataServer);
                                        $('#delete-user-msg').html(data.msg);
                                    } else {
                                        $('#delete-user-msg').html(data.msg);
                                        
                                    }
                                    window.location.reload();
                                }
                            });
                        }, "No": function () {
                            $(this).dialog("close");
                        }
                    },
                    open: function (event, ui) {
                        $('.noOverlayDialog').next('div').css({'opacity': 0.0});
                    }
                });
            }
        </script>
    </body>
</html>
