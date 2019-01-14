<?php
/*
Template Name: Priorities
*/

use Roots\Sage\Titles;
?>
<style>
	/* .banner-image {
		text-align: center;
	}

	*, *::before, *::after {
  box-sizing: inherit;
  position: relative;
} */


/* #overlay {
	background: #000;
	background: rgba(0, 0, 0, 0.3);
	display: block;
	float: left;
	height: 100%;
	position: fixed;
	top: 0; left: 0;
	width: 100%;
	z-index: 99;
}

#gform-notification {
	background: #731454;
	color: white;
	border-radius: 0px;
	display: block;
	margin: auto;
	max-height: 300px;
	max-width: 520px;
	padding: 61px;
	position: fixed;
	top: 0; left: 0; right: 0; bottom: 0;
	text-align: center;
	width: 100%;
	z-index: 101;
}

#gform-notification .button {
	margin: 20px 0 0;
	padding: 12px 24px;
	color: white;
} */

.text-box {
	width: 80%;
	/* padding: 1em; */
	margin: 0em auto;
	/* border: 1px solid red; */
	text-align: center;
	background: rgba(#000, 0.8);
	display:flex;
	justify-content: space-around;
	align-items: center;
}

.full-size {
	display: block;
}

.mobile-img {
	display: none;
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
		width: 45%;
		height: 100%;
		display: inline-block;
	 	text-align: left;
		/* border: 1px solid black; */
	}

	body .text-box .gform_wrapper .gform_body .gform_fields .gfield input[type=text],
	body .text-box .gform_wrapper .gform_body .gform_fields .gfield input[type=email],
	body .text-box .gform_wrapper .gform_body .gform_fields .gfield input[type=tel]  {
		border: 1px solid #C3C6CC;
		border-radius: 0px;
	}
	.text-box .gform_body input[type="text"]:focus, .gform_body textarea:focus {
		background-color: white;
		border-color: #D6D6D6;
		color: #333;
	}

	.small-content {
		padding: 0em;
		width: 50%;
		display: inline-block;
		/* border: 1px solid black; */
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
		padding-top: 4em;
		width: 80%;
		display:block;
		margin:auto;
		/* border: 1px solid black; */
	}
	@media (max-width: 980px){
		.text-box {
			width: 80%;
			/* padding: 1em; */
			margin: 0em auto;
			/* border: 1px solid red; */
			text-align: center;
			background: rgba(#000, 0.8);
			display:block;
		}
		.large-content {
			padding: 0em;
			width: 100%;
			height: 100%;
			display: block;
			text-align: left;
			/* border: 1px solid blue; */
		}
		.small-content {
			padding: 0em;
			width: 100%;
			display: block;
			/* border: 1px solid green; */
		}

	}
	@media (max-width: 780px){
		.full-size {
			display: none;
		}
		.mobile-img {
			display: block;
		}
	}
	@media (max-width: 640px){
	}
</style>

<?php while (have_posts()) : the_post(); ?>
<div class="fs-bottom">
	<img class="full-size" src="https://elizabethshealy.com/wp-content/themes/child-theme/assets/images/email-images/priorities.svg" alt="" />
	<img class="mobile-img" src="https://www.ednc.org/wp-content/uploads/2019/01/mobile.jpg" alt="" />
</div>

<div class="text-box">
	<div class="large-content">
	  <div class="content-box">
			<h2>The People's Session</h2>
	    <p class="donate-body">Lots of folks come to Raleigh when the legislature is in session with an agenda.
				Legislators. Lobbyists. Advocacy groups. We want to understand your agenda for education in North Carolina.
				The issues that keep you up at night. Issues that leave you everything from angry to hopeful. We believe
				the future doesn't just happen to us. We believe your voice can shape the direction of our state. Join us.</p>
	  </div>
	</div>
	<div class="small-content">
		<div class="content-box">
			<?php gravity_form(9, false, false, false, '', true, 12); ?>
		</div>
	</div>
</div>



<?php endwhile; ?>
