<?php
/**
 * @package Charged Tech Bingo Word Generator
 * @author Yorick Phoenix
 * @copyright Copyright (c) 2018, Yorick Phoenix
 */

$url = 'https://rss.simplecast.com/podcasts/4539/rss';

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
//		$res = json_decode($res, TRUE);

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

$id=[];

$ep59time = strtotime('21 Feb 2018 20:46:00 +0000');

foreach ($res['xml']->channel->item as $item)
{
	$when = strtotime($item->pubDate);

	if ($when >= $ep59time)
	{
		$url = (string) $item->enclosure['url'];

		$path = parse_url($url, PHP_URL_PATH);
		$id = pathinfo($path, PATHINFO_FILENAME);

		$ids[] = $id;
	}
	else
	{
		break;
	}

//	echo '<iframe frameborder="0" height="200px" scrolling="no" seamless="" src="https://embed.simplecast.com/' . $id . '?color=3d3d3d" width="100%"></iframe>';
}

exit(json_encode($ids));
