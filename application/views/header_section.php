<div class="navigation">
    <div class="container">
        <div class="fleft logo"><a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>images/pref_logo.png" alt="pref_logo" /></a></div>
        <div class="fright">
        <?php if(isset($_SESSION[USER_LOGIN])) { ?>
        	<div class="res-nav"><img src="<?php echo base_url(); ?>images/res-nav.png" alt="" /></div>
            <nav class="nav-box">
                <ul>
                	<li><a href="<?php echo base_url(); ?>"<?php if($Active=='home') { echo ' class="active"'; } ?>>Start</a></li>
                    <li><a href="<?php echo base_url(); ?>restaurant/profile"<?php if($Active=='profile') { echo ' class="active"'; } ?>>Profile</a></li>
                    <li><a href="<?php echo base_url(); ?>restaurant/menu"<?php if($Active=='menu') { echo ' class="active"'; } ?>>Menu</a></li>
                    <li><a href="<?php echo base_url(); ?>restaurant/staff"<?php if($Active=='staff') { echo ' class="active"'; } ?>>Staff</a></li>
                    <li><a href="<?php echo base_url(); ?>login/logout/">Logout</a></li>
                </ul>
            </nav>
        <?php } else { ?>
		<nav class="normal-nav-box">
        	<ul>
            	<li><a href="<?php echo base_url(); ?>"<?php if($Active=='home') { echo ' class="active"'; } ?>>Home</a></li>
                <li><a href="<?php echo base_url(); ?>login/signup"<?php if($Active=='register') { echo ' class="active"'; } ?>>Register</a></li>
            </ul>
        </nav>
		<?php } ?>    
        </div>
        <div class="clear"></div>
    </div>
</div>