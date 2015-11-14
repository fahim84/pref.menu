<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page" style="min-height:500px;">
	<div class="container">
    	<div class="content-page register-now menu-page">
        <h2><?php echo $MenuBtn; ?></h2>
	
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
    
        <form name="AddMenuForm" id="AddMenuForm" method="post" action="<?php echo base_url(); ?>restaurant/menu" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
            <?php if($id > 0){ ?>
            <input type="hidden" name="ActionType" id="ActionType" value="Edit" />
            <?php } ?>
        	<div class="fleft menu-field" id="AddCategory">
            	<select name="category_id" id="category_id" required class="DropDown">
                	<option value="">Select Category</option>
                    <?php
						foreach($categories->result() as $cat)
						{
							$Selected = $cat->id == @$menu->category_id ? ' selected ' : '';
							?>
				  <option<?php echo $Selected; ?> value="<?php echo $cat->id; ?>"><?php echo $cat->title; ?></option>
				  <?php		
						}
					?>
                    <option value="0">Add New...</option>
                </select>
            </div>
            
            <div class="fleft menu-field">
            	<input type="text" name="title" id="title" placeholder="Item Name" class="TextField" required="required" value="<?php echo @$menu->title; ?>" />
            </div>
            <div class="fleft menu-field">
            	<input type="text" name="description" id="description" placeholder="Description" class="TextField" value="<?php echo @$menu->description; ?>" />
            </div>
            <div class="clear"></div>
            <div class="fleft menu-field">
            	<input type="number" name="price" id="price" placeholder="Price" class="TextField" required="required" value="<?php echo @$menu->price; ?>" />
            </div>
            <div class="fleft menu-field">
            	<input type="number" name="menu_number" id="menu_number" placeholder="Menu Number" class="TextField" required="required" value="<?php echo @$menu->menu_number; ?>" />
            </div>
            <div class="clear"></div>
            <div class="menu-field">
            <input name="image" type="file" id="image" accept="image/*" />
            <?php
			if(@$menu->image != '')
			{
				$image = base_url().UPLOADS.'/'.$menu->image;
				$image = base_url()."thumb.php?src=".$image."&w=100&h=100";
				echo '<img src="'.$image.'" class="menu-dish" /><br />';
				echo $menu->image; 
				echo "<label><input name=\"delete_old\" type=\"checkbox\" id=\"delete_old\" value=\"1\" />Delete</label>
				<input name=\"oldfile\" type=\"hidden\" value=\"$menu->image\" /><br>";
			}
			?>
            </div>
            
            <div class="menu-field">
            <input name="popular" type="checkbox" id="popular" value="1" <?php echo @$menu->popular == 1 ? 'checked="checked"' : ''; ?> /><label for="popular">Add in Most Popular</label>
            </div>
            <div class="clear"></div>
            <div class="menu-field">
              <input type="submit" name="AddMenuBtn" id="AddMenuBtn" class="Button" value="<?php echo $MenuBtn; ?>" />
            </div>
            <div class="clear"></div>
        </form>
        <h2>Our Menu</h2>
        <div id="ShowMenu">
        	
            <table width="100%" cellpadding="5" cellspacing="2">
            <!--<tr class="MenuHead">
                <td width="5%" align="center">#</td>
                <td width="5%" align="center" >Image</td>
                <td align="left">Item Description</td>
            </tr>-->
            <?php
                
                if($menu_query->num_rows())
                {
                    foreach($menu_query->result() as $row)
                    {
                        $row_id = $row->id;
                        if($row->category_id != @$last_category_id)
                        {
                            $last_category_id = $row->category_id;
                            $Class='BgTwo';
            ?>
            <tr class="MenuCategory cursor-pointer" catrowid="<?php echo $last_category_id; ?>" >
                <td colspan="2"><div class="fleft"><?php echo $row->category; ?></div>
                <div class="fright"><a title="Delete" data-record="<?php echo $last_category_id; ?>" class="delete-category"><img src="<?php echo base_url(); ?>images/delete.png" alt="Delete" /></a>&nbsp;</div>
                </td>
            </tr>
            <?php
                        }
						
                        $Class = $Class=='BgTwo' ? 'BgOne' : 'BgTwo';
                        
                        $image_url = $row->image == '' ? base_url().'images/no-dish.png' : base_url().UPLOADS.'/'.$row->image;
						$image = base_url()."thumb.php?src=".$image_url."&w=100&h=100";
            ?>
            <tr class="MenuItem <?php echo $Class; ?> category_items_row_<?php echo $last_category_id; ?>" id="Record_<?php echo $row_id; ?>" catid="<?php echo $last_category_id; ?>">
                <td width="5%" align="center"><a href="<?php echo $image_url; ?>" data-lightbox="roadtrip" data-title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>"><img title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish" /></a></td>
                <td align="left"><div class="title">#<?php echo $row->menu_number; ?> <?php echo $row->title; ?><?php if($row->popular) { ?>&nbsp;<img title="Popular" src="<?php echo base_url(); ?>images/1star.png" alt="popular" /><?php } ?></div>
                <div class="description"><?php echo $row->description; ?></div>
                <div class="price">Price: <?php echo CURRENCY." ".number_format($row->price); ?></div>
                
                
                    <a title="Edit" href="<?php echo base_url(); ?>restaurant/menu?ActionType=Edit&id=<?php echo $row_id; ?>"><img src="<?php echo base_url(); ?>images/edit.png" alt="Edit" /></a>
                    <a title="Delete" data-record="<?php echo $row_id; ?>" class="delete-menu"><img src="<?php echo base_url(); ?>images/delete.png" alt="Delete" /></a>
                </td>
                
            </tr>
            <?php	
                    }
                }
                else
                {
            ?>
            <tr>
                <td colspan="2" align="center">No item found</td>
            </tr>
            <?php		
                }
            ?>
        </table>
            
            
        </div>
        </div>
        <div class="clear"></div>
    </div>
</section>
<script>
$(document).on('change', '#category_id', function(){
	if($(this).val() == 0)
	{
		$('#AddCategory').html('<input type="text" name="new_category" id="new_category" placeholder="Category" class="TextField" required="required" value="" />');
	}
});

$(document).on('click', '.delete-menu', function(){
	var mID = $(this).attr('data-record');
	var Confirm = confirm("All ratings and order records will also be deleted.");
	if(Confirm==true)
	{
		$.post(WEB_URL+"restaurant/delete_menu/"+mID, null, function(Data, textStatus){
			if(Data.Error=='0')
			{
				$('#Record_'+mID).fadeOut().remove();
				window.location.href=WEB_URL+'restaurant/menu';
			}
		}, 'json');
	}
});

$(document).on('click', '.delete-category', function(){
	var cID = $(this).attr('data-record');
	var Confirm = confirm("All items, ratings and orders will also be deleted.");
	if(Confirm==true)
	{
		$.post(WEB_URL+"restaurant/delete_category/"+cID, null, function(Data, textStatus){
			if(Data.Error=='0')
			{
				window.location.href=WEB_URL+'restaurant/menu';
			}
		}, 'json');
	}
});


$( ".MenuCategory" ).click(function() { 

	previous_visible_category_id = $('.MenuItem:visible').attr('catid');
	category_id = $(this).closest('tr').attr('catrowid');
	
	// Hide the previous visible element
	if(previous_visible_category_id != category_id)
	{
		$(".category_items_row_"+previous_visible_category_id).hide();
	}
	
	// Toggle current clicked element
	$( ".category_items_row_"+category_id ).toggle( "slow", function() {
	// Animation complete.
	});
});

// hide all MenuItem 
$('.MenuItem').hide(); 

$(document).on('submit', '#AddMenuForm', function(){
	var Form = $('#AddMenuForm').serialize();
	var Type = $('#ActionType').val();
	$('#MenuMsg').html("Please wait...");
	$('#AddMenuBtn').attr('disabled', 'disabled').addClass('disabled');
});
</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




