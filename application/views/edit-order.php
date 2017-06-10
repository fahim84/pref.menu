<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>
<style>
    .ui-menu-item,ui-state-focus{font-size: 18px;}
</style>
<section class="inner-page">
	<div class="container">
    	<div class="content-page register-now menu-page" id="ShowForm">
       	  <div class="fleft"><h2>Modify Order</h2></div>
          <div class="fright"><?php client_logo(); ?></div>
          <div class="clear"></div>
          <form action="<?php echo base_url(); ?>order/edit_order/" method="post" name="UpdateOrderForm" class="ac-custom ac-checkbox ac-checkmark" id="UpdateOrderForm">
          <div class="feeback-option">
          	<div class="fleft" style="width:200px;">
          <select name="Table" required id="Table" style="width:200px;height:45px;padding:5px;font-size:20px;" >
          	<option value="" >Select Table</option>
            <?php
				foreach(get_tables() as $Key => $Value)
				{
					$selected = $order->table_number == $Key ? 'selected="selected"' : '';
					echo '<option value="'.$Key.'" '.$selected.' >'.$Value.'</option>';
				}
			?>
          </select>
          </div>
          <input name="customerid" type="hidden" id="customerid"  value="1" >
          <div class="clear"></div>
          </div>
          
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
    
          <div class="fleft orders-box">
          <table width="100%" cellpadding="10" cellspacing="2" >
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
							$items_tags_val = $row->category. ' - ' . $row->title;
							$items_tags[$items_tags_key] = $items_tags_val;
							$row->menu_number = ($row->menu_number == 10000 or $row->menu_number == 0) ? '' : '#'.$row->menu_number;
                ?>
                <tr class="MenuItem <?php echo $Class; ?> category_items_row_<?php echo $last_category_id; ?>" id="Record_<?php echo $row_id; ?>" catid="<?php echo $last_category_id; ?>" tabindex="<?php echo $i++; ?>" >
                  <td align="left">
                  	<table width="100%" cellpadding="2" cellspacing="2" border="0">
                    	<tr>
                        	<td valign="top" width="10%"><div id="Item_<?php echo $row_id; ?>_image"><a href="<?php echo $image_url; ?>" data-lightbox="roadtrip" data-title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>"><img title="<?php echo $row->title; ?>" src="<?php echo $image; ?>" alt="" class="menu-dish" /></a></div></td>
                          <td align="left" valign="top" width="90%">
                            <div class="title limit_title" id="Item_<?php echo $row_id; ?>_title"><span title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>"><?php echo $row->menu_number; ?> <?php echo $row->title; ?></span><?php if($row->popular==1) { ?>&nbsp;<img title="Popular" src="<?php echo base_url(); ?>images/1star.png" alt="Popular" /><?php } ?></div>
                            
                            <div class="description" id="Item_<?php echo $row_id; ?>_desc"><?php echo $row->description; ?></div>
                            	
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
                                
                            <div class="fright"><input <?php echo isset($items[$row_id]->quantity) ? 'checked' : ''; ?> type="checkbox" name="Items[]" value="<?php echo $row_id; ?>" id="Item_<?php echo $row_id; ?>" class="OrderItem" /><label for="Item_<?php echo $row_id; ?>"></label></div>
                            </td>
                            
                        </tr>
                        <tr>
                        	<td align="center"><span class="price" id="Item_<?php echo $row_id; ?>_price"><?php echo $row->price > 0 ? CURRENCY." ".number_format($row->price):'&nbsp;'; ?></span></td>
                            <td><input type="text" name="request_comment[<?php echo $row_id; ?>]" id="Item_<?php echo $row_id; ?>_request_comment" value="<?php echo isset($items[$row_id]->request_comment) ? $items[$row_id]->request_comment : ''; ?>" placeholder="Special Reqeust" onKeyUp="update_req_comm(this, '<?php echo $row_id; ?>');"  ></td>
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
                <input type="submit" name="SubmitBtn" id="SubmitBtn" value="Update Order" class="Button" style="background-color:#C30003;" />
                
                </div>
                
          </div>
          <div class="clear"></div>
            
            <div class="clear"></div>
            <input type="hidden" name="Action" id="Action" value="UpdateOrder">
            <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>">
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



