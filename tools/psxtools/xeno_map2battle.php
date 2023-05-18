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
require 'xeno.inc';

define('VRAM_W', 0x400);
define('VRAM_H', 0x200);
define('NO_TRACE', true);

function vramcopy( &$vram, &$part, $dx, $dy, $w, $h )
{
	for ( $y=0; $y < $h; $y++ )
	{
		$dyy = ($dy+$y) * VRAM_W * 2;
		$syy =      $y  * $w       * 2;
		$dxx = $dyy + ($dx * 2);

		$b1 = substr($part, $syy, $w*2);
		str_update($vram, $dxx, $b1);
	}
	return;
}

function tex2vram( &$file )
{
	global $gp_clut;
	$vram = str_repeat(ZERO, VRAM_W*2*VRAM_H);
	$len = strlen($file);
	$pos = 0;
	while ( $pos < $len )
	{
		$b1 = str2int($file, $pos, 4);
		$bak = $pos;
		switch ( $b1 )
		{
			case 0x1200:
			case 0x1201:
				$bx = str2int($file, $pos+ 4, 2);
				$by = str2int($file, $pos+ 6, 2);
				$dx = str2int($file, $pos+ 8, 2);
				$dy = str2int($file, $pos+10, 2);
				$w  = str2int($file, $pos+0x0c, 2);
				$no = str2int($file, $pos+0x18, 2);
					$pos += (0x800 + $no * 0x800);

				$data = '';
				$h = 0;
				for ( $i=0; $i < $no; $i++ )
				{
					$p1 = $bak + 0x1c + ($i * 2);
					$p1 = str2int($file, $p1, 2);

					$p2 = $bak + 0x800 + ($i * 0x800);
					$data .= substr($file, $p2, $p1*$w*2);
					$h += $p1;
				}

				printf("%6x  %4x  %4x,%4x  %4x,%4x\n", $bak, $b1, $bx+$dx, $by+$dy, $w, $h);
				vramcopy( $vram, $data, $bx+$dx, $by+$dy, $w, $h );
				break;
		} // switch ( $b1 )
	} // while ( $pos < $len )

	return $vram;
}
//////////////////////////////
function sectfile1( &$file )
{
	$sect = array();
	for ( $i=0; $i < 8; $i++ )
	//for ( $i=3; $i < 4; $i++ )
	{
		$p = 0x130 + ($i * 4);
		$p1 = str2int($file, $p+0, 3);
		$p2 = str2int($file, $p+4, 3);

		$sub = substr($file, $p1, $p2-$p1);
		xeno_decode($sub);
		$sect[$i] = $sub;
	}
	return $sect;
}
//////////////////////////////
function sectpix( $str, $dir, &$dec_no, &$file2, &$dec4 )
{
	$pix = '';
	$ty = ord( $str[6] );
	printf("$dir/3.dec = %d\n", $ty);

	switch ( $ty )
	{
		case 0: // compressed on $dec[4]
			$p2 = 4 + ($dec_no * 4);
			$p2 = str2int($dec4, $p2, 4);

			$p3 = 8 + ($dec_no * 4);
			$p3 = str2int($dec4, $p3, 4);

			if ( $p3 < 8 )
				$p3 = strlen($dec4);

			$dec_no++;
			$pix = substr($dec4, $p2, $p3-$p2);
			return $pix;
		case 1: // paired file (vram)
			$data = array();

			$sx = str2int($str, 0, 2);
			$sy = str2int($str, 2, 2);
			$data[] = array($sx,$sy);

			//$data[] = array(0x140,0x100); //  966_2 aveh gear
			//$data[] = array(0x180,0x100); //  966_2 aveh gear

			//$data[] = array(0x200,0x100); // 1180_6 bart
			//$data[] = array(0x240,0x100); // 1180_6 bart

			//$data[] = array(0x140,0x100); // 1106_2 repair gear
			//$data[] = array(0x140,0x100); // 1126_2 kislev gear
			//$data[] = array(0x300,    0); // 1236_8 thames gear
			//$data[] = array(0x140,0x100); // 1906_2 virge
			//$data[] = array(0x1c0,0x100); // 1906_3 el stier

			//$data[] = array(0,0); // 1514_4 gaspar uzuki

			$cnt = count($data);
			$pix = str_repeat(ZERO, 4+($cnt*4));
				str_update($pix, 0, chr($cnt));

			foreach ( $data as $k => $v )
			{
				$w = VRAM_W - $v[0];
				if ( $w > 0x40  )  $w = 0x40;
				$h = VRAM_H - $v[1];
				if ( $h > 0x100 )  $h = 0x100;

				$len = strlen($pix);
					str_update($pix, 4+($k*4), chrint($len, 3));
				$pix .= chrint($w, 2);
				$pix .= chrint($h, 2);
				$pix .= rippix8($file2, $v[0]*2, $v[1], $w*2, $h, VRAM_W*2, VRAM_H);
			}

			return $pix;
		default:
			php_error('UNKNOWN');
			return $pix;
	}
	return $pix;
}

function xeno( $fname1, $fname2 )
{
	$file1 = file_get_contents($fname1); // even
	$file2 = file_get_contents($fname2); // odd
	if ( empty($file1) || empty($file2) )
		return;

	$dir = str_replace('.', '_', $fname1);
	$dec = sectfile1($file1);
	$file2 = tex2vram($file2);

	foreach ( $dec as $k => $v )
		save_file("$dir/$k.dec", $v);

	$cnt = str2int($dec[3], 0, 3);

	$dec_no = 0;
	$pix = '';
	$w = 0;
	$h = 0;

	for ( $i=0; $i < $cnt; $i++ )
	{
		$p1 = $i * 8;
		$p1 = substr($file1, $p1, 8);
		$pix = sectpix($p1, $dir, $dec_no, $file2, $dec[4]);

		$p1 = 4 + ($i * 4);
		$base = str2int($dec[3], $p1, 3);
		$c1   = str2int($dec[3], $base+0, 3);
		$z1   = str2int($dec[3], $base+4+($c1*4), 3);
		$data = substr ($dec[3], $base, $z1);

		// same format as monster battle sprites
		$p1 = 8 + 12 + strlen($data);
		$btl =  chrint(1, 4);
		$btl .= chrint($p1, 4);

		$btl .= chrint(20, 4);
		$btl .= chrint($p1, 4);
		$btl .= chrint(0, 4);
		$btl .= $data;
		$btl .= $pix;
		save_file("$dir/spr/$i.bin", $btl);

	} // for ( $i=0; $i < $cnt; $i++ )
	return;
}

for ( $i=1; $i < $argc; $i += 2 )
{
	if ( ! isset( $argv[$i+1] ) )
		continue;
	xeno( $argv[$i+0] , $argv[$i+1] );
}

/*
1236.bin/1237.bin
	thames  8.bin  marine gear
		0 = vram 340,0  1b478, 88w = 180,  0+100
		1 = vram 356,0  23e80,104w = 196, e0+ 20
		2 = vram 370,0  3c240, 36w = 1b0, e0+ 20
		3 = vram 379,0  440c0, 12w = 1b9, e0+ 20
		4 = vram 37c,0  45000, 12w = 1bc,100
		5 = vram 37f,0  4c800,  4w = 1bf,100
		6 = vram 380,0  1f000,256w = 300,  0
 */
