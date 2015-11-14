<?php $this->load->view('header',$this->data); ?>
<section class="header header-report" id="PageMainHeader">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page reports-page">
	<div class="container">
    	<div class="content-page register-now menu-page">
        	<div class="fleft"><h2>Change Password</h2></div>
        	<div class="fright"><?php client_logo(); ?></div>
        	<div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $this->load->view('report-tabs',$this->data); ?>
    <div class="charts">
    	<div class="container">
        	<div class="review-box">
            	<h2>Change Account Password</h2>
                <form name="AccountPasswordForm" id="AccountPasswordForm" method="post" action="">
                	<input name="AccountPassword" type="password" required="required" class="TextField" id="AccountPassword" placeholder="Enter new account password" />
                    <input type="hidden" name="change_password" id="change_password" value="account" >
                    <input name="ConfAccountPassword" type="password" required="required" class="TextField" id="ConfAccountPassword" placeholder="Re-enter account password" />
                    <div class="clear height10"></div>
                    <input type="submit" name="AccountBtn" id="AccountBtn" value="Change Password" class="Button" />&nbsp;<span id="AcPwdMsg">&nbsp;</span>
                </form>
            </div>
            <div class="review-box">
            	<h2>Change Report Password</h2>
                <form name="ReportPasswordForm" id="ReportPasswordForm" method="post" action="">
                	<input name="ReportPassword" type="password" required="required" class="TextField" id="ReportPassword" placeholder="Enter new report password" />
                    <input type="hidden" name="change_password" id="change_password" value="report" >
                    <input name="ConfReportPassword" type="password" required="required" class="TextField" id="ConfReportPassword" placeholder="Re-enter report password" />
                    <div class="clear height10"></div>
                    <input type="submit" name="ReportBtn" id="ReportBtn" value="Change Password" class="Button" />&nbsp;<span id="RptPwdMsg">&nbsp;</span>
                </form>
            </div>
            <div class="clear"></div>
            <div id="pswd_info">
                    <h4>Password must meet the following requirements:</h4>
                    <ul>
                        <li id="letter" class="invalid">At least <strong>one letter</strong></li>
                        <li id="capital" class="invalid">At least <strong>one capital letter</strong></li>
                        <li id="number" class="invalid">At least <strong>one number</strong></li>
                        <li id="length" class="invalid">Be at least <strong>8 characters</strong></li>
                    </ul>
                </div>
        </div>
    </div>
</section>
<script>
$(document).on('focus', '#AccountPasswordForm input[type="password"]', function(){
	$('#pswd_info').css({'top':'180px', 'margin-left':'300px'});
});

$(document).on('submit', '#AccountPasswordForm', function(){
	var Password = $('#AccountPassword').val();
	var ConfPwd = $('#ConfAccountPassword').val();
	if(Password == '')
	{
		$('#AcPwdMsg').html('<div class="alert alert-error">Please type your exising password.</div>');
		$('#AccountPassword').focus();
	}
	else if(Password!=ConfPwd)
	{
		$('#AcPwdMsg').html('<div class="alert alert-error">Passwords does not match. Please try again.</div>');
		$('#ConfAccountPassword').focus();
	}
	else
	{
		var password_validation = validate_password(Password);
		if(password_validation === true)
		{
			var Form = $('#AccountPasswordForm').serialize();
			$('#AccountPasswordForm input').attr('disabled', 'disabled');
			$('#AcPwdMsg').html('Please wait...');
			$.post(WEB_URL+"report/change_password", Form, function(Data, textStatus){
				$('#AcPwdMsg').html(Data.Msg);
			}, 'json');
		}
		else
		{
			$('#AcPwdMsg').html('<div class="alert alert-error">'+password_validation+'</div>');
		}
	}
	return false;
});

$(document).on('focus', '#ReportPasswordForm input[type="password"]', function(){
	$('#pswd_info').css({'top':'370px', 'margin-left':'300px'});
});

$(document).on('submit', '#ReportPasswordForm', function(){
	var Password = $('#ReportPassword').val();
	var ConfPwd = $('#ConfReportPassword').val();
	
	if(Password == '')
	{
		$('#RptPwdMsg').html('<div class="alert alert-error">Please type your exising password.</div>');
		$('#ReportPassword').focus();
	}
	else if(Password!=ConfPwd)
	{
		$('#RptPwdMsg').html('<div class="alert alert-error">Passwords does not match. Please try again.</div>');
		$('#ConfReportPassword').focus();
	}
	else
	{
		var password_validation = validate_password(Password);
		if(password_validation === true)
		{
			var Form = $('#ReportPasswordForm').serialize();
			$('#ReportPasswordForm input').attr('disabled', 'disabled');
			$('#RptPwdMsg').html('Please wait...');
			$.post(WEB_URL+"report/change_password", Form, function(Data, textStatus){
				$('#RptPwdMsg').html(Data.Msg);
			}, 'json');
		}
		else
		{
			$('#RptPwdMsg').html('<div class="alert alert-error">'+password_validation+'</div>');
		}
	}
	return false;
});

</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




