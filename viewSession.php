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
        <script src="bower_components/datePicker/jquery.datetimepicker.full.js"></script>
        <link rel="stylesheet" href="bower_components/datePicker/jquery.datetimepicker.css">
        <script src="bower_components/datePicker/formatDate.js"></script>
        <script src="js/strophe.js" type="text/javascript"></script>
        <script src="js/strophe-openfire.js" type="text/javascript"></script>
        <script src="js/ServerManager.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
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
            <div id="page-wrapper" style="min-height: 0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Session List</h1>
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
                                                    <div class="parent" style="padding-left: 20px ">
                                                        <h4>Available saved session</h4>
                                                        <div class="show-session">
                                                            <label>Select a session to show:</label>
                                                            <select id="session_record" name="session_name" id="session_name">
                                                                <option value="">Select Session</option>
                                                                <?php
                                                                $i = 1;
                                                                foreach ($userSessionData as $value) {
                                                                    ?>
                                                                    <option value="<?php echo $value['id'] ?>"><?php echo 'Session' . $i; ?></option>>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                            </select>
                                                            <button onclick="getRecordedSession();" type="button">Submit</button>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                   
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
                                                        <h3 style="padding-left: 20px ">Location on Map</h3>
                                                        <div id="dvMap" style="width: 100%; height: 600px"></div>

                                                    </div>
    </body>
    <script>
        function getRecordedSession() {
            var session_id = ($('#session_record :selected').val());
            $.ajax({
                url: 'getSavedSession.php',
                data: {'sessionId': session_id},
                type: 'post',
                success: function (result) {
                    var markers = JSON.parse(result);
                    var map;var center;
                     for (i = 0; i < markers.length; i++) {
                        center =  new google.maps.LatLng(markers[i][1], markers[i][2]);
                        }
                    var mapOptions = {center: center, zoom: 3,
                        mapTypeId: google.maps.MapTypeId.ROADMAP};
                    function initialize() {
                        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);

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

                        for (i = 0; i < userCoor.length; i++) {
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(userCoor[i][1], userCoor[i][2]),
                                map: map
                            });


                            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                                return function () {
                                    infowindow.setContent(userCoor[i][0]);
                                    infowindow.open(map, marker);
                                }
                            })(marker, i));



                        }

                    }
                    initialize();
                    google.maps.event.addDomListener(window, 'load', initialize);

                }
            });
        }
    </script>
</html>
