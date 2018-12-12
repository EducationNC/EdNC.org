<?php
/*
        Plugin Name: Custom Cache Purge
*/

add_action("init", "schedule_custom_cron_purge");
add_action("custom_cron_purge", "the_custom_cron_purge");

function schedule_custom_cron_purge()
{
    $timestamp = wp_next_scheduled( "custom_cron_purge" );

    if( $timestamp == false )
    {
        wp_schedule_event( time(), "hourly", "custom_cron_purge");
    }    
    
}


function the_custom_cron_purge()
{

$schema = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
$url = esc_url($schema . $_SERVER["SERVER_NAME"]);

$post_parts = parse_url($url);
$post_uri = $post_parts['path'];

if (!empty($post_parts['query'])) $post_uri.= "?" . $post_parts['query'];
$path = $post_uri;

// SW/CFTP: Ensure the path at least contains a slash

if (!$path) $path = '/*';
$hostname = $post_parts['host'];

if (WPE_CLUSTER_TYPE == "pod") $wpe_varnish_servers = array(
	"localhost"
);
else
if (!isset($wpe_varnish_servers))
{
	if (WPE_CLUSTER_TYPE == "pod") $lbmaster = "localhost";
	else
	if (!defined('WPE_CLUSTER_ID') || !WPE_CLUSTER_ID) $lbmaster = "lbmaster";
	else
	if (WPE_CLUSTER_ID >= 4) $lbmaster = "localhost"; // so the current user sees the purge
	else $lbmaster = "lbmaster-" . WPE_CLUSTER_ID;
	$wpe_varnish_servers = array(
		$lbmaster
	);
}

// Debugging

if (true)
{
	$msg_key = rand();
	$msg = "Varnishes # $msg_key:\n" . "\nHostname:\n" . var_export($hostname, true) . "\nPath:\n" . var_export($path, true);
	var_dump($wpe_varnish_servers);
	var_dump($hostname);
	var_dump($path);
}

// SW/CFTP: Assume we're not using EC�~@�

foreach($wpe_varnish_servers as $varnish)
{
	error_log("JSON: PURGE, $varnish, 9002, $hostname, $path, array( ), 0");
	WpeCommon::http_request_async("PURGE", $varnish, 9002, $hostname, $path, array() , 0);
}

}

?>