<?
	if(!isset($userModal))
		$userModal = false;
	if(!isset($noEdit))
		$noEdit = false;
	if(!isset($btnText))
		$btnText = $_SESSION["SAVE_CHANGES"];
	$userModal = "";
	if($userModal) {
		$modalId = "<span id=\"ModalItemId\"></span>";
	}
	else {
		$modalId = "";
	}
	
	//Si no requiere el edit
	if(!$noEdit) {
?>
	<!-- Modal Edit User -->
	<div class="modal fade" id="divEditModal" tabindex="-1" role="dialog" aria-labelledby="h5ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="h5ModalLabel"><?= $icon ?> <span id="actionId"></span> <?= $title ?> <?= $modalId ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>					
				</div>
				<div class="modal-body" id="modalForm"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= $_SESSION["CLOSE"] ?></button>
					<button type="button" class="btn btn-primary" id="btnSave" name="btnSave"><?= $_SESSION["SAVE_CHANGES"] ?></button>
				</div>
			</div>
		</div>
	</div>
	
<?
	}
?>

	<!-- Modal Reset Password / Activate user -->
	<div class="modal fade" id="divActivateModal" tabindex="-1" role="dialog" aria-labelledby="h5Modal2Label" aria-hidden="true" style="z-index: 99998 !important;">
		<div class="modal-dialog" role="document">
			<div class="modal-content"> 
				<div class="modal-header">
					<h5 class="modal-title" id="h5Modal2Label"><i class="fa fa-exclamation-triangle"></i> <span id="spanTitle"></span> <span id="spanTitleName"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modalBody"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" id="btnCancelActivate" name="btnCancelActivate"><?= $_SESSION["CLOSE"] ?></button>
					<button type="button" class="btn btn-primary" id="btnActivate" name="btnActivate" data-dismiss="modal"><?= $btnText ?></button>
					<input type="hidden" name="hfDefaultTextButton" id="hfDefaultTextButton" value="<?= $btnText ?>" />
					<input type="hidden" name="hfTextButton" id="hfTextButton" value="" />
				</div>
			</div>
		</div>
	</div>	
