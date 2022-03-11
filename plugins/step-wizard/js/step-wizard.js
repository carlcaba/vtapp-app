$(document).ready(function () {

	var navListItems = $('div.setup-panel div a'),
		allWells = $('.setup-content'),
		allNextBtn = $('.nextBtn');		

	allWells.hide();

	navListItems.click(function (e) {
		e.preventDefault();
		var $target = $($(this).attr('href')),
			$item = $(this);

		if ($item.attr('disabled') != "disabled") {
			navListItems.removeClass('btn-success').addClass('btn-default');
			$item.removeClass('btn-default').addClass('btn-success');
			allWells.hide('fast');
			$target.show('slow');
			$target.find('input:eq(0)').focus();
		}
		else {
			notify("", "info", "", "Antes de continuar, por favor completa la información solicitada.", "");
		}
	});

	allNextBtn.click(function () {
			var curStep = $(this).closest(".setup-content"),
				curStepBtn = curStep.attr("id"),
				nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
				curInputs = curStep.find("input[type='text'],input[type='url']"),
				isValid = true,
				placeHolder = "",
				nextStepId = $("#" + curStepBtn).next().attr("id");
		if($("#hfAction").val() != "view") {
			$(".form-group").removeClass("has-error");
			for (var i = 0; i < curInputs.length; i++) {
				if (!curInputs[i].validity.valid) {
					isValid = false;
					$(curInputs[i]).closest(".form-group").addClass("has-error");
					placeHolder = curInputs[i].placeholder;
				}
			}
			if(curStepBtn == "step-1") {
				if($("#cbTBL_SYSTEM_USER_IDENTIFICATION").length > 0) {
					isValid &= $("#cbTBL_SYSTEM_USER_IDENTIFICATION").val() != "";
					if($("#cbTBL_SYSTEM_USER_IDENTIFICATION").val() == "")
						placeHolder = $("#idDocType").html();
				}
				if($("#cbTBL_CLIENT_IDENTIFICATION").length > 0) {
					isValid &= $("#cbTBL_CLIENT_IDENTIFICATION").val() != "";
					if($("#cbTBL_CLIENT_IDENTIFICATION").val() == "")
						placeHolder = $("#idDocType").html();
				}
			}
			else if(curStepBtn == "step-2") {
				isValid = $("#hfContinueStep").val() == "true";
				placeHolder = $("#txtDELIVER_CELLPHONE").attr("placeholder");
			}
			else if(nextStepId == "step-3") {
				$('#cbDeliverType').trigger("change");
				$("#cbDeliverTime").trigger("change");
				$("#cbClient").trigger("change");			
			}
			else if(nextStepId == "step-4") {
				$('#txtTOTAL_WIDTH').trigger("change");
				$("#txtTOTAL_HEIGHT").trigger("change");
				$("#txtTOTAL_LENGTH").trigger("change");			
				$("#txtTOTAL_WEIGHT").trigger("change");			
			}
			if (isValid) 
				nextStepWizard.removeAttr('disabled').trigger('click');
			else 
				notify("", "info", "", "Hay errores en la información, por favor revisa. (" + placeHolder + ")", "");
		}
		else 
			nextStepWizard.removeAttr('disabled').trigger('click');
	});

	$('div.setup-panel div a.btn-success').trigger('click');
});