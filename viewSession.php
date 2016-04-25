<?php
include_once 'admin-class.php';
$admin = new itg_admin();
if (!$_SESSION['admin_login']) {
    header('Location: index.php');
}
if (base64_decode($_GET['id']) == '') {
    header('Location: dashboard.php');
}

$userSessionData = $admin->getUserSessionData(base64_decode($_GET['id']));
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
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="bower_components/raphael/raphael-min.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="http://jawj.github.io/OverlappingMarkerSpiderfier/bin/oms.min.js"></script>
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
            <div id="page-wrapper" style="min-height: 0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header"> Saved Sessions</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="dataTable_wrapper">
                                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                            <?php if (empty($userSessionData)) { ?>
                                                <div class="row">
                                                    <h3 style="padding-left: 20px">No session recorded for the user.</h3>
                                                </div>
                                            <?php } else { ?>
                                                <div class="row">
                                                    <div class="parent" style="padding-left: 20px;">
                                                        <div class="show-session" style="width: 50%;float: left">
                                                            <label>Select a session to show:</label>
                                                            <select style="margin-left: 20px" id="session_record" name="session_name" id="session_name">
                                                                <option value="">Select Session</option>
                                                                <?php
                                                                $i = 1;
                                                                foreach ($userSessionData as $value) {
                                                                    $dates = (json_decode($value['data'], TRUE));
                                                                    $firstDate = date('dS M Y g:i A', strtotime(key($dates[0])));
                                                                    $lastDate = date('dS M Y g:i A', strtotime(key(end($dates))));
                                                                    ?>
                                                                    <option value="<?php echo $value['id'] ?>"><?php echo $firstDate . ' - ' . $lastDate; ?></option>>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="opereations" style="padding: 26px 0 0 0">
                                                            <button class="startbtn" onclick="getRecordedSession();" type="button">Submit</button>
                                                            <button class="stopbtn" onclick="deleteRecordedSession();" type="button">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body" style="padding: 0">

            <div id="dvMap" style="width: 100%; height: 600px"></div>

        </div>
        <div id="dialog-deletesession" title="Personnel Tracker" style="display: none">
            <p>
                <span  style="float:left; margin:0 0px 0px 0;">

                </span><span style="float:left;" id="track-msg-sessn">Session has been deleted.</span></p>
        </div>
    </body>
    <script>


        function getRecordedSession() {
            var session_id = ($('#session_record :selected').val());
            if (session_id == '') {
                alert('select a session');
                return false;
            }
            $.ajax({
                url: 'getSavedSession.php',
                data: {'sessionId': session_id},
                type: 'post',
                success: function (result) {
                    var markers = JSON.parse(result);
                    var map;
                    var center;
                    for (i = 0; i < markers.length; i++) {
                        center = new google.maps.LatLng(markers[i][1], markers[i][2]);
                    }
                    var mapOptions = {center: center, zoom: 3,
                        mapTypeId: google.maps.MapTypeId.ROADMAP};

                    function initialize() {
                        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
                        var oms = new OverlappingMarkerSpiderfier(map);
                        userCoor = markers;
                        var userCoorPath = [];
                        for (i = 0; i < userCoor.length; i++) {
                            console.log(userCoor[i][1], userCoor[i][2]);
                            userCoorPath.push(new google.maps.LatLng(userCoor[i][1], userCoor[i][2]));
                        }
                        // Create a new LatLngBounds object
                        var markerBounds = new google.maps.LatLngBounds();
                        // Add your points to the LatLngBounds object.
                        for (i = 0; i < markers.length; i++) {
                            var point = new google.maps.LatLng(markers[i][1], markers[i][2]);
                            markerBounds.extend(point);
                        }

                        // Then you just call the fitBounds method and the Maps widget does all rest.
                        map.fitBounds(markerBounds);
                        var userCoordinate = new google.maps.Polyline({
                            path: userCoorPath,
                            strokeColor: "#FF0000",
                            strokeOpacity: 1,
                            strokeWeight: 2
                        });
                        userCoordinate.setMap(map);

                        var infowindow = new google.maps.InfoWindow();

                        var marker, i;

                        var length = userCoor.length;
                        var markerData = [];
                        for (i = 0; i < userCoor.length; i++) {
                            var index = i;
                            if (i == (length - 1)) {
                                marker = new google.maps.Marker({
                                    position: new google.maps.LatLng(userCoor[i][1], userCoor[i][2]),
                                    icon: new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/icons/blue.png'),
                                    map: map
                                });
                            }else if(i == 0){
                                 marker = new google.maps.Marker({
                                    position: new google.maps.LatLng(userCoor[i][1], userCoor[i][2]),
                                    icon: new google.maps.MarkerImage('http://54.191.56.95/images/firstMarker.png'),
                                    map: map
                            });
                        }
                            else {
                                marker = new google.maps.Marker({
                                    position: new google.maps.LatLng(userCoor[i][1], userCoor[i][2]),
                                    map: map,
                                });
                            }
                            google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
                                return function () {
                                    infowindow.setContent('Pin ' + userCoor[i][3]+' ,LocationTime '+userCoor[i][0]);
                                    infowindow.open(map, marker);
                                }
                            })(marker, i));
                            google.maps.event.addListener(marker, 'mouseout', (function (marker, i) {
                                return function () {
                                    infowindow.setContent('');
                                    infowindow.close();
                                }
                            })(marker, i));
                            oms.addMarker(marker);
                        }
                    }
                    initialize();
                    google.maps.event.addDomListener(window, 'load', initialize);

                }
            });
        }

        function deleteRecordedSession() {
            var session_id = ($('#session_record :selected').val());
            if (session_id == '') {
                alert('select a session');
                return false;
            }
            $.ajax({
                url: 'getSavedSession.php',
                data: {'sessionId': session_id, 'delete': 1},
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        $("#dialog-deletesession").dialog({
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
