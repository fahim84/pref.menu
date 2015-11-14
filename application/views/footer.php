
<?php
	if(isset($_SESSION[USER_RETURN_MSG])) {
		$msg = $_SESSION[USER_RETURN_MSG]['Msg'];
	}
?>
<div class="notification"><?php echo @$msg; ?></div>
<?php
if(isset($_SESSION['ModalBox']))
{
?>
<div class="modal-box">
	<a class="close-modal-box"></a>
	<?php echo $_SESSION['ModalBox']; ?>
</div>
<?php
}
?>

<script>
<?php 
if(isset($_SESSION[USER_RETURN_MSG])) 
{ ?>
ShowNotification();
<?php unset($_SESSION[USER_RETURN_MSG]);
} ?>

<?php
if(isset($_SESSION['ModalBox']))
{
?>
ShowModalBox();
<?php unset($_SESSION['ModalBox']);
}?>

</script>
<script src="<?php echo base_url(); ?>js/lightbox.js"></script>

<?php if( isset($_SESSION['remember_me']) ){ ?>
<script>
	<?php if( $_SESSION['remember_me'] == 1 ){ ?>
		// Store remember me
		localStorage.setItem("id", <?php echo $_SESSION[USER_LOGIN]['id']; ?>);
	<?php }else{ ?>
		// remove remember me
		localStorage.removeItem("id");
	<?php } ?>
</script>
<?php unset($_SESSION['remember_me']); } ?>
</body>
</html>