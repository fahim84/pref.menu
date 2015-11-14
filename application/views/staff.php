<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page">
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
    
        <form action="<?php echo base_url(); ?>restaurant/staff" method="post" enctype="multipart/form-data" name="AddStaffForm" id="AddStaffForm">
        	<input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
            <?php if($id > 0){ ?>
            <input type="hidden" name="ActionType" id="ActionType" value="Edit" />
            <?php } ?>
        	<div class="fleft menu-field">
            	<input type="text" name="title" id="title" placeholder="Staff Member's Name" class="TextField" required="required" value="<?php echo @$staff->title; ?>" />
            </div>
            <div class="fleft menu-field">
            	<input type="text" name="designation" id="designation" placeholder="Role" class="TextField" required="required" value="<?php echo @$staff->designation; ?>" />
            </div>
            
            <div class="clear"></div>
            <div class="fleft menu-field">
            <input name="image" type="file" id="image" accept="image/*" />
            <?php
			if(@$staff->image != '')
			{
				$image = base_url().UPLOADS.'/'.$staff->image;
				$image = base_url()."thumb.php?src=".$image."&w=100&h=100";
				echo '<img src="'.$image.'" class="menu-dish" /><br />';
				echo $staff->image; 
				echo "<label><input name=\"delete_old\" type=\"checkbox\" id=\"delete_old\" value=\"1\" />Delete</label>
				<input name=\"oldfile\" type=\"hidden\" value=\"$staff->image\" /><br>";
			}
			?>
            </div>
            <div class="clear"></div>
            <div class="fleft menu-field"><input type="submit" name="AddMenuBtn" id="AddMenuBtn" class="Button" value="<?php echo $MenuBtn; ?>" /></div>
            
            <span id="MenuMsg">&nbsp;</span>
        </form>
        <div class="clear"></div>
        <h2>Staff Members</h2>
        <div id="ShowMember">
        	
            <table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#c6c9cd" style="border-collapse:collapse;">
                <tr class="MenuHead">
                    <td align="center" width="5%" >Image</td>
                    <td align="left">Name / Role</td>
                </tr>
                <?php
                    if($staff_query->num_rows())
                    {
                        foreach($staff_query->result() as $row)
                    	{
                            $row_id = $row->id;
							$Class='BgTwo';
							$Class = $Class=='BgTwo' ? 'BgOne' : 'BgTwo';
                        
							$image_url = $row->image == '' ? base_url().'images/no-dish.png' : base_url().UPLOADS.'/'.$row->image;
							$image = base_url()."thumb.php?src=".$image_url."&w=100&h=100";
                            
                ?>
                <tr class="MenuItem <?php echo $Class; ?>" id="Record_<?php echo $row_id; ?>">
                    <td align="center"><a href="<?php echo $image_url; ?>" data-lightbox="roadtrip" data-title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>"><img title="<?php echo htmlspecialchars($row->title,ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish" /></a></td>
                    <td align="left"><div class="title"><?php echo $row->title; ?></div>
                    <div class="description"><?php echo $row->designation; ?></div>
                    <br>
                    
                    <a title="Edit" href="<?php echo base_url(); ?>restaurant/staff?ActionType=Edit&id=<?php echo $row_id; ?>"><img src="<?php echo base_url(); ?>images/edit.png" alt="Edit" /></a>
                    <a title="Delete" data-record="<?php echo $row_id; ?>" class="delete-staff"><img src="<?php echo base_url(); ?>images/delete.png" alt="Delete" /></a>
                    
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
$(document).on('click', '.delete-staff', function(){
	var mID = $(this).attr('data-record');
	var Confirm = confirm("All ratings records will also be deleted.");
	if(Confirm==true)
	{
		$.post(WEB_URL+"restaurant/delete_staff/"+mID, null, function(Data, textStatus){
			if(Data.Error=='0')
			{
				$('#Record_'+mID).fadeOut().remove();
				window.location.href=WEB_URL+'restaurant/staff';
			}
		}, 'json');
	}
});
</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




