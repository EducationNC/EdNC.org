<?php
/*
 * Template Name: Donation Page
 * Template Post Type: page, product
 */
?>
<?php while (have_posts()) : the_post(); ?>
<div class="container">
  <div class="row donate">
    <div class="col-md-6">
      <div class="donate-content">
        <h1>Invest in EdNC’s work today.</h1>
        <div class="video-container">
          <iframe src="https://player.vimeo.com/video/281859270" frameborder="0" allowfullscreen></iframe>
        </div>
        <p class="donate-body"><?php the_content(); ?></p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="donate-iframe">
        <script src="https://donorbox.org/widget.js" paypalExpress="false"></script>
        <iframe class="donorbox" src="https://donorbox.org/embed/donation-page-8?default_interval=m" height="685px" width="100%"
        style="min-width:200px; max-height:none!important" seamless="seamless" name="donorbox"
        frameborder="0" scrolling="no" allowpaymentrequest></iframe>
        <i>EducationNC is a 501(c)(3) nonprofit, EIN # 20-5625322. Your entire contribution is
        tax deductible. You will not receive any goods or services in return for this contribution.
        Because the products of EdNC are available to the general public free of charge, receipt of
        products does not have a value for tax purposes that reduces the deductible amount of your
        contribution.</br></br>
        Financial information about this organization and a copy of its license are available from
        the State Solicitation Licensing Branch at 1-888-830-4989. The license is not an
        endorsement by the state.</i>
      </div>
    </div>
  </div>
</div>
<?php endwhile; ?>
