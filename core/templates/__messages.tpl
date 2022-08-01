<?
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
		$protocol = 'https://';
	else
		$protocol = 'http://';
		
	/*
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$website = $protocol . "localhost";

	$folder = $conf->verifyValue("SITE_ROOT");
	$chatfolder = ($folder == "" ? "/" : $folder) .  $conf->verifyValue("CHAT_DIRECT_SOCKET");
	$port = $conf->verifyValue("CHAT_DIRECT_PORT");
	$scriptname = $conf->verifyValue("CHAT_DIRECT_SCRIPT");
	
	$uri = str_replace("https", "ws", $website . ":" . $port . ($folder== "" ? "" : $chatfolder . "/" . $scriptname));
	$uri = str_replace("http", "ws", $uri);
	*/
	
	$uri = "ws://localhost:8090/chatdirect/php-socket.php";

	$colors = array('#007AFF','#FF7000','#FF7000','#15E25F','#CFC700','#CFC700','#CF1100','#CF00BE','#F00');
	$color_pick = array_rand($colors);
	
	$draw = $parentFileName != null;
	
	/*
	ESTRUCTURA MENSAJE
		Type: sys-usr-grp
		Message: message
		To: user-name, group-name,  all
		Image: image
		Name
		From_ Sender
		Image From: Image from
		Color: color
		
				var res_type 		= response.type;
				var user_message 	= response.message;
				var user_name 		= response.name;
				var user_color 		= response.color;
				var user_to			= response.to;
				var user_image 		= response.image;
				var from			= response.from;
				var image_from		= response.from_image;		
	*/
	
	if(isset($usrx)) 
		$imgusr = $usrx->getUserPicture(true);
	else
		$imgusr = "";
?>
		<!--  notification JS
			============================================ -->
		<script src="js/plugins/notify/bootstrap-notify.min.js"></script>
		<script type="text/javascript">
			var draw = "<?= $parentFileName ?>";
			/*
			 * Notifications
			 */
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
				var delay = _return ? 0 : 5000; 
				var url = window.location.pathname;
				var filename = url.substring(url.lastIndexOf('/')+1);				
				var set = {
					z_index: 999999,
					type: type,
					delay: delay,
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
			
			/*
			*	FUNCIONES
			*/
			var wsSM = function(ev) {
				var response 		= JSON.parse(ev.data);
				var res_type 		= response.type;
				var user_message 	= response.message;
				var user_name 		= response.name;
				var user_color 		= response.color;
				var user_to			= response.to;
				var user_image 		= response.image;
				var from			= response.from;
				var image_from		= response.from_image;
				var msg = "";
				
				if(draw != "") {
					switch(res_type){
						case 'usr':
							//if($("#hfMeInChat").val() == user_to) {
							if(localStorage.getItem('UBIODC_MYSELF') == user_to) {
								msg = "<div class=\"direct-chat-msg right\">" +
											"<div class=\"direct-chat-infos clearfix\">" +
												"<span class=\"direct-chat-name float-right\">" + user_name + "</span>" + 
												"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
											"</div>" +
											"<img class=\"direct-chat-img\" src=\"" + user_image + "\" alt=\"UBIO\">" +  
											"<div class=\"direct-chat-text\">" + user_message +
											"</div>" +
											"</div>";
								msgBox.append(msg);
								var id = from;
								if(!$("#frmDirectChat").is(":visible")) {
									$("#directChatForm").load('core/actions/_load/__loadShowChat.php',
										{ idUser: user_to }, 
										function() {
											var plc = "<?= $_SESSION["TYPE_MESSAGE"] ?>";
											$('[id^=li_]').css("background-color", "");
											$("#li_" + id).css("background-color", "#EBEDEC");
											localStorage.setItem('UBIODC_TO', id);
											$("#txtMESSAGE").val("");
											$("#txtMESSAGE").attr("placeholder", plc + from);
											$("#txtMESSAGE").focus();
										}
									);							
								}
								else {
									var plc = "<?= $_SESSION["TYPE_MESSAGE"] ?>";
									$('[id^=li_]').css("background-color", "");
									$("#li_" + id).css("background-color", "#EBEDEC");
									localStorage.setItem('UBIODC_TO', id);
									$("#txtMESSAGE").val("");
									$("#txtMESSAGE").attr("placeholder", plc + from);
									$("#txtMESSAGE").focus();
								}
							}
							break;
						case 'sys':
							msg = "<div class=\"direct-chat-msg right\">" +
										"<div class=\"direct-chat-infos clearfix\">" +
											"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
											"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
										"</div>" +
										"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"UBIO\">" +  
										"<div class=\"direct-chat-text\">" + user_message +
										"</div>" +
										"</div>";
							msgBox.append(msg);
							break;
					}
					if(msgBox[0] != null)
						msgBox[0].scrollTop = msgBox[0].scrollHeight; //scroll message 
				}
				else {
					if(localStorage.getItem('UBIODC_MYSELF') == user_to || res_type == 'sys') {
						var msgs = parseInt($("#spanMessages").html());
						var div = $("#topAreaDirectChat");
						var item = "<a href=\"#\" class=\"dropdown-item\">\n";
						item += "<div class=\"media\">\n";
						item += "<img src=\"" + image_from + "\" class=\"img-size-50 mr-3 img-circle\">\n";
						item += "<div class=\"media-body\">\n";
						item += "<h3 class=\"dropdown-item-title\">" + user_name + "</h3>\n";
						item += "<p class=\"text-sm\">" + user_message.slice(0,25) + "...</p>\n";
						item += "<p class=\"text-sm text-muted\"><i class=\"fa fa-clock-o mr-1\"></i> " + formatDate() + "</p>\n";
						item += "</div>\n";
						item += "</div>\n";
						item += "</a>\n";
						msgs++; 
						if (msgs > 1) {
							$("#spanMessages").html(msgs);
							$(item + "<div class=\"dropdown-divider\"></div>").insertBefore("#aStartChat");
						}
						else {
							result = "<span class=\"badge badge-danger navbar-badge\" id=\"spanMessages\">" + msgs + "</span>\n";
							$("#directChatCount").html(result);
							div.children("a").replaceWith(item);						
							$("#aStartChat").text("<?= $_SESSION["SEE_ALL"] . " " . $_SESSION["MESSAGES"] ?>");				
						}
						var datas = {
							txtMESSAGE: user_message,
							txtDESTINY: user_to,
							hfSENDER: user_name
						};
						$.ajax({
							url: "core/actions/_save/__newDirectChat.php",
							data: { 
								strModel: datas 
							},
							dataType: "json",
							method: "POST",
							success: function(data){
								DirectChatVar = setTimeout(checkDirectChat, <?= $_SESSION["DIRECT_CHAT_TIME"] ?>);
							}
						});
					}
				}
			};
			
			var wsOO = function (ev) { 
				var msg = "<div class=\"direct-chat-msg right\">" +
							"<div class=\"direct-chat-infos clearfix\">" +
								"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
								"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
							"</div>" +
							"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"Bienvenido\">" +  
							"<div class=\"direct-chat-text bg-success\">" +
								"Bienvenido a Direct Chat de UBIO" +
							"</div>" +
							"</div>";
				msgBox.append(msg);
				localStorage.setItem('UBIODC_ACTIVATED', 'on');
			};
			
			var wsOE = function (ev) {
				console.log(ev);
				var msg = "<div class=\"direct-chat-msg right\">" +
							"<div class=\"direct-chat-infos clearfix\">" +
								"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
								"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
							"</div>" +
							"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"UBIO\">" +  
							"<div class=\"direct-chat-text bg-danger\">Ha ocurrido un error: " + ev.data +
							"</div>" +
							"</div>";
				msgBox.append(msg);
				localStorage.setItem('UBIODC_ACTIVATED', 'off');
			};		

			var wsOC = function (ev) {
				var msg = "<div class=\"direct-chat-msg right\">" +
							"<div class=\"direct-chat-infos clearfix\">" +
								"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
								"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
							"</div>" +
							"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"UBIO\">" +  
							"<div class=\"direct-chat-text bg-warning\">La conexión al chat se ha cerrado" + 
							"</div>" +
							"</div>";
				msgBox.append(msg);
				localStorage.setItem('UBIODC_ACTIVATED', 'off');
			};			
			
			/*
			 * DIRECT CHAT
			 */
			var DirectChatVar;
			var msgBox = $('#directChatMessages');
			var wsUri = "<?= $uri ?>"; 	
			var websocket;
			var imgusr = "<?= $imgusr ?>";
			
			if(imgusr == "")
				imgusr = $('#imgUserInfo').attr('src');
			
			localStorage.setItem('UBIODC_ACTIVATED', 'on');
			localStorage.setItem('UBIODC_MYSELF', '<?= $_SESSION["vtappcorp_userid"] ?>');
			localStorage.setItem('UBIODC_IMAGE', imgusr);
			localStorage.setItem('UBIODC_TO', '');
			localStorage.setItem('UBIODC_FROM', '');			
			localStorage.setItem('UBIODC_FROM_IMAGE', '');			
			
			try {
				websocket = new WebSocket(wsUri); 
			}
			catch(error) {
				notify("", 'danger', "", "<i class=\"fa-solid fa-land-mine-on\"></i> <?= $_SESSION["DIRECTCHAT_NOT_AVAILABLE"] ?>", "");				
			}
			websocket.onopen = wsOO;
			websocket.onmessage = wsSM;
			websocket.onerror = wsOE;
			websocket.onclose = wsOC;

			$("#txtMESSAGE").on("keydown", function( event ) {
				event.preventDefault();
				if(event.which == 13){
					return sendDirectChat();
				}
			});
			 
			function sendDirectChat() {
				/*
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
				*/
				var message_input = $('#txtMESSAGE'); 
				var name_input = localStorage.getItem('UBIODC_MYSELF'); //$('#hfMeInChat'); //user name
				var name_to = localStorage.getItem('UBIODC_TO');
				var status = localStorage.getItem('UBIODC_ACTIVATED');
				
				if(name_input == ""){ //empty name?
					notify("", 'danger', "", "<i class=\"fa-solid fa-triangle-exclamation\"></i> <?= $_SESSION["EMPTY_CHAT_USER_NAME"] ?>", "");				
					return;
				}
				if(message_input.val() == ""){ //emtpy message?
					notify("", 'danger', "", "<i class=\"fa-solid fa-triangle-exclamation\"></i> <?= $_SESSION["EMPTY_CHAT_MESSAGE"] ?>", "");				
					return;
				}
				if(name_to == ""){ //emtpy destination?
					notify("", 'danger', "", "<i class=\"fa-solid fa-triangle-exclamation\"></i> <?= $_SESSION["EMPTY_CHAT_DESTINATION"] ?>", "");				
					return;
				}

				if(status != "on"){ //DirectChat disabled
					notify("", 'danger', "", "<i class=\"fa-solid fa-triangle-exclamation\"></i> <?= $_SESSION["DIRECTCHAT_NOT_AVAILABLE"] ?>", "");				
					return;
				}

				//prepare json data
				var msg = {
					message: message_input.val(),
					name: name_input,
					color : '<?php echo $colors[$color_pick]; ?>',
					to: name_to,
					type: 'usr',
					image: localStorage.getItem('UBIODC_IMAGE'),
					from: localStorage.getItem('UBIODC_MYSELF'),
					image_from: localStorage.getItem('UBIODC_FROM_IMAGE')
				};
				//convert and send data to server
				websocket.send(JSON.stringify(msg));	
				var msg = "<div class=\"direct-chat-msg\">" + 
								"<div class=\"direct-chat-info clearfix\">" +
									"<span class=\"direct-chat-name float-left\"> Yo</span>" +
									"<span class=\"direct-chat-timestamp float-right\">" + formatDate()  + "</span>" +
								"</div>" +
								"<img class=\"direct-chat-img\" src=\"" + localStorage.getItem('UBIODC_IMAGE') + "\" alt=\"Yo\">" +
								"<div class=\"direct-chat-text\">" + message_input.val() + "</div>" +
							"</div>";
				msgBox.append(msg);
				message_input.val(''); 
				return false;
			}
			function checkDirectChat() {
				if(localStorage.getItem('UBIODC_ACTIVATED') != null && localStorage.getItem('UBIODC_ACTIVATED') != "on")
					return false;
				try {
					websocket = new WebSocket(wsUri); 
				}
				catch(error) {
					return false;
				}
				websocket.onopen = wsOO;
				websocket.onmessage = wsSM;
				websocket.onerror = wsOE;
				websocket.onclose = wsOC;
				/*
				websocket.onopen = function(ev) { 
					var msg = "<div class=\"direct-chat-msg right\">" +
								"<div class=\"direct-chat-infos clearfix\">" +
									"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
									"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
								"</div>" +
								"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"Bienvenido\">" +  
								"<div class=\"direct-chat-text\">" +
									"Bienvenido a Direct Chat de UBIO" +
								"</div>" +
								"</div>";
					msgBox.append(msg);
					$("#hfDirectChat").val("on");
					DirectChatVar = null;
				}
				websocket.onmessage = function(ev) {
					var response 		= JSON.parse(ev.data);
					var res_type 		= response.type;
					var user_message 	= response.message;
					var user_name 		= response.name;
					var user_color 		= response.color;
					var user_to			= response.to;
					var user_image 		= response.image;
					var msg = "";

					switch(res_type){
						case 'usr':
							msg = "<div class=\"direct-chat-msg right\">" +
										"<div class=\"direct-chat-infos clearfix\">" +
											"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
											"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
										"</div>" +
										"<img class=\"direct-chat-img\" src=\"" + user_image + "\" alt=\"UBIO\">" +  
										"<div class=\"direct-chat-text\">" + user_message +
										"</div>" +
										"</div>";
							msgBox.append(msg);
							break;
						case 'sys':
							msg = "<div class=\"direct-chat-msg right\">" +
										"<div class=\"direct-chat-infos clearfix\">" +
											"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
											"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
										"</div>" +
										"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"UBIO\">" +  
										"<div class=\"direct-chat-text\">" + user_message +
										"</div>" +
										"</div>";
							msgBox.append(msg);
							break;
					}
					msgBox[0].scrollTop = msgBox[0].scrollHeight; //scroll message 

				};
				
				websocket.onerror	= function(ev) { 
					var msg = "<div class=\"direct-chat-msg right\">" +
								"<div class=\"direct-chat-infos clearfix\">" +
									"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
									"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
								"</div>" +
								"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"UBIO\">" +  
								"<div class=\"direct-chat-text\">Ha ocurrido un error: " + ev.data +
								"</div>" +
								"</div>";
					msgBox.append(msg);
					$("#hfDirectChat").val("off");
					DirectChatVar = setTimeout(checkDirectChat, <?= $_SESSION["DIRECT_CHAT_TIME"] ?>);					
				}; 
				websocket.onclose 	= function(ev) {
					var msg = "<div class=\"direct-chat-msg right\">" +
								"<div class=\"direct-chat-infos clearfix\">" +
									"<span class=\"direct-chat-name float-right\">Direct Chat</span>" + 
									"<span class=\"direct-chat-timestamp float-left\">" + formatDate() + "</span>" +
								"</div>" +
								"<img class=\"direct-chat-img\" src=\"img/logo/icons/apple-icon-120x120.png\" alt=\"UBIO\">" +  
								"<div class=\"direct-chat-text\">La conexión al chat se ha cerrado" + 
								"</div>" +
								"</div>";
					msgBox.append(msg);
					$("#hfDirectChat").val("off");
					DirectChatVar = setTimeout(checkDirectChat, <?= $_SESSION["DIRECT_CHAT_TIME"] ?>);					
				}; 
				*/
				
				/*
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
				*/				
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
				//if($("#topAreaDirectChat").length > 0)
				if(localStorage.getItem('UBIODC_ACTIVATED') != "on")
					DirectChatVar = setTimeout(checkDirectChat, <?= $_SESSION["DIRECT_CHAT_TIME"] ?>);
			});
		</script>
		
		