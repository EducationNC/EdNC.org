<?php
/*
Template Name: Priorities NBS
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



/** Background Image **/
.bgimg {
  position: fixed;
  z-index: -1;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-image: url(https://ednc.test/wp-content/uploads/2019/01/1X2A8765-new.jpg);
  background-size: cover;
  background-position: center;

  &::before {
    content: 'Media Query: Max (larger than 900px by 720px)';
    position: absolute;
    display: block;
    width: auto;
    top: 0;
    left: 0;
    background-color: #fff;
    padding: 0.5em;
  }

	  @media screen and (max-width: 900px) and (max-height: 720px) {
	    background-image: url(https://placeimg.com/900/720/any);
	    &::before {
	      content: 'Media Query: max-width: 900px, max-height: 720px';
	    }
	  }

	  @media screen and (max-width: 800px) and (max-height: 640px) {
	    background-image: url(https://placeimg.com/800/640/any);
	    &::before {
	      content: 'Media Query: max-width: 800px, max-height: 640px';
	    }
	  }

	  @media screen and (max-width: 700px) and (max-height: 560px) {
	    background-image: url(https://placeimg.com/700/560/any);
	    &::before {
	      content: 'Media Query: max-width: 700px, max-height: 560px';
	    }
	  }

	  @media screen and (max-width: 600px) and (max-height: 480px) {
	    background-image: url(https://placeimg.com/600/480/any);
	    &::before {
	      content: 'Media Query: max-width: 600px, max-height: 480px';
	    }
	  }

	  @media screen and (max-width: 500px) and (max-height: 400px) {
	    background-image: url(https://placeimg.com/500/400/any);
	    &::before {
	      content: 'Media Query: max-width: 500px, max-height: 400px';
	    }
	  }

	  @media screen and (max-width: 400px) and (max-height: 320px) {
	    background-image: url(https://placeimg.com/800/800/any);
	    &::before {
	      content: 'Media Query: max-width: 400px, max-height: 320px';
	    }
	  }
	}

	.text-box {
	  width: 90%;
	  padding: 3em;
	  margin: 5em auto;
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

	.large-content {
		padding: 2em;
		width: 40%;
		display: inline-block;
		background-color: black;
		opacity: 0.5;
	 	text-align: left;
	}
	.small-content {
		padding: 2em;
		width: 30%;
		display: inline-block;
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

<div class="bgimg"></div>
<div class="text-box">
	<div class="large-content">
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
	<div class="small-content">
		<div class="content-box">
			<h1>Sign up</h1>
			<form>
			  <div class="form-group">
			    <input type="text" id="full-name" class="form-field" placeholder="Your Name" required>
			    <label for="full-name" class="form-label">Your Name</label>
			  </div>

			  <div class="form-group">
			    <input type="email" id="email" class="form-field" placeholder="Your Email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
			    <label for="email" class="form-label">Your Email</label>
			  </div>

			  <div class="form-group">
			    <textarea id="message" class="form-field" placeholder="Your Message" rows="6" required></textarea>
			    <label for="message" class="form-label">Your Message</label>
			  </div>
			</form>
		</div>
	</div>
</div>


<?php endwhile; ?>
