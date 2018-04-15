$(document).ready(function(){   
	$("select#selectCam").change(function(){
        if($( "select#selectCam option:selected" ).val() == "0"){
			$("select#camList").removeAttr('disabled');
		}
		else{
			$("select#camList").attr('disabled','disabled');
		}
    });

	$("button#addStreamSource").click(function(){
        $("#streamSourceList").append('<div class="form-group"><label for="email">Live stream URL:</label><input type="text" name="ls[]" class="form-control" placeholder="udp"/><a id="removeStreamSource" href="">Remove</a></div>');
		return false;
    });
	
	$("#streamSourceList").on( "click", "a#removeStreamSource", function(){
		$(this).parent().remove();
		return false;
    });
});