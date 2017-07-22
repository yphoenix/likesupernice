<?php
/**
 * @package Charged Tech Bingo Word Generator
 * @author Yorick Phoenix
 * @copyright Copyright (c) 2017, Yorick Phoenix
 */

/**
 * Encode a string for display as HTML
 *
 * @param string $str
 *
 * @return string
 */

function Encode($str)
{
	return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_DISALLOWED, 'UTF-8');
}

/**
 * Pick a random word keeping track of which ones have been used so far
 *
 * @param string[] $words
 * @param string[] $used
 *
 * @return sting
 */

function RandomWord($words, &$used)
{
	$word = NULL;
	$max = count($words) - 1;

	while ($word === NULL)
	{
		$idx = random_int(0, $max);

		$word = $words[$idx];

		if (in_array($word, $used))
		{
			$word = NULL;
		}
		else
		{
			$used[] = $word;
		}
	}

	return $word;
}

