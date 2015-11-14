<?php $this->load->view('header',$this->data); ?>
<?php
// get rating
$rating_result = $this->db->query("SELECT * FROM ratings_view WHERE id=$rating_id");
$rating = $rating_result->row_array();
$restaurant_id = $rating['restaurant_id'];

//my_var_dump($rating);

// get restaurant
$restaurant = $_SESSION[USER_LOGIN];

// get menu list
$menu_result = $this->db->query("SELECT *,(SELECT title FROM categories WHERE id=menus.category_id) category FROM menus WHERE restaurant_id=$restaurant_id AND id IN(SELECT menu_id FROM ratings_items WHERE rating_id=$rating_id)");
//my_var_dump($this->db->last_query());

// get rating items
$rating_item_result = $this->db->query("SELECT * FROM ratings_items WHERE rating_id=$rating_id");
foreach($rating_item_result->result_array() as $rating_item)
{
	$item_id = $rating_item['menu_id'];
	$items[$item_id] = array('rate' => $rating_item['rate'], 'item_comment' => $rating_item['item_comment']);
}

//my_var_dump($items);
?>

<section class="inner-page">
	<div class="container">
    	<div class="clear" style="height:20px;"></div>
        
		<form name="CustomerFeedback" id="CustomerFeedback" method="post" action="">
        <div class="fleft">
        <div class="survey-box2 margin-bottom">
            	<h1>Table : <?php echo $rating['table_number']; ?></h1>
        </div>
        <?php if($menu_result->num_rows()){ ?>
        <div class="survey-box margin-bottom">
        	<div class="item-heading"><h2>Menu Items</h2></div>
            <?php
				foreach($menu_result->result_array() as $menu)
				{
                    if($menu['image']=='')
                    {
                      $image = base_url().'images/'.'no-dish.png';
                    }
                    else
                    {
                      $image = base_url().UPLOADS.'/'.$menu['image'];
                      $image = base_url()."thumb.php?src=".$image."&w=100&h=100";
                    }
			?>
            <div class="sItem-box">
            	<div>
                	<div style="float:left;"><img src="<?php echo base_url().'images/'.$items[$menu['id']]['rate']; ?>star.png" alt="<?php echo $items[$menu['id']]['rate']; ?>" />&nbsp;</div>
                    <div class="item-comment">
                    	<p>&nbsp;<?php echo $items[$menu['id']]['item_comment']; ?></p>
                        
                    </div>
                </div>
                
            	<div>
                    <div class="margin-right-10" style="float:left;"><img title="<?php echo htmlspecialchars($menu['title'],ENT_QUOTES); ?>" src="<?php echo $image; ?>" alt="dish" class="menu-dish" /></div>
                    <div>
                	<div><?php echo $menu['category']; ?></div>
                    <div><strong>#<?php echo $menu['menu_number']; ?> <?php echo $menu['title']; ?></strong></div>
                    <div><?php echo $menu['description']; ?></div>
                    <div class="price"><?php echo CURRENCY." ".number_format($menu['price']); ?></div>
                    </div>
                </div>
                
                <div class="clear"></div>
            </div>
            <?php		
				}
			?>
            </div>
            <?php } ?>
            <div class="survey-box2 margin-bottom">
            	<h3>Which region do you live in?</h3>
                <?php echo $rating['region']; ?>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>How did you hear about us?</h3>
                <?php echo $rating['hear_about_us']; ?>
            </div>
            <div class="clear"></div>
        	<div class="survey-box2 margin-bottom">
            	<h3>Customer Experience <img src="<?php echo base_url().'images/'.$rating['customer_experience']; ?>star.png" alt="<?php echo $rating['customer_experience']; ?>" /></h3>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>Speed of order <img src="<?php echo base_url().'images/'.$rating['order_speed']; ?>star.png" alt="<?php echo $rating['order_speed']; ?>" /></h3>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>Would you come again in the near future?</h3>
                <?php echo $rating['come_again']; ?>
            </div>    
            
            <div class="survey-box2 margin-bottom">
            	<h3>Staff member who stood out</h3>
                <div>
                <?php
					if($rating['image']=='')
                    {
                      $image = base_url().'images/'.'no-dish.png';
					  $show_image = false;
                    }
                    else
                    {
					  $show_image = true;
                      $image = base_url().UPLOADS.'/'.$rating['image'];
                      $image = base_url()."thumb.php?src=".$image."&w=100&h=100";
                    }
					
					if($show_image)
					{
					?>
                <img src="<?php echo $image; ?>" alt="image" class="menu-dish" title="<?php echo $rating['staff_name']; echo $rating['designation']!='' ? " (".$rating['designation'].")" : ''; ?>" /><br>
                <?php } ?>
                <?php echo $rating['staff_name']; echo $rating['designation']!='' ? " (".$rating['designation'].")" : ''; ?>
                </div>
            </div>
            <?php if($rating['suggestion']){?>
            <div class="survey-box2 margin-bottom">
            	<h3>What would you like us to do differently?</h3>
                <?php echo $rating['suggestion']; ?>
            </div>
            <?php } ?>
            <?php if($rating['email']){?>
            <div class="survey-box2 margin-bottom">
            	<h3>Would you like to receive our special offers?</h3>
                <a href="mailto:<?php echo $rating['email']; ?>"><?php echo $rating['email']; ?></a>
            </div>
            <?php } ?>
            <div class="survey-btn margin-bottom" align="right">
            	<span id="FeebackError"></span>&nbsp;
            </div>
        </div>
        </form>
		
    	<div class="clear"></div>
    </div>
</section>
<?php
$this->load->view('footer',$this->data);
?>