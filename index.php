<?php
/**
 * @package Charged Tech Bingo Word Generator
 * @author Yorick Phoenix
 * @copyright Copyright (c) 2017, Yorick Phoenix
 */
?>
<!DOCTYPE HTML>
<html>
    <head>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
		<meta name="copyright" content="Copyright 2017 Yorick Phoenix. All rights reserved" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="google" content="notranslate" />
		<meta name="viewport" content=""initial-scale=1.0>
		<title>Charged Tech Buzzword Bingo</title>
		<link rel="stylesheet" type="text/css" href="styles.css" />
    </head>
<body>
	<div id="header">
		<h1 id="title"><a href="https://char.gd/podcast" target="_blank">Charged Tech Podcast Word Bingo</a></h1>
		<button id="Refresh">Refresh</button>
	</div>
<?php
require('WordList.php');
require('Lib.php');

$w = 5;
$h = 5;

echo '<div id="Enc">';
echo '<div id="BingoBox"><div id="Bingo">BINGO</div></div>';
echo '<table>';

$used = [];

for ($r = 1; $r <= $h; $r++)
{
	echo '<tr>';

	for ($c = 1; $c <= $w; $c++)
	{
		$word = RandomWord($words, $used);

		$attr = ' data-row="' . $r . '" data-col="' . $c . '"';

		if ($word === 'Hibiscus')
		{
			$word = '🌺';
			$attr .= ' id="hibiscus"';
		}

		echo '<td' . $attr . '>' . Encode($word) . '</td>';
	}

	echo '</td>';
}

echo '</table>';
echo '</div>';

echo '<iframe scrolling="no" frameborder="no" ' .
		'src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/users/247987851' .
				'&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp">' .
	 '</iframe>';

?>
</body>
<script type="text/javascript" src="jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="likesupernice.js"></script>
</html>
