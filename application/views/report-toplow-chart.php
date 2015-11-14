<!DOCTYPE HTML>
<html>
<title>Pref</title>
<head>
<script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>
</head>
<body>
<?php

	$Error=1;
	if($restaurant_id and $start_date and $end_date and $graph_interval and $top_item and $low_item )
	{
		$Error=0;
	}
	if($Error==0)
	{
		//my_var_dump($graph_data);
?>
	<script type="text/javascript">
      google.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable(
          <?php echo json_encode($graph_data,JSON_NUMERIC_CHECK); ?>
        );

        var options = {
          title: '<?php echo $heading; ?>',
          //curveType: 'function',
		  vAxis: { title: 'Rating (scale of 1-5)', gridlines: { count: 6 } },
          legend: { position: 'bottom' }
		  
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
<div id="curve_chart" style="width: 100%; height: 300px"></div>
<?php
	}
	else
	{
		echo 'Wrong request';
	}
?>
</body>
</html>
