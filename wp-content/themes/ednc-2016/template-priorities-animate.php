<?php
/*
Template Name: Priorities (animated)
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

	@keyframes fadeInOpacity {
	0% {
		opacity: 0;
	}
	100% {
		opacity: 1;
	}
}

#img1 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 0s;
	animation-fill-mode: forwards;
}

#dot1-1 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 0.2s;
	animation-fill-mode: forwards;
}
#dot1-2 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 0.4s;
	animation-fill-mode: forwards;
}
#dot1-3 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 0.6s;
	animation-fill-mode: forwards;
}
#dot1-4 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 0.8s;
	animation-fill-mode: forwards;
}
#img2 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 1.0s;
	animation-fill-mode: forwards;
}
#dot2-1 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 1.2s;
	animation-fill-mode: forwards;
}
#dot2-2 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 1.4s;
	animation-fill-mode: forwards;
}
#dot2-3 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 1.6s;
	animation-fill-mode: forwards;
}
#dot2-4 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 1.8s;
	animation-fill-mode: forwards;
}

#img3 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 2s;
	animation-fill-mode: forwards;
}
#dot3-1 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 2.2s;
	animation-fill-mode: forwards;
}
#dot3-2 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 2.4s;
	animation-fill-mode: forwards;
}
#dot3-3 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 2.6s;
	animation-fill-mode: forwards;
}

#img4 {
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 2.8s;
	animation-fill-mode: forwards;
}



.large-content{
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 1s;
	animation-fill-mode: forwards;
	-webkit-transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000);
   -moz-transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000);
     -o-transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000);
        transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000); /* ease-out */
}
.small-content{
	opacity: 0;
	animation-name: fadeInOpacity;
	animation-iteration-count: 1;
	animation-timing-function: ease-in;
	animation-duration: 0.5s;
	animation-delay: 1s;
	animation-fill-mode: forwards;
	-webkit-transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000);
   -moz-transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000);
     -o-transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000);
        transition: all 500ms cubic-bezier(0.000, 0.000, 0.580, 1.000); /* ease-out */
}

.text-box {
	width: 70%;
	/* padding: 1em; */
	margin: 0em auto;
	/* border: 1px solid red; */
	text-align: center;
	color: #fff;
	background: rgba(#000, 0.8);
	display:flex;
	justify-content: space-around;
	align-items: center;
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
	}
	@media (max-width: 780px){
	}
	@media (max-width: 640px){
	}
</style>

<?php while (have_posts()) : the_post(); ?>
<div class="fs-bottom">
	<!-- <img class="" src="https://elizabethshealy.com/wp-content/themes/child-theme/assets/images/email-images/priorities.svg" alt="" /> -->
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 990 233"><defs><style>.cls-1{font-size:15px;font-family:PatrickHand-Regular, Patrick Hand;}.cls-1,.cls-3{fill:#12151b;}.cls-2{fill:#98a942;}.cls-4{fill:#f6b042;}.cls-5{fill:#39c;}.cls-6{fill:#e94f37;}</style></defs><title>Untitled-3</title><g id="img4"><text class="cls-1" transform="translate(814.57 69.72) scale(1.23 1)">We surface your <tspan x="-6.46" y="18">priorities for 2019</tspan></text><path class="cls-2" d="M911.83,166.26c4.94,1,3.06,10.58,7.93,11.8q8-23.61,14.69-47.64c.49-1.77.95-3.78,0-5.34a6.75,6.75,0,0,0-3.43-2.34l-53.75-22q3.19,6.38,6.37,12.75a4.49,4.49,0,0,1,.7,3c-.31,1.25-1.6,2-2.77,2.54l-43.13,21c-3,1.44-6.78,2.78-9.24.61l46.94-23.22a7.14,7.14,0,0,0,2.46-1.68c1.63-2,.67-5-.83-7.12s-3.53-4-4-6.5,1.77-5.7,4.16-4.73l56.06,22.66c1.67.67,3.46,1.45,4.37,3,1.06,1.8.61,4.06.13,6.08a402.38,402.38,0,0,1-17.93,56,60.52,60.52,0,0,1-10-15.35,384.26,384.26,0,0,1-51.52,23.67c-.64-1.83,1.43-3.38,3.17-4.22l45.72-22A6.56,6.56,0,0,1,911.83,166.26Z"/></g><g id="dot3-3"><path class="cls-3" d="M769,59.51c2.9-.76,5.56,1.58,7.44,3.68.63.69,1.11,2,.16,2.26a1.73,1.73,0,0,1-1.48-.5A61.23,61.23,0,0,1,769,59.51Z"/></g><g id="dot3-2"><path class="cls-3" d="M741.08,49.52c2.9-.75,5.56,1.59,7.44,3.69.63.69,1.11,1.95.17,2.26a1.77,1.77,0,0,1-1.49-.5A60.1,60.1,0,0,1,741.08,49.52Z"/></g><g id="dot3-1"><path class="cls-3" d="M713.79,33.19c2.75-.7,5.27,1.48,7.06,3.43.59.65,1,1.83.15,2.11a1.66,1.66,0,0,1-1.41-.46A57.77,57.77,0,0,1,713.79,33.19Z"/></g><g id="img3"><g id="Forme_117" data-name="Forme 117"><path class="cls-4" d="M639.54,130c7.9,2.35,16.88,1.65,24.58-1.11a51,51,0,0,0,19.4-12.9c4.23-4.4,7.71-9.4,9.24-14.87,2.09-7.46.36-15.46-3.86-22.29s-10.81-12.55-18.31-17.11a50.66,50.66,0,0,0-16.82-6.87c-7.47-1.41-15.28-.6-22.78.7-10.35,1.81-21.89,5.66-25,14-1.66-.37-1.33-2.41-.45-3.62,6-8.34,18.89-11.24,30.58-12.64,6.15-.73,12.49-1.25,18.54-.09a47.29,47.29,0,0,1,13.09,4.92c8.77,4.61,16.6,10.6,21.82,18s7.67,16.25,5.71,24.64c-2.41,10.33-11.18,19-21.55,25.2-8.09,4.81-18,8.45-27.84,7.25,7.7,2.31,12.92,8.84,13.46,15.54s-3.17,13.37-9,18.1-13.55,7.66-21.54,9.15c7,3.66,9.84,11.78,6.31,17.93s-13,9.53-20.86,7.46c-7-1.84-11.85-7.13-15.08-12.56-7,3.6-16.87,1.84-22.69-3-6.86-5.68-8.27-15.53-2.4-21.9s18.73-7.69,25.73-2.13c-4.26-7.33-.85-17.06,7.5-21.42,1.11-.58,2.6-1.06,3.68-.45-5.9,3.09-9.84,8.58-10.26,14.32s2.68,11.58,8.09,15.24,13,5.06,20,4.23a40.24,40.24,0,0,0,18.84-7.58c4.73-3.45,8.69-8,9.52-13.11.88-5.44-1.94-11.05-6.7-14.91s-11.25-6.08-17.88-6.86c-7.32-.86-15.83.42-19.91,5.48-.86,1.06-2.79,2.33-3.55,1.21,1.53-3.59,5.45-6.25,9.8-7.58a42.19,42.19,0,0,1,13.72-1.32,64.32,64.32,0,0,1-17.31-11.81c-1.68,9-7.87,17.34-16.81,22.7a50,50,0,0,1-31.47,6.26,36.64,36.64,0,0,1-14.09-4.55c-7.65-4.62-11.62-12.34-13.49-20a30.51,30.51,0,0,1-.79-12.56c2.11-12,15.13-21.83,29.76-23.69s30,3.68,39.37,13c-1.62-6.87-1.83-14.08.71-20.76a27.82,27.82,0,0,1,15.69-15.83c-9.11,6.25-14.83,15.7-15.4,25.43-.48,8.17,2.49,16.2,5.88,23.89a23.83,23.83,0,0,0,4.36,7.23,31.71,31.71,0,0,0,6.33,4.62c4.4,2.69,8.89,5.42,14,7m-31.06-.88a20.55,20.55,0,0,0,3.47-7.23c2.89-11-3.42-23-14.69-29.46s-26.72-7.16-39.21-2.52a28.73,28.73,0,0,0-8.33,4.53,22.71,22.71,0,0,0-4.91,5.82c-7.34,12.06-4.32,27.81,7.16,37.44a32,32,0,0,0,9.88,5.74c12.53,4.42,27.3-.05,38-6.95a33.41,33.41,0,0,0,8.6-7.37M581.42,161c-2.57,1.22-4.14,3.45-5.49,5.62-1.48,2.39-2.86,4.88-3.15,7.55-.42,3.81,1.54,7.69,4.88,10.36A22.39,22.39,0,0,0,590.27,189c2.43.13,5.07-.13,6.88-1.46,3-2.23,2.77-6.67,6.27-8.34a5.71,5.71,0,0,0,3.05-5.86c-.54-5.64-6.69-9.9-13.1-12-3.83-1.27-8.38-2-12-.28m31,16.77c-.6.55-1.76,1.16-2.18.52a4.24,4.24,0,0,1,3-3.35,10.09,10.09,0,0,0-4.43-3,15.88,15.88,0,0,1-5.47,13.61,5.24,5.24,0,0,0-1.57,1.84c-.41,1.09.24,2.24.92,3.24a25.14,25.14,0,0,0,6.53,6.86,17,17,0,0,0,9.76,3.06,19.64,19.64,0,0,0,5.71-1.08,18.77,18.77,0,0,0,7.71-4.55,8.51,8.51,0,0,0,2.53-7.37,11.09,11.09,0,0,0-1.64-3.72,18,18,0,0,0-8.83-7.6c-4-1.46-9.14-1.08-12,1.58m-9.71,5.85a5,5,0,0,0,2-3A2.52,2.52,0,0,0,602.68,183.6Z"/></g><text class="cls-1" transform="translate(533.4 22.72) scale(1.23 1)">Statements with greatest <tspan x="1.16" y="18">consensus rise to the top</tspan></text></g><g id="dot2-4"><path class="cls-3" d="M508,50a1.48,1.48,0,0,1-1.34.18c-.69-.4-.05-1.34.59-1.81l6.25-4.53c1.25.52.41,2.2-.61,3A28.12,28.12,0,0,1,508,50Z"/></g><g id="dot2-3"><path class="cls-3" d="M487,65a1.48,1.48,0,0,1-1.34.18c-.69-.4-.05-1.34.59-1.81l6.25-4.53c1.25.52.41,2.2-.61,3A28.12,28.12,0,0,1,487,65Z"/></g><g id="dot2-2"><path class="cls-3" d="M459.64,75.8a13.53,13.53,0,0,1-10.33,1.14,2.26,2.26,0,0,1-1.51-1c-.24-.54.33-1.3.93-1.09a13.16,13.16,0,0,0,10.88-1.26c.54-.34,1.32.28,1.24.87S460.2,75.48,459.64,75.8Z"/></g><g id="dot2-1"><path class="cls-3" d="M410.31,75.39l9,1c.7.07,1.56.26,1.72.87.25,1-1.36,1.31-2.46,1.2l-5.49-.57a5.07,5.07,0,0,1-2.27-.6A1.31,1.31,0,0,1,410.31,75.39Z"/></g><g id="img2"><text class="cls-1" transform="translate(297.24 81.73) scale(1.24 1)">Add your own</text><path class="cls-5" d="M385.88,101.53c5.94,1,8.68,7.5,9.9,13.11a113.13,113.13,0,0,1,.09,47.49c-1.84,8.63-4.68,17.67-1.74,26a24.18,24.18,0,0,0,10.42,12.3c2.95,1.79,6.8,4.72,5,7.55-15.58-13.78-38.42-17.1-59.7-16.88s-42.77,3.33-63.72-.27c-3.91-.67-8.16-1.82-10.31-5a14,14,0,0,1-1.87-5.77,235.78,235.78,0,0,1-2.17-59.8c.26-2.82.59-5.72,2-8.22,3-5.35,9.91-7.37,16.16-8.65a264.86,264.86,0,0,1,96-1.89m-2.11,2.25a266.58,266.58,0,0,0-94.09,2.31c-4.78,1-9.94,2.35-12.79,6.12-2.16,2.84-2.57,6.52-2.84,10a249.15,249.15,0,0,0,2.66,59.88c.62,3.75,5.24,5.41,9.16,6.11,18.17,3.27,36.86,1.48,55.33.74s37.64-.29,54.45,7c-4.16-4.84-5.39-11.44-5-17.69S392.75,166,394,159.86a106.58,106.58,0,0,0-.18-42.77C392.62,111.38,389.82,104.72,383.77,103.78Z"/><path class="cls-5" d="M323.42,128.73q8.26-1.3,16.63-1.91c8.65-.63,17.34-.7,26-.77,1.74,0,3.6,0,5.07.9-1.91,1.79-4.95,1.7-7.63,1.56-13.87-.72-27.77.59-41.46,2.79-.8.13-1.24-1-.77-1.65A3.15,3.15,0,0,1,323.42,128.73Z"/><path class="cls-5" d="M300.86,118.66c-.87.15-1.35-1.11-.84-1.79a3.43,3.43,0,0,1,2.35-1A288.79,288.79,0,0,1,358,113.59c.87,0,2,.33,2,1.16s-1.36,1.2-2.36,1.13A232.29,232.29,0,0,0,300.86,118.66Z"/><path class="cls-5" d="M363.72,141.9c.58.44.28,1.42-.37,1.77a3.73,3.73,0,0,1-2.18.19,126.65,126.65,0,0,0-42.44,2.39c-.83.19-1.37-1-.9-1.69a3.27,3.27,0,0,1,2.22-1.05,157,157,0,0,1,42.74-1.87A1.83,1.83,0,0,1,363.72,141.9Z"/><path class="cls-5" d="M318,159.83a257.41,257.41,0,0,1,43.71,1.63,1.92,1.92,0,0,1,.63.14c.8.39.55,1.67-.23,2.1a4.12,4.12,0,0,1-2.65.13A150.65,150.65,0,0,0,324.58,162C322.11,162.16,319,162,318,159.83Z"/></g><g id="dot1-4"><path class="cls-3" d="M260.82,74.14l8.53.9c.67.07,1.48.25,1.63.82.24.89-1.29,1.22-2.33,1.11l-5.21-.52a4.81,4.81,0,0,1-2.15-.57A1.21,1.21,0,0,1,260.82,74.14Z"/></g><g id="dot1-3"><path class="cls-3" d="M229.09,70l8.53.9c.67.07,1.48.25,1.63.82.24.89-1.29,1.22-2.33,1.11l-5.21-.53a4.67,4.67,0,0,1-2.15-.56A1.21,1.21,0,0,1,229.09,70Z"/></g><g id="dot1-2"><path class="cls-3" d="M204.08,56.52c2.9-.75,5.56,1.59,7.44,3.69.63.69,1.11,1.95.17,2.26a1.77,1.77,0,0,1-1.49-.5A60.1,60.1,0,0,1,204.08,56.52Z"/></g><g id="dot1-1"><path class="cls-3" d="M177.79,42.19c2.75-.7,5.27,1.48,7.06,3.43.59.65,1.05,1.83.15,2.11a1.66,1.66,0,0,1-1.41-.46A57.77,57.77,0,0,1,177.79,42.19Z"/></g><g id="img1"><path class="cls-3" d="M64.3,74.89c4.75-5.78,14.42-7,23-7.69q37.46-2.86,75.08-3.89c2.79-.07,5.73-.11,8.18,1,4.27,1.87,5.47,6.32,5.94,10.23,1.82,15.07-.39,30.25-2.59,45.29-.32,2.13-.7,4.39-2.42,6-2.52,2.4-7,2.6-10.86,2.52-40.74-.84-82.15-11.43-121.54-3-3.58.77-7.84,1.57-10.61-.41,11-.56,20-7.73,24.61-15.81S58.81,92,60.8,83.29c.66-2.93,1.47-5.92,3.5-8.4m21.53-5.58c-7,.58-14.67,1.54-19,6-2.81,2.92-3.55,6.76-4.24,10.41C60.05,99.35,56,114.17,42.46,122.59c39.37-6.46,79.8,3.14,120,4,2.38.05,5,0,6.73-1.33a6.71,6.71,0,0,0,2.14-4.22,160.19,160.19,0,0,0,3.18-45.72c-.28-3.67-1.26-8-5.44-9.47a15.32,15.32,0,0,0-5.3-.51Q124.71,66.1,85.83,69.31Z"/><path class="cls-6" d="M89.41,153.78c-.47-.56-1.53-.45-2.31-.7-1.68-.53-1.61-2.58-.49-3.72s2.86-1.76,4.13-2.78,1.92-2.9.56-3.85a34.33,34.33,0,0,1-5-4.24c-.75-.77-1.5-1.76-1.08-2.69.48-1.08,2.34-1.38,3.62-.88a7,7,0,0,1,2.87,2.48l2.23,2.84a2,2,0,0,0,1.38.93,2.06,2.06,0,0,0,1.58-.93l5.42-6.42A11.87,11.87,0,0,1,105,137.3a3.46,3.46,0,0,1-.62,3.92c-1.67,1.61-5.47,2-5.43,4.14s4.11,2.9,4.14,5c0,1.36-2,2.3-3.66,1.92a4.48,4.48,0,0,1-3.15-3c-.25-.72-.79-1.71-1.67-1.47l-3.3,5.27c-.39.62-1.43,1.3-1.9.72m5.67-9a49,49,0,0,0,6.67-4.17,3.9,3.9,0,0,0,1.47-1.62,1.3,1.3,0,0,0-.93-1.67l-6.14,5.55c-.6.54-1.24,1.2-1.07,1.91m-2.74,2.73A6.88,6.88,0,0,0,88,151.4c2.39-.12,4.44-2,4.34-3.89m-3.82-9.1,3,2.34A6.46,6.46,0,0,0,88,136.14a1.29,1.29,0,0,0-.67.91,2.34,2.34,0,0,1,1.14,1.37m10.3,9.63a1.63,1.63,0,0,0-1.72-.88,5,5,0,0,0,3.6,3.58,3.54,3.54,0,0,0-1.8-2.7Z"/><path class="cls-2" d="M121.2,147.45a5.36,5.36,0,0,1-2-2.09,2.31,2.31,0,0,1,1.28-2.82,3.73,3.73,0,0,1,3.7.26,74,74,0,0,1,7.52,6.39q6.09-6.37,12.54-12.54a1.53,1.53,0,0,1,.85-.49,1.57,1.57,0,0,1,1.21.62l3.59,3.67c.27.29.56.6.53,1s-.34.63-.64.88q-7.55,6.22-15.81,11.85a3.08,3.08,0,0,1-1.37.63,3.28,3.28,0,0,1-2.21-.83l-9.15-6.49m2.4-.5,6.38,4.69a3.45,3.45,0,0,0,2.34.92,3.27,3.27,0,0,0,1.61-.77l10.12-7.5a7.27,7.27,0,0,0,2.67-2.8,2.23,2.23,0,0,0-1.51-3l-12.7,12.26c-.29.28-.83.57-1.14.3a89.86,89.86,0,0,0-7.91-6.52c-.64-.46-1.56-1-2.29-.58C121.29,145.18,122.48,146.12,123.6,147Z"/><path class="cls-3" d="M118.26,100.1q14.71-.19,29.41,0a2.24,2.24,0,0,1,1.25.27c.55.4.27,1.2-.35,1.54a4.54,4.54,0,0,1-2.16.32l-24.49-.39C120.33,101.81,118.22,101.37,118.26,100.1Z"/><path class="cls-3" d="M90.77,78.49q22.16-.73,44.33-1.36l16.23-.48c0,1.37-2.2,1.86-3.9,1.9L92.46,80a3.21,3.21,0,0,1-1.79-.31A.67.67,0,0,1,90.77,78.49Z"/><path class="cls-3" d="M153.43,89c.72.27.55,1.23-.12,1.58a4.29,4.29,0,0,1-2.32.19c-11.51-1.11-23.13.27-34.7.77-1.07,0-2.62-.48-2.11-1.23a1.71,1.71,0,0,1,1.26-.46,296.79,296.79,0,0,1,37.37-.94A2.11,2.11,0,0,1,153.43,89Z"/><text class="cls-1" transform="translate(76 18.73) scale(1.24 1)">Weigh in on <tspan x="-15.64" y="18">statements from </tspan><tspan x="-13.06" y="36">your community</tspan></text></g></svg>
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
