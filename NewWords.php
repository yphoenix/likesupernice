<?php
/**
 * @package Charged Tech Bingo Word Generator
 * @author Yorick Phoenix
 * @copyright Copyright (c) 2017, Yorick Phoenix
 */

require('WordList.php');
require('Lib.php');

/**
 * Respond to Ajax call to get some new words
 */

$used = [];

$data = [];

$w = 5;		// @TODO should be passed arguments
$h = 5;

for ($r = 1; $r <= $h; $r++)
{
	for ($c = 1; $c <= $w; $c++)
	{
		$word = RandomWord($words, $used);

		$id = '';

		if ($word === 'Hibiscus')
		{
			$word = '🌺';
			$id = 'hibiscus';
		}

		$data[$r][$c] = ['w' => Encode($word)];

		if ($id !== '')
		{
			$data[$r][$c]['id'] = $id;
		}
	}
}

echo json_encode($data);
