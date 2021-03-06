<?php
	require_once(__DIR__ . '/api/v2/model/DBConnection.class.php');

	if(false)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	}

	$dev = isset($_GET['dev']);
	$conn = YTT\DBConnection::getConnection();
	$customPeriodDisplayed = isset($_GET['startPeriod']) && isset($_GET['endPeriod']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- JQuery -->
    <script src="//code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="css/libs/fontawesome-free-5.11.1-web/css/fontawesome.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="//cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- Datatables -->
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/v/bs4/dt-1.10.22/datatables.min.css"/>
    <script type="text/javascript" src="//cdn.datatables.net/v/bs4/dt-1.10.22/datatables.min.js"></script>

    <!-- AmCharts -->
    <script src="//www.amcharts.com/lib/4/core.js"></script>
    <script src="//www.amcharts.com/lib/4/charts.js"></script>
    <script src="//www.amcharts.com/lib/4/themes/animated.js"></script>
    <script src="//www.amcharts.com/lib/4/themes/dark.js"></script>
    <script src="//www.amcharts.com/lib/4/themes/material.js"></script>

    <!-- Custom -->
    <link rel="stylesheet" href="css/main.css"/>
    <!--    <link rel="stylesheet" href="css/bootstrap.min.css">-->
    <script type="text/javascript" src="js/script.js"></script>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
    <meta name="viewport" content="width = device-width, initial-scale = 1">
    <title>YTTracker stats</title>
</head>
<body>
<?php
	include __DIR__ . "/header.php";
?>

<div class="container-fluid" style="margin-top:40px">
    <!-- Nav tabs -->
    <ul class="nav nav-pills nav-fill nav-justified" role="tablist">
        <li class="nav-item">
            <a class="nav-link" id="graph-tab" data-toggle="pill" href="#nav-graph" role="tab" aria-controls="home" aria-selected="true">Graphs (last month)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" id="table-tab" data-toggle="pill" href="#nav-table" role="tab" aria-controls="profile" aria-selected="false">Table</a>
        </li>
    </ul>

    <hr/>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane fade" id="nav-graph" role="tabpanel" aria-labelledby="graph-tab">
			<?php
				include __DIR__ . "/graphs.php"
			?>
        </div>
        <div class="tab-pane fade show active" id="nav-table" role="tabpanel" aria-labelledby="table-tab">
			<?php
				include __DIR__ . "/table.php"
			?>
        </div>
    </div>
</body>
</html>