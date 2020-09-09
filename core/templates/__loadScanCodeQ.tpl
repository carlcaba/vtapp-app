    <!-- Quagga JS
		============================================ -->
    <link rel="stylesheet" type="text/css" href="plugins/quagga/styles.css" />
    <link rel="stylesheet" type="text/css" href="plugins/quagga/morestyles.css" />
    <script type="text/javascript" src="plugins/quagga/jquery-migrate-1.4.1.js"></script>
    <script type="text/javascript" src="plugins/quagga/quagga.js"></script>
    <script type="text/javascript" src="plugins/quagga/load.js"></script>
	<script>
		$(document).ready(function() {
			$("[type=file]").on("change", function(){
				var file = this.files[0].name;
				var dflt = $(this).attr("placeholder");
				if($(this).val()!=""){
					$(this).next().text(file);
				} 
				else {
					$(this).next().text(dflt);
				}
			});
		});
	</script>
								<div class="controls">
									<fieldset class="input-group">
										<input id="file2Upload" type="file" placeholder="Scan/Load" type="file" accept="image/*" capture="camera" />
										<label for="file2Upload">Scan/Load</label>										
									</fieldset>
									<fieldset class="reader-config-group d-none d-sm-none d-md-none d-lg-block d-xl-block">
										<label>
											<span>Barcode-Type</span>
											<select name="decoder_readers">
												<option value="code_128" selected="selected">Code 128</option>
												<option value="code_39">Code 39</option>
												<option value="code_39_vin">Code 39 VIN</option>
												<option value="ean">EAN</option>
												<option value="ean_extended">EAN-extended</option>
												<option value="ean_8">EAN-8</option>
												<option value="upc">UPC</option>
												<option value="upc_e">UPC-E</option>
												<option value="codabar">Codabar</option>
												<option value="i2of5">Interleaved 2 of 5</option>
												<option value="2of5">Standard 2 of 5</option>
												<option value="code_93">Code 93</option>
											</select>
										</label>
										<label>
											<span>Resolution (long side)</span>
											<select name="input-stream_size">
												<option value="320">320px</option>
												<option value="640">640px</option>
												<option selected="selected" value="800">800px</option>
												<option value="1280">1280px</option>
												<option value="1600">1600px</option>
												<option value="1920">1920px</option>
											</select>
										</label>
										<label>
											<span>Patch-Size</span>
											<select name="locator_patch-size">
												<option value="x-small">x-small</option>
												<option value="small">small</option>
												<option value="medium">medium</option>
												<option selected="selected" value="large">large</option>
												<option value="x-large">x-large</option>
											</select>
										</label>
										<label>
											<span>Half-Sample</span>
											<input type="checkbox" name="locator_half-sample" />
										</label>
										<label>
											<span>Single Channel</span>
											<input type="checkbox" name="input-stream_single-channel" />
										</label>
										<label>
											<span>Workers</span>
											<select name="numOfWorkers">
												<option value="0">0</option>
												<option selected="selected" value="1">1</option>
											</select>
										</label>
									</fieldset>
								</div>
								<div id="result_strip">
									<ul class="thumbnails"></ul>
									<ul class="collector"></ul>
								</div>
								<div id="interactive" class="viewport"></div>
								<div id="debug" class="detection"></div>
							</div>
						</div>
