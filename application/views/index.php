<?php $this->load->view('header',$this->data); ?>
<section class="header">
	<?php $this->load->view('header_section',$this->data); ?>
    <?php
		if(isset($_SESSION[USER_LOGIN]))
			include_once("user-home.php");
		else
			include_once("login.php");
	?>
</section>
<?php
$this->load->view('clients',$this->data);
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);



