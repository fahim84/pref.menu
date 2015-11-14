<div class="container circle-options">
	<ul>
    <?php if($_SESSION[USER_LOGIN]['ordering_feature']){ ?>
    	<li>
        	<h2>Take Order</h2>
            <input type="button" name="FeedbackBtn" id="FeedbackBtn" value="Enter" class="Button" onclick="window.location.href='<?php echo base_url(); ?>order/index';" />
        </li>
        <li>
        	<h2>Select Existing Order</h2>
            <input type="button" name="FeedbackBtn" id="FeedbackBtn" value="Enter" class="Button" onclick="window.location.href='<?php echo base_url(); ?>order/existing_orders';" />
        </li>
    <?php } ?>
        <li>
        	<h2>Customer Feedback</h2>
            <input type="button" name="FeedbackBtn" id="FeedbackBtn" value="Enter" class="Button" onclick="window.location.href='<?php echo base_url(); ?>order/menu_list';" />
        </li>
    </ul>
</div>