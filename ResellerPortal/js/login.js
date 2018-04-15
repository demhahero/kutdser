$(document).ready(function(){   
	$("button.login_button").click(function(){
        $("form.login-form").submit();
    });
	$('.message a').click(function(){
		$('form').animate({height: "toggle", opacity: "toggle"}, "slow");
	});
});