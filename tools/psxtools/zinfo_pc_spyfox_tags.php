<?php
require 'common.inc';
require 'common-guest.inc';

function sect_tags( &$file, $dir, $st, $ed )
{
	global $gp_tags;
	$func = __FUNCTION__;

	while ( $st < $ed )
	{
		$tag = substr ($file, $st+0, 4);
		$siz = str2big($file, $st+4, 4);
		printf("%8x  %8x  %s/%s\n", $st, $siz, $dir, $tag);

		if ( ! isset($gp_tags[$tag]) )
			$gp_tags[$tag] = 0;
		$gp_tags[$tag] += $siz;

		switch ( $tag )
		{
			case 'AKAX': case 'AKOS': case 'AUXD': case 'AWIZ':
			case 'DEFA': case 'DIGI':
			case 'IMAG':
			case 'LECF': case 'LFLF':
			case 'MULT':
			case 'OBCD': case 'OBIM':
			case 'PALS':
			case 'RMDA': case 'RMIM': case 'RMSC': case 'ROOM':
			case 'SEQI': case 'SQDB':
			case 'TALK':
			case 'WRAP':

			case 'IM00': case 'IM01': case 'IM02': case 'IM03':
			case 'IM04': case 'IM05': case 'IM06': case 'IM07':
			case 'IM08': case 'IM09': case 'IM0A': case 'IM0B':
			case 'IM0C': case 'IM0D': case 'IM0E': case 'IM0F':
			case 'IM10':
				$func($file, "$dir/$tag", $st+8, $st+$siz);
				break;

			case "\x00\x00\x00\x00": // ft.la1 0x2ba41c8
				return;
		} // switch ( $tag )

		$st += $siz;
	} // while ( $st < $ed )

	return;
}

function spyfox( $fname )
{
	$file = load_file($fname);
	if ( empty($file) )  return;

	$mgc = substr($file, 0, 4);
	switch ( $mgc )
	{
		case "\x3b\x27\x28\x24": // RNAM  .0
		case "\x25\x2c\x2a\x2f": // LECF  .1  .(a)  .he1
		case "\x24\x28\x31\x3a": // MAXS  .he0
		case "\x3d\x25\x22\x2b": // TLKB  .he2
			printf("[^69] %s\n", $fname);
			$len = strlen($file);
			for ( $i=0; $i < $len; $i++ )
			{
				$c  = ord($file[$i]);
				$c ^= 0x69;
				$file[$i] = chr($c);
			}
			save_file($fname, $file);
			break;
	} // switch ( $mgc )

	echo "== $fname\n";
	$len = strlen($file);
	sect_tags($file, $fname, 0, $len);

	return;
}

$gp_tags = array();
for ( $i=1; $i < $argc; $i++ )
	spyfox( $argv[$i] );

asort($gp_tags);
foreach ( $gp_tags as $k => $v )
	printf("%4s  %8x\n", $k, $v);

/*
V      Game
0/1/2  Maniac Mansion
1/2/3  Zak McKracken and the Alien Mindbenders
3      Indiana Jones and the Last Crusade
3/4    Loom
4      Passport to Adventure
4/5    The Secret of Monkey Island
5      Monkey Island 2 : LeChuck's Revenge
5      Indiana Jones and the Fate of Atlantis
6      Day of the Tentacle
6      Sam and Max Hit the Road
6      HE 6.x/7.x/8.x/9.x/10.x
	Putt-Putt
		1992  Joins the Parade
		1993  Goes to the Moon
		1995  Saves the Zoo
		1997  Travels Through Time
		1998  Enters the Race
		2000  Joins the Circus
	Freddi Fish
		1994  The Case of the Missing Kelp Seeds
		1996  The Case of the Haunted Schoolhouse
		1998  The Case of the Stolen Conch Shell
		1999  The Case of the Hogfish Rustlers of Briny Gulch
		2001  The Case of the Creature of Coral Cove
	Pajama Sam
		1996  No Need to Hide When It's Dark Outside
		1998  Thunder and Lightning Aren't so Frightening
		2000  You Are What You Eat from Your Head to Your Feet
	Spy Fox
		1997  Dry Cereal
		1999  Some Assembly Required
		2001  Operation Ozone
7      Full Throttle
7      The Dig
8      The Curse of Monkey Island



LFLF/ROOM/CLUT
	atlantis
	monkey
	monkey2
LFLF/ROOM/PALS/WRAP/APAL
	tentacle
	samnmax
	ft
	dig
	comi

LFLF/ROOM/RMIM/IM00/SMAP
	atlantis
	monkey
	monkey2
	tentacle
	samnmax
	ft
	dig
LFLF/ROOM/IMAG/WRAP/SMAP
	comi

LFLF/RMDA/PALS/WRAP/APAL
	puttputt
	puttmoon
	puttzoo
	putttime
	puttrace
	puttcircus
	freddi
	freddi2
	freddi3
	freddi4
	freddicove
	pajamanhd
	pajama2
	pajama3
	spyfoxdc
	spyfox2
	spyozon

LFLF/RMIM/IM00/SMAP
	puttputt
	puttmoon

LFLF/RMIM/IM00/BMAP
	puttzoo
	putttime
	puttrace
	puttcircus
	freddi
	freddi2
	freddi3
	freddi4
	freddicove
	pajamanhd
	pajama2
	pajama3
	spyfoxdc
	spyfox2
	spyozon
 */
