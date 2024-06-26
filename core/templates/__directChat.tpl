<?
	_error_log("Direct chat loading start at " . date("Y-m-d h:i:s"));

	require_once("core/classes/directchat.php");
	$chat = new directchat();
	$userChat = $chat->getLastChat();
	$myChats = $chat->getTotalCount();
	$sentChats = $chat->getTotalSentCount();
	$chatBadge = $myChats > 0 ? "<span data-toggle=\"tooltip\" title=\"" . $myChats . " " . $_SESSION["MESSAGES"] . "\" class=\"badge badge-warning\">$chats</span>" : "";
?>
							<!-- DIRECT CHAT -->
							<div class="card direct-chat direct-chat-primary" id="directChatCard">
								<div class="card-header">
									<h3 class="card-title"><?= $_SESSION["DIRECT_CHAT"] ?></h3>
									<div class="card-tools">
										<?= $chatBadge ?>
										<button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
										<button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-times"></i></button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body" id="directChatBody">
<?= $chat->showLastChats() ?>
								</div>
								<!-- /.card-body -->
								<div class="card-footer" id="directChatForm">
<?
	if(($myChats + $sentChats) > 0) 
		echo $chat->showForm($userChat);
	
	_error_log("Direct chat finishes at " . date("Y-m-d h:i:s"));
	
?>
								</div>
								<!-- /.card-footer-->
							</div>
							<!--/.direct-chat -->