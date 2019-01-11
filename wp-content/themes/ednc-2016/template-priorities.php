<?php
/*
Template Name: Priorities
*/

use Roots\Sage\Titles;
?>
<style>
	.banner-image {
		text-align: center;
	}
	.priorities-img {
  	display: inline-block;
	}
	.large-content {
		padding: 2em;
	}
	.small-content {
		padding: 2em;
	}
	.content-box {
		padding: 1em;
	}
	.small-content .content-box {
		background-color: #DCDfe5;
	}


	@media (max-width: 980px){
	}
	@media (max-width: 780px){
	}
	@media (max-width: 640px){
	}
</style>

<?php while (have_posts()) : the_post(); ?>
<div class="container">
  <div class="row spacing">
    <div class="col-md-8 large-content">
      <div class="content-box">
				<h4>Sign up for our survey</h4>
        <p class="donate-body">Here at the North Carolina Center for Public Policy Research,
					we want to put the public back in public policy. We’re introducing a new initiative called “AskNC.”</br></br>
					Have you ever wondered why North Carolina’s legislature has a long session and a short session?
					Why North Carolina uses lottery money to fund education? Or why the Eastern box turtle is
					the state reptile of North Carolina?</br></br>
					With AskNC, we want to help you find answers to your questions, to help you understand how North
					Carolina works and why our state is the way it is.</p>
      </div>
    </div>
    <div class="col-md-4 small-content">
			<div class="content-box">
				<h1>Sign up for our survey</h1>
			</div>
    </div>
  </div>
</div>


<?php endwhile; ?>
