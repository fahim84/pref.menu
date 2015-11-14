<?php $this->load->view('header',$this->data); ?>
<section class="header-survey">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page">
	<div class="container">
    	<div class="clear" style="height:20px;"></div>
        <div class="feedback-tablet-header">
        <div class="fright"><?php client_logo(); ?></div>
<?php setcookie('googtrans', '/en/en'); // set English by default on page load
//my_var_dump($_COOKIE['googtrans']); ?>
<style>
.goog-te-banner-frame.skiptranslate {
    display: none !important;
    } 
body {
    top: 0px !important; 
    }

</style>
<div id="google_translate_element"></div>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

        
        <div class="fleft letusknow"><img src="<?php echo base_url(); ?>images/letusknow.png" alt="" /></div>
        <div class="clear" style="height:20px;"></div>
        </div>
		<?php
        	if($menu_query->num_rows())
			{
		?>
        <form name="CustomerFeedback" id="CustomerFeedback" method="post" action="">
        <div class="fleft">
        <div>Customer <?php echo $customerid; ?> @ Table <?php echo $_SESSION['SurveyTable']; ?></div>
        <div class="survey-box survey-items margin-bottom">
        	<div class="item-heading"><h2>Please rate your order</h2></div>
            <?php
				foreach($menu_query->result() as $row)
				{
                    $image_url = $row->image == '' ? base_url().'images/no-dish.png' : base_url().UPLOADS.'/'.$row->image;
					$image = base_url()."thumb.php?src=".$image_url."&w=100&h=100";
			?>
            <div class="sItem-box">
            	<table width="100%" border="0" cellpadding="2" cellspacing="2">
                  <tbody>
                    <tr>
                      <td width="5%" valign="top"><div><a href="<?php echo $image_url; ?>" data-lightbox="roadtrip" data-title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>"><img title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish menu-dish-flexible" /></a></div>
                      <div class="description price"><?php echo CURRENCY." ".number_format($row->price); ?></div></td>
                      <td valign="top"><div>
                	<div class="description"><?php echo $row->category; ?></div>
                    <div class="title limit_title">#<?php echo $row->menu_number; ?> <?php echo $row->title; ?></div>
                    <div class="raty"></div>
                    </div>
                    <input type="text" autocomplete="off" name="comments[]" class="special_comment_textbox" placeholder="How can it be better?" />
                    </td>
                    </tr>
                  </tbody>
                </table>

            	<div class="clear"></div>
            </div>
            <?php		
				}
			?>
            <div style="padding:10px;" id="menu_items_error_div" tabindex="1"></div>
            </div>
            <div class="fleft region">
            	<h3>Which region do you live in?</h3>
                <div id="region_div">
                <select name="Region" required id="Region" style="width:280px;height:45px;padding:5px;font-size:20px;">
                	<option value="">Please Select</option>
                    <option value="0">Other</option>
					<?php
						foreach(get_regions() as $region)
						{
							echo '<option value="'.htmlspecialchars($region,ENT_QUOTES).'">'.htmlspecialchars($region,ENT_QUOTES).'</option>';
						}
					?>
                    <option value="0">Other</option>
                </select>
                </div>
                <div id="region_text_div">
                <input name="region_text" autocomplete="off" type="text" id="region_text" placeholder="Please type region" class="TextField" style="width:280px;text-transform:capitalize;" >
                </div>
          </div>
            <div class="fleft hear-aboutus">
            	<h3>How did you hear about us?</h3>
                <div id="hear_about_div">
                <select name="HearAboutUs" required id="HearAboutUs" style="width:280px;height:45px;padding:5px;font-size:20px;">
                	<option value="">Please Select</option>
                    <?php
						foreach(references() as $reference)
						{
							echo '<option value="'.htmlspecialchars($reference,ENT_QUOTES).'">'.htmlspecialchars($reference,ENT_QUOTES).'</option>';
						}
					?>
                    <option value="0">Other</option>
                </select>
                </div>
                <div id="hear_about_text_div">
                <input name="hear_about_text" autocomplete="off" type="text" id="hear_about_text" placeholder="Please type reference" class="TextField" style="width:280px;text-transform:capitalize;" >
                </div>
            </div>
        </div>
        <div class="fright survey-fix">
        	<div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Customer Experience</h3></div>
                <div class="raty2 padding20"></div>
                <div style="padding:10px;" id="customer_experience_error_div" tabindex="2"></div>
            </div>
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Speed of service</h3></div>
                <div class="raty3 padding20"></div>
                <div style="padding:10px;" id="speed_order_error_div" tabindex="3"></div>
            </div>
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Would you come again in the near future?</h3></div>
                <div class="padding20">
                	<select name="ComeAgain" id="ComeAgain">
                    	<?php
						foreach(get_come_again_options()  as $option)
						{
							echo '<option value="'.$option.'">'.$option.'</option>';
						}
                        ?>
                    </select>
                </div>
            </div>
            <?php
				if($staff_query->num_rows())
				{
					
			?>
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Which member of staff did you like?</h3></div>
                <div class="padding20">
                	<ul class="staff-list">
                    	<li><label></label></li>
                        <li><label><input name="Member" type="radio" value="0" checked="checked" /> <img title="I don't recognise them here" src="<?php echo base_url().'images/no-dish.png'; ?>" width="50" alt="" class="menu-dish" /> <span style="font-size:16px;">I don't recognise them here</span></label></li>
                    <?php
						foreach($staff_query->result() as $row)
						{
							$image_url = $row->image == '' ? base_url().'images/no-dish.png' : base_url().UPLOADS.'/'.$row->image;
							$image = base_url()."thumb.php?src=".$image_url."&w=50&h=50";
					?>
                    	<li><label><input type="radio" name="Member" value="<?php echo $row->id; ?>" /> <img title="<?php echo $row->title." (".$row->designation.")"; ?>" src="<?php echo $image; ?>" alt="" class="menu-dish" /> <?php echo $row->title." (".$row->designation.")"; ?></label></li>
                    <?php
						}
					?>
                    </ul>
                </div>
            </div>
            <?php
				}
				else
				{
					echo '<input type="hidden" name="Member" value="0" />';
				}
			?>
            <div class="survey-box2 margin-bottom">
            	<h3>How can we do better?</h3>
                <textarea name="suggestion" id="suggestion" class="UserComment"></textarea>
            </div>
            <div class="survey-box3 margin-bottom">
            	<h3>Would you like to receive our special offers?</h3>
                <input type="email" autocomplete="off" name="Email" id="Email" class="TextField" placeholder="Enter your email address (Optional)" />
            </div>
            <div id="FeebackError"></div>
            <div class="survey-btn margin-bottom" align="right">
            	&nbsp;<input type="submit" name="SubmitBtn" id="SubmitBtn" value="Submit" class="Button" />
            </div>
        </div>
        </form>
		<?php
			}
			else
			{
				echo '<div align="center">Sorry, something is wrong with system. Please try again.</div>';
			}
		?>	
    	<div class="clear"></div>
    </div>
</section>
<script>
$(document).ready(function(){
	
	$('.raty').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'rates[]' });
	$('.raty2').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'customerExp' });
	$('.raty3').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'OrderSpeed' });
	
});

$(function() {
    var regions_tags = <?php echo json_encode(array_values(get_regions())); ?>;
    $( "#region_text" ).autocomplete({
      source: regions_tags
    });
});

$( '#region_text_div' ).hide();

$(function() {
    var references_tags = <?php echo json_encode(array_values(references())); ?>;
    $( "#hear_about_text" ).autocomplete({
      source: references_tags
    });
});

$( '#hear_about_text_div' ).hide();

$(document).on('submit', '#CustomerFeedback', function(){
	var Email = $('#Email').val();
	
	if(Email!='' && isEmail(Email)==false)
	{
		$('#FeebackError').html('Please enter a valid email address.');
		$('#Email').focus();
	}
	else
	{
		var Form = $('#CustomerFeedback').serialize();
		
		$('#FeebackError').html('Please wait...');
		$('#CustomerFeedback input').attr('disabled', 'disabled');
		
		$.post(WEB_URL+"order/submit_feedback/", Form, function(Data, textStatus){
			
			$('#CustomerFeedback input').removeAttr('disabled');
			
			if(Data.focus_element != '')
			{
				$('#menu_items_error_div').html('');
				$('#customer_experience_error_div').html('');
				$('#speed_order_error_div').html('');
				$('#FeebackError').html('');
				
				$('#'+Data.focus_element).html(Data.Msg);
				$('#'+Data.focus_element).focus();
				
			}
			else
			{
				$('#FeebackError').html(Data.Msg);
			}
			
			if(Data.Error==0)
				window.location.href=WEB_URL+'welcome/thankyou';
			/*
			if(Data.NextPerson>0 && Data.Error==0)
				window.location.href=WEB_URL+'start-feedback.html';
			if(Data.NextPerson==0 && Data.Error==0)
				window.location.href=WEB_URL+'thankyou.html';
			*/
		}, 'json');
	}
	return false;
});

$(document).on('change', '#HearAboutUs', function(){
	if($(this).val() == '0')
	{
		$('#hear_about_div').html('');
		$( '#hear_about_text_div' ).show();
		$("#hear_about_text").prop('required',true);
	}
});
$(document).on('change', '#Region', function(){
	if($(this).val() == '0')
	{
		$('#region_div').html('');
		$( '#region_text_div' ).show();
		$("#region_text").prop('required',true);
	}
});
</script>

<footer>
	<div class="container">
        <div align="center">
        <?php if(!isset($_SESSION[USER_LOGIN])) { ?>
        &nbsp;<a href="<?php echo base_url(); ?>">Home</a>&nbsp;&nbsp;<a href="<?php echo base_url(); ?>">Login</a>.&nbsp;&nbsp;<a href="<?php echo base_url(); ?>login/signup">Register</a>.
        <?php } ?>
        <br>&copy; Copyright <?php echo date('Y'); ?>. All rights reserved. &nbsp;
        </div>
        <div class="clear"></div>
    </div>
</footer>

<?php
$this->load->view('footer',$this->data);




