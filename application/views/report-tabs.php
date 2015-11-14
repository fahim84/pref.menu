<style>
/* Navigation styling */
.nav {
	margin-bottom:30px;
}
.nav-list {
}
.nav-item {
	float:left;
	zoom:1;
}
.nav-item a {
	margin:1px;
	width:150px;
	text-align:center;
	padding:7px 10px;
	color:#FFF;
	background:#34495E;
	display:block;
	border-radius:5px 5px 5px 5px;
}
.nav-item a:hover {
	background:#2C3E50;
}
.nav-item a.active {
	background:#d2d2d2;
}
</style>
<table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <nav class="nav">
                <span class="nav-item"><a href="<?php echo base_url(); ?>report/index" <?php if($Active=='Reports') { ?> class="active"<?php } ?>>Feedback Stats</a></span>
                <span class="nav-item"><a href="<?php echo base_url(); ?>report/menu_ratings" <?php if($Active=='menu-ratings') { ?> class="active"<?php } ?>>Menu Ratings</a></span>
                <span class="nav-item"><a href="<?php echo base_url(); ?>report/review_report" <?php if($Active=='Review') { ?> class="active"<?php } ?>>Customer Reviews</a></span>
                <span class="nav-item"><a href="<?php echo base_url(); ?>report/change_password" <?php if($Active=='Password') { ?> class="active"<?php } ?>>Change Password</a></span>
                <span class="nav-item"><a href="<?php echo base_url(); ?>report/deleted_orders" <?php if($Active=='deleted-orders') { ?> class="active"<?php } ?>>Deleted Orders</a></span>
                <span class="nav-item"><a href="<?php echo base_url(); ?>report/download_report" <?php if($Active=='Download') { ?> class="active"<?php } ?>>Download Report</a></span>
            </nav>
            
        </td>
    </tr>
</table>
