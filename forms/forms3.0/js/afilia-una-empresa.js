const msg = "¡Te has registrado con éxito!";
const validateStep1 = function() {
    if($("#txtCompany").val() == "") {
        toastr["error"]("El nombre de la empresa es un dato requerido.");
        $("#txtCompany").focus();
        return false;
    }
    if($("#txtId").val() == "") {
        toastr["error"]("El número de identificación tributaria es un dato requerido.");
        $("#txtId").focus();
        return false;
    }
    if($("#txtId").val().trim().match(/^\d+$/) == null) {
        toastr["error"]("El número de identificación tributaria no es válido.");
        $("#txtId").focus();
        return false;
    }
    if($("#txtName").val() == "") {
        toastr["error"]("El nombre del contacto es un dato requerido.");
        $("#txtName").focus();
        return false;
    }
    if($("#txtEmail").val() == "") {
        toastr["error"]("El correo electrónico es un dato requerido.");
        $("#txtEmail").focus();
        return false;
    }
    if($("#txtEmail").val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
        toastr["error"]("El correo electrónico ingresado no es válido.");
        $("#txtEmail").focus();
        return false;
    }
    if($("#txtPhone").val() == "") {
        toastr["error"]("El número telefónico es un dato requerido.");
        $("#txtPhone").focus();
        return false;
    }
    if($("#txtPhone").val().trim().match(/^\d+$/) == null) {
        toastr["error"]("El número telefónico no es válido.");
        $("#txtPhone").focus();
        return false;
    }
    return true;
};
const validateStep2 = function() {
    if($("#txtPartner").val() == "") {
        toastr["error"]("El nombre de la empresa aliada es un dato requerido.");
        $("#txtPartner").focus();
        return false;
    }
    if($("#txtNamePartner").val() == "") {
        toastr["error"]("El nombre del contacto de la empresa aliada es un dato requerido.");
        $("#txtNamePartner").focus();
        return false;
    }
    if($("#txtEmailPartner").val() == "") {
        toastr["error"]("El correo electrónico de la empresa aliada es un dato requerido.");
        $("#txtEmailPartner").focus();
        return false;
    }
    if($("#txtEmailPartner").val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
        toastr["error"]("El correo electrónico de la empresa aliada ingresado no es válido.");
        $("#txtEmailPartner").focus();
        return false;
    }
    if($("#txtPhonePartner").val() == "") {
        toastr["error"]("El número telefónico de la empresa aliada es un dato requerido.");
        $("#txtPhonePartner").focus();
        return false;
    }
    if($("#txtPhonePartner").val().trim().match(/^\d+$/) == null) {
        toastr["error"]("El número telefónico de la empresa aliada no es válido.");
        $("#txtPhonePartner").focus();
        return false;
    }
    return true;
};
$(document).ready(function() {
    $("#txtCompany").focus();
});