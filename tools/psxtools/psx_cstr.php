<?php
/*
[license]
[/license]
 */
require "common.inc";

define("CLEN", 4);

function sjisstr( &$str, $siz, $fname, $pos )
{
	$st = 0;
	while ( $st < $siz )
	{
		$b1 = ord( $str[$st+0] );

		if ( $b1 >= 0x81 && $b1 <= 0x84 ) // full-width
		{
			if ( ! isset( $str[$st+1] ) )
				return;
			$b2 = ord( $str[$st+1] );
			if ( $b2 >= 0x40 && $b2 <= 0xfc )
				$st += 2;
			else
				return;
		}
		else
		if ( $b1 == 0x87 ) // cp932 nec extend (page 13)
		{
			if ( ! isset( $str[$st+1] ) )
				return;
			$b2 = ord( $str[$st+1] );
			if ( $b2 >= 0x54 && $b2 <= 0x9c )
				$st += 2;
			else
				return;
		}
		else
		if ( $b1 >= 0x88 && $b1 <= 0x9f ) // kanji 1
		{
			if ( ! isset( $str[$st+1] ) )
				return;
			$b2 = ord( $str[$st+1] );
			if ( $b2 >= 0x40 && $b2 <= 0xfc )
				$st += 2;
			else
				return;
		}
		else
		if ( $b1 >= 0xa1 && $b1 <= 0xdf ) // half-width
			$st++;
		else
		if ( $b1 >= 0xe0 && $b1 <= 0xea ) // kanji 2
		{
			if ( ! isset( $str[$st+1] ) )
				return;
			$b2 = ord( $str[$st+1] );
			if ( $b2 >= 0x40 && $b2 <= 0xfc )
				$st += 2;
			else
				return;
		}
		else
		if ( $b1 >= 0xed && $b1 <= 0xee ) // cp932 nec extend (page 89-92)
		{
			if ( ! isset( $str[$st+1] ) )
				return;
			$b2 = ord( $str[$st+1] );
			if ( $b2 >= 0x40 && $b2 <= 0xfc )
				$st += 2;
			else
				return;
		}
		else
		if ( $b1 >= 0xfa && $b1 <= 0xfc ) // cp932 ibm extend (page 115-119)
		{
			if ( ! isset( $str[$st+1] ) )
				return;
			$b2 = ord( $str[$st+1] );
			if ( $b2 >= 0x40 && $b2 <= 0xfc )
				$st += 2;
			else
				return;
		}
		else
		if ( $b1 == 0x09 || $b1 == 0x0d || $b1 == 0x0a ) // tab + newline
		{
			$str[$st] = ' ';
			$st++;
		}
		else
		if ( $b1 >= 0x20 && $b1 <= 0x7e ) // alphanum
			$st++;
		else
			return;
	} // while ( $st < $siz )

	printf("%s+%6x  %s\n", $fname, $pos, $str);
	return;
}

function psxcstr( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$ed = strlen($file);
	$st = 0;
	while ( $st < $ed )
	{
		if ( $file[$st] == ZERO )
		{
			$st += 4;
			continue;
		}

		$bak = $st;
		$str = substr0($file, $st, ZERO);
		$siz = strlen($str);
			$st = int_ceil($st + $siz, 4);

		if ( $siz < CLEN )
			continue;
		sjisstr($str, $siz, $fname, $bak);
	} // while ( $st < $ed )

	return;
}

for ( $i=1; $i < $argc; $i++ )
	psxcstr( $argv[$i] );
