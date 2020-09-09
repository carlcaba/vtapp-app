<?
	require_once("core/classes/configuration.php");
	
	$conf = new configuration("FB_URL_JS_SDK");
	$script = $conf->verifyValue();
	$conf = new configuration("FB_APP_ID");
	$appId = $conf->verifyValue();	//Facebook App ID
	$conf = new configuration("FB_APP_SECRET");
	$appSecret = $conf->verifyValue();	//Facebook App Secret

	$bool = ($parent == "index") ? "true" : "false";
	$welcome = ($bool == "true") ? $_SESSION["FB_WELCOME"] : $_SESSION["FB_WELCOME_REGISTER"];
?>
	<script>
		window.fbAsyncInit = function() {
			var noty;
			$.ajax({
				url: "<?= $script ?>",
				dataType: "script",
				async: true,
				beforeSend: function() {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["CONNECTING_TO_FACEBOOK"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				error: function (request, status, error) {
					var message = "<?= $_SESSION["AN_ERROR_OCCURRED"] ?> <?= $_SESSION["STATUS"] ?>: " + status + " <?= $_SESSION["ERROR"] ?>: " + error;
					notify("", "danger", "", message, "", false);												
				},
				success: function (data) {
					noty.close();
					FB.init({
						appId            : '<?= $appId ?>',
						autoLogAppEvents : true,
						xfbml            : true,
						version          : 'v3.3'
					});
					FB.login(function(response) {
						if (response.authResponse) {
							FB.api('/me', {fields: 'first_name,last_name,email,id,name,address,hometown'}, function(response) {
								notify("", "success", "", "<?= $welcome ?>".replace("{0}",response.name), "");
								completeData(response);
								if(<?= $bool ?>)
									submitForm();
							});
						}
						else {
							notify("", "info", "", "<?= $_SESSION["FB_NOT_AUTHORIZED"] ?>", "");	
							completeData(null);
						}
					}, {
						scope: 'email', 
						return_scopes: true
					});
				}
			});
		};
		function completeData(response) {
			if(response != null) {
				$("#hfFBID").val(response.id);
				$("#hfFBFirstName").val(response.first_name);
				$("#hfFBLastName").val(response.last_name);
				$("#hfFBMail").val(response.email);
				$("#hfFBCity").val(response.hometown);
				$("#hfFBAddress").val(response.address);
				$("#txtPassword").val(response.id);
				$("#txtPassword").attr("disabled", response.id != "");
				$("#txtConfirmPassword").val(response.id);
				$("#txtConfirmPassword").attr("disabled", response.id != "");
				$("#txtEmail").val(response.email);
				var nameParts = response.email.split("@");
				$("#txtUser").val(nameParts.length == 2 ? nameParts[0] : "");
			}
			else {
				$("#hfFBID").val(null);
				$("#hfFBFirstName").val(null);
				$("#hfFBLastName").val(null);
				$("#hfFBMail").val(null);
				$("#hfFBCity").val(null);
				$("#hfFBAddress").val(null);
				$("#txtPassword").val(null);
				$("#txtPassword").attr("disabled", false);
				$("#txtConfirmPassword").val(null);
				$("#txtConfirmPassword").attr("disabled", false);
				$("#txtEmail").val(null);
				$("#txtUser").val(null);
			}
				
		}
	</script>
