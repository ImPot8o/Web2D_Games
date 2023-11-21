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
require 'common-guest.inc';
require 'common-zlib.inc';

function png_chunk( &$png )
{
	$chunk = array();
	$chunk['PNG'] = substr($png, 0, 8);

	$ed = strlen($png);
	$st = 8;
	while ( $st < $ed )
	{
		//   uppercase     lowercase
		// 1 is critical / optional
		// 2 is public   / private
		// 3 *reserved*  / *invalid*
		// 4 is unsafe   / safe to copy by editor
		$mgc = substr($png, $st+4, 4);
		$len = str2big($png, $st+0, 4);
		printf("%8x , %8x , $mgc\n", $st, $len);

		$dat = substr($png, $st+8, $len);
		if ( ! isset( $chunk[$mgc] ) )
			$chunk[$mgc] = '';
		$chunk[$mgc] .= $dat;

		$st += (8 + $len + 4);
	} // while ( $st < $ed )

	$chunk['IDAT'] = zlib_decode( $chunk['IDAT'] );
	//file_put_contents("png.idat", $chunk['IDAT']);
	return $chunk;
}
//////////////////////////////
function png_unfilter( &$idat, $w, $h, $byte )
{
	echo "== png_unfilter( $w , $h , $byte )\n";
	$rows = array();
	$dpw = $w * $byte;
	for ( $y=0; $y < $h; $y++ )
		$rows[] = substr($idat, $y*($dpw+1), $dpw+1);

	// https://www.w3.org/TR/PNG-Filters.html
	// dp PLTE = 1 2 4 8 , true = 8 16
	// for PLTE , left is left byte regardless bit depth
	// for true , left is left.RGB(A) byte correspond to filtered RGB(A)
	$prv = '';
	for ( $y=0; $y < $h; $y++ )
	{
		$fil = ord( $rows[$y][0] );
		$dat = substr($rows[$y], 1);
		switch ( $fil )
		{
			case 1: // sub
				for ( $x=0; $x < $dpw; $x++ )
				{
					$b0 = ord( $dat[$x] );
					$b1 = ( isset($dat[$x-$byte]) ) ? ord($dat[$x-$byte]) : 0; // left

					$b = ($b0 + $b1) & BIT8;
					$dat[$x] = chr($b);
				}
				break;
			case 2: // up
				for ( $x=0; $x < $dpw; $x++ )
				{
					$b0 = ord( $dat[$x] );
					$b1 = ( isset($prv[$x]) ) ? ord($prv[$x]) : 0; // up

					$b = ($b0 + $b1) & BIT8;
					$dat[$x] = chr($b);
				}
				break;
			case 3: // average
				for ( $x=0; $x < $dpw; $x++ )
				{
					$b0 = ord( $dat[$x] );
					$b1 = ( isset($dat[$x-$byte]) ) ? ord($dat[$x-$byte]) : 0; // left
					$b2 = ( isset($prv[$x      ]) ) ? ord($prv[$x      ]) : 0; // up

					$bs = ($b1 + $b2) / 2;
					$b  = (int)($b0 + $bs) & BIT8;
					$dat[$x] = chr($b);
				}
				break;
			case 4: // paeth
				for ( $x=0; $x < $dpw; $x++ )
				{
					$b0 = ord( $dat[$x] );
					$b1 = ( isset($dat[$x-$byte]) ) ? ord($dat[$x-$byte]) : 0; // left
					$b2 = ( isset($prv[$x      ]) ) ? ord($prv[$x      ]) : 0; // up
					$b3 = ( isset($prv[$x-$byte]) ) ? ord($prv[$x-$byte]) : 0; // up left

					$bs = ($b1 + $b2) - $b3;
					$ba = abs($bs - $b1); // always positive
					$bb = abs($bs - $b2);
					$bc = abs($bs - $b3);

					if ( $ba <= $bb && $ba <= $bc )
						$b = ($b0 + $b1) & BIT8;
					else
					if ( $bb <= $bc )
						$b = ($b0 + $b2) & BIT8;
					else
						$b = ($b0 + $b3) & BIT8;

					$dat[$x] = chr($b);
				}
				break;
			default: // none
				break;
		} // switch ( $fil )

		$rows[$y] = $dat;
		$prv = $dat;
	} // for ( $y=0; $y < $h; $y++ )

	$idat = implode('', $rows);
	return;
}

function png_8bpp( &$idat, $dp )
{
	echo "== png_8bpp( $dp )\n";
	switch ( $dp )
	{
		case 8:
			return $idat;
		case 4:
			$pix = '';
			$len = strlen($idat);
			for ( $i=0; $i < $len; $i++ )
			{
				$b = ord( $idat[$i] );
				$cnt = 8;
				while ( $cnt > 0 )
				{
					$cnt -= 4;
					$b1 = ($b >> $cnt) & 0x0f;
					$pix .= chr($b1);
				}
			} // for ( $i=0; $i < $len; $i++ )
			return $pix;
		case 2:
			$pix = '';
			$len = strlen($idat);
			for ( $i=0; $i < $len; $i++ )
			{
				$b = ord( $idat[$i] );
				$cnt = 8;
				while ( $cnt > 0 )
				{
					$cnt -= 2;
					$b1 = ($b >> $cnt) & 3;
					$pix .= chr($b1);
				}
			} // for ( $i=0; $i < $len; $i++ )
			return $pix;
		case 1:
			$pix = '';
			$len = strlen($idat);
			for ( $i=0; $i < $len; $i++ )
			{
				$b = ord( $idat[$i] );
				$cnt = 8;
				while ( $cnt > 0 )
				{
					$cnt -= 1;
					$b1 = ($b >> $cnt) & 1;
					$pix .= chr($b1);
				}
			} // for ( $i=0; $i < $len; $i++ )
			return $pix;
	} // switch ( $dp )

	return '';
}

function png_plte( &$chunk )
{
	if ( ! isset( $chunk['PLTE'] ) )
		return '';
	echo "== png_plte()\n";

	$len = strlen($chunk['PLTE']);
	$num = (int)($len / 3);
	$pal = '';
	for ( $i=0; $i < $num; $i++ )
	{
		$pal .= substr($chunk['PLTE'], $i*3, 3);
		if ( isset( $chunk['tRNS'][$i] ) )
			$pal .= $chunk['tRNS'][$i];
		else
			$pal .= BYTE;
	}
	return $pal;
}
//////////////////////////////
function png2clut( &$chunk, $w, $h, $dp, $cl, $fname )
{
	echo "== png2clut( $w , $h , $dp , $cl , $fname )\n";

	// cl 3 valid dp = 1 2 4 8
	$pix = '';
	switch ( $dp )
	{
		case 1:
			$w = int_ceil($w, 8);
			png_unfilter($chunk['IDAT'], $w/8, $h, 1);
			$pix = png_8bpp($chunk['IDAT'], 1);
			break;
		case 2:
			$w = int_ceil($w, 4);
			png_unfilter($chunk['IDAT'], $w/4, $h, 1);
			$pix = png_8bpp($chunk['IDAT'], 2);
			break;
		case 4:
			$w = int_ceil($w, 2);
			png_unfilter($chunk['IDAT'], $w/2, $h, 1);
			$pix = png_8bpp($chunk['IDAT'], 4);
			break;
		case 8:
			//$w = int_ceil($w, 1);
			png_unfilter($chunk['IDAT'], $w,   $h, 1);
			$pix = $chunk['IDAT'];
			break;
	} // switch ( $dp )

	$pal = png_plte($chunk);
	$cc  = strlen($pal) / 4;

	$rgba = 'CLUT';
	$rgba .= chrint($cc, 4);
	$rgba .= chrint($w,  4);
	$rgba .= chrint($h,  4);
	$rgba .= $pal;
	$rgba .= $pix;
	file_put_contents("$fname.clut", $rgba);
	return;
}

function png2rgba( &$chunk, $w, $h, $dp, $cl, $fname )
{
	echo "== png2rgba( $w , $h , $dp , $cl , $fname )\n";

	// cl 2 valid dp = 8 16
	// cl 6 valid dp = 8 16
	// tRNS shall NOT appear on (cl & 4)
	$dpw = ( $cl & 4 ) ? 4 : 3;
	if ( $dp == 16 )  $dpw *= 2;

	//save_file("png1.idat", $chunk['IDAT']);
	//save_file("pix1.$dpw", debug_block($chunk['IDAT'],$dpw*$w+1));
	png_unfilter($chunk['IDAT'], $w, $h, $dpw);
	//save_file("png2.idat", $chunk['IDAT']);
	//save_file("pix2.4", debug_block($chunk['IDAT'],4*$w));

	$rgba = 'RGBA';
	$rgba .= chrint($w, 4);
	$rgba .= chrint($h, 4);

	$sz = strlen( $chunk['IDAT'] );
	$i = 0;
	while ( $i < $sz )
	{
		$rgba .= $chunk['IDAT'][$i+0] . $chunk['IDAT'][$i+1] . $chunk['IDAT'][$i+2];
		if ( $cl & 4 ) // is RGBA
		{
			$rgba .= $chunk['IDAT'][$i+3];
			$i += 4;
		}
		else // is RGB
		{
			$rgba .= BYTE;
			$i += 3;
		}
	}
	file_put_contents("$fname.rgba", $rgba);
	return;
}
//////////////////////////////
function png2img( $fname )
{
	$png = file_get_contents($fname);
	if ( empty($png) )  return;

	if ( substr($png, 0, 8) !== PNG_MAGIC )
		return;

	$chunk = png_chunk($png);
	$w = str2big($chunk['IHDR'], 0, 4);
	$h = str2big($chunk['IHDR'], 4, 4);
	$dp = ord( $chunk['IHDR'][ 8] ); // bit depth
	$cl = ord( $chunk['IHDR'][ 9] ); // color type , +1=index  +2=rgb  +4=alpha  (invalid=1 1+4 1+2+4)
	$cm = ord( $chunk['IHDR'][10] ); // compression , 0=zlib
	$fl = ord( $chunk['IHDR'][11] ); // filter , 0=adaptive/5 type
	$in = ord( $chunk['IHDR'][12] ); // interlace , 0=none , 1=adam7

	if ( ($cl & 2) == 0 )  return printf("grayscale not supported\n");
	if (       $dp >  8 )  return printf("bit depth >8 not supported\n");
	if (       $in != 0 )  return printf("adam7 interlace not supported\n");

	if ( $cl & 1 ) // indexed color , CLUT
		return png2clut($chunk, $w, $h, $dp, $cl, $fname);
	else // true color , RGBA
		return png2rgba($chunk, $w, $h, $dp, $cl, $fname);
	return;
}

function pngfile( $ent )
{
	if ( is_file($ent) )
		return png2img($ent);
	if ( ! is_dir($ent) )
		return;

	$list = array();
	lsfile_r($ent, $list);
	foreach ( $list as $fn )
		png2img($fn);
	return;
}

for ( $i=0; $i < $argc; $i++ )
	pngfile( $argv[$i] );
