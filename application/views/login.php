
<div class="container login-box" id="loginBox" align="center">
    <h2>Login</h2>
    <form name="login" id="login" method="post" action="<?php echo base_url(); ?>login">
    <?php if (validation_errors()): ?>
    <div class="alert alert-danger">
    <?php echo validation_errors();?>
    </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['msg_error'])){ ?>
    <div class="alert alert-danger">
        <?php echo display_error(); ?>
    </div>
    <?php } ?>
    
    <?php if(isset($_SESSION['msg_success'])){ ?>
    <div class="alert alert-success">
        <?php echo display_success_message(); ?>
    </div>
    <?php } ?>
        
        <div>Email:</div>
        <div class="login-field"><input type="email" name="email" id="email" class="TextField" required="required" placeholder="Email" /></div>
        <div class="clear"></div>
        <div>Password:</div>
        <div class=" login-field"><input type="password" name="password" id="password" class="TextField" required="required" placeholder="Password" /></div>
        <div class="clear"></div>
        <div class=" login-text"></div>
        <div class=" remember-me"><label><input name="remember_me" type="checkbox" id="remember_me" value="1" />Remember Me</label></div>
        
        <div class="clear"></div>
        <div class=" login-text"></div>
        <div>
            <input type="submit" name="LoginBtn" id="LoginBtn" value="Login" class="Button" />
        </div>
        <div class="clear"></div>
        <div ><a onclick="loginShow(2);">Forgot Password?</a></div>
        <div class=" login-text"></div>
        <div id="LoginMsg">&nbsp;</div>
        <div class="clear"></div>
    </form>

</div>
<div class="container login-box" id="forgetBox" style="display:none;">
    <h2>Forgot Password?</h2>
    <form name="ForgetPasswordForm" id="ForgetPasswordForm" method="post" action="<?php echo base_url(); ?>login/forgot_password">
        <div class="fleft login-text">Email:</div><div class="fleft login-field"><input type="email" name="fEmail" id="fEmail" class="TextField" required="required" /></div>
        <div class="fleft">
            <input type="submit" name="fBtn" id="fBtn" value="Submit" class="Button" />&nbsp;<span class="forgot-password"><a  onclick="loginShow(1);">Login</a></span>
        </div>
        <div class="clear"></div>
        <div class="fleft login-text"></div>
        <div class="fleft" id="forgetMsg">&nbsp;</div>
        <div class="clear"></div>
    </form>
</div>

<script>
$(document).on('submit', '#ForgetPasswordForm', function(){
	var Form = $('#ForgetPasswordForm').serialize();
	$('#forgetMsg').html('Please wait...');
	$('#fBtn').attr('disabled', 'disabled').addClass('disabled');
	$.post(WEB_URL+"login/forgot_password/", Form, function(Data, textStatus){
		$('#forgetMsg').html(Data.Msg);
		$('#fBtn').removeAttr('disabled').removeClass('disabled');
	}, 'json');
	return false;
});


if(typeof(Storage) !== "undefined") 
{
    // Code for localStorage/sessionStorage.
	console.log(localStorage.getItem("id"));
	if (localStorage.getItem("id") > 0) // if user selected remember me option
	{
		//console.log('Log user in, because user set option to remember him/her');
		window.location.href='<?php base_url(); ?>login/remember_me/'+localStorage.getItem("id");
	}
} 
else 
{
    // Sorry! No Web Storage support..
	//console.log('Sorry! No HTML5 Local Storage support in your browser..');
}
</script>

<style>
    footer{
        bottom: 0px;
        position: absolute;
    }
</style>