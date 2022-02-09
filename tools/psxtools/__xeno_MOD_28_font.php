<?php
/*
[license]
Copyright (C) 2019 by Rufas Wan

This file is part of Web2D Games.
    <https://github.com/rufaswan/Web2D_Games>

Web2D Games is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Web2D Games is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Web2D Games.  If not, see <http://www.gnu.org/licenses/>.
[/license]
 */
require "common.inc";

function xeno( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$len = strlen($file);
	$pix = '';
	$p = array(ZERO, "\x01");
	for ( $i=0; $i < $len; $i++ )
	{
		$c = ord( $file[$i] );
		$j = 8;
		while ( $j > 0 )
		{
			$j--;
			$b = ($c >> $j) & 1;
			$pix .= $p[$b];
		}
	} // for ( $i=0; $i < $len; $i++ )

	$w = 16;
	$h = strlen($pix) >> 4;

	$img = array(
		'cc' => 2,
		'w' => $w,
		'h' => $h,
		'pal' => grayclut(2),
		'pix' => $pix,
	);
	save_clutfile("$fname.clut", $img);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	xeno( $argv[$i] );
