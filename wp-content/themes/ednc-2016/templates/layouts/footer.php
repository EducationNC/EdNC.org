<?php

use Roots\Sage\Assets;

?>
<?php get_template_part('templates/components/social-share'); ?>

<div class="above-footer print-no">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-push-2">
        <div class="hidden-xs">
          <?php get_template_part('templates/components/email-signup'); ?>
        </div>
        <div class="visible-xs-block text-center extra-bottom-margin">
          <a class="btn btn-default" data-toggle="modal" data-target="#emailSignupModal">Free Subscription</a>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="content-info" role="contentinfo">
  <div class="ribbon print-no">
    <ul class="list-inline text-center">
       <li><img src="<?php echo Assets\asset_path('images/z-smith-reynolds-foundation.png'); ?>" width="153" alt="Z. Smith Reynolds Foundation" /></li>
      <li><img src="<?php echo Assets\asset_path('images/jw-pope-foundation.png'); ?>" width="150" alt="John William Pope Foundation" /></li>
      <li><img src="<?php echo Assets\asset_path('images/burroughs-wellcome-fund.png'); ?>" width="100" alt="Burroughs Wellcome Fund" /></li>
      <li><img src="<?php echo Assets\asset_path('images/park-foundation.png'); ?>" width="130" alt="Park Foundation" /></li>
      <li><img src="<?php echo Assets\asset_path('images/sas.png'); ?>" width="100" alt="SAS Institute" /></li>
      <li><img src="<?php echo Assets\asset_path('images/duke-energy.png'); ?>" width="150" alt="Duke Energy" /></li>
      <li><img src="<?php echo Assets\asset_path('images/SECU_Foundation_logo_trademark_White.png'); ?>" width="150" alt="Viacom" /></li>
      <li><a title="Blue Cross Blue Shield" href="https://www.bcbsnc.com/"><img src="<?php echo Assets\asset_path('images/Blue-Cross-Blue-Sheild-NC-Logo.png'); ?>" width="130" alt="Blue Cross Blue Shield" /></a></li>
      <li><img src="<?php echo Assets\asset_path('images/logo-belk-foundation.png'); ?>" width="130" alt="Belk Foundation" /></li>
      <li><img src="<?php echo Assets\asset_path('images/william-trust.png'); ?>" width="170" alt="The William R. Kenan, Jr. Charitable Trust" /></li>
      <li><img src="<?php echo Assets\asset_path('images/mebane-foundation-logo-white.png'); ?>" width="170" alt="Mebane Foundation" /></li>
      <li><img src="<?php echo Assets\asset_path('images/duke-endowment.png'); ?>" width="200" alt="The James P. Duke Endowment" /></li>
      <li><img src="<?php echo Assets\asset_path('images/BelkEndowment_ logo.png'); ?>" width="200" alt="John M. Belk Endowment" /></li>
      
      </ul>
  </div>

  <div class="container">
    <div class="row">
      <?php
      wp_nav_menu(array(
        'theme_location' => 'footer_navigation',
        'container' => false,
        'menu_class' => 'col-sm-8 menu-footer-nav print-no',
        // 'walker' => new Walker_Nav_Menu
      ));
      ?>

      <div class="col-sm-4">
        <div class="h5">Support us</div>
        <p><a class="btn btn-gray" href="https://support.ednc.org/donate">Donate Now</a></p>

        <hr />

        <p class="small">
          <span class="copyright">&copy; <?php echo date('Y'); ?> EducationNC. All rights reserved.</span><br />
          <a href="<?php echo get_permalink('1528'); ?>">Terms of service</a> | <a href="<?php echo get_permalink('1530'); ?>">Privacy policy</a>
        </p>
      </div>
    </div>
  </div>
  <script type="text/javascript">
	 jQuery('#gform_wrapper_6').css("display","block");
  </script>
</footer>

<div class="modal fade email-signup-modal print-no" id="emailSignupModal" tabindex="-2" role="dialog" aria-labelledby="emailSignupModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php get_template_part('templates/components/email-signup'); ?>
    </div>
  </div>
</div>


<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '138414986648733',
      xfbml      : true,
      version    : 'v2.10'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>