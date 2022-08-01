<?
	require_once("core/classes/users.php");
	$contact = new users();
?>
									<!-- Contacts are loaded here -->
									<div class="direct-chat-contacts">
										<ul class="contacts-list" id="ulContactList">
<?
	echo $contact->showContactPanel();
?>
										</ul>
										<!-- /.contacts-list -->
									</div>
<script>
	function showDirectChat(id) {
		if(localStorage.getItem('UBIODC_ACTIVATED') != null && localStorage.getItem('UBIODC_ACTIVATED') != "on") {
			notify("", 'danger', "", "<i class=\"fa fa-triangle-exclamation\"></i> <?= $_SESSION["DIRECTCHAT_NOT_AVAILABLE"] ?>", "");				
			return false;
		}
		if($("#li_" + id).css("background-color") == "#EBEDEC")
			return false;
		$("#directChatForm").load('core/actions/_load/__loadShowChat.php',
			{ idUser: id }, 
			function() {
				var plc = "<?= $_SESSION["TYPE_MESSAGE"] ?>";
				$('[id^=li_]').css("background-color", "");
				$("#li_" + id).css("background-color", "#EBEDEC");
				localStorage.setItem('UBIODC_TO', id);
				$("#txtMESSAGE").attr("placeholder", plc + $("#spanName_" + id).text());
				$("#txtMESSAGE").focus();
			}
		);
	}
	setInterval(function() {
		if(localStorage.getItem('UBIODC_ACTIVATED') != null && localStorage.getItem('UBIODC_ACTIVATED') != "on") 
			return false;
		$("#overlayLoadContacts").show();
		$("#ulContactList").load("core/actions/_load/__loadOnlineContacts.php", function() {
				$("#overlayLoadContacts").hide();
		});
	}, 30000);	
</script>