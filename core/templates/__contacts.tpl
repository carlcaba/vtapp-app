<?
	require_once("core/classes/users.php");
	$contact = new users();
?>
									<!-- Contacts are loaded here -->
									<div class="direct-chat-contacts">
										<ul class="contacts-list">
<?
	echo $contact->showContactPanel();
?>
										</ul>
										<!-- /.contacts-list -->
									</div>
<script>
	function showDirectChat(id) {
		$("#directChatForm").load('core/actions/_load/__loadShowChat.php',
			{ idUser: id }, 
			function() {
				$("#txtMESSAGE").focus();
			}
		);
	}
</script>