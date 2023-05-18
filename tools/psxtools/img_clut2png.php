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

function pngfilter( &$pix, $w, $h, $byte )
{
	// add filter byte on the beginning of every row
	// 0 = none
	// 1 = Sub(x) + Raw(x-bpp)
	// 2 = Up(x) + Prior(x)
	// 3 = Average(x) + floor((Raw(x-bpp)+Prior(x))/2)
	// 4 = Paeth(x) + PaethPredictor(Raw(x-bpp), Prior(x), Prior(x-bpp))
	$idat = '';
	for ( $y=0; $y < $h; $y++ )
		$idat .= ZERO . substr($pix, $y*$w*$byte, $w*$byte);
	$pix = $idat;
	return;
}

function pngchunk( $name, $data, $zlib=false )
{
	$sect = $name;
	if ( $zlib )
		//$sect .= zlib_encode($data, ZLIB_ENCODING_DEFLATE, 9);
		$sect .= zlib_deflate_store($data);
	else
		$sect .= $data;

	$len = strlen($sect) - 4;
	$crc = crc32 ($sect);

	$png = '';
	$png .= chrbig($len, 4);
	$png .= $sect;
	$png .= chrbig($crc, 4);
	return $png;
}
//////////////////////////////
function clut2png( &$file, $fname )
{
	echo "== clut2png( $fname )\n";
	$cc = str2int($file,  4, 4);
	$w  = str2int($file,  8, 4);
	$h  = str2int($file, 12, 4);

	$plte = '';
	$trns = '';
	for ( $i=0; $i < $cc; $i++ )
	{
		$p = 0x10 + ($i * 4);
		$plte .= $file[$p+0] . $file[$p+1] . $file[$p+2];
		$trns .= $file[$p+3];
	}
	if ( $cc > 0x100 )
	{
		php_notice('PLTE over 0x100 colors , TRIMMED');
		$plte = substr($plte, 0, 0x300);
		$trns = substr($trns, 0, 0x100);
	}
	$trns = rtrim($trns, BYTE);

	$idat = substr($file, 0x10 + $cc*4, $w*$h);
	pngfilter($idat, $w, $h, 1);

	// PNG 8-bit CLUT
	$ihdr = '';
	$ihdr .= chrbig($w, 4); // width
	$ihdr .= chrbig($h, 4); // height
	$ihdr .= chr(8); // bit depth , 1 2 4 8 16
	$ihdr .= chr(3); // color type , +1=index  +2=rgb  +4=alpha  (invalid=1 1+4 1+2+4)
	$ihdr .= ZERO; // compression , 0=zlib
	$ihdr .= ZERO; // filter , 0=adaptive/5 type
	$ihdr .= ZERO; // interlace , 0=none , 1=adam7

	//$png = chr(0x89) . "PNG\r\n" . chr(0x1a) . "\n";
	$png = "\x89PNG\x0d\x0a\x1a\x0a";
	$png .= pngchunk("IHDR", $ihdr);
	$png .= pngchunk("PLTE", $plte);
	if ( ! empty($trns) )
		$png .= pngchunk("tRNS", $trns);
	$png .= pngchunk("IDAT", $idat, true);
	$png .= pngchunk("IEND", '');

	file_put_contents("$fname.png", $png);
	return;
}

function rgba2png( &$file, $fname )
{
	echo "== rgba2png( $fname )\n";
	$w = str2int($file, 4, 4);
	$h = str2int($file, 8, 4);

	$idat = substr($file, 12, $w*$h*4);
	pngfilter($idat, $w, $h, 4);

	// PNG 8-bit RGBA
	$ihdr = '';
	$ihdr .= chrbig($w, 4); // width
	$ihdr .= chrbig($h, 4); // height
	$ihdr .= chr(8); // bit depth
	$ihdr .= chr(6); // color type , +1=index  +2=rgb  +4=alpha  (invalid=1 1+4 1+2+4)
	$ihdr .= ZERO; // compression , 0=zlib
	$ihdr .= ZERO; // filter , 0=adaptive/5 type
	$ihdr .= ZERO; // interlace , 0=none , 1=adam7

	//$png = chr(0x89) . "PNG\r\n" . chr(0x1a) . "\n";
	$png = "\x89PNG\x0d\x0a\x1a\x0a";
	$png .= pngchunk("IHDR", $ihdr);
	$png .= pngchunk("IDAT", $idat, true);
	$png .= pngchunk("IEND", '');

	file_put_contents("$fname.png", $png);
	return;
}
//////////////////////////////
function img2png( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$mgc = substr($file, 0, 4);
	if ( $mgc === 'CLUT' )
		return clut2png($file, $fname);
	if ( $mgc === 'RGBA' )
		return rgba2png($file, $fname);

	return;
}

for ( $i=0; $i < $argc; $i++ )
	img2png( $argv[$i] );
