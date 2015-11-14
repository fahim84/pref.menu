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

    	<div class="container">
            <div class="chart-box" align="center">
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
            <h2>Download Report Summary</h2>
        	<form name="GenerateReportForm" id="GenerateReportForm" method="post" action="<?php echo base_url(); ?>report/download_report/">
            <div >From:<br />
            <input name="start_date" type="text" required class="TextField DateField" id="start_date" value="<?php echo $start_date; ?>" />
            </div>
            <div >To:<br />
            <input name="end_date" type="text" required class="TextField DateField" id="end_date" value="<?php echo $end_date; ?>" />
            </div>
            <div ><input type="submit" name="tl-btn" id="tl-btn" value="Submit" class="Button" />&nbsp;<span id="tlError">&nbsp;</span></div>
            <div class="clear"></div>
            </form>
            <?php
                if(@$showDownloadLink==1)
                {
            ?>
            <a href="<?php echo WEB_URL; ?>report/summary_report.xls" target="_blank" class="download-link">Click here to download file</a>
            <?php        
                }
            ?>
            </div>
            <div class="clear"></div>

        </div>
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
	
});

</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




