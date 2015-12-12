<?php
include_once 'admin-class.php';
$admin = new itg_admin();
if ($_POST) {
    $admin->addUserTrackingDetails($_POST);
}
if (base64_decode($_GET['id']) == '') {
    header('Location: dashboard.php');
}
$userTrackData = $admin->getUserTrackingDetalis(base64_decode($_GET['id']));
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
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="http://maps.google.com/maps/api/js?sensor=false&libraries=drawing&dummy=.js"></script>
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
                            <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                            </li>
                            <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
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
                            <h1 class="page-header">Track Your Users</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="dataTable_wrapper">
                                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                            <div class="row">
                                                <h3 style="padding-left: 20px">Users details</h3>
                                                <div style="padding-left: 30px">
                                                    <div style="float: left">
                                                        <table>
                                                            <tr><td>Name :</td><td><?php echo $userTrackData['name']; ?></td></tr> 
                                                            <tr><td>Email :</td><td><?php echo $userTrackData['email']; ?></td></tr> 
                                                            <tr><td>Tracking :</td><td><?php
                                                                    if ($userTrackData['is_on_track'] == 1) {
                                                                        echo 'Enabled';
                                                                    } else {
                                                                        echo 'Disabled';
                                                                    }
                                                                    ?></td></tr> 
                                                        </table>
                                                    </div>
                                                    <div style="float: right;padding-right: 50px">
                                                        <h4><a href="javascript:void(0)" onclick="showOptions()">Click here ho change the options</a></h4>

                                                    </div>
                                                </div>
                                            </div><div class="row">
                                                <?php if ($userTrackData['is_on_track'] == 1) { ?>
                                                    <div class="panel-body" style="padding: 0">
                                                        <h3>Location on Map</h3>
                                                        <div id="dvMap" style="width: auto; height: 400px"></div>
                                                        <script type="text/javascript">
                                                            var markers = [{
                                                                    "title": '1',
                                                                    "lat": '30.705911',
                                                                    "lng": '76.679656',
                                                                    "description": '1'
                                                                }, {
                                                                    "title": '2',
                                                                    "lat": '30.701713',
                                                                    "lng": '76.684097',
                                                                    "description": '2'
                                                                }, {
                                                                    "title": '2',
                                                                    "lat": '30.703291',
                                                                    "lng": ' 76.701022',
                                                                    "description": '2'
                                                                }, {
                                                                    "title": '2',
                                                                    "lat": '30.691888',
                                                                    "lng": ' 76.710721',
                                                                    "description": '2'
                                                                }

                                                            ];
                                                            window.onload = function () {
                                                                var mapOptions = {
                                                                    center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
                                                                    zoom: 10,
                                                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                                                };
                                                                var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
                                                                var infoWindow = new google.maps.InfoWindow();
                                                                var lat_lng = new Array();
                                                                var latlngbounds = new google.maps.LatLngBounds();
                                                                for (i = 0; i < markers.length; i++) {
                                                                    var data = markers[i]
                                                                    var myLatlng = new google.maps.LatLng(data.lat, data.lng);
                                                                    lat_lng.push(myLatlng);
                                                                    var marker = new google.maps.Marker({
                                                                        position: myLatlng,
                                                                        map: map,
                                                                        title: data.title
                                                                    });
                                                                    latlngbounds.extend(marker.position);
                                                                    (function (marker, data) {
                                                                        google.maps.event.addListener(marker, "click", function (e) {
                                                                            infoWindow.setContent(data.description);
                                                                            infoWindow.open(map, marker);
                                                                        });
                                                                    })(marker, data);
                                                                }
                                                                map.setCenter(latlngbounds.getCenter());
                                                                map.fitBounds(latlngbounds);

                                                                //***********ROUTING****************//

                                                                //Intialize the Path Array
                                                                var path = new google.maps.MVCArray();

                                                                //Intialize the Direction Service
                                                                var service = new google.maps.DirectionsService();

                                                                //Set the Path Stroke Color
                                                                var poly = new google.maps.Polyline({
                                                                    map: map,
                                                                    strokeColor: '#4986E7'
                                                                });

                                                                //Loop and Draw Path Route between the Points on MAP
                                                                for (var i = 0; i < lat_lng.length; i++) {
                                                                    if ((i + 1) < lat_lng.length) {
                                                                        var src = lat_lng[i];
                                                                        var des = lat_lng[i + 1];
                                                                        // path.push(src);
                                                                        poly.setPath(path);
                                                                        service.route({
                                                                            origin: src,
                                                                            destination: des,
                                                                            travelMode: google.maps.DirectionsTravelMode.DRIVING
                                                                        }, function (result, status) {
                                                                            if (status == google.maps.DirectionsStatus.OK) {
                                                                                for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                                                                                    path.push(result.routes[0].overview_path[i]);
                                                                                }
                                                                            }
                                                                        });
                                                                    }
                                                                }
                                                            }</script>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div id="user-form-values" class="row" style="display: none;margin-top: 20px">
                                                <form class="configure-input" method="post" action="userProfile.php">
                                                    <h3 style="padding-left: 20px">Configure the tracking details</h3>
                                                    <div class="tracking-status">
                                                        <p>Enable Tracking to fill the below deails : </p>
                                                        <select id="tracking-status" name="tracking-status">
                                                            <option value="1" <?php if ($userTrackData['is_on_track'] == 1) echo 'selected'; ?>>Yes</option>
                                                            <option value="0" <?php if ($userTrackData['is_on_track'] == 0) echo 'selected'; ?>>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="confg-panel">
                                                        <table style="padding: 10px">
                                                            <tr>
                                                                <td><p> Enter Start Date/Time:</p></td>
                                                                <td><input class="startdatetimepicker" type="text" name="sdate" value="<?php echo $userTrackData['trackStart'] ?>"/></td>
                                                            </tr>
                                                            <tr>
                                                                <td><p> Enter End Date/Time:</p></td>
                                                                <td><input class="startdatetimepicker" type="text" value="<?php echo $userTrackData['trackEnd'] ?>" name="edate"/></td>
                                                            </tr>
                                                            <tr>
                                                                <td><p> Enter Tracking Interval:</p></td>
                                                                <td> <input id="interval" type="text" value="<?php echo $userTrackData['trackInterval'] ?>" name="interval"/>
                                                                    <p style="float: right;padding-left: 15px;color:red;font-family: fantasy">(value in minutes)</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="text-align: center">
                                                                    <input type="hidden" value="<?php echo base64_decode($_GET['id']) ?>" name="user_id"/>
                                                                    <input type="button" onclick="submitForm();" value="submit"/>                                                                    
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
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
            <div id="dialog-confirm" title="Action" style="display: none">
                <p>
                    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">

                    </span><span id="track-msg-sucss"></span></p>
            </div>
            <div id="dialog-error" title="Action" style="display: none">
                <p>
                    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">

                    </span><span id="track-msg-err"></span></p>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                var today = new Date().format("%Y-%m-%d");
                $('.startdatetimepicker').datetimepicker({
                    format: 'Y-m-d H:i:s',
                    dayOfWeekStart: 1,
                    lang: 'en',
                    step: 1,
                    startDate: today,
                    minDate: today
                });
                ServerManager.connect('http://52.24.255.248:7070', 'personneltracker', '<?php echo $_SESSION['data']['admindatabase'] . '@personneltracker' ?>', '<?php echo $_SESSION['data']['databasepassword'] ?>', 'roster_entry');
            });

            function submitForm() {
                var data = $('.configure-input').serialize();
                $.ajax({
                    url: 'userProfile.php',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.status == true) {
//                            ServerManager.sendChatMessage('<?php echo $userTrackData['uniqueCode'] . '@personneltracker' ?>', JSON.stringify(data));
                            ServerManager.sendChatMessage('KMNE7_288@personneltracker', JSON.stringify(data));
                            $('#track-msg-sucss').html(result.message);
                            $("#dialog-confirm").dialog({
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
                })
            }

            function showOptions() {
                $('#user-form-values').show();
                $(window).scrollTop($('#user-form-values').position().top);
            }

        </script>
    </body>
</html>