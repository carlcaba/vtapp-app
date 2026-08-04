const changeCity = function() {
	if($(this).val() == "") {
		return false;
	}
    $.ajax({
        url: 'core/actions/_load/__loadZonesByCity.php',
        type: 'POST',
        data: {
            id: $(this).find("option:selected").val()
        },
        dataType: "json",
        beforeSend: function (xhrObj) {
            let message = "<i class=\"fa fa-spinner fa-spin\"></i> Generando planilla...";
            noty = notify("", "dark", "", message, "", false);												
        },
        error: function (request, status, error) {
            let message = "An error has ocurred. Status: " + status + " Err: " + error;
            noty = notify("", "danger", "", message, "", false);												
        },
        success: function (data) {
            if(!data.success) {
                notify("", 'danger', "", data.message, "");
            }
            else {
                if(data.to_download) {
                    $("#frmDownloadTemplate").attr('action', data.file_path);
                }
                $('#frmDownloadTemplate').submit();
            }
        },
        complete: function() {
            noty.close();
        }
    });
	
};
$(document).ready(function() {
	$(".fnCityChange").unbind("change");
	$(".fnCityChange").bind("change",changeCity);
});
