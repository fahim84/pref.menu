<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page">
	<div class="container">
    	<div class="content-page register-now menu-page" id="ShowForm">
       	  <div class="fleft"><h2>Take Order</h2></div>
          <div class="fright"><?php client_logo(); ?></div>
          <div class="clear"></div>
          <form name="TakeOrderForm" id="TakeOrderForm" class="ac-custom ac-checkbox ac-checkmark" action="">
          <input type="hidden" name="temporary" id="temporary" value="0">
          <input type="hidden" name="order_timestamp" id="order_timestamp" value="<?php echo $order_timestamp; ?>">
          <input type="hidden" name="redirect_file" id="redirect_file" value="order/index/">
          <div class="feeback-option">
          <div class="fleft" style="width:200px;">
          <select name="Table" required id="Table" style="width:200px;height:45px;padding:5px;font-size:20px;" >
          	<option value="" >Select Table</option>
            <?php
				foreach(get_tables() as $Key => $Value)
				{
					$selected = $Table == $Key ? 'selected="selected"' : '';
					echo '<option value="'.$Key.'" '.$selected.' >Table '.$Key.'</option>';
				}
			?>
          </select>
          </div>
          <input name="customerid" type="number" required="required" class="TextField" id="customerid" placeholder="Customer #" style="width:200px;" max="100" min="1" value="" >
          
          <div class="clear"></div>
          </div>
          
          <div class="fleft orders-box">
          <table width="100%" cellpadding="10" cellspacing="2">
                <?php
                    if($menu_query->num_rows())
                    {
						$i = 1;
						foreach($menu_query->result() as $row)
						{
							$row_id = $row->id;
							if($row->category_id != @$last_category_id)
							{
								$last_category_id = $row->category_id;
								$Class='BgTwo';
                ?>
                <tr class="MenuCategory cursor-pointer" catrowid="<?php echo $last_category_id; ?>" >
                  <td align="left"><?php echo $row->category; ?></td>
                </tr>
                <?php
                            }
							
							$Class = $Class=='BgTwo' ? 'BgOne' : 'BgTwo';
							
							$image_url = $row->image == '' ? base_url().'images/no-dish.png' : base_url().UPLOADS.'/'.$row->image;
							$image = base_url()."thumb.php?src=".$image_url."&w=75&h=75";
							
							# These variables will be use in jquery and json search function.
							$items_tags_key = $last_category_id.'-'.$row_id;
							$items_tags_val = $row->category. ' - ' . $row->menu_number . ' ' . $row->title;
							$items_tags[$items_tags_key] = $items_tags_val;
							
                ?>
                <tr class="MenuItem <?php echo $Class; ?> category_items_row_<?php echo $last_category_id; ?>" id="Record_<?php echo $row_id; ?>" catid="<?php echo $last_category_id; ?>" tabindex="<?php echo $i++; ?>" >
                  <td align="left">
                  	<table width="100%" cellpadding="2" cellspacing="2" border="0">
                    	<tr>
                        	<td valign="top" width="10%"><div id="Item_<?php echo $row_id; ?>_image"><a href="<?php echo $image_url; ?>" data-lightbox="roadtrip" data-title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>"><img title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish" /></a></div></td>
                          <td align="left" valign="top" width="90%">
                            <div class="title limit_title" id="Item_<?php echo $row_id; ?>_title"><span title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>">#<?php echo $row->menu_number; ?> <?php echo $row->title; ?></span><?php if($row->popular==1) { ?>&nbsp;<img title="Popular" src="<?php echo base_url(); ?>images/1star.png" alt="Popular" /><?php } ?></div>
                            
                            <div class="description" id="Item_<?php echo $row_id; ?>_desc"><?php echo $row->description; ?></div>
                            	
                                <div>
                                <div class="order-amounts" id="Amount_Item_<?php echo $row_id; ?>">
                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                  <tbody>
                                    <tr>
                                      <td><a class="minus-amount" data-id="Item_<?php echo $row_id; ?>"><img title="Minus" src="<?php echo base_url(); ?>images/minus.png" alt="-"></a></td>
                                      <td valign="middle"><span id="Item_<?php echo $row_id; ?>_quantity_box" class="DisplayQuantities"><?php echo isset($items[$row_id]->quantity) ? $items[$row_id]->quantity : 1; ?></span></td>
                                      <td><a class="add-amount" data-id="Item_<?php echo $row_id; ?>"><img title="Plus" src="<?php echo base_url(); ?>images/plus.png" alt="+"></a></td>
                                    </tr>
                                  </tbody>
                                </table>    
                            </div>
                                <div class="fright"><input type="checkbox" name="Items[]" value="<?php echo $row_id; ?>" id="Item_<?php echo $row_id; ?>" class="OrderItem" /><label for="Item_<?php echo $row_id; ?>"></label></div>
                                </div>
                            </td>
                            
                        </tr>
                        <tr>
                        	<td align="center"><span class="price" id="Item_<?php echo $row_id; ?>_price"><?php echo CURRENCY." ".number_format($row->price); ?></span></td>
                            <td><input class="special_comment_textbox" type="text" name="request_comment[<?php echo $row_id; ?>]" id="Item_<?php echo $row_id; ?>_request_comment" value="" placeholder="Special Reqeust" onKeyUp="update_req_comm(this, '<?php echo $row_id; ?>');"  >
                            </td>
                        </tr>
                    </table>
                    </td>
                </tr>
                <?php	
                        }
                    }
                    else
                    {
                ?>
                <tr>
                  <td align="center">No item found</td>
                </tr>
                <?php		
                    }
                ?>
            </table> 
          </div>
          <div class="fleft order-summary">
          	<div class="survey-box">
            	<div class="item-heading"><h2>Order Summary</h2></div>
                <p id="SelectItemMsg">Please select items</p>
                <ul id="ShowSelectedItems">
                </ul>
            </div>
            
            	<div id="sticker" class="feedback-btn">
                <div style="padding-top:5px;padding-bottom:5px;" >
                    <input type="search" name="keyword" id="keyword" value="" placeholder="Search Items" class="TextField" style="width:280px; opacity:1.0;" >
                </div>
                <div class="price" id="TakOrderError"></div> 
                <input type="submit" name="SubmitBtn" id="SubmitBtn" value="Confirm Order" class="Button" style="background-color:#C30003;" />
                
                </div>
            
          </div>
          <div class="clear"></div>
            
            <div class="clear"></div>
          </form>
        </div>
        <div class="clear"></div>
    </div>
</section>
<script src="<?php echo base_url(); ?>js/orders.js"></script>
<script>

var items_tags = <?php echo json_encode($items_tags); ?>;

$(function() {
    var items_tags = <?php echo json_encode(array_values($items_tags)); ?>;
    $( '#keyword' ).autocomplete({
      source: items_tags,
	  close : function() { search_item(); }
    });
});



</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);



