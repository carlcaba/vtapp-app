    <!-- Modal -->
	<div class="modal fade" id="modalFAQ" tabindex="-1" role="dialog" aria-labelledby="modalFAQLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="modalFAQLabel"><i class="fa fa-circle-question"></i>&nbsp;<?= $_SESSION["FAQS"] ?></h4>
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $_SESSION["CLOSE"] ?></span></button>
				</div>
				<div class="modal-body">
					<p id="pTextFAQ"><?= $_SESSION["YOUR_QUESTION"] ?></p>
					<textarea name="txtQuestion" id="txtQuestion" class="form-control col-xs-12"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= $_SESSION["CLOSE"] ?></button>
					<button type="button" id="btnSave" name="btnSave" class="btn btn-primary" onclick="Save();"><?= $_SESSION["SAVE"] ?></button>
				</div>
			</div>
		</div>
	</div> 