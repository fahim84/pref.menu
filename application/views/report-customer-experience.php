<!DOCTYPE HTML>
<html>
<title>Pref</title>
<head>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
</head>
<body>
<?php
	$Error=1;
	if($start_date and $end_date)
	{
		$Error=0;	
	}
	
	if($Error==0)
	{
		//my_var_dump($graph_data);
?>
	<script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart', 'line']});
		google.setOnLoadCallback(drawBasic);
		
		function drawBasic() {

      var data = google.visualization.arrayToDataTable(
	  <?php echo json_encode($graph_data,JSON_NUMERIC_CHECK); ?>
	  );

      var options = {
        title: '<?php echo $heading; ?>',
        vAxis: {
          title: 'Rating (scale of 1-5)'
        },
		legend: { position: 'bottom' }
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('chart_div'));

      chart.draw(data, options);
    }
    </script>
<div id="chart_div" style="width: 100%; height: 300px;"></div>

	
<?php
	}
	else
	{
		echo 'Wrong request';
	}
?>
</body>
</html>
