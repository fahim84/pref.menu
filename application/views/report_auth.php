<?php $this->load->view('header',$this->data); ?>
<section class="header">
	<?php $this->load->view('header_section',$this->data); ?>
    
    <div class="container circle-options">
        <ul>
            <li>
                <h2>Feedback Reports</h2>
                <form name="EnterFeedbackForm" id="EnterFeedbackForm" method="post" action="">
                    <input autocomplete="off" type="password" name="Password" id="Password" placeholder="Enter Password..." class="TextField" required="required" />
                    <input type="submit" name="EnterFeedbackBtn" id="EnterFeedbackBtn" value="Enter" class="Button" />
                </form>
            </li>
        </ul>
    </div>
</section>
<script>
$(document).on('submit', '#EnterFeedbackForm', function(){
	var Form = $('#EnterFeedbackForm').serialize();
	$('#EnterFeedbackForm input').attr('disabled', 'disabled');
	$('.notification').html('Loggin in. Please wait...').addClass('show-notification');
	$.post(WEB_URL+"report/auth/", Form, function(Data, textStatus){
		$(".notification").removeClass("show-notification");
		$('.notification').html(Data.Msg).addClass('show-notification');
		if(Data.Error=='0')
			window.location.href=Data.Url;
		$('#EnterFeedbackForm input').removeAttr('disabled');
	}, 'json');
	return false;
});
</script>
<?php
$this->load->view('clients',$this->data);
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);



