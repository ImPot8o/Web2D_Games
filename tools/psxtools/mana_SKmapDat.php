<?php
/*
[license]
[/license]
 */
/*
 * prefixes
 *  bon  Bone Fortress (Dragons arc)
 *  cv1  Mekiv Caverns
 *  drl  The Flames <- Underworld (Dragons arc)
 *  dst  Duma Desert
 *  esl  Lucemia (Fairy arc)
 *  grb  Junkyard
 *  hel  Underworld
 *  htw  Domina
 *  jgl  Jungle
 *  jul  Bejewled City (Jewel arc)
 *  lak  Lake Kilma
 *  mgc  Geo
 *  min  Ulkan Mines
 *  mhm  Home
 *  mon  Lumina
 *  mnt  Norn Peaks (Dragons arc)
 *  prt  Polpota Harbor
 *  rui  Mindas Ruins
 *  sea  Madora Beach
 *  sei  Tree of Mana (FINAL)
 *  shp  SS Buccaneer
 *  snw  Fieg Snowfields
 *  twr  Tower of Leires
 *  wal  Gato Grottoes
 *  wd1  White Forest (Dragons arc)
 *  wst  Luon Highway
 */
require "common.inc";

$gp_pixd = "";
$gp_clut = array();

// map files are loaded to RAM 80180000
// offsets here are RAM pointers
function ramint( &$file, $pos )
{
	$int = str2int($file, $pos, 3);
	if ( $int )
		$int -= 0x180000;
	else
		printf("ERROR ramint zero @ %x\n", $pos);
	return $int;
}
//////////////////////////////
function secttile( &$pix, $dat, $bpp, $x, $y )
{
	$cn = $dat & 0x7f;

	$blk = ($dat >>  8) & 0x0f;
	$col = ($dat >> 16) & 0x0f;
	$row = ($dat >> 20) & 0x0f;

	// texture data is mixed of
	//   4-bit  (4 columns)
	//   8-bit  (2 columns)
	//   RGB555 (1 column)
	//
	//  4-bit           |  8-bit   |  RGB555
	//   0  10  20  30  |   0  10  |   0
	//   1  11  21  31  |   1  11  |   1
	//   2  12  22  32  |   2  12  |   2
	//   3  13  23  33  |   3  13  |   3
	//  ...             |  ...     |  ...
	//   f  1f  2f  3f  |   f  1f  |   f
	//  40  50  60  70  |  20  30  |  10
	//  41  51  61  71  |  21  31  |  11
	//  ...             |  ...     |  ...
	global $gp_pixd, $gp_clut;
	if ( $bpp == 'c8' ) // 8-bit , 256 colors , 1 pixel/byte
	{
		$blk = ($blk * 0x8000);
		$col1 = (int)($col / 2) * 0x2000;

		$cls = 0x20;
		if ( ! isset( $gp_pixd[ $blk+$col1+0x1000 ] ) )
			$cls = 0x10;

		$col2 = ($col % 2) * 16;

		$row *= ($cls * 0x10);
		$row += ($col2 + $blk + $col1);

		$ripd = substr($gp_pixd, $row, 0x200);
		$pix['src']['pix'] = rippix8($ripd, 0, 0, 16, 16, $cls, 0x100);
		$pix['src']['pal'] = $gp_clut[$cn];
		$pix['dx'] = $x;
		$pix['dy'] = $y;
		copypix($pix, 1);
		return;
	}
	if ( $bpp == 'c4' ) // 4-bit , 16 colors , 2 pixel/byte
	{
		$blk = ($blk * 0x8000);
		$col1 = (int)($col / 4) * 0x2000;

		$cls = 0x40;
		if ( ! isset( $gp_pixd[ $blk+$col1+0x1800 ] ) )
			$cls = 0x30;
		if ( ! isset( $gp_pixd[ $blk+$col1+0x1000 ] ) )
			$cls = 0x20;
		if ( ! isset( $gp_pixd[ $blk+$col1+0x800 ] ) )
			$cls = 0x10;

		$col2 = ($col % 4) * 8;

		$row *= ($cls/2 * 0x10);
		$row += ($col2 + $blk + $col1);

		$ripd = substr($gp_pixd, $row, 0x200);
		$pix['src']['pix'] = rippix4($ripd, 0, 0, 16, 16, $cls, 0x100);

		$cn1 = ($cn >> 0) & BIT4;
		$cn2 = ($cn >> 4) & BIT4;
		$pix['src']['pal'] = substr($gp_clut[$cn2], $cn1*0x40, 0x40);
		$pix['dx'] = $x;
		$pix['dy'] = $y;
		copypix($pix, 1);
		return;
	}
	if ( $bpp == 'rgb' ) // RGB555 , 2 byte/pixel
	{
		$blk *= 0x8000;
		$col *= 0x2000;
		$row *= 0x200;

		$row += ($blk + $col);
		$ripd = substr($gp_pixd, $row, 0x200);

		$pix['src']['pix'] = pal555($ripd);
		$pix['src']['pal'] = '';

		$pix['dx'] = $x;
		$pix['dy'] = $y;
		copypix($pix, 4);
	}
	return;
}

function sectmap( &$file, $nid, $base )
{
	printf("=== sectmap( $nid , %x )\n", $base);
	$map_off = ramint($file, $base);
	if ( $map_off == 0 )
		return;

	$map_w = ord( $file[$base+10] ) * 0x10;
	$map_h = ord( $file[$base+11] ) * 0x10;
	echo "map : $map_w x $map_h\n";

	$b8 = ord( $file[$base+8] );
	printf("map : b+8 %02x\n", $b8);
	// & 0x01 - display (base frame for animation)
	// & 0x02
	// & 0x04
	// & 0x08
	// & 0x10 - 8-bit image
	// & 0x20 - RGB555 image
	// & 0x40 - animated layer
	// & 0x80
	$bpp = 'c4';
	if ( $b8 & 0x10 )  $bpp = 'c8';
	if ( $b8 & 0x20 )  $bpp = 'rgb';
	printf("pix = %s\n", $bpp);
	if ( ($b8 & 0x21) == 0x20 )  return;

	$pix = COPYPIX_DEF();
	$pix['rgba']['w'] = $map_w;
	$pix['rgba']['h'] = $map_h;
	$pix['rgba']['pix'] = canvpix($map_w,$map_h);

	$pix['src']['w'] = 16;
	$pix['src']['h'] = 16;

	$pos = $map_off;
	$map = "";
	for ( $y=0; $y < $map_h; $y += 0x10 )
	{
		for ( $x=0; $x < $map_w; $x += 0x10 )
		{
			$dat = str2int($file, $pos, 4);
				$pos += 4;
			$map .= sprintf("%8x ", $dat);

			if ( $dat == 0 )
				continue;
			if ( ($dat & 0x80) == 0 )
				continue;

			secttile($pix, $dat, $bpp, $x, $y);
		} // for ( $x=0; $x < $map_w; $x += 0x10 )

		$map .= "\n";
	} // for ( $y=0; $y < $map_h; $y += 0x10 )

	echo "$map\n";
	savpix($nid, $pix);
	return;
}

function sect2( &$file, $nid, $base )
{
	printf("=== sect2( $nid , %x )\n", $base);
	$off1 = ramint($file, $base+0);
	$off2 = ramint($file, $base+4); // ?brightness animation?
	if ( $off2 == 0 )
		printf("%x + 4 is zero\n", $base);

	// mhm_bdrm.prs = bb8
	$st = $off1;
	$id = 1;
	while (1)
	{
		$off = ramint($file, $st);
		if ( $off == 0 )
			break;
		// mhm_bdrm.prs = bc8,dbc,e3c,0
		sectmap($file, "$nid-$id", $off);
		$st += 4;
		$id++;
	}
	return;
}

function sect1( &$file, $nid, $base )
{
	printf("=== sect1( $nid , %x )\n", $base);
	$data_off = ramint($file, $base+0);

	global $gp_clut;
	if ( empty($gp_clut) )
	{
		$clut_off = ramint($file, $base+4);
		$clut_end = ramint($file, $base+8);
		if ( $clut_end < $clut_off )
			$clut_end = $data_off;

		$siz = $clut_end - $clut_off;
		while ( $siz >= 0x200 )
		{
			printf("add CLUT 0x200 @ %x\n", $clut_off);
			$gp_clut[] = strpal555($file, $clut_off, 0x100);
			$clut_off += 0x200;
			$siz -= 0x200;
		} // while ( $siz >= 0x200 )

		if ( $siz > 0 )
		{
			printf("add CLUT 0x%x @ %x\n", $siz, $clut_off);
			$gp_clut[] = strpal555($file, $clut_off, $siz/2);
		}
	}

	$st = $data_off;
	$id = 1;
	while (1)
	{
		$off = ramint($file, $st);
		if ( $off == 0 )
			break;
		// mhm_bdrm.prs = b88,106c,0
		sect2($file, "$nid-$id", $off);
		$st += 4;
		$id++;
	}
	return;
}
//////////////////////////////
function srcpix( &$pix, $dir )
{
	save_file("$dir/pix.meta", $pix);
	$len = strlen($pix);
	$w = 0x20;
	$h = $len / 0x20;

	// 8-bpp
	$clut = "CLUT";
	$clut .= chrint(0x100, 4);
	$clut .= chrint($w, 4);
	$clut .= chrint($h, 4);
	$clut .= grayclut(0x100);
	$clut .= $pix;
	save_file("$dir/pix-8.clut", $clut);

	// 4-bpp
	$clut = "CLUT";
	$clut .= chrint(0x10, 4);
	$clut .= chrint($w*2, 4);
	$clut .= chrint($h,   4);
	$clut .= grayclut(0x10);
	for ( $i=0; $i < $len; $i++ )
	{
		$b = ord( $pix[$i] );
		$b1 = ($b >> 0) & BIT4;
		$b2 = ($b >> 4) & BIT4;
		$clut .= chr($b1) . chr($b2);
	}
	save_file("$dir/pix-4.clut", $clut);
	return;
}

function mana( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	if ( substr($file, 0, 8) != "SKmapDat" )
		return;

	$dir = str_replace('.', '_', $fname);

	global $gp_pixd, $gp_clut;
	$pixp = ramint ($file, 0x14);
	$pixz = str2int($file, 0x0c, 4);
	$gp_pixd = substr($file, $pixp, $pixz);
	$gp_clut = array();
	srcpix($gp_pixd, $dir);

	$file = substr($file, 0, $pixp);

	$st = ramint($file, 0x20);
	$id = 1;
	while (1)
	{
		$off = ramint($file, $st);
		if ( $off == 0 )
			break;
		// mhm_bdrm.prs = 118,0
		sect1($file, "$dir/$id", $off);
		$st += 4;
		$id++;
	}
	return;
}

for ( $i=1; $i < $argc; $i++ )
	mana( $argv[$i] );
