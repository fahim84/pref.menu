<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Log out</title>
</head>

<body>
<script>
		// remove remember me
		localStorage.removeItem("id");
		
		window.location.href='<?php echo base_url(); ?>login';
</script>
</body>
</html>