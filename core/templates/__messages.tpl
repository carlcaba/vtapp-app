		<!--  notification JS
			============================================ -->
		<script src="js/plugins/notify/bootstrap-notify.min.js"></script>
		<script type="text/javascript">
			/*
			 * Notifications
			 */
			 var DirectChatVar;
			function notify(icon, type, title, message, url, dismiss, bckdrp) {
				var from = "top",
					align = "right",
					_return = false,
					bckdrpclss = "";
				if (dismiss === undefined)
					dismiss = true;
				if (type == "dark") {
					from = "top";
					align = "center";
					_return = true;
				}
				if (bckdrp === undefined)
					bckdrp = false;
				if (bckdrp)
					bckdrpclss = "backdrop";
				var opt = {
					message: message,
					icon: icon
				};
				var url = window.location.pathname;
				var filename = url.substring(url.lastIndexOf('/')+1);				
				var set = {
					z_index: 999999,
					type: type,
					allow_dismiss: dismiss,						
					placement: {
						from: from,
						align: align
					},
					template: '<div data-notify="container" class="col-11 col-sm-3 ' + bckdrpclss + ' alert alert-{0}" role="alert">' +
								'<span data-notify="icon"></span> ' +
								'<span data-notify="title">{1}</span> ' +
								'<span data-notify="message">{2}</span>' +
								'<div class="progress" data-notify="progressbar">' +
								'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
								'</div>' +
								'<a href="{3}" target="{4}" data-notify="url"></a>' +
								'</div>' 
				}
				if(type != "dark") {
					var datas = {
						User: "<?= $_SESSION["vtappcorp_userid"] ?>",
						Message: message,
						Source: filename,
						Type: type 
					};
					$.ajax({
						url: "core/actions/_save/__newNotification.php",
						data: { strModel: datas },
						dataType: "json",
						success: function(data){
							console.log("New notification");
						}
					});
				}
				if(_return)
					return $.notify(opt, set);
				else 
					$.notify(opt, set);
			}
			function sendDirectChat() {
				if(!$("#txtMESSAGE") && $("#txtMESSAGE").val() != "")
					return false;
				if(!$("#txtDESTINY"))
					if($("#txtDESTINY").val() != "") {
						if(!$("#hfLastChatUser") || $("#hfLastChatUser").val() != "")
							return false;
						$("#txtDESTINY").val($("#hfLastChatUser").val());
					}
				var datas = {
					txtMESSAGE: $("#txtMESSAGE").val(),
					txtDESTINY: $("#txtDESTINY").val()
				};
				$.ajax({
					url: "core/actions/_save/__newDirectChat.php",
					data: { strModel: datas },
					dataType: "json",
					success: function(data){
						$("#directChatMessages").load("core/actions/_load/__loadDirectChat.php", { idUserDestiny: $("#txtDESTINY").val() });
						$("#txtMESSAGE").val("");
						$("#txtMESSAGE").focus();
						DirectChatVar = setTimeout(checkDirectChat, <?= $_SESSION["DIRECT_CHAT_TIME"] ?>);
					}
				});
			}
			function checkDirectChat() {
				$.ajax({
					url: "core/actions/_load/__checkDirectChat.php",
					dataType: "json",
					success: function(data){
						if(data.success) {
							if(data.count > 0) {
								$("#directChatCount").html("<span class=\"badge badge-danger navbar-badge\">" + data.count + "</span>");
								$("#topAreaDirectChat").html(data.messages);
								if($("#directChatMessages")) {
									$("#directChatMessages").html(data.directchats);
									$("#directChatMessages").scrollTop();
								}
							}
						}
						DirectChatVar = setTimeout(checkDirectChat, <?= $_SESSION["DIRECT_CHAT_TIME"] ?>);
					}
				});				
			}
	
<?
	if(!empty($_SESSION["vtappcorp_user_message"])) {
?>
				notify("", "info", "", "<?= $_SESSION["vtappcorp_user_message"] ?>", "");							
<?
		unset($_SESSION["vtappcorp_user_message"]);
	}

	if(!empty($_SESSION["vtappcorp_user_alert"])) {
?>
				notify("", "danger", "", "<?= $_SESSION["vtappcorp_user_alert"] ?>", "");							
<?
		unset($_SESSION["vtappcorp_user_alert"]);
	}
?>		
			$(document).ready(function() {
				if($("#topAreaDirectChat").length > 0)
					DirectChatVar = setTimeout(checkDirectChat, <?= $_SESSION["DIRECT_CHAT_TIME"] ?>);
			});
		</script>
		
		