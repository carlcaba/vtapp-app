									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["LOAD_TEMPLATE"] ?>" id="btnLoad" name="btnLoad" class="btn btn-default pull-right" onclick="upload();">
										<i class="fa fa-cloud-upload"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["LOAD_TEMPLATE"] ?></span>
									</button>
									<form id="frmDownloadTemplate" name="frmDownloadTemplate" method="get" action="templates/<?= $template ?>" >
										<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["DOWNLOAD_TEMPLATE"] ?>" id="btnTemplate" name="btnTemplate" class="btn btn-default pull-right" onclick="document.getElementById('frmDownloadTemplate').submit();">
											<i class="fa fa-file-excel-o"></i>
											<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["DOWNLOAD_TEMPLATE"] ?></span>
										</button>
									</form>
