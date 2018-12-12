<?php

namespace Stringable\DataDash\Extras;

/**
 * Utility function to test if page has children
 */
 function has_children($type = 'page') {
   global $post;

   $children = get_pages(['child_of' => $post->ID, 'post_type' => $type]);
   if( count( $children ) == 0 ) {
     return false;
   } else {
     return true;
   }
}


/**
 * Add CartoDB oembed support
 */
add_action('init', function() {
  wp_oembed_add_provider( '#https?://(?:www\.)?[^/^\.]+\.cartodb\.com/\S+#i', 'http://educationnc.cartodb.com/api/v1/oembed', true );
});


/**
 * Get post ID by full post URL
 */
function full_url_to_postid($url) {
  global $wpdb;
  $postid = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ));
  $postid = array_filter($postid);
  if (!empty($postid)) {
    return $postid[0];
  }
}


/**
 * Retrieves the embed code for a specific post.
 * MODIFIED: To allow-popups in sandboxed iframe
 *
 * @since 4.4.0
 *
 * @param int         $width  The width for the response.
 * @param int         $height The height for the response.
 * @param int|WP_Post $post   Optional. Post ID or object. Default is global `$post`.
 * @return string|false Embed code on success, false if post doesn't exist.
 */
function get_post_embed_html( $width, $height, $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$embed_url = get_post_embed_url( $post );

	$output = '<blockquote class="wp-embedded-content"><a href="' . esc_url( get_permalink( $post ) ) . '">' . get_the_title( $post ) . "</a></blockquote>\n";

	$output .= "<script type='text/javascript'>\n";
	$output .= "<!--//--><![CDATA[//><!--\n";
	if ( SCRIPT_DEBUG ) {
		$output .= file_get_contents( ABSPATH . WPINC . '/js/wp-embed.js' );
	} else {
		/*
		 * If you're looking at a src version of this file, you'll see an "include"
		 * statement below. This is used by the `grunt build` process to directly
		 * include a minified version of wp-embed.js, instead of using the
		 * file_get_contents() method from above.
		 *
		 * If you're looking at a build version of this file, you'll see a string of
		 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
		 * and edit wp-embed.js directly.
		 */
		$output .=<<<JS
		!function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content"),j=b.querySelectorAll("blockquote.wp-embedded-content");for(c=0;c<j.length;c++)j[c].style.display="none";for(c=0;c<i.length;c++)if(d=i[c],d.style.display="",!d.getAttribute("data-secret")){if(f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f),g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}else;}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.style.display="","height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(200>~~g)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);
JS;
	}
	$output .= "\n//--><!]]>";
	$output .= "\n</script>";

	$output .= sprintf(
		'<iframe sandbox="allow-scripts allow-popups allow-popups-to-escape-sandbox" security="restricted" src="%1$s" width="%2$d" height="%3$d" title="%4$s" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content"></iframe>',
		esc_url( $embed_url ),
		absint( $width ),
		absint( $height ),
		esc_attr__( 'Embedded EdNC.org Post' )
	);

	/**
	 * Filter the embed HTML output for a given post.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $output The default HTML.
	 * @param WP_Post $post   Current post object.
	 * @param int     $width  Width of the response.
	 * @param int     $height Height of the response.
	 */
	return apply_filters( 'embed_html', $output, $post, $width, $height );
}
