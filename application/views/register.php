<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
    <?php $this->load->view('header_section',$this->data); ?>
</section>
<section class="inner-page">
	<div class="container">
    	<div class="fleft content-page register-now">
        <form name="RegisterNowForm" id="RegisterNowForm" method="post" action="<?php echo base_url(); ?>login/signup/" enctype="multipart/form-data" class="UserProfileForm">
        <div class="register-form">
        	<div class="reg-box"><h2>Register Now</h2></div>
            <div class="reg-box">
                <div class="fleft" style="min-width:240px;">
                Business Type <span class="Mandatory">*</span><br />
                <select name="business_type" id="business_type">
                	<option value="">Please Select</option>
                    <?php foreach(get_business_types() as $business_type){ ?>
                    	<option value="<?php echo $business_type; ?>" ><?php echo $business_type; ?></option>
                    <?php } ?>
                </select>
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
              <div class="fleft" style="min-width:240px;">
                I will use Pref for <span class="Mandatory">*</span><br />
                  <label>
                    <input name="ordering_feature" type="radio" required="required" id="ordering_and_feedback" value="1" checked="checked">
                    Ordering and Feedback</label>
                    <br>
                  <label>
                    <input name="ordering_feature" type="radio" required="required" id="feedback_only" value="0">
                    Feedback only</label>
                  
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
            <div class="fleft">
                Business Name <span class="Mandatory">*</span><br />
                <input type="text" name="name" id="name" class="TextField" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
            <div class="fleft">
                Manager's Name <span class="Mandatory">*</span><br />
                <input type="text" name="manager_name" id="manager_name" class="TextField" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Address <span class="Mandatory">*</span><br />
                <input type="text" name="address" id="address" class="TextField" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Phone <span class="Mandatory">*</span><br />
                <input type="text" name="phone" id="phone" class="TextField" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Email <span class="Mandatory">*</span><br />
                <input type="email" name="email" id="RegEmail" class="TextField" required />
                </div>
                <div class="fleft">
                	<span id="EmailMsg"></span>
                </div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Password <span class="Mandatory">*</span><br />
                <input type="password" name="password" id="password" class="TextField" required />
                <div class="clear height10"></div>
                Retype Password <span class="Mandatory">*</span><br />
                <input type="password" name="confirm_password" id="confirm_password" class="TextField" required />
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
                <div class="fleft"><span id="PasswordMsg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box no-bottom">
            	<div class="fleft">
                Set Logo <span class="Mandatory">*</span><br />
                <input type="file" name="logo" id="logo" accept="image/*" required />
                </div>
                <div class="fleft ImageMsg">Only jpg and png images allowed.<br />Max Image size should be 512KB</div>
                <div class="clear"></div>
            </div>
        </div>    
        <div class="reg-bottom">
        	<div class="fleft"><span class="Mandatory">*</span> Fields that must be completed.</div>
            <div class="fright"><input type="submit" name="RegBtn" id="RegBtn" value="Submit" class="Button GreenBtn" /></div>
            <div class="clear"></div>
        </div>
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
        </form>
        </div>
        <div class="clear"></div>
    </div>
</section>
<script>
$(document).on('blur', '#RegEmail', function(){
	var Email = $('#RegEmail').val();
	if(Email!='')
	{
		$('#EmailMsg').html('Validating email address... Please wait...');
		$.post(WEB_URL+"login/is_email_available/", {'email':Email}, function(Data, textStatus){
			if(Data.Error=='0')
			{
				EmailError=0;
				$('#EmailMsg').html('<span class="green">'+Data.Msg+'</span>');
				$('#RegBtn').removeAttr('disabled').removeClass('disabled');
			}
			else
			{
				EmailError=1;
				$('#EmailMsg').html('<span class="red">'+Data.Msg+'</span>');
				$('#RegBtn').attr('disabled', 'disabled').addClass('disabled');
			}
		}, 'json');
	}
});

$(document).on('submit', '#RegisterNowForm', function(){
	var password = $('#password').val();
	var confirm_password = $('#confirm_password').val();
	var BusinessType = $('.sod_label').html();
	
	
	var re1 = /^02[0-9]{7}$/; // Abu Dhabi
	var re2 = /^03[0-9]{7}$/; // Al Ain
	var re3 = /^04[0-9]{7}$/; // Dubai
	var re4 = /^06[0-9]{7}$/; // Sharjah, Ajman, Umm ul-Quwain
	var re5 = /^07[0-9]{7}$/; // Ras Al Khaimah
	var re6 = /^09[0-9]{7}$/; // Fujairah
	var re7 = /^05[0-9]{8}$/; // mobile
	var re8 = /^08[0-9]{8}$/; // mobile
	var number = $('#phone').val();
	
	if(BusinessType=='Please Select')
	{
		$('.notification').html('Please select business type').addClass('show-notification');
		return false;
	}
	if(
	!number.match(re1) && 
	!number.match(re2) && 
	!number.match(re3) && 
	!number.match(re4) && 
	!number.match(re5) && 
	!number.match(re6) &&
	!number.match(re7) &&
	!number.match(re8)
	)
	{
		//$('.notification').html('Please enter correct phone number like "041234567"').addClass('show-notification');
		//$('#Phone').focus();
		//return false;
	}
	
	if(password!=confirm_password)
	{
		$('.notification').html('Passwords does not match').addClass('show-notification');
		$('#confirm_password').focus()
		return false;
	}
	
	var password_validation = validate_password(password);
		
	if(password_validation !== true)
	{
		$('.notification').html(password_validation).addClass('show-notification');
		$('#password').focus()
		return false;
	}
		
	if(EmailError=='0')
		return true;
	else
	{
		$('#RegEmail').focus();
		return false;
	}
});
</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);



