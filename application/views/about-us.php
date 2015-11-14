<?php $this->load->view('header',$this->data); ?>
<section class="header about-us-header">
	<?php $this->load->view('header_section',$this->data); ?>
</section>
<section class="inner-page">
	<div class="container about-us-content">
    	<div class="video">
        <video height="397" controls>
        <source src="<?php echo base_url(); ?>movie.mp4" type="video/mp4">
        Your browser does not support the video tag.
        </video>
        </div>
        <p>Pref is an essential feedback tool which many F&B establishments are using on a daily basis to record their customer's opinions and maintain the highest level of customer service. Pref simplifies the customer ordering process and generates valuable feedback statistics, giving businesses an edge over their competition. When your competitors are using Pref, can you afford not to?</p>
    </div>
</section>
<?php
$this->load->view('clients',$this->data);
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);



