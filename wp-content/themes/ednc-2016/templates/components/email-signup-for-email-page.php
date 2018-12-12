<?php
$source = '';
if (isset($_GET['utm_source'])) {
  $source = $_GET['utm_source'];
}
?>

	<div class="email-signup-form">
	  <!-- Begin MailChimp Signup Form -->
	  <div id="mc_embed_signup" class="mc_embed_signup">
		<form action="//ednc.us9.list-manage.com/subscribe/post?u=8ba11e9b3c5e00a64382db633&amp;id=2696365d99" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"  novalidate="">
		  <div id="mc_embed_signup_scroll">

			<div class="mc-field-group input-group full-width">
			  <ul>
				<div class="row">
					<div class="col-sm-4">
						<li><input type="checkbox" value="1" name="group[13145][1]" id="mce-group[13145]-13145-0"><label for="mce-group[13145]-13145-0">Daily Digest</label></li>
					</div>
					<div class="col-sm-4">
						<li><input type="checkbox" value="2" name="group[13145][2]" id="mce-group[13145]-13145-1"><label for="mce-group[13145]-13145-1">Weekly Wrapup</label></li>
					</div>
					<div class="col-sm-4">
						<li><input type="checkbox" value="2097152" name="group[13145][2097152]" id="mce-group[13145]-13145-3"><label for="mce-group[13145]-13145-3">Reach Roundup</label></li>
					</div>
					<div class="col-sm-4">
					    <li><input type="checkbox" id="group_4194304" name="group[13145][4194304]" id="mce-group[13145]-13145-4"><label for="mce-group[13145]-13145-4">Friday@Five</label></li>
					</div> 
					<div class="col-sm-4">
					    <li><input id="mce-group[13145]-13145-5" name="group[13145][8388608]" type="checkbox" value="8388608" /><label for="mce-group[13145]-13145-5">Awake 58</label></li> 
					</div>
					<div class="col-sm-4">
					    <li><input type="checkbox" value="16777216" name="group[13145][16777216]" id="mce-group[13145]-13145-6"><label for="mce-group[13145]-13145-6">EdNC STEM</label></li>
					</div>   
					<div class="col-sm-4">
					    <li><input type="checkbox" value="33554432" name="group[13145][33554432]" id="mce-group[13145]-13145-7"><label for="mce-group[13145]-13145-7">Gametime NC</label></li>
					</div>  								
				</div>			
			  </ul>
			</div>

			<div class="hidden">
			  <input type="hidden" name="MERGE3" id="MERGE3" value="<?php echo $source; ?>">
			</div>

			<div id="mce-responses" class="clear">
			  <div class="response" id="mce-error-response" style="display:none"></div>
			  <div class="response" id="mce-success-response" style="display:none"></div>
			</div>

			<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
			<div style="position: absolute; left: -5000px;"><input type="text" name="b_8ba11e9b3c5e00a64382db633_2696365d99" tabindex="-1" value=""></div>

			<div class="form-inline">
			  <div class="form-group full-width">
				<div class="row">
					<div class="col-sm-8 no-padding-right">
						<input type="email" value="" name="EMAIL" placeholder="Email address" class=" full-width required email form-control" id="mce-EMAIL">				
					</div>
					<div class="col-sm-4 no-padding-left">
						<input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class=" full-width bg-purple btn btn-default">				
					</div>
				</div>
			  </div>
			</div>
		  </div>
		</form>
	  </div>
	  <!--End mc_embed_signup-->
	</div>
	
</div>