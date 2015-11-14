<link href="<?php echo base_url(); ?>css/bootstrap.min.css" rel="stylesheet" media="screen">
<?php $this->load->view('header',$this->data); ?>


<link href="<?php echo base_url(); ?>css/bootstrap-formhelpers.min.css" rel="stylesheet" media="screen">

<script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap-formhelpers.min.js"></script>

<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page reports-page" style="min-height:400px;">
	<div class="container">
    	<div class="content-page register-now menu-page">
        	<div class="fleft"><h2>Menu Ratings</h2></div>
        	<div class="fright"><?php client_logo(); ?></div>
        	<div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $this->load->view('report-tabs',$this->data); ?>
    <div class="charts">
    <?php
		if($count_feedbacks > 4)
		{
	?>
    	<div class="container">
        	<div class="fleft chart-box1">
            	<div class="chart-box">
                	<h2>Item Reviews</h2>
                    <form name="MenuReviewPageForm" id="MenuReviewPageForm" method="post" action="<?php echo base_url(); ?>report/menu_ratings">
                        <div class="fleft">From:<br />
                        <input type="text" name="start_date" id="start_date" class="TextField DateField" value="<?php echo $start_date; ?>" />
                        </div>
                        <div class="fleft">To:<br />
                        <input type="text" name="end_date" id="end_date" class="TextField DateField" value="<?php echo $end_date; ?>" />
                        </div>
                        <div class="clear"></div>
                        <div>
                        <label>
                          <input name="graph_interval" type="radio" required="required" id="graph_interval_daily" value="Daily" <?php echo $graph_interval == 'Daily' ? 'checked="checked"' : ''; ?> >
                          Daily</label>
                        
                        <!--<label>
                          <input name="graph_interval" type="radio" required="required" id="graph_interval_weekly" value="Weekly">
                          Weekly</label>-->
                        
                        <label>
                          <input name="graph_interval" type="radio" required="required" id="graph_interval_monthly" value="Monthly" <?php echo $graph_interval == 'Monthly' ? 'checked="checked"' : ''; ?> >
                          Monthly</label>
                          
                        </div>
                        <br>
                        <div class="clear"></div>
                        <div>
                        Select Items to display on graph: (Ctrl+Click)<br />
                        <select name="items" size="6" required multiple="MULTIPLE" id="items" style="width:280px;">
                        <?php 
						$numItems = $menu_query->num_rows();
						$i = 0;
						foreach($menu_query->result() as $row)
						{
							$row_id = $row->id;
							if($row->category_id != @$last_category_id)
                            {
								if( isset($last_category_id) ) echo '</optgroup>';
								$last_category_id = $row->category_id;
								echo '<optgroup label="'.$row->category.'">';
							}
							
							# get first menu item id here
							if($i==0) $first_menu_id=$row_id;
							
							# These variables will be use in jquery and json search function.
							$items_tags_key = $row_id;
							$items_tags_val = $row->category. ' - ' . $row->menu_number . ' ' . $row->title;
							$items_tags[] = $items_tags_val;
							$items[$row_id] = $items_tags_val;
							?>
                                <option value="<?php echo $row->id; ?>" <?php echo $i < 3 ? 'selected="selected"' : ''; ?> ><?php echo $row->title; ?></option>
                        <?php
							if(++$i === $numItems) 
							{
								echo '</optgroup>';
							} 
						} ?>
                        </select>
                        </div>
                        <div>
                        <input type="submit" name="tl-btn" id="tl-btn" value="Submit" class="Button" />&nbsp;<span id="tlError">&nbsp;</span></div>
                  </form>
                        
                        <iframe src="" frameborder="0" id="AllMenuFrame" scrolling="no" width="100%" height="400"></iframe>
                </div>
            </div>
            <div class="fright chart-box2">
            	<div class="employee-month">
                	<div class="chart-box">
                    <h2>Menu Items Rating</h2>
                    <div style="width:250px;">
                    <!--<select name="cMenuitems" id="cMenuitems">-->
                    	<div class="bfh-selectbox" data-name="menu_item" data-value="<?php echo $first_menu_id; ?>" data-filter="true">
                    <?php
						//foreach($menu_query->result() as $row)
						foreach($items as $key => $value)
						{
					?>
                    	<div data-value="<?php echo $key; ?>"><?php echo $value; ?></div>
                    <?php
						}
					?>
                    <!--</select>-->
                    	</div>
                    </div>
                    
                    
                    <iframe src="" id="ShowItemRates" width="100%" height="300" frameborder="0" scrolling="yes"></iframe>
                    <div class="clear height10">&nbsp;</div>
                    <div id="ShowItemReview"></div>
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
	
	$('.bfh-selectbox').on('change.bfhselectbox', function () {
		
		var menu_id = $(this).val();
		//console.log(menu_id);
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		
		ReloadItemRateIframe(menu_id,start_date,end_date);
		LoadItemReviews(menu_id,start_date,end_date);
	});
});
var start_date = $('#start_date').val();
var end_date = $('#end_date').val();
var graph_interval = $("input:radio[name ='graph_interval']:checked").val();
var items = $('#items').val();
var selected_item_id = <?php echo $first_menu_id; ?>; //$('#cMenuitems').val();

ShowAllItemLineChart(start_date, end_date, graph_interval, items);

ReloadItemRateIframe(selected_item_id,start_date,end_date);
LoadItemReviews(selected_item_id,start_date,end_date);

$(document).on('submit', '#MenuReviewPageForm', function(){
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	var graph_interval = $("input:radio[name ='graph_interval']:checked").val();
	var items = $('#items').val();
	ShowAllItemLineChart(start_date, end_date, graph_interval, items);
	return false;
});

/*$(document).on('change', '#cMenuitems', function(){
	var menu_id = $(this).val();
	
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	
	ReloadItemRateIframe(menu_id,start_date,end_date);
	LoadItemReviews(menu_id,start_date,end_date);
});*/
</script>

<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




