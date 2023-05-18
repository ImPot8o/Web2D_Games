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
require 'common-json.inc';
require 'class-atlas.inc';
require 'quad.inc';
require 'quad_mana.inc';

define('CANV_S', 0x100);

$gp_pix  = '';
$gp_clut = array();

function sect1( &$file, $base, $fn )
{
	$num = ord( $file[$base] );
		$base++;
	printf("=== sect1( %x, $fn ) = $num\n", $base);
	if ( $num == 0 )
		return;

	$data = array();
	while ( $num > 0 )
	{
		if ( $base[7] != BYTE )
			array_unshift($data, substr($file, $base, 12));
		$base += 12;
		$num--;
	}
	if ( empty($data) )
		return;

	$pix = copypix_def(CANV_S,CANV_S);

	global $gp_pix, $gp_clut;
	foreach ( $data as $v )
	{
		zero_watch("v7" , $v[ 7]);
		zero_watch("v8" , $v[ 8]);
		zero_watch("v10", $v[10]);
		zero_watch("v11", $v[11]);

		//if ( $v[9] == chr(1) )
			//continue;

		// 0  1  2  3  4 5 6  7 8 9 a b
		// dx dy sx sy w h cn - - - - -
		$dx = sint8( $v[0] );
		$dy = sint8( $v[1] );
		$pix['dx'] = $dx + (CANV_S / 2);
		$pix['dy'] = $dy + (CANV_S / 2);

		$sx = ord( $v[2] );
		$sy = ord( $v[3] );
		$w  = ord( $v[4] );
		$h  = ord( $v[5] );
		$cn = ord( $v[6] );

		$pix['src']['w'] = $w;
		$pix['src']['h'] = $h;
		$pix['src']['pix'] = rippix8($gp_pix, $sx, $sy, $w, $h, 0x100, 0x3e);
		$pix['src']['pal'] = substr($gp_clut, $cn*0x40, 0x40);
		$pix['bgzero'] = 0;

		$p9 = ord($v[9]);
		$pix['alpha'] = '';
		if ( $p9 == 1 ) // mask / 1 + image
			$pix['alpha'] = 'wm_alp1';
		if ( $p9 == 3 ) // mask / 5 + image
			$pix['alpha'] = 'wm_alp3';

		printf("%4d , %4d , %4d , %4d , %4d , %4d", $dx, $dy, $sx, $sy, $w, $h);
		printf(" , $cn , $p9\n");
		copypix($pix);
	} // foreach ( $data as $v )

	savepix($fn, $pix);
	return;
}
//////////////////////////////
function landdata( &$file, $dir )
{
	printf("=== landdata( $dir )\n");

	global $gp_pix, $gp_clut;
	$pix = substr($file, 0, 0x1f00);
	$pal = substr($file, 0x1f00, 0x100);

	bpp4to8($pix);
	$gp_pix  = $pix;
	$gp_clut = pal555($pal);

	$id = 0;
	$st = 0x2044;
	while (1)
	{
		$p1 = str2int($file, $st, 2);
		if ( $p1 == 0 )
			return;
		$fn = sprintf('%s/%04d', $dir, $id);
		sect1($file, $p1+0x2004, $fn);

		$st += 2;
		$id++;
	}
	return;
}

function mana( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$dir = str_replace('.', '_', $fname);

	if ( strlen($file) > 0x4000 ) // for /wm/test/landdata.dat
	{
		$id = 0;
		$st = 0;
		while (1)
		{
			$p1 = str2int($file, $st+0, 3);
			$p2 = str2int($file, $st+4, 3);
				$id++;
				$st += 4;
			if ( $p1 == BIT24 )
				return;
			if ( $p2 == BIT24 )
				$p2 = strlen($file);

			$sz = $p2 - $p1;
			printf("extract %x - %x [%x]\n", $p1, $p2, $sz);
			if ( $sz < 0x1f00 )
				continue;

			$str = substr($file, $p1, $sz);
			landdata($str, "{$dir}_{$id}");

		}
		return;
	}
	else // for /wm/wland/*.dat
		landdata($file, $dir);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	mana( $argv[$i] );

/*
v9 == 0
	bone.dat
	gato.dat
	gomiyama.dat
	jungle.dat
	kiruma.dat
	naraku.dat
	orcha.dat
	rusheim.dat
	rushei2.dat
	ryuon.dat
	sand.dat
	urukan.dat
	wmori.dat
v9 != 1
	fiegu.dat
	kirameki.dat
	norun.dat
	roa.dat

// image masking
// Legend of Mana - /wm/wmland/roa.dat
//  38,46  0-4  fg#502808 + bg#784048
//
// screenshot
//  2 #d76f50 =+603008 =0 1,3
//  6 #c76750 =+502808 =1 1
//  1 #a75748 =+301800
//  5 #974f48 =+201000
//  4 #874848 =+100800
//
//  97,93 += 135,139 / +7,+11
//  0  1,3
//  1  1
//  2  3,3,3
//  3  3,3
//  4  3
 */
