<?php
/*
[license]
[/license]
 */
require "common.inc";

function mana( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$dir = str_replace('.', '_', $fname);

	$cnt = str2int($file, 0, 4);
	for ( $i=0; $i < $cnt; $i++ )
	{
		$p = 4 + ($i * 4);
		$ps = str2int($file, $p, 4);

		if ( $file[$ps] == ZERO ) // allface.dat
		{
			$clut = "CLUT";
			$clut .= chrint(16,4); // no clut
			$clut .= chrint(48,4); // width
			$clut .= chrint(48,4); // height
			$clut .= strpal555($file, $ps, 16);
				$ps += 0x20;

			$sz = 0x18 * 0x30;
			while ( $sz )
			{
				$b = ord( $file[$ps] );

				$b1 = ($b >> 0) & BIT4;
				$b2 = ($b >> 4) & BIT4;
				$clut .= chr($b1) . chr($b2);

				$ps++;
				$sz--;
			}
			$fn = sprintf("$dir/%04d.clut", $i);
			save_file($fn, $clut);
			continue;
		}

		if ( $file[$ps] != chr(0x10) ) // TIM file
			continue;

		// TIM with CLUT
		if ( ord( $file[$ps+4] ) & 8 )
		{
			$str = substr($file, $ps);
			$tim = psxtim($str);

			foreach ( $tim["clut"] as $k => $v )
			{
				if ( trim($v, ZERO.BYTE) == "" )
					continue;
				$clut = "CLUT";
				$clut .= chrint($tim["cc"], 4); // no clut
				$clut .= chrint($tim['w'], 4); // width
				$clut .= chrint($tim['h'], 4); // height
				$clut .= $v;
				$clut .= $tim["pix"];

				$fn = sprintf("$dir/%04d_%d.clut", $i, $k);
				save_file($fn, $clut);
			} // foreach ( $tim["clut"] as $k => $v )
			continue;
		}

		// TIM with 16-bit RGB555 pixels
		$w = str2int($file, $ps+0x10, 2);
		$h = str2int($file, $ps+0x12, 2);
			$ps += 0x14;

		$rgba = "RGBA";
		$rgba .= chrint($w, 4); // width
		$rgba .= chrint($h, 4); // height

		$sz = $w * $h;
		while ( $sz > 0 )
		{
			$rgba .= rgb555( $file[$ps+0] . $file[$ps+1] );
			$ps += 2;
			$sz--;
		}
		$fn = sprintf("$dir/%04d.rgba", $i);
		save_file($fn, $rgba);
	} // for ( $i=0; $i < $cnt; $i++ )
	return;
}

for ( $i=1; $i < $argc; $i++ )
	mana( $argv[$i] );

