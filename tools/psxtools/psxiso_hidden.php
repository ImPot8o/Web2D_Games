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

define('XASTRH', "x60x01x01x80");
//define('DRY_RUN', true);

function chrbase10( $chr )
{
	$b = ord( $chr );
	$b1 = ($b >> 0) & BIT4;
	$b2 = ($b >> 4) & BIT4;
	return ($b2 * 10) + $b1;
}

function cdpos2int( $min , $sec , $frame )
{
	$m = chrbase10($min);
	$s = chrbase10($sec);
	$f = chrbase10($frame);

	$s -= 2;
	$s += ($m * 60);
	$f += ($s * 75);
	if ( $f < 0 )
		return 0;
	return $f * 0x800;
}

//////////////////////////////
function valkyrie_decrypt( &$str, &$dic, $key )
{
	printf("valkyrie TOC key : %8x\n", $key);
	$toc = '';
	$ed = strlen($dic);
	$st = 0;
	$k = $key;
	while ( $st < $ed )
	{
		$w = str2int($str, $st*4, 4);
		$b = ord( $dic[$st] );
			$st++;

		$w ^= $k;
		$k ^= ($k << 1);
		$toc .= chrint($w,4);

		$b ^= $k;
		$k  = ~($k) ^ $key;
	}
	return $toc;
}
function valkyrie_toc( $fp, $dir, &$toc )
{
	$list = array();
	$ed = strlen($toc);
	$st = 4;
	while ( $st < $ed )
	{
		$no = $st / 4;
		$lba = cdpos2int($toc[$st+0], $toc[$st+1], $toc[$st+2]);
			$st += 4;

		if ( $lba != 0 )
			$list[] = array($no, $lba);
	}

	$txt = '';
	$ed = count($list);
	$st = 0;
	while ( $st < $ed )
	{
		list($no,$lba) = $list[$st];
		$txt .= sprintf("%4x , %8x\n", $no, $lba);

		$fn = sprintf('%s/%06d.bin', $dir, $no);
		if ( isset( $list[$st+1] ) )
			$sz = $list[$st+1][1] - $lba;
		else
		{
			fseek($fp, 0, SEEK_END);
			$sz = ftell($fp) - $lba;
		}

		$sub = fp2str($fp, $lba, $sz);
		if ( substr($sub, 0, 4) === XASTRH )
			$sub = ZERO;
		save_file($fn, $sub);
		$st++;
	}

	echo "$txt\n";
	save_file("$dir/toc.bin", $toc);
	save_file("$dir/toc.txt", $txt);
	return;
}
//////////////////////////////
function iso_valkyrie($fp, $dir)
{
	printf("%s [%s]\n", $dir, __FUNCTION__);

	// sub_80011d38 , SLPM_863.79
	$str = fp2str($fp, 0x4b000, 0x5000);
	$dic = fp2str($fp, 0x50000, 0x1400);
	$key = 0x64283921;

	$toc = valkyrie_decrypt( $str, $dic, $key );
	valkyrie_toc($fp, $dir, $toc);
	return;
}

function iso_starocean2nd1($fp, $dir)
{
	printf("%s [%s]\n", $dir, __FUNCTION__);

	// sub_80011c20 , SLPM_861.05
	$str = fp2str($fp, 0x96000, 0x4800);
	$dic = fp2str($fp, 0x9a800, 0x1200);
	$key = 0x13578642;

	$toc = valkyrie_decrypt( $str, $dic, $key );
	valkyrie_toc($fp, $dir, $toc);
	return;
}
function iso_starocean2nd2($fp, $dir)  { return iso_starocean2nd1($fp, $dir); }

function iso_xenogears($fp, $dir)
{
	printf("%s [%s]\n", $dir, __FUNCTION__);
	$str = fp2str($fp, 0xc000, 0x8000);

	$txt = '';
	$dn = '';
	for ( $i=0; $i < 0x8000; $i += 7 )
	{
		$no = $i / 7;
		$lba = str2int($str, $i+0, 3, true);
		$siz = str2int($str, $i+3, 4, true);

		if ( $lba == -1 )
			break;
		if ( $lba == 0 || $siz == 0 )
			continue;

		if ( $siz < 0 )
		{
			$dn = $no;
			$siz *= -1;
			$txt .= sprintf("%8x , DIR  , %8x , %s\n", $lba*0x800, $siz, $dn);
		}
		else
		{
			$fn = sprintf('%s/%06d.bin', $dn, $no);
			$txt .= sprintf("%8x , FILE , %8x , %s\n", $lba*0x800, $siz, $fn);

			$sub = fp2str($fp, $lba*0x800, $siz);
			if ( substr($sub, 0, 4) === XASTRH )
				$sub = ZERO;
			save_file("$dir/$fn", $sub);
		}
	} // for ( $i=0; $i < 0x8000; $i += 7 )

	echo "$txt\n";
	save_file("$dir/toc.bin", $str);
	save_file("$dir/toc.txt", $txt);
	return;
}

function iso_dewprism($fp, $dir)
{
	printf("%s [%s]\n", $dir, __FUNCTION__);
	$str = fp2str($fp, 0xc000, 0x4cd8);

	$txt = '';
	$ed = strlen($str) - 4;
	$st = 0;
	$dn = '';
	while ( $st < $ed )
	{
		$no = $st / 4;
		$lba1 = str2int($str, $st+0, 3) & 0x7fffff;
		$lba2 = str2int($str, $st+4, 3) & 0x7fffff;
			$st += 4;

		$sz = $lba2 - $lba1;
		if ( $sz > 0 )
		{
			$fn = sprintf('%s/%06d.bin', $dn, $no);
			$txt .= sprintf("%8x , FILE , %8x , %s\n", $lba1*0x800, $sz*0x800, $fn);

			$sub = fp2str($fp, $lba1*0x800, $sz*0x800);
			if ( substr($sub, 0, 4) === XASTRH )
				$sub = ZERO;
			save_file("$dir/$fn", $sub);
		}
		else
		{
			$dn = $no;
			$txt .= sprintf("%8x , DIR  , %8x , %s\n", $lba1*0x800, 0, $dn);
		}
	} // while ( $st < $ed )

	echo "$txt\n";
	save_file("$dir/toc.bin", $str);
	save_file("$dir/toc.txt", $txt);
	return;
}
//////////////////////////////
function isofile( $fname )
{
	$fp = fopen_file($fname);
	if ( ! $fp )  return;

	$root = fp2str($fp, 0x8000, 0x800);
	if ( substr($root, 1, 5) !== 'CD001' )
		return printf("%s is not an ISO 2048/sector file\n", $fname);

	$dir = str_replace('.', '_', $fname);

	$mgc = substr($root, 0x28, 0x20);
	$mgc = strtolower( trim($mgc, " ".ZERO) );

	$func = "iso_" . $mgc;
	if ( ! function_exists($func) )
		return printf("%s [%s] is not supported (yet)\n", $fname, $func);

	$func($fp, $dir);
	fclose($fp);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	isofile( $argv[$i] );
