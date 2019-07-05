<?php
/**
 * @package Charged Tech Bingo Word Generator
 * @author Yorick Phoenix
 * @copyright Copyright (c) 2018-2019, Yorick Phoenix
 */

$url = 'https://feeds.simplecast.com/tbwR5Zjg';
$url = 'https://anchor.fm/s/c5e3024/podcast/rss';

function curlCall($url, $headers = [], $post = [])
{
	static $cBlk;

	if (!isset($cBlk))
	{
		$cBlk = curl_init();

		$curlOptions = [CURLOPT_RETURNTRANSFER	=> TRUE,	// return the results of the request
						CURLOPT_TIMEOUT			=> 20,		// Allow 20 seconds to execute
						CURLOPT_CONNECTTIMEOUT	=> 20];		// Wait 20 seconds while trying to connect
	}

	$curlOptions[CURLOPT_HTTPHEADER] = $headers;
	$curlOptions[CURLOPT_POSTFIELDS] = $post;
	$curlOptions[CURLOPT_URL]		 = $url;

	$curlOptions[CURLOPT_POST] = !empty($post);

	curl_setopt_array($cBlk, $curlOptions);

	$res = curl_exec($cBlk);

	if ($res === FALSE)
	{
		$res = [];
	}
	else
	if (is_string($res))
	{
		$res = ['xml' => simplexml_load_string($res)];
	}

	if (curl_errno($cBlk))
	{
		$res['curl'] = ['errno'  => curl_errno($cBlk),
					  	'errmsg' => curl_error($cBlk)];
	}
	else
	{
		$cErr = curl_getinfo($cBlk, CURLINFO_HTTP_CODE);

		if ($cErr != 200)
		{
			$res['http'] = ['errno'  => $cErr,
						 	'errmsg' => 'HTTP_ERROR'];
		}
	}

	return $res;
}

$res = curlCall($url);

// var_dump($res);

$id   = [];
$urls = [];

$oldest = isset($_GET['oldest']) ? (int) $_GET['oldest'] : 0;

$newOldest = $oldest;
$count = 0;

foreach ($res['xml']->channel->item as $item)
{
	$when = strtotime($item->pubDate);

	if ($oldest === 0 ||
		$when < $oldest)
	{
		$newOldest = $when;

		$url = (string) $item->enclosure['url'];

		$path = parse_url($url, PHP_URL_PATH);
		$bits = explode('/', $path);
		$id = $bits[count($bits)-2];

		// $ids[] = $id;
		$urls[] = str_replace('episodes', 'embed/episodes', $item->link);

		$count++;

		if ($oldest === 0 ||
			$count === 10)
		{
			break;
		}
	}
//	echo '<iframe frameborder="0" height="200px" scrolling="no" seamless="" src="https://embed.simplecast.com/' . $id . '?color=3d3d3d" width="100%"></iframe>';
}

if ($newOldest === $oldest)
{
	$newOldest = 0;
}

exit(json_encode(['oldest' => $newOldest,
				  'ids'	   => $ids,
				  'urls'   => $urls]));

