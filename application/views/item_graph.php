<?php
	$Field = 'Item';
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class='no-js' lang='en'>
<!--<![endif]-->
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no">
<title>Pref.menu</title>
<!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/r29/html5.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/pie.chart.css" />
<script src="<?php echo base_url(); ?>js/jquery.js"></script>
<script src="<?php echo base_url(); ?>js/piechart.js"></script>
</head>
<body>

<div class="pieID pie" id="pie<?php echo $Field; ?>"></div>
<ul class="pieID legend" id="data<?php echo $Field; ?>">
    <li>
        <em><img src="<?php echo base_url(); ?>images/1star.png" alt="" /></em>
        <span><?php echo $graph_data[1]; ?></span>
    </li>
    <li>
        <em><img src="<?php echo base_url(); ?>images/2star.png" alt="" /></em>
        <span><?php echo $graph_data[2]; ?></span>
    </li>
    <li>
        <em><img src="<?php echo base_url(); ?>images/3star.png" alt="" /></em>
        <span><?php echo $graph_data[3]; ?></span>
    </li>
    <li>
        <em><img src="<?php echo base_url(); ?>images/4star.png" alt="" /></em>
        <span><?php echo $graph_data[4]; ?></span>
    </li>
    <li>
        <em><img src="<?php echo base_url(); ?>images/5star.png" alt="" /></em>
        <span><?php echo $graph_data[5]; ?></span>
    </li>
</ul>
<script>
createPie("#data<?php echo $Field; ?>", "#pie<?php echo $Field; ?>",'%');
</script>

</body>
</html>