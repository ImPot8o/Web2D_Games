<?php
/*
[license]
[/license]
 */
require "common.inc";

function saga2( $fname )
{
	// only allchr.tcl
	if ( stripos($fname, "allchr.tcl") === false )
		return;

	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$pix = "";
	for ( $i=0; $i < 0xb000; $i++ )
	{
		$b = ord( $file[$i] );
		$b1 = ($b >> 0) & BIT4;
		$b2 = ($b >> 4) & BIT4;
		$pix .= chr($b1) . chr($b2);
	}

	$clut = mstrpal555($file, 0xb000, 0x10, 64);
	$dir = str_replace('.', '_', $fname);

	foreach ( $clut as $k => $c )
	{
		if ( trim($c, ZERO.BYTE) == "" )
			continue;
		$fn = sprintf("$dir/%02d.clut", $k);

		$data = "CLUT";
		$data .= chrint(0x10, 4); // no clut
		$data .= chrint(256 , 4); // width
		$data .= chrint(352 , 4); // height
		$data .= $c;
		$data .= $pix;

		printf("%8x , %8x , $fn\n", $st, $siz);
		save_file($fn, $data);
	}

	return;
}

for ( $i=1; $i < $argc; $i++ )
	saga2( $argv[$i] );
