/* Copyright 2017-2018 Yorick Phoenix. All rights reserved */

/* @flow */

var _gYPCS = "Copyright (c) 2017-2018, Yorick Phoenix";

$(document).ready(
	function DocReady()
	{
		'use strict';
		var oldest = 0;

		/**
		 * Flip / rotate a tile in 20 steps
		 *
		 * @param {jQueryObject} which
		 * @param {string}		 dir - rotate, rotateX, rotateY etc
		 * @param {Number}		 [degree] - how much rotated so far
		 */

		function FlipTile(which, dir, degree)
		{
			if (degree === undefined)
			{
				degree = 18;
			}

			which.css('transform', dir + '(' + degree + 'deg)');

			if (degree !== 360)
			{
				setTimeout(function(){FlipTile(which, dir, degree+18);}, 50);
			}
		}

		/**
		 * Flash the BINGO message 3 times
		 *
		 * @param {Number} [count] - on/off toggles so far
		 */

		function FlashBingo(count)
		{
			if (count === undefined)
			{
				count = 0;
			}

			$('#BingoBox').toggle(count % 2 === 0);

			if (count !== 5)
			{
				setTimeout(function(){FlashBingo(count+1);}, 250);
			}
		}

		/**
		 * Update the grid with new words
		 *
		 * @param {Array} words
		 */

		function ApplyNewWords(words)
		{
			var r, row, c, cell;

			$('td').removeClass('sel')
				   .removeAttr('id');

			for (r in words)
			{
				row = words[r];

				for (c in row)
				{
					cell = $('td[data-row="'+r+'"][data-col="'+c+'"]');

					cell.html(row[c].w);

					if (row[c].id !== undefined)
					{
						cell.attr('id', row[c].id);
					}
				}
			}
		}

		/**
		 * Reload the words when user clicks on 'Refresh button'
		 */

		$('#Refresh').click(
			function ReloadWords()
			{
				jQuery.ajax(
					{
						type:		'POST',
						url:		'NewWords.php',
						dataType:	'json',
						cache:	 	false,
						complete:	function(response, status)
									{
										if (status === 'success')
										{
											ApplyNewWords(response.responseJSON);
										}
										else
										{
											alert('Failed to fetch new words');
										}
									}
					});
			});

		/**
		 * Process a click on a tile
		 */

		$('td').click(
			function ToggleSelection()
			{
				var jThis, row, col, hor, ver, dia, bingo;

				jThis = $(this);

				jThis.toggleClass('sel');

				row = jThis.data('row');

				hor = $('td.sel[data-row="' + row + '"]');

				bingo = hor.length === 5;

				if (bingo)
				{
					FlipTile(hor, 'rotateX');
				}

				if (!bingo)
				{
					col = jThis.data('col');

					ver = $('td.sel[data-col="' + col + '"]');

					bingo = ver.length === 5;

					if (bingo)
					{
						FlipTile(ver, 'rotateY');
					}
				}

				if (!bingo &&
					col === row)
				{
					dia = $('td.sel[data-col="1"][data-row="1"],' +
							'td.sel[data-col="2"][data-row="2"],' +
							'td.sel[data-col="3"][data-row="3"],' +
							'td.sel[data-col="4"][data-row="4"],' +
							'td.sel[data-col="5"][data-row="5"]');

					bingo = dia.length === 5;

					if (bingo)
					{
						FlipTile(dia, 'rotate');
					}
				}

				if (!bingo &&
					col === 6 - row)
				{
					dia = $('td.sel[data-col="1"][data-row="5"],' +
							'td.sel[data-col="2"][data-row="4"],' +
							'td.sel[data-col="3"][data-row="3"],' +
							'td.sel[data-col="4"][data-row="2"],' +
							'td.sel[data-col="5"][data-row="1"]');

					bingo = dia.length === 5;

					if (bingo)
					{
						FlipTile(dia, 'rotate');
					}
				}

				if (bingo)
				{
					FlashBingo();
				}
			});

		function BuildRSSFeed(data)
		{
			var jList = $('#simpleCastList');

			if (data.ids !== null)
			{
				data.ids.forEach(
					function _AddIFrame(id)
					{
						var html;

						html = '<iframe frameborder="0" height="200px" scrolling="no" seamless="" '
							 +		   'src="https://embed.simplecast.com/' + id + '?color=3d3d3d" width="100%">'
							 + '</iframe>';

						jList.append(html);
					});
			}

			oldest = data.oldest;

			$('#loadmore').toggleClass('visible', oldest !== 0 && data.ids !== null);
		}

		function FetchRSSFeed()
		{
			jQuery.ajax(
				{
					type:		'GET',
					url:		'rssfeed.php?oldest=' + oldest,
					dataType:	'json',
					cache:	 	false,
					complete:	function(response, status)
								{
									if (status === 'success')
									{
										BuildRSSFeed(response.responseJSON);
									}
								}
				});
		}

		$('#loadmore').click(FetchRSSFeed);

		FetchRSSFeed();
	});
