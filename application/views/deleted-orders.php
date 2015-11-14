<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page reports-page" style="min-height:400px;">
	<div class="container">
    	<div class="content-page register-now menu-page">
        	<div class="fleft"><h2>Deleted Orders</h2></div>
        	<div class="fright"><?php client_logo(); ?></div>
        	<div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $this->load->view('report-tabs',$this->data); ?>
    <div class="container">
		
       	  
          
          
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
    
    
    
		<?php
			if($orders_query->num_rows())
            {
        ?>
        
        
          
          
          <form name="OrderTablesList" id="OrderTablesList" method="post" action="">
          	<input type="hidden" name="SelectedOrder" id="SelectedOrder" />
            <div id="action_link" style="padding-top:20px;padding-bottom:20px;"><a class="Button" href="#_" onClick="delete_selected();" >Delete Selected (<span id="selected_ids_on_this_page">0</span>) Items</a></div>
            <label><input type="checkbox" name="select_all" id="select_all" value="1">Select All</label>
          	    <?php
					$TablesArray = get_tables();
					foreach($orders_query->result() as $row)
					{
						$order_id = $row->id;
						$Table = $TablesArray[$row->table_number];
						$Customer = $row->customer_number;
						
						$Class = @$Class=='BgTwo' ? 'BgOne' : 'BgTwo';
						
						$order_items_query = $this->order_model->get_order_items($order_id);
						//my_var_dump($item_result->num_rows);
				?>
                <table width="100%" cellpadding="5" cellspacing="2">
            	<tr class="MenuCategory">
                    <td><div class="title"><input onchange="select_single(<?php echo $order_id; ?>);" class="selected_ids" type="checkbox" name="selected_ids[]" id="selected_id_<?php echo $order_id; ?>" value="<?php echo $order_id; ?>"> Customer <?php echo $Customer; ?> @ <?php echo $Table; ?></div></td>
                </tr>
                
                <tr class="MenuItem <?php echo $Class; ?>">
                	<td>
                    <?php if($order_items_query->num_rows()){ ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
					<?php foreach($order_items_query->result() as $item)
					{
						$image_url = $item->image == '' ? base_url().'images/no-dish.png' : base_url().UPLOADS.'/'.$item->image;
						$image = base_url()."thumb.php?src=".$image_url."&w=100&h=100";
						
						
						?>
                        <tr>
                          <td width="5%"><div style="padding-right:10px;"><a href="<?php echo $image_url; ?>" data-lightbox="roadtrip" data-title="<?php echo htmlspecialchars($item->title,ENT_QUOTES); ?>"><img title="<?php echo $item->title; ?>" src="<?php echo $image; ?>" alt="image" class="menu-dish menu-dish-flexible" /></a></div></td>
                          <td valign="top">
                          <div class="title limit_title" title="<?php echo $item->title; ?>">#<?php echo $item->menu_number; ?> <?php echo $item->title; ?>&nbsp;<?php if($item->popular==1) { ?>&nbsp;<img title="Popular" src="<?php echo base_url(); ?>images/1star.png" alt="Popular" /><?php } ?></div>
                          <div class="description"><?php echo $item->description; ?></div>
                          <div class="description"><?php echo $item->request_comment; ?></div>
                          <div class="description price">AED <?php echo $item->price; ?> x <?php echo $item->quantity; ?></div></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    </table>
                    <?php }else{ ?>
                    <div class="alert alert-error">No Item found in this order.</div>
                    <?php } ?>
                    <div><?php echo date('jS M, Y - g:i a',strtotime($row->date_created)); ?></div>
                    <!--<a href="actions.php?order_id=<?php echo $order_id; ?>&Action=DeleteOrder" onclick="return confirm('Are you sure you want to delete?');">Delete</a>-->
					</td>
                </tr>	
                
			</table>
            <br>
                <?php		
					}
				?>
			
		</form>
		<?php		
            }else{ 
        ?>
        	<div class="alert alert-success">Record not found</div>
        <?php } ?>
        <div class="clear"></div>
	</div>
</section>
<script>
$('#select_all').click(function() {
    var c = this.checked;
    //$(':checkbox').prop('checked',c);
	$('.selected_ids:checkbox').prop('checked',c);
	
	select_checkbox_actions();
});
function select_checkbox_actions()
{
	// get selected ids on this page
	var selected_ids_on_this_page = $("input[name='selected_ids[]']:checked").map(function(){
		   return $(this).val();
		}).get();
	
	$('#selected_ids_on_this_page').html(Object.keys(selected_ids_on_this_page).length);
	
	if(Object.keys(selected_ids_on_this_page).length)
	{
		$('#action_link').show();
	}
	else
	{
		$('#action_link').hide();
	}
}
function select_single(selected_id)
{
	select_checkbox_actions();
}

function delete_selected()
{
	if(confirm('Are you sure you want to delete selected items?')){}
	else return false;
	
	var selected_ids_on_this_page = $("input[name='selected_ids[]']:checked").map(function(){
		   return $(this).val();
		}).get();
		
	$.ajax({
		dataType: 'html',
		type: "POST",
		url: "<?php echo base_url(); ?>order/delete_selected_orders/",
		beforeSend: function() {  },
		complete: function() {  },
		data: { 'selected_ids': selected_ids_on_this_page,'hard_delete' : 1}
	})
	.done(function( response ){
		
		window.parent.location='<?php echo base_url(); ?>report/deleted_orders';
		
	});
}

select_checkbox_actions();
</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




