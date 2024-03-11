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
require 'common.inc';
require 'class-bakfile.inc';
require 'xeno.inc';

function xeno( $fname )
{
	$bak = new bak_file;
	$bak->load($fname);
	if ( $bak->is_empty() )
		return;

	xeno_decode($bak->file);

	printf("%8x -> %8x  %s\n", $bak->filesize(0), $bak->filesize(1), $fname);
	$bak->save();
	return;
}

for ( $i=1; $i < $argc; $i++ )
	xeno( $argv[$i] );
