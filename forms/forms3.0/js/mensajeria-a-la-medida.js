const msg = "¡Te has registrado con éxito!";
const validateStep1 = function() {
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
    if($("#txtConfirmEmail").val() == "") {
        toastr["error"]("La confirmación del correo electrónico es un dato requerido.");
        $("#txtConfirmEmail").focus();
        return false;
    }
    if($("#txtConfirmEmail").val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
        toastr["error"]("La confirmación del correo electrónico ingresado no es válido.");
        $("#txtConfirmEmail").focus();
        return false;
    }
    if($("#txtConfirmEmail").val() != $("#txtEmail").val()) {
        toastr["error"]("Los correos electrónicos ingresados no coinciden.");
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
    if($("#txtCompany").val() == "") {
        toastr["error"]("El nombre de la empresa es un dato requerido.");
        $("#txtCompany").focus();
        return false;
    }
    if($("#cbPackage").find("option:selected").val() == "") {
        toastr["error"]("Debes seleccionar la cantidad de envíos al mes.");
        $("#cbPackage").focus();
        return false;
    }
    return true;
};
$(document).ready(function() {
    $("#txtName").focus();
});