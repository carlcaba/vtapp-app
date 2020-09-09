<? 
	$extensions = "['csv', 'xls', 'xlsx']";
	if($imageToUpload) {
		$extensions = "['jpg', 'png', 'jpeg'], maxFileCount: 1, minImageWidth: 200, minImageHeight: 200";
	}
?>

	<!-- FileInput -->
    <link href="plugins/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="plugins/fileinput/themes/explorer-fas/theme.css" media="all" rel="stylesheet" type="text/css"/>

		<!-- Modal Upload -->
		<div class="modal fade" id="divUpload" tabindex="-1" role="dialog"  aria-labelledby="myModalUploadLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="myModalUploadLabel"><i class="fa fa-upload"></i> <?= $titleUpload ?></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="uploadForm">
						<p></p>
						<form enctype="multipart/form-data" id="frmUpload" name="frmUpload" role="form">
							<p><?= $textUpload ?></p>
							<div class="file-loading">
								<input id="file2Upload" name="file2Upload" type="file">
							</div>
							<input type="hidden" name="hfCounterFile" id="hfCounterFile" value="0" />							
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?= $_SESSION["CLOSE"] ?></button>
<?
	if($imageToUpload != true) {
?>
						<button type="button" class="btn btn-primary" id="btnUpload" name="btnUpload" data-dismiss="modal"><?= $_SESSION["LOAD"] ?></button>
<?
	}
?>
					</div>
				</div>
			</div>
		</div>
	<!-- FileInput -->
    <script src="plugins/fileinput/js/plugins/sortable.js" type="text/javascript"></script>
    <script src="plugins/fileinput/js/fileinput.js" type="text/javascript"></script>
    <script src="plugins/fileinput/themes/fa/theme.js" type="text/javascript"></script>
    <script src="plugins/fileinput/themes/explorer-fa/theme.js" type="text/javascript"></script>
<?
	$fup = "false";
	if($_SESSION["LANGUAGE"] != "1") {
		$fup = "true";
?>
    <script src="plugins/fileinput/js/locales/<?= $_SESSION["LANGUAGE"] ?>.js" type="text/javascript"></script>
<?
	}
?>
    <script>
		function upload() {
			$('#file2Upload').fileinput('destroy').fileinput({
				theme: 'fa',
				language: (<?= $fup ?>) ? "<?= $_SESSION["LANGUAGE"] ?>" : "en",
				uploadUrl: 'core/actions/_save/__uploadFile.php<?= $parameters ?>',
				allowedFileExtensions: <?= $extensions ?>
			});
			$('#file2Upload').on('fileuploaded', function(event, data, previewId, index) {
				var response = data.response;
				var cont = parseInt($("#hfCounterFile").val());
				var id = 'hfFile' + cont;
				$('<input>').attr({
					type: 'hidden',
					id: id,
					name: id,
					value: response.initialPreviewThumbTags.url
				}).appendTo('#frmUpload');
				cont++;
				$("#hfCounterFile").val(cont);
			});		
			$('#divUpload').modal('toggle');
		}
		$(document).ready(function() {
			$.getScript( "js/resources.js");
			$("#btnUpload").on("click", function(e) {
				var cont = parseInt($("#hfCounterFile").val());
				if(cont == 0) {
					notify("", "danger", "", "<?= $_SESSION["NO_FILE_FOR_UPLOAD"] ?>", "");
					return false;
				}
				$frm = $("#frmUpload");
				var datas = JSON.stringify($frm.serializeObject());
				var noty;
				$.ajax({
					url: "<?= $saveUpload ?>",
					data: { strModel: datas },
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						$("#divUpload").modal('hide');
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
						if(data.success)
							location.href = data.link;
					}
				});
			});
		});
	</script>
