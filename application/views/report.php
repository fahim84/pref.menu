<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page reports-page" style="min-height:400px;">
	<div class="container">
    	<div class="content-page register-now menu-page">
        	<div class="fleft"><h2>My Performance</h2></div>
        	<div class="fright"><?php client_logo(); ?></div>
        	<div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $this->load->view('report-tabs',$this->data); ?>
    <div class="charts">
    <?php
		if($count_feedbacks > 1)
		{
	?>
    	<div class="container">
        	<div align="center"><h2 class="error" id="BusinessRank"></h2></div>
        	<div class="fleft chart-box1">
            	<div class="chart-box">
                	<h2>Top/Lowest Rated Menu Item</h2>
                    <form name="TopLowRatedItemsForm" id="TopLowRatedItemsForm" method="post" action="<?php echo base_url(); ?>report/index">
                        <div class="fleft">From:<br />
                        <input type="text" name="start_date" id="start_date" class="TextField DateField" value="<?php echo $start_date; ?>" />
                        </div>
                        <div class="fleft">To:<br />
                        <input type="text" name="end_date" id="end_date" class="TextField DateField" value="<?php echo $end_date; ?>" />
                        </div>
                        <div class="fleft">
                          
                            <label>
                              <input name="graph_interval" type="radio" required="required" id="graph_interval_daily" value="Daily" <?php echo $graph_interval == 'Daily' ? 'checked="checked"' : ''; ?> >
                              Daily</label>
                            
                            <!--<label>
                              <input name="graph_interval" type="radio" required="required" id="graph_interval_weekly" value="Weekly">
                              Weekly</label>-->
                            
                            <label>
                              <input name="graph_interval" type="radio" required="required" id="graph_interval_monthly" value="Monthly" <?php echo $graph_interval == 'Monthly' ? 'checked="checked"' : ''; ?> >
                              Monthly</label>
                            
                          
                      <input type="submit" name="tl-btn" id="tl-btn" value="Submit" class="Button" />&nbsp;<span id="tlError">&nbsp;</span></div>
                        <div class="clear"></div>
                        </form>
                    <div class="menu-rates" id="ShowTopLowItem">Loading...</div>
                    <iframe src="<?php echo base_url(); ?>report/top_low_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&graph_interval=<?php echo $graph_interval; ?>" frameborder="0" id="TopLowGraph" scrolling="yes" width="100%" height="320"></iframe>
                </div>
                <div class="chart-box">
                	<h2>Customer Experience</h2>
                    <iframe src="<?php echo base_url(); ?>report/customer_experience_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&graph_interval=<?php echo $graph_interval; ?>" frameborder="0" id="CustomerExpFrame" scrolling="no" width="100%" height="320"></iframe>
                </div>
                <div class="chart-box">
                	<h2>Speed of service</h2>
                    <iframe src="<?php echo base_url(); ?>report/order_speed_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" frameborder="0" scrolling="no" width="100%" height="400"></iframe>
                </div>
                
            </div>
            <div class="fright chart-box2">
            	<div class="employee-month">
                	
                    <div class="chart-box">
                    <h2>Which staff member served you?</h2>
                    <?php
					$image_url = $top_staff_member->image == '' ? base_url().'images/employee.png' : base_url().UPLOADS.'/'.$top_staff_member->image;
					$image = $top_staff_member->image == '' ? $image_url : base_url()."thumb.php?src=".$image_url."&w=100&h=100";
					?>
                    <div class="fleft"><img title="<?php echo htmlspecialchars($top_staff_member->staff_name,ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish"  /></div>
                    <div class="fleft staff-detail">
                    	<div class="bigtitle"><?php echo $top_staff_member->staff_name; ?><br /><span><?php echo $top_staff_member->designation; ?></span></div>
                        <h3><?php echo number_format($top_staff_member->percent); ?>%</h3>
                    </div>
                    <div class="clear"></div>
                    </div>
                    
                    
                    <div class="chart-box">
                    <h2>Customer’s home address</h2>
                    <iframe src="<?php echo base_url(); ?>report/region_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" width="100%" height="340" frameborder="0" scrolling="yes"></iframe>
                    </div>
                    
                    <div class="chart-box">
                    <h2>How customers heard about us</h2>
                    <iframe src="<?php echo base_url(); ?>report/hearabout_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" width="100%" height="315" frameborder="0" scrolling="auto"></iframe>
                    </div>
                    <div class="chart-box">
                        <h2>Would customers come back?</h2>
                        <iframe src="<?php echo base_url(); ?>report/comeagain_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" frameborder="0" scrolling="no" width="100%" height="350"></iframe>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    <?php
		}
		else
		{
			echo '<div align="center"><h2 class="error">You should have atleast 10 feedbacks to generate report</h2></div>';
		}
	?>
    </div>
</section>
<!--<link href="<?php echo base_url(); ?>css/jquery-ui-timepicker-addon.css" rel="stylesheet" media="screen">
<script src="<?php echo base_url(); ?>js/jquery-ui-timepicker-addon.js"></script>-->
<script>
$(document).ready(function(){
	
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

GetTopLowRateItem('<?php echo $start_date; ?>', '<?php echo $end_date; ?>','<?php echo $graph_interval; ?>');
GetBusinessRank();
</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




