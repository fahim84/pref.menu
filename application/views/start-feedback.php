<?php $this->load->view('header',$this->data); ?>
<section class="header-survey">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page">
	<div class="container">
    	<div class="clear" style="height:20px;"></div>
        <div class="feedback-tablet-header">
        <div class="fright"><?php client_logo(200,200); ?></div>
<?php setcookie('googtrans', '/en/en'); // set English by default on page load
//my_var_dump($_COOKIE['googtrans']); ?>
<style>
.goog-te-banner-frame.skiptranslate {
    display: none !important;
    } 
body {
    top: 0px !important; 
    }
.navigation{display:none;}
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
        <form name="CustomerFeedback" id="CustomerFeedback" method="post" action="<?php echo base_url(); ?>order/submit_feedback/">
        <div class="fleft">
        <div><!--Customer <?php echo $customerid; ?> @ -->Table <?php echo $_SESSION['SurveyTable']; ?></div>
        <div class="survey-box survey-items margin-bottom">
        	<div class="item-heading"><h2>Please rate your order</h2></div>
            <?php
				foreach($menu_query->result() as $row)
				{
                    $image_url = $row->image == '' ? base_url().'images/no-dish.png' : base_url().UPLOADS.'/'.$row->image;
					$image = base_url()."thumb.php?src=".$image_url."&w=100&h=100";
					
					$row->menu_number = ($row->menu_number == 10000 or $row->menu_number == 0) ? '' : '#'.$row->menu_number;
			?>
            <div class="sItem-box">
            	<table width="100%" border="0" cellpadding="2" cellspacing="2">
                  <tbody>
                    <tr>
                      <td width="5%" valign="top"><div><a href="<?php echo $image_url; ?>" data-lightbox="roadtrip" data-title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>"><img title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish menu-dish-flexible" /></a></div>
                      <div class="description price"><?php echo $row->price > 0 ? CURRENCY." ".number_format($row->price):'&nbsp;'; ?></div></td>
                      <td valign="top"><div>
                	<div class="description"><?php echo $row->category; ?></div>
                    <div class="title limit_title"><?php echo $row->menu_number; ?> <?php echo $row->title; ?></div>
                    <table><tr><td>
                    <div class="raty" id="<?php echo $row->id; ?>"></div>
                    </td><td><div class="raty-result" id="raty-result-<?php echo $row->id; ?>"></div></td>
                     </tr></table>
                    </div>
                    
                    
                    <input type="text" autocomplete="off" name="comments[]" class="special_comment_textbox" placeholder="How can it be better? (Optional)" />
                    
                    </td>
                    </tr>
                  </tbody>
                </table>

            	<div class="clear"></div>
            </div>
            <?php		
				}
			?>
            <div  id="menu_items_error_div" tabindex="1"></div>
            </div>
            <div class="fleft region">
            	<h3 style="font-size: 24px;">Which region do you live in?</h3>
                <div id="region_div">
                <select name="Region" required id="Region" style="width:280px;height:45px;padding:5px;font-size:24px;">
                	<option value="">Please Select</option>
                    <option value="0" style="color:#45aa68;">OTHER</option>
					<?php
						foreach(get_regions() as $region)
						{
							echo '<option value="'.htmlspecialchars($region,ENT_QUOTES).'">'.htmlspecialchars($region,ENT_QUOTES).'</option>';
						}
					?>
                    <option value="0" style="color:#45aa68;">OTHER</option>
                </select>
                </div>
                <div id="region_text_div">
                <input name="region_text" autocomplete="off" type="text" id="region_text" placeholder="Please type region" class="TextField" style="width:280px;text-transform:capitalize;" >
                </div>
          </div>
            <div class="fleft hear-aboutus">
            	<h3 style="font-size: 24px;">How did you hear about us?</h3>
                <div id="hear_about_div">
                <select name="HearAboutUs" required id="HearAboutUs" style="width:280px;height:45px;padding:5px;font-size:24px;">
                	<option value="">Please Select</option>
                    <?php
						foreach(references() as $reference)
						{
							echo '<option value="'.htmlspecialchars($reference,ENT_QUOTES).'">'.htmlspecialchars($reference,ENT_QUOTES).'</option>';
						}
					?>
                    <option value="0" style="color:#45aa68;">OTHER</option>
                </select>
                </div>
                <div id="hear_about_text_div">
                <input name="hear_about_text" autocomplete="off" type="text" id="hear_about_text" placeholder="Please type reference" class="TextField" style="width:280px;text-transform:capitalize;" >
                </div>
            </div>
        </div>
        <div class="fright survey-fix">
        	<div class="survey-box margin-bottom">
            	<div class="item-heading"><h3 style="font-size: 24px;">Customer Experience</h3></div>
                <table><tr><td><div class="raty2 padding20" style="padding-right:0px;"></div></td><td><div id="raty2-result"></div></td></tr></table>
                <div  id="customer_experience_error_div" tabindex="2"></div>
            </div>
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3 style="font-size: 24px;">Speed of service</h3></div>
                <table><tr><td><div class="raty3 padding20" style="padding-right:0px;"></div></td><td><div id="raty3-result"></div></td></tr></table>
                <div  id="speed_order_error_div" tabindex="3"></div>
            </div>
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3 style="font-size: 24px;">Would you come again in the near future?</h3></div>
                <div class="padding20">
                	<select name="ComeAgain" id="ComeAgain" style="font-size: 24px;">
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
            	<div class="item-heading"><h3 style="font-size: 24px;">Which staff member served you?</h3></div>
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
            	<h3 class="fleft" style="font-size: 24px;">How can we do better? <span class="description">(Optional)</span></h3>
                <textarea name="suggestion" id="suggestion" class="UserComment"></textarea>
            </div>
            <div class="survey-box3 margin-bottom">
            	<h3>Receive a generous discount on your next visit <span class="description">(Optional)</span></h3>
                <input type="text" autocomplete="off" name="name" id="name" class="TextField" placeholder="Enter your name" />
                <input type="email" autocomplete="off" name="Email" id="Email" class="TextField" placeholder="Enter your email address" />
            </div>
            <div id="FeebackError"></div>
            <div class="survey-btn margin-bottom" align="right">
            	&nbsp;<input type="submit" name="SubmitBtn" id="SubmitBtn" value="Submit" class="Button" style="background-color:#45aa68;font-size: 30px;" />
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
	
	$('.raty').raty({ 
	number: 5, 
	path : '<?php echo base_url(); ?>images/', 
	scoreName:'rates[]' ,
	click: function(score, evt) {
		id = $(this).attr('id');
		$('#raty-result-'+id).html('<img src="<?php echo base_url(); ?>images/face'+score+'.png" />');
		}
	});
	
	$('.raty2').raty({ 
	number: 5, 
	path : '<?php echo base_url(); ?>images/', 
	scoreName:'customerExp' ,
	click: function(score, evt) { $('#raty2-result').html('<img src="<?php echo base_url(); ?>images/face'+score+'.png" />');}
	});
	
	$('.raty3').raty({ 
	number: 5, 
	path : '<?php echo base_url(); ?>images/', 
	scoreName:'OrderSpeed' ,
	click: function(score, evt) { $('#raty3-result').html('<img src="<?php echo base_url(); ?>images/face'+score+'.png" />');}
	});
	
	/*$('.raty').raty({
		scoreName:'rates[]',
		number: 5,
		single: true,
		click: function(score, evt) {$('.raty').raty('click', score);},
		iconRange: [
			{ range: 1, on: '<?php echo base_url(); ?>images/face-a.png', off: '<?php echo base_url(); ?>images/face-a-off.png' },
			{ range: 2, on: '<?php echo base_url(); ?>images/face-b.png', off: '<?php echo base_url(); ?>images/face-b-off.png' },
			{ range: 3, on: '<?php echo base_url(); ?>images/face-c.png', off: '<?php echo base_url(); ?>images/face-c-off.png' },
			{ range: 4, on: '<?php echo base_url(); ?>images/face-d.png', off: '<?php echo base_url(); ?>images/face-d-off.png' },
			{ range: 5, on: '<?php echo base_url(); ?>images/face-e.png', off: '<?php echo base_url(); ?>images/face-e-off.png' }
		]
	});
	$('.raty2').raty({
		scoreName:'customerExp',
		number: 5,
		single: true,
		click: function(score, evt) {$('.raty2').raty('click', score);},
		iconRange: [
			{ range: 1, on: '<?php echo base_url(); ?>images/face-a.png', off: '<?php echo base_url(); ?>images/face-a-off.png' },
			{ range: 2, on: '<?php echo base_url(); ?>images/face-b.png', off: '<?php echo base_url(); ?>images/face-b-off.png' },
			{ range: 3, on: '<?php echo base_url(); ?>images/face-c.png', off: '<?php echo base_url(); ?>images/face-c-off.png' },
			{ range: 4, on: '<?php echo base_url(); ?>images/face-d.png', off: '<?php echo base_url(); ?>images/face-d-off.png' },
			{ range: 5, on: '<?php echo base_url(); ?>images/face-e.png', off: '<?php echo base_url(); ?>images/face-e-off.png' }
		]
	});
	$('.raty3').raty({
		scoreName:'OrderSpeed',
		number: 5,
		single: true,
		click: function(score, evt) {$('.raty3').raty('click', score);},
		iconRange: [
			{ range: 1, on: '<?php echo base_url(); ?>images/face-a.png', off: '<?php echo base_url(); ?>images/face-a-off.png' },
			{ range: 2, on: '<?php echo base_url(); ?>images/face-b.png', off: '<?php echo base_url(); ?>images/face-b-off.png' },
			{ range: 3, on: '<?php echo base_url(); ?>images/face-c.png', off: '<?php echo base_url(); ?>images/face-c-off.png' },
			{ range: 4, on: '<?php echo base_url(); ?>images/face-d.png', off: '<?php echo base_url(); ?>images/face-d-off.png' },
			{ range: 5, on: '<?php echo base_url(); ?>images/face-e.png', off: '<?php echo base_url(); ?>images/face-e-off.png' }
		]
	});*/

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
	var name = $('#name').val();
	
	if(Email != '' || name != '')
	{
		if(Email == '' || isEmail(Email)==false)
		{
			$('#FeebackError').html('Please enter a valid email address.');
			$('#Email').focus();
			console.log('Please enter a valid email address.');
			return false;
		}
		else if(name == '')
		{
			$('#FeebackError').html('Please enter your name.');
			$('#name').focus();
			console.log('Please enter your name.');
			return false;
		}
	}
	/*if(Email!='' && isEmail(Email)==false)
	{
		$('#FeebackError').html('Please enter a valid email address.');
		$('#Email').focus();
	}
	else*/
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
		<div class="fleft logo"><a href="<?php echo base_url(); ?>"><img width="100" src="<?php echo base_url(); ?>images/pref_logo.png" alt="pref_logo" /></a></div>

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




