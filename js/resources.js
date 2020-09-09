$(document).ready(function(e){
	$(".txtResource").bind("change paste", function() {
		var fld = $(this).data("hidden").substr(2);
		var lang = $("#cb" + fld).val();
		if(!lang)
			return false;
		var id = $(this).data("hidden") + "_" + lang;
		$("#" + id).val($(this).val());
	});
});

function changeResourceText(id,ctrl,lang,div) {
	var txt = "#txt" + ctrl;
	var hdn = "#hf" + ctrl + "_" + id;
	var regx = "#hfDocType_" + id + "_regex";
	var fld = "#cb" + ctrl;
	if($(hdn).val())
		$(txt).val($(hdn).val());
	else 
		$(txt).val("");
	$(fld).val(id);
	$("#" + div).html(lang);
	$("#txt" + ctrl).removeAttr("pattern");		
	if($(regx).val() != "") {
		$("#txt" + ctrl).attr("pattern", $(regx).val());
	}
	$("#txt" + ctrl).focus();
};

$(document).on('change','input[change2Upper]',function() {
	$(this).val(this.value.toUpperCase());
});	

// Serialize Object
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

if(typeof $.fn.dataTable !== "undefined") {
	$.fn.dataTable.render.ellipsis = function () {
		return function ( data, type, row ) {
			return type === 'display' && data.length > 15 ?
				data.substr( 0, 15 ) +'…' :
				data;
		}
	};
}

/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
function FormatNumber (data, n, x) {
	var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
	data = parseFloat(data);
    return data.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};

function rad(x) {
  return x * Math.PI / 180;
}

function getDistance(p1, p2) {
	// Earth’s mean radius in meter	
	var R = 6378137; 
	var dLat = rad(p2.lat - p1.lat);
	var dLong = rad(p2.lng - p1.lng);
	var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(rad(p1.lat)) * Math.cos(rad(p2.lat)) * Math.sin(dLong / 2) * Math.sin(dLong / 2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
	var d = R * c;
	// returns the distance in meter	
	return d;
}