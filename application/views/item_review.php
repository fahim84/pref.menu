<?php
			if($review_query->num_rows()>0)
			{
				$item_name = $review_query->row()->title;
				
				echo "<h3>User rating/comment on ".$item_name."</h3>";
				echo '<div class="ShowItemReview">';
				foreach($review_query->result() as $row)
				{
					$itemComments = $row->item_comment == '' ? 'No Comment' : $row->item_comment;
					
					$RateID = "Rate_".$row->id;
					$RateIDs[] = $RateID;
?>
<div class="review-box">
    <div><img title="<?php echo $row->rate; ?> Stars" src="<?php echo base_url().'images/'.$row->rate; ?>star.png" alt="image" /> <span class="fright"><?php echo date('jS M, Y - g:i a',strtotime($row->date_created)); ?></span></div>
    <div><?php echo $itemComments; ?></div>
</div>
<?php
				}
				echo '<div class="clear"></div></div>';
?>
<script>
$(document).ready(function(){
<?php foreach($RateIDs as $RateID) { ?>
	$('#<?php echo $RateID; ?>').raty({ readOnly: true, score: $('#<?php echo $RateID; ?>').attr('data-rate'), path : '<?php echo base_url(); ?>images/' });
<?php } ?>
});
</script>
<?php				
			}
			else
			{
				//echo "No comments found";
			}
		
	
?>