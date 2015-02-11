<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>CG Cookie Segment Tracking</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-2">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						<h3><span>Segment Settings</span></h3>
						<div class="inside">

							<form name="cgc_segment_write_key_form" method="post" action="">

							<input type="hidden" name="cgc_segment_write_key_form_submitted" value="Y">

								<table class="form-table">
									<tr>
										<td for="cgc_segment_write_key">Segment Project Write Key:</td>
										<?php 
											if( isset( $cgc_segment_write_key ) ): ?>
												<td><input name="cgc_segment_write_key" id="" type="text" value="<?php echo $cgc_segment_write_key; ?>" class="regular-text" /></td>
											<?php else: ?>
												<td><input name="cgc_segment_write_key" id="" type="text" value="" class="regular-text" /></td>
											<?php endif; ?>
									</tr>
								</table>
								<p>
									<input class="button-primary" type="submit" name="cgc_segment_write_key_submit" value="Save" />
								</p>

							</form>

						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->
			
			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				
				<div class="meta-box-sortables">
					
					<div class="postbox">
					
						<h3><span>Help</span></h3>
						<div class="inside">
							<p>
								You can find your Segment write key under your <em>Project Settings > API Keys</em>
							</p>
						</div> <!-- .inside -->
						
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables -->
				
			</div> <!-- #postbox-container-1 .postbox-container -->
			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->