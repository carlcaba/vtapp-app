    <!-- InstaScan JS
		============================================ -->
	<script type="text/javascript" src="plugins/instascan/instascan.min.js"></script>

    <script type="text/javascript">
		var scanner;
		$(document).ready(function() {
			let scanner = new Instascan.Scanner({ video: document.getElementById('scanPreview') });
			scanner.addListener('scan', function (content) {
				var patt = new RegExp(/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i);
				var res = patt.test(content);
				$("#hfIdScan").val(content);
				if(res) {
					if($('#cbProduct option[value="' + content + '"]').length) {
						try {
							$("#messageP").removeClass("alert-info").removeClass("alert-danger").addClass("alert-info");
							$("#messageP").html(msg4);
							$("#hfIdScan").val(content);
							$('#cbProduct').val(content);
							$('#cbProduct').trigger("change");
							var data = $('#cbProduct option[value="' + content + '"]').data();
							var focus = "txtQUANTITY";
							$("#txtQUANTITY").attr("disabled", data.productQuantity == 0);
							if (data.productQuantity == 0) {
								notify("", 'danger', "", msg5, "");
								focus = "cbProduct";
							}
							if(data.factor != 1) {
								notify("", 'warning', "", msg7, "");
							}
							if(data.factormoney != 1) {
								notify("", 'warning', "", msg8, "");
							}
							$("#btnSave").attr("disabled", data.productQuantity == 0);
							$("#moneyTypePrice").html(data.productMoneytype);
							$("#moneyTypeTotal").html(data.factormoneyconversion);
							$("#unitDiv").html(data.unit);
							$("#txtPRICE").val(data.productPrice);
							$("#txtEXISTENCE").val(data.productQuantity);
							$("#hfCODE").val(data.code);
							$("#hfID").val(content);
							$("#hfFactor").val(data.factor);
							$("#hfMoneyFactor").val(data.factormoney);
							$("#hfUNIT").val(data.unit);
							$('#txtQUANTITY').val("");
							$("#txtQUANTITY").css("background-color", (data.factor != 1 ? "LightYellow" : ""));
							$("#txtTOTAL").val(0 * data.productPrice * data.factormoney);
							$('#' + focus).focus();
						}
						catch(e) {
							console.log(e);
							$('#txtQUANTITY').focus();
						}
						$('#divScanCamera').modal('toggle');
						$('#txtQUANTITY').focus();
					}
					else {
						$("#messageP").removeClass("alert-info").removeClass("alert-danger").addClass("alert-danger");
						$("#messageP").html(msg3);
					}
				}
				else {
					$("#messageP").removeClass("alert-info").removeClass("alert-danger").addClass("alert-danger");
					$("#messageP").html(msg2);
				}
			});
			
			Instascan.Camera.getCameras().then(function (cameras) {
				if (navigator.getUserMedia) {
					navigator.getUserMedia(
						{
							video: true
						},

						function(localMediaStream) {

						},

						function(err) {
							console.log('The following error occurred when trying to use getUserMedia: ' + err);
						}
					);

				} 
				else {
					alert('Sorry, your browser does not support getUserMedia');
				}			
				for(i = 0; i<cameras.length; i++) {
					var span = "";
					if(i == 0)
						span = "<span title=\"" + cameras[i].name + "\" class=\"active\">" + cameras[i].name + "</span>";
					else 
						span = "<span title=\"" + cameras[i].name + "\"><a onclick=\"selectCamera(" + i + ");\">" + cameras[i].name + "</a></span>";
					$("#ulCamera").append("<li>" + span + "</li>")
				}
				if (cameras.length > 0) {
					scanner.start(cameras[0]);
				}
				else {
					$("#messageP").removeClass("alert-info").removeClass("alert-danger").addClass("alert-danger");
					$("#messageP").html('No cameras found.');
				}
			}).catch(function (e) {
				$("#messageP").removeClass("alert-info").removeClass("alert-danger").addClass("alert-danger");
				$("#messageP").html(e);
			});
			var selectCamera = function(camera) {
				scanner.start(cameras[camera]);
			};
			$("#messageP").html(msg1);
			$("#titleCamera").html(msg6);
			$('#divScanCamera').modal('toggle');
		});
    </script>
						<div class="card">
							<div class="card-header p-2">
								<ul class="nav nav-pills" id="ulCamera">
								</ul>
							</div>
							<div class="card-body">
								<div class="tab-content">
									<div class="active tab-pane">
										<div class="post">
											<div class="row">
												<div class="col-sm-12">
													<video id="scanPreview"></video>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<div class="alert alert-info alert-mg-b-0" role="alert" id="messageP"></div>						
								<input type="hidden" id="hfIdScan" name="hfIdScan" value="" />
							</div>
						</div>
