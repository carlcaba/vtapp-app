$.fn.serializeObject = function() { 
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name] !== undefined) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} 
		else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};
$(document).ready(function() {
    var current_fs, next_fs, previous_fs; 
    var opacity;
    $(".next").click(function() {
        let doTheCall = false;
        current_fs = $(this).parent();
        next_fs = $(this).parent().next();
        if(current_fs.attr("id") == "step1") {
            if(!validateStep1()) {
                return false;
            }
        }
        else if(current_fs.attr("id") == "step2") {
            doTheCall = true;
            if(!validateStep2()) {
                return false;
            }
        }
        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
        next_fs.show(); 
        current_fs.animate({opacity: 0}, {
            step: function(now) {
                opacity = 1 - now;
                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                next_fs.css({'opacity': opacity});
            }, 
            duration: 600
        });
        if(doTheCall) {
            let $frm = $("form:first"); 
            let dataObj = $frm.serializeObject();
            if(dataObj.hasOwnProperty("hfType")) {
                dataObj["hfType"] = $("#hfType").val();
            }
            let datas = JSON.stringify(dataObj);
            /*
            //Debuggin purposes
            console.log(dataObj);
            $("#divProgress").addClass("invisible");
            $("#h2Title").html(msg);
            $("#divEnd").removeClass("invisible");
            $("#h5End").removeClass("invisible");
            return false;
            */
            $.ajax({
                url: 'http://ubioapp.logicaestudio.com/webservices/__registerUser.php',
                type: 'POST',
                dataType: "json",
                data: { 
                    strModel: datas
                },
                beforeSend: function (xhrObj) {
                    $("#iconProgress").addClass("fa-spin");
                },
                error: function (request, status, error) {
                    var message = "Ha ocurrido un error interno. Status: " + status + " Err: " + error;
                    toastr["error"](message);												
                },
                success: function (data) {
                    if(!data.success) {
                        $("#iconProgress").removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-triangle-exclamation");
                        toastr["error"](data.message);												
                        $("#h2Title").html(data.message);
                    }
                    else {
                        $("#divProgress").addClass("invisible");
                        $("#h2Title").html(msg);
                        $("#divEnd").removeClass("invisible");
                        $("#h5End").removeClass("invisible");
                    }
                }
            });

        }
    });

    $(".previous").click(function() {
        current_fs = $(this).parent();
        previous_fs = $(this).parent().prev();
        $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
        previous_fs.show();
        current_fs.animate({opacity: 0}, {
            step: function(now) {
                opacity = 1 - now;
                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                previous_fs.css({'opacity': opacity});
            }, 
            duration: 600
        });
    });
	$(".change2Upper").on("input propertychange paste", function() {
		$(this).val($(this).val().toUpperCase());
	});
	$(".change2Lower").on("input propertychange paste", function() {
		$(this).val($(this).val().toLowerCase());
	});
});
