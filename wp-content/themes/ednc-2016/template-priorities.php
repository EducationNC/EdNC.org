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

	*, *::before, *::after {
  box-sizing: inherit;
  position: relative;
}

	.text-box {
	  width: 90%;
	  /* padding: 1em; */
	  margin: 0em auto;
		border: 0px solid black;
	  text-align: center;
	  color: #fff;
	  background: rgba(#000, 0.8);
	}

	.content-container {
		display: inline-block;
	}

	.priorities-img {
  	display: inline-block;
	}

	h4.light, h1.light {
		color: black;
	 	text-align: left;
	}

	.large-content {
		padding: 0em;
		width: 40%;
		display: inline-block;
	 	text-align: left;
	}

	body .text-box .gform_wrapper .gform_body .gform_fields .gfield input[type=text],
	body .text-box .gform_wrapper .gform_body .gform_fields .gfield input[type=email],
	body .text-box .gform_wrapper .gform_body .gform_fields .gfield input[type=tel]  {
		border: 2px solid #44474D;
		border-radius: 0px;
	}


	.small-content {
		padding: 0em;
		width: 30%;
		display: inline-block;
	}
	.content-box {
		padding: 1em;
	}

	.content-box p {
		color: #44474D;
	}
	.small-content .content-box {
		/* background-color: #DCDfe5; */
		/* background-color: black;
		opacity: 0.5; */
	}
	.fs-bottom {
		margin-top: 3em;
		/* border: 1px solid black; */
	}
	.fs-bottom img {
		display:block;
		margin:auto;
	}
	@media (max-width: 980px){
	}
	@media (max-width: 780px){
	}
	@media (max-width: 640px){
	}
</style>

<?php while (have_posts()) : the_post(); ?>
<div class="fs-bottom">
	<img class="" src="http://edncstaging.wpengine.com/wp-content/uploads/2019/01/Untitled-3.jpg" alt="" />
</div>

<div class="text-box">
	<div class="large-content">
	  <div class="content-box">
			<h4 class="light">Sign up for our survey</h4>
	    <p class="donate-body">Here at the North Carolina Center for Public Policy Research,
				we want to put the public back in public policy. We’re introducing a new initiative called “AskNC.”</br></br>
				Have you ever wondered why North Carolina’s legislature has a long session and a short session?
				Why North Carolina uses lottery money to fund education? Or why the Eastern box turtle is
				the state reptile of North Carolina?</br></br>
				With AskNC, we want to help you find answers to your questions, to help you understand how North
				Carolina works and why our state is the way it is.</p>
	  </div>
	</div>
	<div class="small-content">
		<div class="content-box">
			<?php gravity_form(9, false, false, false, '', true, 12); ?>
		</div>
	</div>
</div>



<?php endwhile; ?>
