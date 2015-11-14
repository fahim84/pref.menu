<!doctype html>
<!--[if lt IE 7]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class='no-js' lang='en' ng-app="MyAngularApp">
<!--<![endif]--><head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no">
<title>Pref.menu</title>
<!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/r29/html5.min.js"></script>
<![endif]-->

<!-- jQuery UI -->
<link href="<?php echo base_url(); ?>css/jquery-ui.css" rel="stylesheet" media="screen">


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/animate.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/fontface.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/jquery.pwdmeter.css" />
<?php if(isset($LoadCheckbox) and $LoadCheckbox==1) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/checkbox.css" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/selectordie.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/raty.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/lightbox.css" >
<link rel="icon" type="image/png" href="<?php echo base_url(); ?>images/favico.png">

<script>
var WEB_URL = '<?php echo base_url(); ?>';
</script>
<script src="<?php echo base_url(); ?>js/jquery-2.1.4.js"></script>

<script src="<?php echo base_url(); ?>js/angular.js"></script>
<script src="<?php echo base_url(); ?>js/MyAngularApp.js"></script>

<script src="<?php echo base_url(); ?>js/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.migrate.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.easing.js"></script>
<script src="<?php echo base_url(); ?>js/modernizer.js"></script>
<script src="<?php echo base_url(); ?>js/viewportchecker.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.carouFredSel.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.backstretch.min.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.pwdmeter.js"></script>
<script src="<?php echo base_url(); ?>js/selectordie.min.js"></script>
<script src="<?php echo base_url(); ?>js/raty.js"></script>
<!--<script src="<?php echo base_url(); ?>js/client.js"></script>-->
<script src="<?php echo base_url(); ?>js/common_shared.js"></script>
</head>
<body>