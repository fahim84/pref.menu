// JavaScript Document
// New Order, Edit Order and Customer Feedback page common code written here

$(document).ready(function(){
		
	// Code for floating button while scrolling start.....
	// page take-order.html
	var s = $("#sticker");
	var pos = s.position();
	
	$(window).scroll(function() {
		browser_width = $( window ).width();
		survey_box_height = $(".survey-box").height();
		survey_box_width = $(".survey-box").width();
		var windowpos = $(window).scrollTop();
		
		
		gap_height = browser_width <= 1180 ? windowpos+400 : windowpos-survey_box_height;
		
		//s.html("Survey-Box: "+survey_box_height+ " <br>Distance from top:" + pos.top + "<br />Scroll position: " + windowpos + "<br>Browser Width: " + browser_width);
		
		if (gap_height >= pos.top) {
			
			s.addClass("stick");
			s.css('width',survey_box_width);
		} else {
			s.removeClass("stick"); 
		}
	});
	// Code for floating button while scrolling end.......
	
	// Edit Order Form auto populate order summary on load
	var order_items_selected = $("input[name='Items[]']:checked").map(function(){
		   return $(this).val();
		}).get();
	
	$.each(order_items_selected,function(index,item_id){
		//console.log(item_id);
		populate_order_summary_box(item_id);
	});
	
});

$(document).on('click', '#overwrite_cancel', function(){
	$('#TakOrderError').html('&nbsp;');
	$('#SubmitBtn').show();
	$('#start_feedback_button').show();
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

$(document).on('click', '.add-amount', function(){
	var ItemID = $(this).attr('data-id');
	var CurrentValue = $('#'+ItemID+'_selected input[name="selecteditemsQuantity[]"]').val();
	var NewValue = parseInt(CurrentValue) + parseInt(1);
	if($('#'+ItemID+'_selected').length)
	{
		$('#'+ItemID+'_selected input[name="selecteditemsQuantity[]"]').val(NewValue);
		$('#'+ItemID+'_quantity_box').html(NewValue);
		$('#'+ItemID+'_quantity_box_summary').html(NewValue);
	}
});

$(document).on('click', '.minus-amount', function(){
	var ItemID = $(this).attr('data-id');
	if($('#'+ItemID+'_selected').length)
	{
		var CurrentValue = $('#'+ItemID+'_selected input[name="selecteditemsQuantity[]"]').val();
		if(CurrentValue>1)
		{
			var NewValue = parseInt(CurrentValue) - parseInt(1);
			$('#'+ItemID+'_selected input[name="selecteditemsQuantity[]"]').val(NewValue);
			$('#'+ItemID+'_quantity_box').html(NewValue);
			$('#'+ItemID+'_quantity_box_summary').html(NewValue);
		}
	}
});

$(document).on('click', '.OrderItem', function(){
	var ItemID = $(this).val();
	
	if($(this).is(':checked'))
	{
		/*$('#Amount_'+ItemID+' li a').css('opacity', '1');
		var ItemImage = $('#'+ItemID+'_image').html();
		var Quantity = $('#'+ItemID+'_quantity_box').html();
		var ItemName = $('#'+ItemID+'_title').html();
		var ItemDesc = $('#'+ItemID+'_desc').html();
		var ItemPrice = $('#'+ItemID+'_price').html();
		var ItemRequestComment = $('#'+ItemID+'_request_comment').val();
		$('#SelectItemMsg').hide();
		var HTML = '<li id="'+ItemID+'_selected">';
			HTML += '<input type="hidden" name="selecteditemsQuantity[]" value="'+Quantity+'" /><input type="hidden" name="selecteditems[]" value="'+ItemID+'" />';
			HTML += '<div class="fleft" style="padding-right:10px;">'+ItemImage+'</div>';
			HTML += '<div ><div class="title limit_title">'+ItemName+' x <span class="'+ItemID+'_dAmount">'+Quantity+'</span></div><div class="description">'+ItemDesc+'</div><div class="description price" id="'+ItemID+'_drequest_comment">'+ItemRequestComment+'</div><div class="fleft price">'+ItemPrice+' x <span class="'+ItemID+'_dAmount">'+Quantity+'</span></div></div>';
			HTML += '<div><a title="Delete Item" class="fright removeSelected" id="'+ItemID+'">X</a></div>';
			HTML += '<div class="clear"></div>';
			HTML += '';
		HTML += '</li>';
		$('#ShowSelectedItems').append(HTML);*/
		populate_order_summary_box(ItemID);
	}
	else
	{
		$('#Amount_Item_'+ItemID+' a').css('opacity', '0.4');
		$('#Item_'+ItemID+'_selected').remove();
	}
});

$(document).on('submit', '#TakeOrderForm', function(){
	var Form = $('#TakeOrderForm').serialize();
	$('#TakOrderError').html('Please wait...');
	$.post(WEB_URL+"order/take_order", Form, function(Data, textStatus){
			if(Data.Error==0)
			{
				$('.notification').html(Data.Msg);
				ShowNotification();
				$('#TakOrderError').html('&nbsp;');
				
				$('input[type="checkbox"]').removeAttr('checked');
				$('#ShowSelectedItems li').remove();
				$('#SelectItemMsg').css('display', '');
				$('.DisplayQuantities').html('1');
				$('.order-amounts li a').removeAttr('style');
				$('.special_comment_textbox').val('');
				$('#customerid').val('');
				$('#SubmitBtn').show();
				// hide all MenuItem 
				$('.MenuItem').hide();
				$('#keyword').val('');
				$('#customerid').focus();
				window.scrollTo(0, 0); // scroll top of the page.
				
				if(Data.temporary_pending_orders_count > 0 && Data.table_number > 0)
				{
					check_temporary_pending_orders(Data.table_number);
				}
			}
			else
			{
				if(Data.action=='hide confirm order button')
				{
					$('#SubmitBtn').hide();
					$('#start_feedback_button').hide();
				}
				$('#TakOrderError').html(Data.Msg);
			}
			
			//if(Data.Url!=''){ window.location.href=Data.Url; }
			
	}, 'json');
	return false;
});

$(document).on('click', '.removeSelected', function(){
	var ItemID = $(this).attr('id');
	$('input[id="'+ItemID+'"]').removeAttr('checked');
	$('#'+ItemID+'_selected').remove();
});

function update_req_comm(element,index_id)
{
	$('#Item_'+index_id+'_drequest_comment').val(element.value);
}

function update_req_comm_duplicate(element,index_id)
{
	$('#Item_'+index_id+'_request_comment').val(element.value);
}

function populate_order_summary_box(ItemID)
{
	item_id = ItemID;
	ItemID = 'Item_'+ItemID;
	$('#Amount_'+ItemID+' a').css('opacity', '1');
	var ItemImage = $('#'+ItemID+'_image').html();
	var Quantity = $('#'+ItemID+'_quantity_box').html();
	var ItemName = $('#'+ItemID+'_title').html();
	var ItemDesc = $('#'+ItemID+'_desc').html();
	var ItemPrice = $('#'+ItemID+'_price').html();
	var ItemRequestComment = $('#'+ItemID+'_request_comment').val();
	$('#SelectItemMsg').hide();
	var HTML = '<li id="'+ItemID+'_selected">';
		HTML += '<input type="hidden" name="selecteditemsQuantity[]" value="'+Quantity+'" /><input type="hidden" name="selecteditems[]" value="'+ItemID+'" />';
		HTML += '<div class="fleft" style="padding-right:10px;">'+ItemImage+'</div>';
		HTML += '<div class="title limit_title">'+ItemName+'</div>';
		HTML += '<div class="description">'+ItemDesc+'</div>';
		HTML += '<table border="0" align="left" cellpadding="0" cellspacing="0"><tbody><tr>';
		HTML += '<td><a class="minus-amount" data-id="'+ItemID+'"><img title="Minus" src="'+WEB_URL+'images/minus.png" alt="-"></a></td>';
		HTML += '<td valign="middle"><span id="'+ItemID+'_quantity_box_summary" class="DisplayQuantities">'+Quantity+'</span></td>';
		HTML += '<td><a class="add-amount" data-id="'+ItemID+'"><img title="Plus" src="'+WEB_URL+'images/plus.png" alt="+"></a></td>';
		HTML += '</tr></tbody></table>';
		HTML += '<div class="clear price fleft"><div style="width:78px;" class="fleft">'+ItemPrice+'</div> &nbsp; <input type="text" name="request_comment_summary_box[]" id="'+ItemID+'_drequest_comment" value="'+ItemRequestComment+'" placeholder="Special Reqeust" onKeyUp="update_req_comm_duplicate(this, \''+item_id+'\');"  ></div>';
		HTML += '<div><a title="Delete Item" class="fright removeSelected" id="'+ItemID+'">X</a></div>';
		HTML += '<div class="clear"></div>';
		HTML += '';
	HTML += '</li>';
	$('#ShowSelectedItems').append(HTML);
}

function search_item()
{
	keyword = $( '#keyword' ).val();
	
	$.each(items_tags,function(index,value){
		
		//console.log(value);
		if(value == keyword)
		{
			chunks = index.split('-');
			category_id = chunks[0];
			item_id = chunks[1];
			
			display_search_result(category_id,item_id);
			return false;
		}
		
	});
	
	
}
function display_search_result(category_id,item_id)
{
	// hide all MenuItem 
	$('.MenuItem').hide(); 
	
	previous_visible_category_id = $('.MenuItem:visible').attr('catid');
	
	// Hide the previous visible element
	$(".category_items_row_"+previous_visible_category_id).hide();
	
	// Toggle current clicked element
	$( ".category_items_row_"+category_id ).toggle( "slow", function() {
	// Animation complete.
	});
	
	$('#Record_'+item_id).focus();
	$('#Record_'+item_id).effect("pulsate", {}, 1000);
}

