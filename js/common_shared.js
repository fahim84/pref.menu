// JavaScript Document
// all common js code written here 

$(document).ready(function(){
	
	$('.login-box, .home-content, .our-clients').viewportChecker({
		classToAdd: 'visible animated fadeIn',
		offset: 100
	});
	
	$("#rMenuitems, #cMenuitems, #tlfMonth, #tltMonth, #business_type, #ComeAgain, #Category").selectOrDie();
	
});

$(document).on('click', '.res-nav ', function(){
	if($('.nav-box').hasClass('nav-box-show'))
		$('.nav-box').removeClass('nav-box-show');
	else
		$('.nav-box').addClass('nav-box-show');
});

function ShowNotification()
{
	$('.notification').addClass('show-notification');
	setTimeout('$(".notification").removeClass("show-notification");', 5000);
}


$(document).on('click', '.close-modal-box', function(){
	HideModalBox();
});

function GetBusinessRank()
{
	$('#BusinessRank').html('Calculating business rank...');
	$.post(WEB_URL+"report/load_rank", null, function(Data, textStatus){
		$('#BusinessRank').html(Data);
	});
}

function ReloadItemRateIframe(menu_id,start_date,end_date)
{
	$('#ShowItemRates').attr('src', WEB_URL+'report/item_graph/?menu_id='+menu_id+'&start_date='+start_date+'&end_date='+end_date);
}

function LoadItemReviews(menu_id,start_date,end_date)
{
	$.post(WEB_URL+"report/item_review/?menu_id="+menu_id+'&start_date='+start_date+'&end_date='+end_date, null, function(Data, textStatus){
		$('#ShowItemReview').html(Data);
	});
}

function ShowAllItemLineChart(start_date, end_date, graph_interval, items)
{
	var CustomerExpURL = WEB_URL+'report/items_graph/?graph_interval='+graph_interval+'&start_date='+start_date+'&end_date='+end_date+'&'+$.param({ 'items': items });
	$('#AllMenuFrame').attr('src', CustomerExpURL);
}

function GetTopLowRateItem(start_date, end_date, graph_interval)
{
	$.post(WEB_URL+"report/top_low_item/?start_date="+start_date+"&end_date="+end_date, null, function(Data, textStatus){
		
		var TopLowHTML = '<ul>';
		if(Data.top_item && Data.low_item)
		{
			TopLowHTML += '<li class="high-rate" title="'+Data.top_item.title+'">'+Data.top_item.title2+'<br /><span>'+Data.top_item.average_rate+'</span></li>';
			TopLowHTML += '<li class="low-rate" title="'+Data.low_item.title+'">'+Data.low_item.title2+'<br /><span>'+Data.low_item.average_rate+'</span></li>';
			
			//$('.sod_label').html(Data.top_item.title);
			
			
			//var CustomerExpURL = WEB_URL+'report-customer-experience.php?graph_interval='+graph_interval+'&start_date='+start_date+'&end_date='+end_date;
			//$('#CustomerExpFrame').attr('src', CustomerExpURL);
		}
		else
		{
			TopLowHTML += '<li class="high-rate">None</li><li class="low-rate">None</li>';
		}
		TopLowHTML += '</ul>';
		$('#ShowTopLowItem').html(TopLowHTML);

	}, 'json');
}





function ShowModalBox()
{
	var wHeight = $(window).height();
	var mHeight = $('.modal-box').height();
	var mTop = parseInt(wHeight) - parseInt(mHeight);
	mTop = parseInt(mTop) / parseInt(2);
	$('.modal-box').css('top', mTop+'px').addClass('show-modal-box');
	setTimeout("HideModalBox();", 7000);
}

function HideModalBox()
{
	$('.modal-box').removeClass('show-modal-box');
}

function isEmail(theStr) {
	var atIndex = theStr.indexOf('@');
 	var dotIndex = theStr.indexOf('.', atIndex);
 	var flag = true;
 	theSub = theStr.substring(0, dotIndex+1)
 	if ((atIndex < 1)||(atIndex != theStr.lastIndexOf('@'))||(dotIndex < atIndex + 2)||(theStr.length <= theSub.length)) 
 	{	 
 		flag = false; 
 	}
 	else 
	{ 
 		flag = true; 
 	}
 	return(flag);
}

function loginShow(toShow)
{
	$('#forgetBox, #loginBox').css('display', 'none');

	if(toShow==2)
		$('#forgetBox').css('display', '');
	else
		$('#loginBox').css('display', '');
}

function validate_password(password)
{
	
	//validate the length
	if ( password.length < 8 ) {
		return 'Password must be at least 8 character long.'
	} 
	
	//validate letter
	if ( ! password.match(/[A-z]/) ) {
		return 'Password must have at least one letter.'
	}
	
	//validate uppercase letter
	if ( ! password.match(/[A-Z]/) ) {
		return 'Password must have at least one capital letter.'
	} 
	
	//validate number
	if ( ! password.match(/\d/) ) {
		return 'Password must have at least one number.'
	} 
	
	return true;
}