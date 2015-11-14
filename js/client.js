var EmailError='';
$(document).ready(function(){
	
	var current_page = window.location.pathname.split('/').pop(); // get the filename from current url
	
	if(current_page == 'take-order.html' || current_page == 'customer-feedback.html')
	{
		
	}
	
	
	if(current_page == 'index.html' || current_page == 'about-us.html')
	{
		
	}
	
	
	
	/*$('#start_date, #end_date').datetimepicker({
		lang:'en',
		timepicker:false,
		format:'Y-m-d',
		formatDate:'Y-m-d'
	});*/
	
	
	$('#FromTime, #ToTime').datetimepicker({
		lang:'en',
		timepicker:true,
		datepicker:false,
		format:'h:i a',
		formatTime:'h:i a',
		step:30
	});
	
	
	
	
	
var startDateTextBox = $('#startDateTextBox');
var endDateTextBox = $('#endDateTextBox');

$.timepicker.datetimeRange(
	startDateTextBox,
	endDateTextBox,
	{
		minInterval: (1000*60*60), // 1hr
		dateFormat: 'yy-mm-dd', 
		timeFormat: 'HH:mm:ss',
		start: {}, // start picker options
		end: {} // end picker options					
	}
);	
	
		
});






$(document).on('click', '.OrderDone', function(){
	var oID = $(this).attr('id');
	$.post(WEB_URL+"action/OrderDone/"+oID+"/"+Math.random(), null, function(Data, textStatus){
		location.reload();
	});
});


















$(document).on('submit', '#FeedbackForm', function(){
	var Form = $('#FeedbackForm').serialize();
	$('#FeebackError').html('Please wait...');
	$('#FeedbackForm input').attr('disabled', 'disabled');
	$.post(WEB_URL+"action/StartFeedback/", Form, function(Data, textStatus){
		if(Data.Error=='0')
		{
			$('.notification').html(Data.Msg);
			ShowNotification();
			$('#FeebackError').html('&nbsp;');
			$('input[type="checkbox"]').removeAttr('checked');
		}
		else
			$('#FeebackError').html(Data.Msg);
		if(Data.Url!='')
			window.location.href=Data.Url;
		
		$('#FeedbackForm input').removeAttr('disabled');
	}, 'json');
	return false;
});














