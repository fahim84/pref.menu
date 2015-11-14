<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page reports-page">
	<div class="container">
    	<div class="content-page register-now menu-page">
        	<div class="fleft"><h2>Reviews</h2></div>
        	<div class="fright"><?php client_logo(); ?></div>
        	<div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $this->load->view('report-tabs',$this->data); ?>
    <div class="charts">
    	<div class="container" align="center">
        
        <div class="review-container" >
			<br><br>
        	<form name="SearchReviewsForm" id="SearchReviewsForm" method="post" action="<?php echo base_url(); ?>report/review_report">
            
            <div align="center">
            From Date:<br />
            <input type="text" name="start_date" id="start_date" class="TextField" value="<?php echo $start_date; ?>" />
            <div class="clear height10">&nbsp;</div>
            
            </div>
            <div align="center">
            To Date:<br />
            <input type="text" name="end_date" id="end_date" class="TextField" value="<?php echo $end_date; ?>" />
            <br><br>
            <input type="submit" name="SubmitBtn" id="SubmitBtn" class="Button" value="Submit" >
            <div class="clear height10">&nbsp;</div>
            
            </div>
            <div class="clear height10">&nbsp;</div>
            <div align="center" >
            
            </div>
            <div align="center"><?php echo $rating_query->num_rows(); ?> total records found</div>
            <div class="clear height10">&nbsp;</div>
            </form>
        <?php
			if($rating_query->num_rows())
			{
				foreach($rating_query->result() as $row )
				{
					$rating_id = $row->id;
					$PostedBy = $row->email;
					if($PostedBy!='')
						$Emails[] = $PostedBy;
					
					if($PostedBy=='')
						$PostedBy='User';
					
					$Comments = $row->suggestion;
					if($Comments=='')
						$Comments='No comments';
					
					$StaffMember = $row->staff_id;
					$StaffMemberName = $row->staff_name;
					$StaffMemberName .= $row->designation != '' ? " (".$row->designation.")" : '';
					
					$image_url = $row->image == '' ? base_url().'images/employee.png' : base_url().UPLOADS.'/'.$row->image;
					$image = $row->image == '' ? $image_url : base_url()."thumb.php?src=".$image_url."&w=100&h=100";
					
					$CustomerRegionName = $row->region == 'I am a tourist' ? 'Tourist' : $row->region;
						
					$HowHearUs = $row->hear_about_us;
		?>
        <div class="review-box">
        	<div><h2>Posted by: <?php echo $PostedBy; ?></h2></div>
            <div class="clear border-bottom"></div>
            <div class="clear height10"></div>
            <div>Posted Date: <?php echo date('F d Y',strtotime($row->date_created))." at ".date('h:i a',strtotime($row->date_created)); ?></div>
            <div class="clear height10"></div>
            <div class="menu-stars">
            	<h3 data-id="ms<?php echo $rating_id; ?>">Menu Ratings</h3>
                <div class="ratings-accordian" style="display:none;" id="rating-accordian-ms<?php echo $rating_id; ?>">
            <?php
				# Get rating items
				$rating_items_query = $this->report_model->get_rating_items($rating_id);
				
				if($rating_items_query->num_rows())
				{
					foreach($rating_items_query->result() as $item)
					{
						$itemComments = $item->item_comment;
						if($itemComments=='')
							$itemComments='No Comment';
							
						$RateID = "Rate_".$item->id;
			?>
            <div class="comments"><strong><?php echo $item->title; ?><br /><span id="<?php echo $RateID ?>" class="showStarRating" data-rate="<?php echo $item->rate; ?>"></span></strong><br /><?php echo $itemComments; ?></div>
            <?php
					}
				}
			?>
            </div>
            </div>
            <div class="other-stars">
            	<h3 data-id="cs<?php echo $rating_id; ?>">Customer Service</h3>
                <div class="services-accordian" style="display:none;" id="rating-accordian-cs<?php echo $rating_id; ?>">
            <div class="comments"><strong>Customer Experience<br /><span class="showStarRating" data-rate="<?php echo $row->customer_experience; ?>"></span></strong></div>
            <div class="comments"><strong>Speed of order<br /><span class="showStarRating" data-rate="<?php echo $row->order_speed; ?>"></span></strong></div>
            <div class="comments"><strong>Would customer like to come back</strong><br /><?php echo $row->come_again; ?></div>
            <div class="comments"><strong>Staff member who stood out</strong><br /><?php echo $StaffMemberName; ?><br>
            								<img title="<?php echo htmlspecialchars($StaffMemberName,ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish"  />
                                            </div>
            <div class="comments"><strong>Which region customer live in?</strong><br /><?php echo $CustomerRegionName; ?></div>
            <div class="comments"><strong>How customer heard about us?</strong><br /><?php echo $HowHearUs; ?></div>
            </div>
            </div>
            <div class="clear"></div>
            <div class="comments"><?php echo nl2br($Comments); ?></div>
            <div class="clear"></div>
        </div>
        <?php			
				}
			}
			else
			{
				echo '<div align="center">No reviews found</div>';
			}
		?>
        <div >
        	<div class="review-box email-addresses">
            	<div class="fleft"><h2>Email Addresses</h2></div>
                <div class="fright"><?php if(is_array(@$Emails)) { ?><a href="<?php echo base_url(); ?>report/export_email_addresses/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">Export</a><?php } ?></div>
                <div class="clear"></div>
                <?php
					if(is_array(@$Emails))
					{
						echo '<ul>';
						foreach($Emails as $Email)
						{
							echo '<li>'.$Email.'</li>';
						}
						echo '</ul>';
					}
					else
						echo '<div allign="center">No email address found</div>';
				?>
            </div>
        </div>
        </div>
        
        <div class="clear"></div>
        </div>
    </div>
</section>
<script>
$(document).ready(function(){
	
	$('.showStarRating').raty({ readOnly: true, score: function(){ return $(this).attr('data-rate'); }, path : '<?php echo base_url(); ?>images/' });
	
	$("#start_date").datepicker({dateFormat: 'yy-mm-dd',
        //minDate: 0,
        //maxDate: "+60D",
        numberOfMonths: 1,
        onSelect: function(selected) {
          $("#end_date").datepicker("option","minDate", selected)
        }
    });
    $("#end_date").datepicker({ dateFormat: 'yy-mm-dd',
        //minDate: 0,
        //maxDate:"+60D",
        numberOfMonths: 1,
        onSelect: function(selected) {
           $("#start_date").datepicker("option","maxDate", selected)
        }
    });
	
});

$(document).on('click', '.menu-stars h3, .other-stars h3', function(){
	var id = $(this).attr('data-id');
	var ShowID = 'rating-accordian-'+id;
	if($(this).hasClass('active'))
	{
		$(this).removeClass('active');
		$('#'+ShowID).slideUp();
	}
	else
	{
		$('.menu-stars h3, .other-stars h3').removeClass('active');
		$(this).addClass('active');
		$('.ratings-accordian, .services-accordian').slideUp();
		$('#'+ShowID).slideDown();
	}
});

</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




