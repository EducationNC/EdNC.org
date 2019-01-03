<?php
/*
 * Template Name: Donation Page
 * Template Post Type: post, page, product
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
          <p class="donate-body">As a nonprofit organization, we rely on the support of people just
            like you to power our storytelling, research, and our work in communities across the state.</br></br>
            Real faces. Real places. Authentic relationships are built in community. We amplify
            your voice to deliver real solutions to our pressing challenges and to provide
            pathways to the opportunities ahead for a greater North Carolina. We do this work for
            students like Miracle -- but we really do it with Miracle.</br></br>
            Please invest in our work to be your trusted, go-to source of information on education
            from birth to career. Your support today will help us tell more stories, provide more data,
            and engage more communities.</br></br>
            Thank you.</br></br>
            <i>If you would prefer to pay by check, please mail your donation
            to EducationNC, P. O. Box 1636, Raleigh, NC 27602.</i>
          </p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="donate-iframe">
          <script src="https://donorbox.org/widget.js" paypalExpress="false"></script>
          <iframe class="donorbox" src="https://donorbox.org/embed/donation-page-8?default_interval=m" height="685px" width="100%"
          style="min-width:310px; max-height:none!important" seamless="seamless" name="donorbox"
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



    <!-- <script src=”https://donorbox.org/widget.js” paypalExpress=”false”></script>
    <iframe src=”https://donorbox.org/embed/donation-page-8″
    height=”685px” width=”100%” style=”max-width:500px; min-width:310px; max-height:none!important”
    seamless=”seamless” name=”donorbox” frameborder=”0″ scrolling=”no” allowpaymentrequest>
    </iframe> -->

<?php endwhile; ?>
