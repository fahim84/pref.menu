<script>
$(document).ready(function(){

	$('#OurClients').carouFredSel({
		auto: true,
		scroll : {
			items : 1
		},
		circular:true
	});
});
</script>
<section class="our-clients">
	<div class="container">
    	<h2>Our Clients</h2>
        <ul id="OurClients">
        	<li><img src="<?php echo base_url(); ?>images/l1.png" alt="" /></li>
            <li><img src="<?php echo base_url(); ?>images/l2.png" alt="" /></li>
            <li><img src="<?php echo base_url(); ?>images/l3.png" alt="" /></li>
            <li><img src="<?php echo base_url(); ?>images/l4.png" alt="" /></li>
        </ul>
    </div>
</section>