<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
    <?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page">
	<div class="container">
    	<div class="fleft content-page register-now">
        <form name="ProfileForm" id="ProfileForm" method="post" action="<?php echo base_url(); ?>restaurant/profile/" enctype="multipart/form-data" class="UserProfileForm">
        <div class="register-form">
        	<div class="reg-box"><h2>My Profile</h2></div>
            <div class="reg-box">
                <div class="fleft" style="min-width:240px;">
                Business Type <span class="Mandatory">*</span><br />
                <select name="business_type" id="business_type">
                	<option value="">Please Select</option>
                    <?php foreach(get_business_types() as $business_type){ ?>
                    	<option value="<?php echo $business_type; ?>" <?php echo $profile['business_type'] == $business_type ? 'selected="selected"' : ''; ?> ><?php echo $business_type; ?></option>
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
                    <input name="ordering_feature" type="radio" required="required" id="ordering_and_feedback" value="1" <?php echo $profile['ordering_feature'] ? 'checked="checked"' : '' ; ?> >
                    Ordering and Feedback</label>
                    <br>
                  <label>
                    <input name="ordering_feature" type="radio" required="required" id="feedback_only" value="0" <?php echo $profile['ordering_feature']==0 ? 'checked="checked"' : '' ; ?> >
                    Feedback only</label>
                  
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
            	<div class="fleft">
                Business Name <span class="Mandatory">*</span><br />
                <input type="text" name="name" id="name" class="TextField" value="<?php echo $profile['name']; ?>" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
            <div class="fleft">
                Manager's Name <span class="Mandatory">*</span><br />
                <input type="text" name="manager_name" id="manager_name" class="TextField" value="<?php echo $profile['manager_name']; ?>" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Address <span class="Mandatory">*</span><br />
                <input type="text" name="address" id="address" class="TextField" value="<?php echo $profile['address']; ?>" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Phone <span class="Mandatory">*</span><br />
                <input type="text" name="phone" id="phone" class="TextField" value="<?php echo $profile['phone']; ?>" required />
                </div>
                <div class="fleft"></div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Email <span class="Mandatory">*</span><br />
                <input type="email" name="email" id="email" class="TextField" value="<?php echo $profile['email']; ?>" readonly />
                </div>
                <div class="fleft">
                	<span id="EmailMsg"></span>
                </div>
                <div class="clear"></div>
            </div>
            <div class="reg-box">
                <div class="fleft">
                Password <span class="Mandatory">*</span><br />
                <input type="password" name="password" id="password" class="TextField" />
                <div class="clear height10"></div>
                Retype Password <span class="Mandatory">*</span><br />
                <input type="password" name="confirm_password" id="confirm_password" class="TextField" />
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
                <input type="file" name="logo" id="logo" accept="image/*" />
                <?php
				if($profile['logo'] != '')
				{
					$Image = base_url().UPLOADS.'/'.$profile['logo'];
					$Image = base_url()."thumb.php?src=".$Image."&w=200&h=200";
					echo '<div><img src="'.$Image.'" class="menu-dish" /></div><br />';
					echo "<label><input name=\"Delete1\" type=\"checkbox\" id=\"Delete1\" value=\"1\" />Delete<br>$profile[logo]</label><input name=\"oldfile\" type=\"hidden\" value=\"{$profile['logo']}\" /><br>";
				}
				?>
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
$(document).on('submit', '#ProfileForm', function(){
	var password = $('#password').val();
	var confirm_password = $('#confirm_password').val();
	var business_type = $('.sod_label').html();
	
	if(business_type=='Please Select')
	{
		$('.notification').html('Please select business type').addClass('show-notification');
		return false;
	}
	
	if(password != '' && password != confirm_password)
	{
		$('.notification').html('Passwords do not match').addClass('show-notification');
		$('#confirm_password').focus()
		return false;
	}
	if(password != '')
	{
		
		var password_validation = validate_password(password);
		
		if(password_validation !== true)
		{
			$('.notification').html(password_validation).addClass('show-notification');
			$('#password').focus()
			return false;
		}
	}
	else
	{
		return true;
	}
});
</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);



