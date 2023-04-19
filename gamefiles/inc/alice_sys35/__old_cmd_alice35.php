<?php
/*
[license]
Copyright (C) 2019 by Rufas Wan

This file is part of Web2D_Games. <https://github.com/rufaswan/Web2D_Games>

Web2D_Games is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Web2D_Games is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Web2D_Games.  If not, see <http://www.gnu.org/licenses/>.
[/license]
 */

function B_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+1] );
	switch( $type )
	{
		// B0,num:  メッセージウィンドウをクリアする
		case 0:
			$st += 2;
			$num = sco_calli($file, $st);
			trace("text B $type , $num");
			return;
		// B1,num,X1,Y1,X2,Y2,V:  選択肢ウィンドウの座標を設定する
		case 1:
			$st += 2;
			$arg = sco_var_args( 6, $file, $st );
			$num = array_shift($arg);
			trace("select B $type , %s", serialize($arg));
			$gp_pc["B1"][$num] = $arg;
			return;
		// B2,num,W,C1,C2,C3,dot:  選択肢ウィンドウを切り替える
		case 2:
			$st += 2;
			$arg = sco_var_args( 6, $file, $st );
			trace("select B $type , %s", serialize($arg));
			$gp_pc["B2"] = $arg;
			return;
		// B3,num,X1,Y1,X2,Y2,V:  メッセージウィンドウの座標を設定する
		case 3:
			$st += 2;
			$arg = sco_var_args( 6, $file, $st );
			$num = array_shift($arg);
			trace("text B $type , %s", serialize($arg));
			$gp_pc["B3"][$num] = $arg;
			return;
		// B4,num,W,C1,C2,N(C3),M(dot):  メッセージウィンドウを切り替える
		case 4:
			$st += 2;
			$arg = sco_var_args( 6, $file, $st );
			trace("text B $type , %s", serialize($arg));
			$gp_pc["B4"] = $arg;

			$t = $gp_pc["B3"][ $arg[0] ];
			$gp_pc["T"] = array($t[0] , $t[1]);
			return;
		// B10 var_x,var_y:  メッセージを次に表示する座標を取得する
		case 10:
			$st += 2;
			$arg = sco_var_args( 2, $file, $st );
			trace("text B $type , %s", serialize($arg));
			sco_var_put( $arg[0], 0, $gp_pc["T"][0] );
			sco_var_put( $arg[1], 0, $gp_pc["T"][1] );
			return;
		// B11,var_s,var_m:  現在の選択肢、メッセージウィンドウ番号を取得する
		case 11:
			return;
		// B12, ver:  登録された選択肢数取得
		case 12:
			return;
		// B13, ver:  選択肢最大文字幅取得
		case 13:
			return;
		// B14, ver:  選択肢最大文字幅取得(ASCII版)
		case 14:
			return;
		// B21,num,var_x,var_y:  現在のカレントの選択肢ウィンドウの左上座標を取得する
		case 21:
			return;
		// B22,num,var_x_size,var_y_size:  現在のカレントの選択肢ウィンドウのサイズを取得する
		case 22:
			return;
		// B23,num,var_x,var_y:  現在のカレントのメッセージウィンドウの左上座標を取得する
		case 23:
			return;
		// B24,num,var_x_size,var_y_size:  現在のカレントのメッセージウィンドウのサイズを取得する
		case 24:
			return;
		// B31,num,var_x,var_y:  設定されてある選択肢ウィンドウの左上座標を取得する
		case 31:
			return;
		// B32,num,var_x_size,var_y_size:  設定されてある選択肢ウィンドウのサイズを取得する
		case 32:
			return;
		// B33,num,var_x,var_y:  設定されてあるメッセージウィンドウの左上座標を取得する
		case 33:
			return;
		// B34,num,var_x_size,var_y_size:  設定されてあるメッセージウィンドウのサイズを取得する
		case 34:
			return;
	}
	return;
}

function F_cmd35( &$file, &$st, &$ajax )
{
	global $sco_file, $gp_pc;
	$type = ord( $file[$st+1] );
	switch( $type )
	{
		// F1,str_number,skip:  テーブルデータ文字列取得(ベース移動,オフセット指定)
		case 1:
			$st += 2;
			$str_number = sco_calli($file, $st);
			$skip = sco_calli($file, $st);

			$ind = $gp_pc["F"][0];
			$pos = $gp_pc["F"][1] + ($skip * 2);
			$old = $pos;
			sco_load_sco( $ind );
			$jp = sco_ascii( $sco_file[ $ind ], $pos, ZERO );
			trace("array F1 $str_number , $skip = $jp");
			$gp_pc["F"][1] += ($pos - $old);
			$gp_pc["X"][$str_number] = $jp;
			return;
		// F2,read_var,skip:  テーブルデータ数値取得(ベース移動,オフセット指定)
		case 2:
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$skip = sco_calli($file, $st);
			$move = true;
			goto array_int1;
			return;
		// F3,read_var,skip:  テーブルデータ数値取得(ベース固定,オフセット指定)
		case 3:
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$skip = sco_calli($file, $st);
			$move = false;
			goto array_int1;
			return;
		// F4,read_var,count:  テーブルデータ数値取得(ベース移動,個数指定)
		case 4:
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$count = sco_calli($file, $st);
			$move = true;
			goto array_intm;
			return;
		// F5,read_var,count:  テーブルデータ数値取得(ベース固定,個数指定)
		case 5:
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$count = sco_calli($file, $st);
			$move = false;
			goto array_intm;
			return;
		// F6,var,index:  F7コマンドで読み込む変数の指定
		case 6:
			return;
		// F7,data_width,count:  F6で指定されたデータの読み込み
		case 7:
			return;
		// F8,SerchNum,SkipCount:  SerchNumの数値と一致する数値を探す(ベース移動,オフセット指定)
		case 8:
			return;
		// F9,SerchNum,SkipCount:  SerchNumの数値より小さい数値を探す(ベース移動,オフセット指定)
		case 9:
			return;
		// F10,SerchNum,SkipCount:  SerchNumの数値より大きい数値を探す(ベース移動,オフセット指定)
		case 10:
			return;
		// F11,SerchString,EndString:  SerchStringの数値より大きい数値を探す(ベース移動,オフセット指定)
		case 11:
			return;
	}
	return;

array_int1:
	$ind = $gp_pc["F"][0];
	$pos = $gp_pc["F"][1] + ($skip * 2);
	sco_load_sco( $ind );
	$int = str2int( $sco_file[ $ind ], $pos, 2 );

	trace("array F $type $v+$e , $skip = $int");
	sco_var_put( $v, $e, $int );

	if ( $move )
		$gp_pc["F"][1] += 2;
	return;
array_intm:
	$ind = $gp_pc["F"][0];
	$pos = $gp_pc["F"][1];
	sco_load_sco( $ind );
	trace("array F $type $v+$e , $count");

	$data = array();
	for ( $i=0; $i < $count; $i++ )
	{
		$p = $pos + ($i * 2);
		$data[$i] = str2int( $sco_file[ $ind ], $p, 2 );
	}
	$gp_pc["page"][$v+$e] = $data;

	if ( $move )
		$gp_pc["F"][1] += ($count * 2);
	return;

}

function J_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+1] );
	switch( $type )
	{
		// J0 x,y:  次に来るGコマンドでのCG表示開始座標を指定する (絶対座標指定)
		// J1 x,y:  次に来るGコマンドでのCG表示開始座標を指定する (相対指定)
		case 0:
		case 1:
			$st += 2;
			$x = sco_calli($file, $st);
			$y = sco_calli($file, $st);
			trace("cg J  $type , $x , $y");
			$gp_pc["J"][$type] = array($x, $y);
			return;
		// J2 x,y:  GコマンドでのCG表示開始座標を指定する (絶対座標指定)
		// J3 x,y:  GコマンドでのCG表示開始座標を指定する (相対指定)
		// J4:  J2ｺﾏﾝﾄﾞ･J3ｺﾏﾝﾄﾞによる座標指定を解除する
/*
			if ( isset( $gp_pc["J"][2] ) )
				unset( $gp_pc["J"][2] );
			if ( isset( $gp_pc["J"][3] ) )
				unset( $gp_pc["J"][3] );
*/
	}
	return;
}

function CK_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+2] );
	switch ( $type )
	{
		// CK 1,x0,y0,cx,cy,col,rate,0,0:  (ﾌﾙｶﾗｰ専用) 指定範囲に色を重ねる
		case 1:
			$st += 3;
			$arg = sco_var_args( 5, $file, $st );
			trace("CK $type , %s", serialize($arg));
			return;
		// CK 2,x0,y0,cx,cy,col,dx,dy,0:  指定範囲に網掛けする
		case 2:
			$st += 3;
			$arg = sco_var_args( 5, $file, $st );
			trace("CK $type , %s", serialize($arg));
			return;
		// CK 3,x0,y0,cx,cy,dst,src,count,0:  (256色専用) 指定範囲の色を変更する
		case 3:
			$st += 3;
			$arg = sco_var_args( 5, $file, $st );
			trace("CK $type , %s", serialize($arg));
			return;
	}
	return;
}

function PF_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+2] );
	switch ( $type )
	{
		// PF0,num:  // fade in
		// PF1,num:  // fade out
		case 0:
		case 1:
			$st += 3;
			$num = sco_calli($file, $st);
			trace("PF $type , $num");
			return;
		// PF2,num,wait_flag:  グラフィック画面をフェードインする（黒画面→通常画面）
		// PF3,num,wait_flag:  グラフィック画面をフェードアウトする（通常画面→黒画面）
		case 2:
		case 3:
			return;
	}
	return;
}

function PT_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	// PT 0,var,x,y:  (256色用) 指定座標の色番号を取得する
	// PT 1,r_var,g_var,b_var,x,y:  (ﾌﾙｶﾗｰ専用) 指定座標の色を取得する
	return;
}

function PW_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+2] );
	switch ( $type )
	{
		// PW0,num: // fade in
		// PW1,num: // fade out
		case 0:
		case 1:
			$st += 3;
			$num = sco_calli($file, $st);
			trace("PW $type , $num");
			return;
		// PW2,num,wait_flag:  グラフィック画面をホワイトフェードインする（白画面→通常画面）
		// PW3,num,wait_flag:  グラフィック画面をホワイトフェードアウトする（通常画面→白画面）
		case 2:
		case 3:
			return;
	}
	return;
}

function SG_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+2] );
	switch ( $type )
	{
		// SG 0,0:  演奏中のMIDIを停止する
		// SG 1,num:  MIDIを演奏する
		case 0:
		case 1:
			$st += 3;
			$num = sco_calli($file, $st);
			trace("midi SG 1 , $num");
			$gp_pc["bgm"] = array("midi", $num);
			return;
		// SG 2,var:  MIDI演奏位置を1/100秒単位で取得する
		case 2:
			$st += 3;
			$var = sco_calli($file, $st);
			trace("midi SG 2 , %s", serialize($var));
			sco_var_put($var, 0, 999);
			return;
		// SG 3,0:  演奏中のMIDIを一時停止する
		// SG 3,1:  一時停止中のMIDIの一時停止を解除する
		case 3:
			$st += 3;
			$num = sco_calli($file, $st);
			trace("midi SG 3 , $num");
			return;
		// SG 4,count:  次のSG1ｺﾏﾝﾄﾞでのMIDI演奏の繰り返し回数指定
		case 4:
			$st += 3;
			$count = sco_calli($file, $st);
			trace("midi SG 4 , $count");
			return;
		// SG 5,fnum,num:  MIDIフラグの設定
		// SG 6,vnum,num:  MIDI変数の設定
		// SG 7,fnum,var:  MIDIフラグの取得
		// SG 8,vnum,var:  MIDI変数の取得
		case 5:
		case 6:
		case 7:
		case 8:
			return;

	}
	return;
}

function SR_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	return;
}

function SX_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$bak = $st;
	$device = ord( $file[$st+2] );
	$type   = ord( $file[$st+3] );
	$st += 4;
	switch ( $type )
	{
		// SX device,1,time,stop,volume:  フェード
		case 1:
			$arg = sco_var_args( 3, $file, $st );
			trace("SX $device , $type , %s", serialize($arg));
			return;
		// SX device,2,var:  フェード終了確認  cont/end
		case 2:
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("SX $device , $type , $v+$e");
			sco_var_put($v, $e, 1); // 0=play , 1=stop
			return;
		// SX device,3:  フェード強制終了
		case 3:
			trace("SX $device , $type");
			return;
		// SX device,4,var:  ボリューム取得  0-100%
		case 4:
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("SX $device , $type , $v+$e");
			sco_var_put($v, $e, 100);
			return;
	}
	$st = $bak;
	return;
}

function VA_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	return;
}

function WZ_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	// WZ0, sw:  描画DIB→画面反映のon/off切替
	$b1 = ord( $file[$st+2] );
	$st += 3;
	$sw = sco_calli($file, $st);
	trace("WZ $b1 , $sw");
	return;
}

function ZT_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+2] );
	switch ( $type )
	{
		// ZT0,var:  現在の日時を var0〜var6 の変数列に返す
		case 0:
			$st += 3;
			//$var = sco_calli($file, $st);
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("ZT $type , $v+$e += 6");
			$time = time2date( time() );
			sco_var_put( $v, $e+0, $time[0] ); // year 1980-2079
			sco_var_put( $v, $e+1, $time[1] ); // month 1-12
			sco_var_put( $v, $e+2, $time[2] ); // days 1-31
			sco_var_put( $v, $e+3, $time[3] ); // hour 0-23
			sco_var_put( $v, $e+4, $time[4] ); // minute 0-59
			sco_var_put( $v, $e+5, $time[5] ); // second 0-59
			sco_var_put( $v, $e+6, 1 ); // day 1-7:Sun-Sat
			return;
		// ZT1,n:  タイマーを n の数値でクリアする
		case 1:
			$st += 3;
			$var = sco_calli($file, $st);
			trace("ZT $type , $var");
			$gp_pc["ZT"] = 0;
			$ajax = true;
			$gp_input = array("key",-1);
			return;
		// ZT2,var:  タイマーを var に取得する 1/10
		// ZT3,var:  タイマーを var に取得する 1/30
		// ZT4,var:  タイマーを var に取得する 1/60
		// ZT5,var:  タイマーを var に取得する 1/100
		case 2:
		case 3:
		case 4:
		case 5:
			$st += 3;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("ZT $type , $v+$e");
			$gp_pc["ZT"] += 1;
			sco_var_put( $v, $e, $gp_pc["ZT"] );
			return;
		// ZT 10,num,base,count:  高精度タイマー設定
		case 10:
			$st += 3;
			$arg = sco_var_args( 3, $file, $st );
			trace("ZT $type , %s", serialize($arg));
			return;
		// ZT 11,num,var:  高精度タイマー取得
		case 11:
			$st += 3;
			$num   = sco_calli($file, $st);
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("ZT $type , $num , $v+$e");
			sco_var_put( $v, $e, 9999 );
			return;
	}
	return;
}

function ZZ_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$type = ord( $file[$st+2] );
	switch ( $type )
	{
		// ZZ0,sw:  SYSTEM3.5を終了する
		// ZZ0 return RND 1/2 = OK , 254/255 = ERROR
		case 0:
			$bak = $st;
			$st += 3;
			$num = sco_calli($file, $st);
			trace("ZZ 0 , $num");
			$gp_pc["var"][0] = $num;
			$st = $bak;
			sco_text_add( "_NEXT_" );
			sco_text_add( "SYSTEM 3.5 END" );
			//$gp_pc["pc"] = array(0,0);
			return;
		// ZZ1,var:  現在の動作機種コードを var に返す
		case 1:
			$st += 3;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("ZZ 1 , $v+$e");
			sco_var_put( $v, $e, 1 ); // windows
			return;
		// ZZ2,num:  機種文字列を文字列領域 num に返す(MAX12文字)
		case 2:
			$st += 3;
			$num = sco_calli($file, $st);
			trace("ZZ 2 , $num");
			return;
		// ZZ3,var:  WINDOWSの全画面サイズや表示色数を変数列に返す
		case 3:
			$st += 3;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("ZZ 9 , $v+$e");
			sco_var_put( $v, $e+0, 4096 ); // width
			sco_var_put( $v, $e+1, 4096 ); // height
			sco_var_put( $v, $e+2, 256 ); // bit
			return;
		// ZZ9,var:  起動時のｽｸﾘｰﾝｻｲｽﾞを取得する
		case 9:
			$st += 3;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("ZZ 9 , $v+$e");
			list($w,$h,$c) = $gp_pc["WW"];
			sco_var_put( $v, $e+0, $w ); // width
			sco_var_put( $v, $e+1, $h ); // height
			sco_var_put( $v, $e+2, $c ); // bit
			return;
		// ZZ13,num:  表示ﾌｫﾝﾄを設定する
		case 13: // 0xd
			$st += 3;
			$num = sco_calli($file, $st);
			trace("ZZ 13 , $num");
			return;
		// ZZ4,var:  DIB の全画面 サイズや色数を変数列に返す
		// ZZ5,var:  SYSTEM3.5用表示画面 の サイズや色数を変数列に返す
		// ZZ7,var:  セーブドライブの残りディスク容量を得る
		// ZZ8,var:  メモリオンバッファの残り容量を得る
	}
	return;
}

// 拡張コマンド一覧
function Y_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	$st++;
	$arg = sco_var_args( 2, $file, $st );
	trace("Y %s", serialize($arg));

	switch( $arg[0] )
	{
		// Y1,0:  −−−代替えコマンド策定中−−−
		// == "A"
		// SYS 3.6+ => B0
		case 1:
			sco_text_add( "_NEXT_" );
			return;
		// Y2,0:  −−−今後なるべくNBコマンドを使用する様にしてください−−−
		// var_init V00-V20
		// => NB
		case 2:
			$copy = array_fill(0, 20+1 , 0);
			sco_var_put(0, 0, $copy);
			return;
		// Y3,n:  −−−今後なるべく使用しない様にしてください−−− sleep
		// Y3 return RND key
		// => ZT20 , ZT21
		case 3:
			$gp_pc["var"][0] = 0;
			return;
		// Y4,n:  −−−代替えコマンド策定中−−−
		// Y4 return RND rand() [1-n]
		// SYS 3.6+ => ZR
		case 4:
			$n = $arg[1];
			if ( $n == 0 )
			{
				$gp_pc["var"][0] = 0;
				return;
			}
			//$rand = (rand() * rand()) & BIT24;
			//$rand = (rand() << 8) + rand();
			$rand = rand();
			$gp_pc["var"][0] = ($rand % $n) + 1;
			return;
	}
	return;
}

function C_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// CB start_x,start_y,lengs_x,lengs_y,color:  BOX-line
		case 'B':
			$st += 2;
			$arg = sco_var_args( 5, $file, $st );
			trace("CB %s", serialize($arg));
			sco_div_add( "_BORDER_", $arg );
			return;
		// CC sorce_x,sorce_y,sorce_lengs_x,sorce_lengs_y,destin_x,destin_y:  画面のコピー
		case 'C':
			$st += 2;
			$arg = sco_var_args( 6, $file, $st );
			trace("CC %s", serialize($arg));
			sco_div_add( "_BG_", $arg );
			return;
		// CD sorce_x,sorce_y,sorce_lengs_x,sorce_lengs_y,destin_x,destin_y,effect_number,option,wait_flag,color:  エフェクト機能付きスプライトコピー
		case 'D':
			return;
		// CE sorce_x,sorce_y,sorce_lengs_x,sorce_lengs_y,destin_x,destin_y,effect_number,option,wait_flag:  エフェクト機能付きコピー (CD)
		case 'E':
			$st += 2;
			$arg = sco_var_args( 9, $file, $st );
			trace("CE %s", serialize($arg));
			sco_div_add( "_BG_", $arg );
			return;
		// CF start_x,start_y,lengs_x,lengs_y,color:  BOX-FILL
		case 'F':
			$st += 2;
			$arg = sco_var_args( 5, $file, $st );
			trace("CF %s", serialize($arg));
			sco_div_add( "_COLOR_", $arg );
			return;
		case 'K':
			return CK_cmd35( $file, $st, $ajax );
		// CL start_x,start_y,end_x,end_y,color:  LINE (このコマンドのみ指定パラメータが違うので注意)
		case 'L':
			return;
		// CM sorce_x,sorce_y,sorce_lengs_x,sorce_lengs_y,destin_x,destin_y,destin_lengs_x,destin_lengs_y,mirror_switch:  拡大・縮小・反転コピー（個々の機能はスイッチで指定可能）
		case 'M':
			$st += 2;
			$arg = sco_var_args( 9, $file, $st );
			trace("CM %s", serialize($arg));
			return;
		// CP start_x,start_y,color:  ペイント
		case 'P':
			$st += 2;
			$arg = sco_var_args( 3, $file, $st );
			trace("CP %s", serialize($arg));
			sco_div_add( "_PAINT_", $arg );
			return;
		// CS sorce_x,sorce_y,sorce_lengs_x,sorce_lengs_y,destin_x,destin_y,splite:  画面のスプライトコピー
		case 'S':
			$st += 2;
			$arg = sco_var_args( 7, $file, $st );
			trace("CS %s", serialize($arg));
			$gp_pc["CS"] = $arg;
			return;
		// CT var,x,y:  影データを取得する (16/24bitのみ)
		case 'T':
			return;
        // CU x,y,x_size,y_size,border,set:  指定矩形領域内の影データのborder以下のデータをsetに変更する (16/24bitのみ)
		case 'U':
			return;
        // CV x,y,x_size,y_size,border,set:  指定矩形領域内の影データのborder以上のデータをsetに変更する (16/24bitのみ)
		case 'V':
			return;
		// CX mode,src_x,src_y,len_x,len_y,dst_x,dst_y,col:  16bit以上専用のDIBのスプライトコピー（半透明処理が可能）
		case 'X':
			return;
		// CY src_x,src_y,width,height,alpha:  影データ塗りつぶし(16/24bitのみ)
		case 'Y':
			return;
		// CZ src_x,src_y,width,height,dst_x,dst_y,flag:  影データコピー(16/24bitのみ)
		case 'Z':
			return;
	}
	return;
}

function D_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// DC page_no,size,save_flag  配列領域の作成(メモリを確保して配列領域のページを作成する)
		case 'C':
			$st += 2;
			$arg = sco_var_args( 3, $file, $st );
			trace("data DC %s", serialize($arg));
			return;
		// DF data_var,count,data  配列のクリア
		case 'F':
			$st += 2;
			$arg = sco_var_args( 3, $file, $st );
			trace("data DF %s", serialize($arg));
			list($v,$c,$d) = $arg;
			$gp_pc["page"][$v[0]+$v[1]] = array_fill(0, $c, $d);
			return;
		// DI page_no,use_flag,size:  配列領域の設定情報を取得
		case 'I':
			return;
		// DR data_var  配列の解除
		case 'R':
			return;
		// DS poin_var,data_var,位置,ページ  配列の設定 (DCコマンドを参照のこと)
		case 'S':
			$st += 2;
			list($v1,$e1) = sco_varno($file, $st);
				$st++; // skip 0x7f
			list($v2,$e2) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$pos  = sco_calli($file, $st);
			$page = sco_calli($file, $st);
			trace("data DS $v1+$e1 , $v2+$e2 , $pos , $page");
			return;
	}
	return;
}

function E_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// EC num:  ESコマンドで設定された画面領域をクリア
		case 'C':
			$st += 2;
			$num = sco_calli($file, $st);
			trace("EC $num");
			sco_ec_clear( $num );
			return;
		// EG 番号,x変数,y変数,cx変数,cy変数:  矩形の座標・サイズを取得する
		case 'G':
			return;
        // EM 番号,変数,x,y:  点が矩形内にあるかどうか調べる
		case 'M':
			return;
        // EN 変数,最小番号,最大番号,x,y:  点が含まれる矩形領域番号を取得する
		case 'N':
			return;
		// ES num,c,x1,y1,x2,y2:  ECコマンドで使用する座標範囲を設定
		case 'S':
			$st += 2;
			$arg = sco_var_args( 6, $file, $st );
			trace("ES %s", serialize($arg));
			$num = array_shift($arg);
			$gp_pc["ES"][$num] = $arg;
			return;
	}
	return;
}

function G_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch ( $file[$st+1] )
	{
		// GS num,var:  num 番にリンクされているCGの座標とサイズを取得する
		case 'S':
			$st += 2;
			$num = sco_calli($file, $st);
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("GS $num , $v+$e += 4");
			list($x,$y,$w,$h) = sco_img_meta( PATH_META, $num );
			sco_var_put( $v, $e+0, $x );
			sco_var_put( $v, $e+1, $y );
			sco_var_put( $v, $e+2, $w );
			sco_var_put( $v, $e+3, $h );
			return;
		// GX cg_no,shadow_no:(24bitDIB only)  影データを指定してCGをロードする
		case 'X':
			return;
	}

	$type = ord( $file[$st+1] );
	switch( $type )
	{
		// G num:  num 番にリンクされてるCGを表示する
		case 0:
			$st += 2;
			$num = sco_calli($file, $st);
			$c = -1;
			goto cg_add;
			return;
		// G num,c: (256色モードのみ/6万色モードでは不要)  num 番にリンクされてるCGを c 番の色だけ抜いて表示する
		case 1:
			$st += 2;
			$num = sco_calli($file, $st);
			$c   = sco_calli($file, $st);
			goto cg_add;
			return;
	}
	return;

cg_add:
	trace("cg G $type , $num , $c");

	// PC = palette read/decompress/cd
	if ( ! isset( $gp_pc["PPC"]) )
		$gp_pc["PPC"] = 7;

	// vsp/pms -> screen(cg)
	if ( $gp_pc["PPC"] & 1 )
	{
		trace("G PC 1 add_cg");
		sco_g0_add( $num , $c );
	}

	// program -> screen(clut)[fade-in/out]
	//if ( $gp_pc["PPC"] & 2 ) { }

	// vsp/pms -> program
	if ( $gp_pc["PPC"] & 4 )
	{
		trace("G PC 4 add_clut");
		sco_g0_clut( $num );
	}
	return;
}

function I_cmd35( &$file, &$st, &$ajax )
{
	global $sco_input;
	switch( $file[$st+1] )
	{
		// IC cursol_num,oldcursol  マウスカーソルの形状変更
		case 'C':
			$st += 2;
			$num = sco_calli($file, $st);
			$old = sco_calli($file, $st);
			trace("IC $num , $old");
			return;
		// IE exp1,exp2:  RA.ALDにリンクされた.CUR/.ANIファイルをカーソルとして読み込む
		case 'E':
			$st += 2;
			$exp1 = sco_calli($file, $st);
			$exp2 = sco_calli($file, $st);
			trace("IE $exp1 , $exp2");
			return;
		// IG var,code,num,reserve:  キー入力状態取得
		case 'G':
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$code = sco_calli($file, $st);
			$num  = sco_calli($file, $st);
			$resv = sco_calli($file, $st);
			trace("IG $v+$e , $code , $num , $resv");
			sco_var_put( $v, $e, 0 ); // 1=pressed 0=released
			return;
		// IK num:  キー入力状態取得
		// IK return RND = key
		case 'K':
			$bak = $st;
			$num = ord( $file[$st+2] );
			$st += 3;

			if ( ! $sco_input( "IK{$num}", "", $file, $st ) )
				$st = $bak;
			return;
		// IM cursol_x,cursol_y  マウスカーソルの座標取得
		// IM return RND = left/right click
		case 'M':
			$bak = $st;
			$st += 2;
			list($v1,$e1) = sco_varno($file, $st);
				$st++; // skip 0x7f
			list($v2,$e2) = sco_varno($file, $st);
				$st++; // skip 0x7f

			$src = array($v1,$e1,$v2,$e2);
			if ( ! $sco_input( "IM", $src, $file, $st ) )
				$st = $bak;
			return;
		// IX var:  「次の選択肢まで進む」の状態取得
		case 'X':
			$st += 2;
			list($v,$e) = sco_varno( $file, $st );
				$st++; // skip 0x7f
			trace("IX $v+$e");
			sco_var_put( $v, $e, 0 );
			return;
		// IY 0:  [次の選択肢まで進む]のフラグ解除
		// IY 2: 　次の選択肢まで進む状態のときに、選択肢もしくはマウスクリックでメッセージ送りを停止する様に指定
		// IY 3: 　次の選択肢まで進む状態のときに、選択肢もしくはマウスクリックでもメッセージ送りを停止しない様に指定
		case 'Y':
			$st += 2;
			$type = sco_calli($file, $st);
			trace("IY $type");
			return;
		// IZ start_x,start_y:  マウスカーソルの座標を変更する (マウスカーソルはスムーズに移動する)
		case 'Z':
			$st += 2;
			$start_x = sco_calli($file, $st);
			$start_y = sco_calli($file, $st);
			trace("IZ $start_x , $start_y");
			return;
	}
	return;
}

function K_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// KI var,port_num,user_max:  ネットワークのチャンネルを新規作成する
		case 'I':
			return;
		// KK user_num:  user_num番のﾕｰｻﾞｰを切断する
		case 'K':
			return;
		// KN var:  自分自身の接続番号を取得する
		case 'N':
			return;
		// KP var:  データ受信バッファにデータがあるか調べる
		case 'P':
			return;
		// KQ var,user_num:  ユーザーが接続されているかどうか確認する
		case 'Q':
			return;
		// KR var:  データ受信バッファからデータを取得する
		case 'R':
			return;
		// KW var,num:  データを送信する
		case 'W':
			return;
	}
	return;
}


// LC x,y,ﾌｧｲﾙ名:  CGを表示する
// LH? 1,no:  CDのデータをHDDへ登録する
// LH? 2,no:  CDのデータをHDDへ削除する
function L_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// LD num:  セーブデータをロードする（全ロード）
		// LD return RND 0 = OK , 200 > ERROR
		case 'D':
			$st += 2;
			$num = sco_calli($file, $st);
			trace("save LD $num");
			$pc = load_savefile( "sav{$num}" );
			if ( empty($pc) )
				$gp_pc["var"][0] = 255;
			else
			{
				$gp_pc = $pc;
				$st = $pc["pc"][1];
			}
			// copy( SAVE_FILE."sav{$num}" , SAVE_FILE."pc" );
			return;
		// LE type,file_name,start_var,read_num:  変数の値をファイルから読み込む
		// LE return RND 0 = OK , 200 > ERROR
		case 'E':
			$type = ord( $file[$st+2] );
			switch ( $type )
			{
				case 0: // int
				case 1: // str
					$st += 3;
					$fn = sco_ascii( $file, $st, ':' );
					list($v,$e) = sco_varno( $file, $st );
						$st++; // skip 0x7f
					$read_num  = sco_calli($file, $st);
					trace("LE $type , $fn , $v+$e , $read_num");

					$ret = sco_load_data($fn , $read_num);
					if ( empty($ret) )
						$gp_pc["var"][0] = 255;
					else
					{
						$gp_pc["page"][$v+$e] = $ret;
						$gp_pc["var"][0] = 0;
					}
					return;
			}
			return;
		// LL 0,link_no,start_var,read_num:  変数の値をリンクファイルから読み込む
		// LL return RND 0 = OK , 200 > ERROR
		case 'L':
			$type = ord( $file[$st+2] );
			switch ( $type )
			{
				case 0:
					$st += 3;
					$link_no = sco_calli($file, $st);
					list($v,$e) = sco_varno( $file, $st );
						$st++; // skip 0x7f
					$read_num = sco_calli($file, $st);
					trace("data LL $type , $link_no , $v+$e , $read_num");

					$ret = sco_load_data($link_no , $read_num);
					if ( empty($ret) )
						$gp_pc["var"][0] = 255;
					else
					{
						$gp_pc["page"][$v+$e] = $ret;
						$gp_pc["var"][0] = 0;
					}
					return;
			}
			return;
		// LP num,point,count:  セーブデータの一部分をロードする(数値変数部)
		// LP return RND 0 = OK , 200 > ERROR
		case 'P':
			$st += 2;
			$num = sco_calli($file, $st);
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$count = sco_calli($file, $st);
			trace("save LP $num , $v+$e , $count");

			$pc = load_savefile( "sav{$num}" );
			if ( empty($pc) )
				$gp_pc["var"][0] = 255;
			else
			{
				for ( $i=0; $i < $count; $i++ )
					$gp_pc["var"][$v+$e+$i] = $pc["var"][$v+$e+$i];
				$gp_pc["var"][0] = 0;
			}
			return;
		// LT num,var:  タイムスタンプの読み込み
		// LT return RND 0 = OK , 200 > ERROR
		case 'T':
			$st += 2;
			$num = sco_calli($file, $st);
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			if ( file_exists( SAVE_FILE . "sav{$num}" ) )
			{
				$mod = time2date( filemtime(SAVE_FILE . "sav{$num}") );
				sco_var_put( $v, $e+0, $mod[0] ); // year 1980-2079
				sco_var_put( $v, $e+1, $mod[1] ); // month 1-12
				sco_var_put( $v, $e+2, $mod[2] ); // days 1-31
				sco_var_put( $v, $e+3, $mod[3] ); // hour 0-23
				sco_var_put( $v, $e+4, $mod[4] ); // minute 0-59
				sco_var_put( $v, $e+5, $mod[5] ); // second 0-59
				$gp_pc["var"][0] = 0;
				trace("save LT $num , $v+$e (%d-%d-%d %d:%d:%d)", $mod[0], $mod[1], $mod[2], $mod[3], $mod[4], $mod[5]);
			}
			else
			{
				trace("save LT $num , $var");
				$gp_pc["var"][0] = 255;
			}
			return;
	}
	return;
}

// MA num1,num2:  num1 の文字列の後ろに num2 をつなげる
// MD dst_str_no,src_str_no,len:  文字列変数を指定長さ分コピーする
// ME dst_str_no,dst_pos,src_str_no,src_pos,len:  位置指定つきの文字列コピー
// MF var,dst_no,key_no,start_pos:  文字列中から指定文字列の位置を探す
// MF return RND 0 = OK , 255 = ERROR
// MH num1,fig,num2:  数値を文字列に変換する (参考;Hｺﾏﾝﾄﾞ)
// MP num1,num2:  指定の文字列を指定文字数だけ表示する(Xコマンドの桁数指定)
function M_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// MC num1,num2:  num1 , num2 を比較して結果を RND に返す (RND=0 不一致 , RND=1 一致)
		// MC return RND 0 = DIFF , 1 = SAME
		case 'C':
			$st += 2;
			$num1 = sco_calli($file, $st);
			$num2 = sco_calli($file, $st);
			trace("MC $num1 , $num2");
			if ( $gp_pc["X"][$num1] == $gp_pc["X"][$num2] )
				$gp_pc["var"][0] = 1;
			else
				$gp_pc["var"][0] = 0;
			return;
		case 'G':
			$type = ord( $file[$st+2] );
			switch ( $type )
			{
				// MG0, sw:  表示文字列を文字列変数に取得する/しない切替
				// MG1, str_no:  表示文字列取得開始文字列番号の指定
				// MG2, sw:  表示文字列取得の文字列番号更新の設定
				// MG3, sw:  表示文字列取得の改頁時の動作の指定
				// MG4, no:  表示文字列取得番号設定
				// MG5, var:  表示文字列取得番号取得
				// MG6, sw:  表示文字列取得の番号を強制更新/更新解除
				// MG7, var:  表示文字列取得、現在番号の取得済み文字数の取得
				// MG100,switch:  文字列表示オン/オフ
				case 0:
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
				case 6:
				case 7:
				case 100:
					$st += 3;
					$no = sco_calli($file, $st);
					trace("MG $type , $no");
					return;
			}
			return;
		// MI dst_no,max_len,title:  ユーザーによる文字列の入力
		// MI return RND strlen
		case 'I':
			$st += 2;
			$dst_no  = sco_calli($file, $st);
			$max_len = sco_calli($file, $st);
			$title   = sco_sjis($file, $st);
				$st++; // skip ':'
			trace("MI $dst_no , $max_len , $title");
			return;
		// ML var,str_no:  文字列の長さを取得する
		case 'L':
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$str_no = sco_calli($file, $st);
			trace("ML $v+$e , $str_no");
			$str = $gp_pc["X"][$str_no];
			$len = strlen($str) / 2;
			sco_var_put($w, $e, (int)$len);
			return;
		// MM num1,num2:  num1 の文字列に num2 をコピーする
		case 'M':
			$st += 2;
			$num1 = sco_calli($file, $st);
			$num2 = sco_calli($file, $st);
			trace("MM $num1 , $num2");
			$gp_pc["X"][$num1] = $gp_pc["X"][$num2];
			return;
		// MS num,string:  Xコマンドで表示される文字列領域に文字列を入れる
		case 'S':
			$st += 2;
			$num    = sco_calli($file, $st);
			$string = sco_sjis($file, $st);
				$st++; // skip ':'
			trace("MS $num , %s", $string);
			$gp_pc["X"][$num] = $string;
			return;
		// MT title:  ウインドウのタイトル文字列を設定する
		case 'T':
			$st += 2;
			$title = sco_sjis($file, $st);
				$st++; // skip ':'
			trace("MT %s", $title);
			$gp_pc["MT"] = $title;
			return;
		// MV version:  シナリオバージョンをシステムへ通知する
		case 'V':
			$st += 2;
			$version = sco_calli($file, $st);
			trace("MV $version");
			$gp_pc["MV"] = $version;
			return;
		// MZ0, max_len,max_num,reserve:  文字列変数の文字数・個数の設定の変更
		case 'Z':
			$type = ord( $file[$st+2] );
			switch ( $type )
			{
				case 0:
					$st += 3;
					$max_len = sco_calli($file, $st);
					$max_num = sco_calli($file, $st);
					$reserve = sco_calli($file, $st);
					trace("MZ $type , $max_len , $max_num , $reserve");
					return;
			}
			return;
	}
	return;
}

// NI var,default,min,max:  数値入力
// NT ﾀｲﾄﾙ:  NIコマンドで表示するタイトルを設定する
// NR var1,var2:  var1にvar2のルートを求める
// N> var1,num,count,var2:  var1 から始まるcount個の変数からnumより大きいければ1を、以下ならば0を
// N< var1,num,count,var2:  var1から始まるcount個の変数からnumより小さければ1を、以上ならば0を
// N= var1,num,count,var2:  var1から始まるcount個の変数からnumに等しければ1を、等しくなければ0を
// N¥ var1,count:  var1から始まるcount個の変数の0,1を反転する
// N‾ var,count:  ﾋﾞｯﾄ反転する
// NDM str,w64n:  数値w64nを文字列領域strへ文字列として反映
// NDA str,w64n:  文字列領域strを数値としてw64nへ反映
// NDH str,w64n:  数値w64nを画面に表示（パラメータの意味はHコマンドに準拠）
function N_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// NB var1,var2,count:  var1 から始まるcount個の変数へ
		case 'B':
			$st += 2;
			list($v1,$e1) = sco_varno( $file, $st );
				$st++; // skip 0x7f
			list($v2,$e2) = sco_varno( $file, $st );
				$st++; // skip 0x7f
			$count = sco_calli($file, $st);
			trace("NB $v1+$e1 , $v2+$e2 , $count");
			$copy = sco_var_get($v1, $e1, $count);
			sco_var_put($v2, $e2, $copy);
			return;
		// NC var1,count:  var1から始まるcount個の変数を0でクリアする
		case 'C':
			$st += 2;
			list($v,$e) = sco_varno( $file, $st );
				$st++; // skip 0x7f
			$count = sco_calli($file, $st);
			trace("NC $v+$e , $count");
			$copy = array_fill(0, $count , 0);
			sco_var_put($v, $e, $copy);
			return;
		case 'D':
			switch( $file[$st+2] )
			{
				// NDC w64n,num:  w64nにnumをコピーする
				case 'C':
					$st += 3;
					$w64n = sco_calli($file, $st);
					$num  = sco_calli($file, $st);
					trace("NDC $w64n , $num");
					$gp_pc["ND"][$w64n] = $num;
					return;
				// NDD var,w64n:  verにw64nをコピーする(変数に読み出す)
				case 'D':
					$st += 3;
					list($v,$e) = sco_varno($file, $st);
						$st++; // skip 0x7f
					$w64n = sco_calli($file, $st);
					trace("NDD $v+$e , $w64n");
					sco_var_put($v, $e, $gp_pc["ND"][$w64n]);
					return;
				// ND+ w64n1,w64n2,w64n3:  w64n2とw64n3を足してw64n1に代入
				case '+':
					$opr = "add";
					goto w64_math;
					return;
				// ND- w64n1,w64n2,w64n3:  w64n2からw64n3を引いてw64n1に代入
				case '-':
					$opr = "sub";
					goto w64_math;
					return;
				// ND* w64n1,w64n2,w64n3:  w64n2とw64n3を掛けてw64n1に代入
				case '*':
					$opr = "mul";
					goto w64_math;
					return;
				// ND/ w64n1,w64n2,w64n3:  w64n2をw64n3で割ってw64n1に代入
				case '/':
					$opr = "div";
					goto w64_math;
					return;
			}
			return;
		case 'O':
			$type = ord( $file[$st+2] );
			switch ( $type )
			{
				// NO 0,dst_var,src_var,bit_num:  変数並びをビット列に圧縮する
				// NO 1,dst_var,src_var,bit_num:  ビット列を変数並びに展開する
				case 0:
				case 1:
					$st += 3;
					list($v1,$e1) = sco_varno($file, $st);
						$st++; // skip 0x7f
					list($v2,$e2) = sco_varno($file, $st);
						$st++; // skip 0x7f
					$bit_num = sco_calli($file, $st);
					trace("NO $type , $v1+$e1 , $v2+$e2 , $bit_num");
					return;
			}
			return;
		// N& var1,count,var2:  var1,var2のcount個の変数のANDをとる
		case '&': // 0x26
			$opr = "and";
			goto n_bits;
			return;
		// N| var1,count,var2:  var1から始まるcount個の変数のORをとる
		case '|': // 0x7c
			$opr = "or";
			goto n_bits;
			return;
		// N^ var1,count,var2:  var1から始まるcount個の変数のXORをとる
		case '^': // 0x5e
			$opr = "xor";
			goto n_bits;
			return;
		// N+ var1,num,count:  var1から始まるcount個の変数にnumを足す
		case '+': // 0x2b
			$opr = "add";
			goto n_math;
			return;
		// N- var1,num,count:  var1から始まるcount個の変数からnumを引く
		case '-': // 0x2d
			$opr = "sub";
			goto n_math;
			return;
		// N* var1,num,count:  var1から始まるcount個の変数にnumを掛ける
		case '*': // 0x2a
			$opr = "mul";
			goto n_math;
			return;
		// N/ var1,num,count:  var1から始まるcount個の変数をnumで割る
		case '/': // 0x2f
			$opr = "div";
			goto n_math;
			return;
	}
	return;

w64_math:
	$st += 3;
	$w64n1 = sco_calli($file, $st);
	$w64n2 = sco_calli($file, $st);
	$w64n3 = sco_calli($file, $st);
	trace("ND_$opr $w64n1 , $w64n2 , $w64n3");
	$gp_pc["ND"][$w64n1] = var_math( $opr, $gp_pc["ND"][$w64n2] , $gp_pc["ND"][$w64n3] );
	return;
n_bits:
	$st += 2;
	list($v1,$e1) = sco_varno( $file, $st );
		$st++; // skip 0x7f
	$count = sco_calli($file, $st);
	list($v2,$e2) = sco_varno( $file, $st );
		$st++; // skip 0x7f
	trace("data N_$opr $v1+$e1 , $count , $v2+$e2");

	$copy1 = sco_var_get($v1, $e1, $count);
	$copy2 = sco_var_get($v2, $e2, $count);
	sco_n_math( $opr, $copy1, $copy2 );
	sco_var_put($v1, $e1, $copy1);
	return;
n_math:
	$st += 2;
	list($v,$e) = sco_varno( $file, $st );
		$st++; // skip 0x7f
	$num   = sco_calli($file, $st);
	$count = sco_calli($file, $st);
	trace("data N_$opr $v+$e , $num , $count");

	$copy = sco_var_get($v, $e, $count);
	sco_n_math( $opr, $copy, $num );
	sco_var_put($v, $e, $copy);
	return;
}

// PD num:  CG展開の明度を指定する
function P_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// PC num:  システム制御コマンドにつき通常は使用しないこと
		case 'C':
			$st += 2;
			$num = sco_calli($file, $st);
			trace("PC $num");
			$gp_pc["PPC"] = $num;
			return;
		case 'F':
			return PF_cmd35( $file, $st, $ajax );
		// PG ver,num1,num2:  直接画面には反映されないので使用注意 CLUT_READ
		case 'G':
			$st += 2;
			list($v,$e) = sco_varno( $file, $st );
				$st++; // skip 0x7f
			$num1 = sco_calli($file, $st);
			$num2 = sco_calli($file, $st);
			trace("PG $v+$e , $num1 , $num2");
			return;
		// PP ver,num1,num2:  直接画面には反映されないので使用注意 CLUT_WRITE
		case 'P':
			$st += 2;
			list($v,$e) = sco_varno( $file, $st );
				$st++; // skip 0x7f
			$num1 = sco_calli($file, $st);
			$num2 = sco_calli($file, $st);
			trace("PP $v+$e , $num1 , $num2");
			return;
		// PS Plane,Red,Green,Blue:  直接画面には反映されないので使用注意
		case 'S':
			$st += 2;
			$plane = sco_calli($file, $st);
			$red   = sco_calli($file, $st);
			$green = sco_calli($file, $st);
			$blue  = sco_calli($file, $st);
			trace("PS $plane , $red , $green , $blue");
			$color = sprintf("#%02x%02x%02x", $red , $green , $blue);
			$gp_pc["PS"][$plane] = $color;
			return;
		case 'T':
			return PT_cmd35( $file, $st, $ajax );
		case 'W':
			return PW_cmd35( $file, $st, $ajax );
	}
	return;
}

// QP num,point,count:  変数領域などのデータを一部セーブする(数値変数部)
// QP return RND 1 = OK , 200 > ERROR
// QC num1,num2:  セーブファイルをnum2の領域からnum1の領域へコピー
// QC return RND 1 = OK , 200 > ERROR
function Q_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// QD num:  変数領域などのデータをセーブする（全セーブ）
		// QD return RND 1 = OK , 200 > ERROR
		case 'D':
			$st += 2;
			$num = sco_calli($file, $st);
			trace("save QD $num");
			save_savefile( "sav{$num}", $gp_pc );
			$gp_pc["var"][0] = 1;
			return;
		// QE 0,file_name,start_var,write_num:  変数の値をファイルに書き込む
		// QE return RND 1 = OK , 200 > ERROR
		case 'E':
			$type = ord( $file[$st+2] );
			switch ( $type )
			{
				case 0: // int
				case 1: // str
					$st += 3;
					$fn = sco_ascii( $file, $st, ':' );
					list($v,$e) = sco_varno( $file, $st );
						$st++; // skip 0x7f
					$write_num  = sco_calli($file, $st);
					trace("QE $type , $fn , $v+$e , $write_num");
					$gp_pc["var"][0] = 255;
					return;
			}
			return;
	}
	return;
}

// SM no:  PCMデータをメモリ上に乗せる
// SO var:  PCMデバイスのサポート情報を取得
// SQ noL, noR, loop:  左右別々のPCMデータを合成して演奏する
// SW var,channel,S-rate,bit:  指定データ形式が演奏出来るかチェックする．
function S_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// SC var:  CDの再生位置を取得する
		case 'C':
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("bgm SC $v+$e += 4");
			sco_var_put($v, $e+0, 999); // track
			sco_var_put($v, $e+1, 999); // min
			sco_var_put($v, $e+2, 999); // sec
			sco_var_put($v, $e+3, 999); // frame
			return;
		case 'G':
			return SG_cmd35( $file, $st, $ajax );
		case 'I':
			$type = ord( $file[$st+2] );
			switch ( $type )
			{
				// SI 0,var:  MIDIが使用可能かどうか調べる
				// SI 1,var:  WAVEが使用可能かどうか調べる
				// SI 2,var:  CDが使用可能かどうか調べる
				case 0:
				case 1:
				case 2:
					$st += 3;
					list($v,$e) = sco_varno($file, $st);
						$st++; // skip 0x7f
					trace("SI $type , $v+$e");
					sco_var_put($v, $e, 1); // 0=error , 1=ok
					return;
			}
			return;
		// SL num:  次の音楽(CD)のループ回数を指定する
		case 'L':
			$st += 2;
			$num = sco_calli($file, $st);
			trace("bgm SP $num");
			return;
		// SP no,loop:  PCMデータを演奏する
		case 'P':
			$st += 2;
			$no   = sco_calli($file, $st);
			$loop = sco_calli($file, $st);
			trace("wave SP $no , $loop");
			$gp_pc["SP"] = $no;
			return;
		case 'R':
			return SR_cmd35( $file, $st, $ajax );
		// SS num:  音楽演奏を開始する(CD)
		case 'S':
			$st += 2;
			$num = sco_calli($file, $st);
			trace("bgm SS $num");
			$gp_pc["bgm"] = array("audio", $num);
			return;
		// ST time:  PCMデータの演奏を停止する
		case 'T':
			$st += 2;
			$time = sco_calli($file, $st);
			trace("wave ST $time");
			return;
		// SU var1,var2:  PCMの演奏状態を変数 var1 , var2 に返す
		case 'U':
			$st += 2;
			list($v1,$e1) = sco_varno($file, $st);
				$st++; // skip 0x7f
			list($v2,$e2) = sco_varno($file, $st);
				$st++; // skip 0x7f
			trace("wave SU $v1+$e1 , $v2+$e2");
			sco_var_put($v1, $e1, 0); // 0=stop , 1=play
			sco_var_put($v2, $e2, 100); // timer 1/100
			return;
		case 'X':
			return SX_cmd35( $file, $st, $ajax );
	}
	return;
}

// UC mode,num:  (getd,cali) ラベル・シナリオコールのスタックフレームを削除する
// UD mode:  (cali) mode = モード
// UR var:  ｽﾀｯｸ情報取得
// UP 3,work_dir,file_name:  外部ﾌﾟﾛｸﾞﾗﾑ起動後SYSTEM3.5終了
function U_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// UG 変数,cali:  システム制御コマンドにつき通常は使用しないこと
		// stack pop
		case 'G':
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$cali = sco_calli($file, $st);
			trace("UG $v+$e , $cali");
			while ( $cali > 0 )
			{
				$cali--;
				$p = $v + $e + $cali;
				$gp_pc["var"][$p] = array_shift( $gp_pc["stack"] );
			}
			return;
		// US 変数,cali:  システム制御コマンドにつき通常は使用しないこと
		// stack push
		case 'S':
			$st += 2;
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$cali = sco_calli($file, $st);
			trace("US $v+$e , $cali");
			for ( $i=0; $i < $cali; $i++ )
			{
				$p = $v + $e + $i;
				array_unshift( $gp_pc["stack"] , $gp_pc["var"][$p] );
			}
			return;
	}
	return;
}

// VF:  ユニットマップの画面への反映
// VT sp,sa,sx,sy,cx,cy,dp,da,dx,dy:  ユニットマップデータを矩形指定でコピーする
// VIC x,y,cx,cy:  ユニットマップの画面反映(ユニットマップ座標指定)
// VIP x,y,cx,cy:  ユニットマップの画面反映(画面座標指定)
function V_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		case 'A':
			return VA_cmd35( $file, $st, $ajax );
		// VC nPageNum,x0Map,y0Map,cxMap,cyMap,cxUnit,cyUnit:  ユニットマップディスプレイ表示領域の設定 （転送先設定）
		// VC return RND 0 = ERROR , 1 = OK
		case 'C':
			$st += 2;
			$nPageNum = sco_calli($file, $st);
			$x0Map  = sco_calli($file, $st);
			$y0Map  = sco_calli($file, $st);
			$cxMap  = sco_calli($file, $st);
			$cyMap  = sco_calli($file, $st);
			$cxUnit = sco_calli($file, $st);
			$cyUnit = sco_calli($file, $st);
			trace("VC $nPageNum , $x0Map , $y0Map , $cxMap , $cyMap , $cxUnit , $cyUnit");
			$gp_pc["VC"] = array( $nPageNum , $x0Map , $y0Map , $cxMap , $cyMap , $cxUnit , $cyUnit );
			$gp_pc["var"][0] = 1;
			return;
		// VE pos_x,pos_y,len_x,len_y,out_ptn,flag:  範囲指定付きでユニットマップの内容を画面に描画する
		case 'E':
			$st += 2;
			$pos_x = sco_calli($file, $st);
			$pos_y = sco_calli($file, $st);
			$len_x = sco_calli($file, $st);
			$len_y = sco_calli($file, $st);
			$out_ptn = sco_calli($file, $st);
			$flag  = sco_calli($file, $st);
			trace("VE $pos_x , $pos_y , $len_x , $len_y , $out_ptn , $flag");
			$src = array( $pos_x , $pos_y , $len_x , $len_y , $out_ptn , $flag );
			sco_vp_div_add( $src );
			return;
		// VG nPage,nType,x,y:  ユニットマップの値の取得
		// VG return RND Data
		case 'G':
			$st += 2;
			$nPage = sco_calli($file, $st);
			$nType = sco_calli($file, $st);
			$x = sco_calli($file, $st);
			$y = sco_calli($file, $st);

			$varno = $gp_pc["VR"][$nPage][$nType];
			list($n,$x0,$y0,$mx,$my,$ux,$uy) = $gp_pc["VC"];
			$pos = ($y * $mx) + $x;

			$bak = $gp_pc["var"][$varno][$pos];
			trace("VG $nPage , $nType , $x , $y = %d", $bak);
			$gp_pc["var"][0] = $bak;
			return;
		// VH nPage,x,y,lengs_x,lengs_y,max:  歩数ペイント
		case 'H':
			$st += 2;
			$nPage = sco_calli($file, $st);
			$x = sco_calli($file, $st);
			$y = sco_calli($file, $st);
			$lengs_x = sco_calli($file, $st);
			$lengs_y = sco_calli($file, $st);
			$max = sco_calli($file, $st);
			trace("VH $nPage , $x , $y , $lengs_x , $lengs_y , $max");
			return;
		// VP nPage,x0Unit,y0Unit,nxUnit,nyUnit,bSpCol:  ユニットCG取得位置＆並び状態設定＆スプライト色指定 (転送元設定)
		case 'P':
			$st += 2;
			$nPage  = sco_calli($file, $st);
			$x0Unit = sco_calli($file, $st);
			$y0Unit = sco_calli($file, $st);
			$nxUnit = sco_calli($file, $st);
			$nyUnit = sco_calli($file, $st);
			$bSpCol = sco_calli($file, $st);
			trace("VP $nPage , $x0Unit , $y0Unit , $nxUnit , $nyUnit , $bSpCol");
			$src = array( $nPage , $x0Unit , $y0Unit , $nxUnit , $nyUnit , $bSpCol );
			$gp_pc["VP"][$nPage] = sco_vp_g0( $src );
			$gp_pc["VP"][$nPage]["set"] = $src;
			return;
		// VR nPage,nType,var:  変数→MAPデータ転送
		case 'R':
			$st += 2;
			$nPage = sco_calli($file, $st);
			$nType = sco_calli($file, $st);
			list($v,$e) = sco_varno( $file, $st );
				$st++; // skip 0x7f
			trace("VR $nPage , $nType , $v+$e");
			$gp_pc["VR"][$nPage][$nType] = $v+$e;
			return;
		// VS nPage,nType,x,y,wData:  ユニットマップへの値のセット
		// VS return RND previous wData
		case 'S':
			$st += 2;
			$nPage = sco_calli($file, $st);
			$nType = sco_calli($file, $st);
			$x = sco_calli($file, $st);
			$y = sco_calli($file, $st);
			$wData = sco_calli($file, $st);
			trace("VS $nPage , $nType , $x , $y , $wData");

			$varno = $gp_pc["VR"][$nPage][$nType];
			list($n,$x0,$y0,$mx,$my,$ux,$uy) = $gp_pc["VC"];
			$pos = ($y * $mx) + $x;

			$bak = $gp_pc["page"][$varno][$pos];
			$gp_pc["page"][$varno][$pos] = $wData;
			$gp_pc["var"][0] = $bak;
			return;
		// VV nPage,fEnable:  ユニットマップの層ごとの表示有効/無効の切り替え
		case 'V':
			$st += 2;
			$arg = sco_var_args( 2, $file, $st );
			trace("VV %s", serialize($arg));

			list($p,$s) = $arg;
			$gp_pc["VV"][$p] = $s;
			return;
		// VW nPage,nType,var:  MAP→変数データ転送
		case 'W':
			$st += 2;
			$arg = sco_var_args( 3, $file, $st );
			trace("VW %s", serialize($arg));

			list($p,$t,$v) = $arg;
			$gp_pc["VR"][$p][$t] = $v;
			//unset( $gp_pc["VR"][$p][$t] );
			return;
		case 'X':
			$st += 2;
			$arg = sco_var_args( 4, $file, $st );
			trace("VX %s", serialize($arg));

			list($t,$p,$x,$y) = $arg;
			switch ( $t )
			{
				// VX 0,nPage,x0Unit,y0Unit:  ユニットCG取得位置の変更 (VP x0Unit,y0Unit)
				case 0:
					$src = $gp_pc["VP"][$p]["set"];
					$src[1] = $x;
					$src[2] = $y;

					//$gp_pc["VP"][$p] = sco_vp_g0( $src );
					$gp_pc["VP"][$p]["set"] = $src;
					return;
				// VX 1,nPage,nxUnit,nyUnit:  ユニットCG取得並び個数の変更 (VP nxUnit,nyUnit)
				case 1:
					$src = $gp_pc["VP"][$p]["set"];
					$src[3] = $x;
					$src[4] = $y;

					//$gp_pc["VP"][$p] = sco_vp_g0( $src );
					$gp_pc["VP"][$p]["set"] = $src;
					return;
				// VX 2,nPage,bSpCol,reserve:  ユニットCGスプライト色変更 (VP bSpCol)
				// VX 3,nPage,0,0:  VFのユニットマップ全反映指定
			}
			return;
		case 'Z':
			$type = ord( $file[$st+2] );
			$st += 3;
			$arg = sco_var_args( 2, $file, $st );
			trace("VZ $type , %s", serialize($arg));
			switch ( $type )
			{
				// VZ 0,nPage,reserve:  透明パターン番号指定解除(ﾃﾞﾌｫﾙﾄでは解除されている)
				case 0:
					return;
				// VZ 1,nPage,unit_no:  透明パターン番号の指定(ﾃﾞﾌｫﾙﾄでは指定無し)
				case 1:
					return;
				// VZ 2,x0Map,y0Map:  ユニットマップ表示位置の変更 (VC x0Map,y0Map)
				case 2:
					$gp_pc["VC"][1] = $arg[0];
					$gp_pc["VC"][2] = $arg[1];
					return;
				// VZ 3,cxUnit,cyUnit:  ユニットサイズの変更 (VC cxUnit,cyUnit)
				case 3:
					$gp_pc["VC"][5] = $arg[0];
					$gp_pc["VC"][6] = $arg[1];
					return;
			}
			return;
	}
	return;
}

function W_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc;
	switch( $file[$st+1] )
	{
		// WV start_x,start_y,size_x,size_y:  VIEW領域を設定する
		case 'V':
			$st += 2;
			$arg = sco_var_args( 4, $file, $st );
			trace("WV %s", serialize($arg));
			$gp_pc["WV"] = $arg;
			return;
		// WW x_size,y_size,color:  全画面領域を設定する
		case 'W':
			$st += 2;
			$arg = sco_var_args( 3, $file, $st );
			trace("WW %s", serialize($arg));
			$gp_pc["WW"] = $arg;
			return;
		// WX x0,y0,cx,cy:  画面反映 on (指定範囲のみ再描画)
		case 'X':
			$st += 2;
			$arg = sco_var_args( 4, $file, $st );
			trace("WX %s", serialize($arg));
			return;
		case 'Z':
			return WZ_cmd35( $file, $st, $ajax );
	}
	return;
}

function Z_cmd35( &$file, &$st, &$ajax )
{
	global $gp_pc, $gp_input, $gp_key;
	switch( $file[$st+1] )
	{
		// ZA 0,type:  文字飾りの種類を指定する
		// ZA 1,col:  文字飾りに用いる色の指定
		// ZA 3,mode:  自動改頁制御
		case 'A':
			$b1 = ord( $file[$st+2] );
			$st += 3;
			$b2 = sco_calli($file, $st);
			trace("ZA $b1 , $b2");
			$gp_pc["ZA"] = array($b1 , $b2);
			return;
		// ZB 太さ:  メッセージ文字を太さを設定
		case 'B':
			$st += 2;
			$bold = sco_calli($file, $st);
			trace("ZB $bol");
			$gp_pc["ZB"] = $bold;
			return;
		// ZC m,n:  システムの使用環境を変更する
		case 'C':
			$st += 2;
			$arg = sco_var_args( 2, $file, $st );
			trace("ZC %s", serialize($arg));
			$gp_pc["ZC"][ $arg[0] ] = $arg[1];
			return;
		case 'D':
			$type = ord( $file[$st+2] );
			$st += 3;
			$sw = sco_calli($file, $st);
			trace("ZD $type , $sw");
			switch( $type )
			{
				// ZD0,sw:  デバッグモード時のデバッグメッセージの出力 ON/OFF/PAUSE
				case 0:
					return;
				// ZD1,sw:  デバッグ用コマンド change disc dialog
				case 1:
					return;
				// ZD2,num:  デバッグ用コマンド num dialog
				// ZD3,value:  デバッグ用コマンド value dialog
			}
			return;
		// ZE sw:  選択肢を選んだらメッセージ領域を初期化するかどうかを指定する
		case 'E':
			$st += 2;
			$sw = sco_calli($file, $st);
			trace("ZE $sw");
			$gp_pc["ZE"] = $sw;
			return;
		// ZF sw:  選択肢枠サイズを可変にするか固定にするかを指定
		case 'F':
			$st += 2;
			$sw = sco_calli($file, $st);
			trace("ZF $sw");
			$gp_pc["ZF"] = $sw;
			return;
		// ZG var:  CGのロードした回数をリンク番号毎に配列に書き込む配列を設定
		case 'G':
			$st += 2;
			$var = sco_calli($file, $st);
			trace("ZG $var");
			return;
		// ZH switch:  全角半角切替え
		case 'H':
			$st += 2;
			$switch = sco_calli($file, $st);
			trace("ZH $switch");
			return;
		// ZI key,mode:  Aコマンドのキー入力待ち時の各キーの動作の指定
		case 'I':
			$st += 2;
			$arg = sco_var_args( 2, $file, $st );
			trace("ZI %s", serialize($arg));
			return;
		// ZL line:  メッセージ領域の文字の縦方向行間ドット数を指定する
		case 'L':
			$st += 2;
			$line = sco_calli($file, $st);
			trace("ZL $size");
			return;
		// ZM size:  シナリオメッセージのフォントサイズを指定する
		case 'M':
			$st += 2;
			$size = sco_var( sco_calli($file, $st) );
			trace("ZM $size");
			$gp_pc["ZM"] = $size;
			return;
		// ZS size:  選択肢のフォントサイズを指定する
		case 'S':
			$st += 2;
			$size = sco_var( sco_calli($file, $st) );
			trace("ZS $size");
			$gp_pc["ZS"] = $size;
			return;
		case 'T':
			return ZT_cmd35( $file, $st, $ajax );
		// ZW sw:  CAPS 状態の内部的制御を変更する
		case 'W':
			$st += 2;
			$sw = sco_calli($file, $st);
			trace("ZW $sw");
			$gp_pc["ZW"] = $sw;
			return;
		case 'Z':
			return ZZ_cmd35( $file, $st, $ajax );
	}
	return;
}

function sco_cmd( &$id, &$st, &$run, &$ajax )
{
	$func = __FUNCTION__;
	global $sco_file, $gp_pc, $gp_input, $gp_key;
	$file = &$sco_file[$id];
	switch( $file[$st] )
	{
		case 'B':  return B_cmd35( $file, $st, $ajax );
		case 'C':  return C_cmd35( $file, $st, $ajax );
		case 'D':  return D_cmd35( $file, $st, $ajax );
		case 'E':  return E_cmd35( $file, $st, $ajax );
		case 'F':  return F_cmd35( $file, $st, $ajax );
		case 'G':  return G_cmd35( $file, $st, $ajax );
		case 'I':  return I_cmd35( $file, $st, $ajax );
		case 'J':  return J_cmd35( $file, $st, $ajax );
		case 'K':  return K_cmd35( $file, $st, $ajax );
		case 'L':  return L_cmd35( $file, $st, $ajax );
		case 'M':  return M_cmd35( $file, $st, $ajax );
		case 'N':  return N_cmd35( $file, $st, $ajax );
		case 'P':  return P_cmd35( $file, $st, $ajax );
		case 'Q':  return Q_cmd35( $file, $st, $ajax );
		case 'S':  return S_cmd35( $file, $st, $ajax );
		case 'U':  return U_cmd35( $file, $st, $ajax );
		case 'V':  return V_cmd35( $file, $st, $ajax );
		case 'W':  return W_cmd35( $file, $st, $ajax );
		case 'Y':  return Y_cmd35( $file, $st, $ajax );
		case 'Z':  return Z_cmd35( $file, $st, $ajax );
		// A  キー入力待ちをして、入力があればメッセージ領域の初期化と
		case 'A':
			trace("text NEXT");
			if ( ! empty( $gp_input ) )
			{
				if ( $gp_input[0] == "key" && $gp_input[1] == -1 )
				{
					$gp_input = array();
					return;
				}
				$st++;
				sco_text_add( "_NEXT_" );
				$gp_input = array();
			}
			return;
		// H fig,num:  数字を表示する
		case 'H':
			if ( $file[$st+1] == 'H' )
				$st++;
			$fig = ord( $file[$st+1] );
			$st += 2;
			$num = sco_var( sco_calli($file, $st) );
			trace("text H $fig , $num");
			$str = sprintf("%0{$fig}d ", $num);
			sco_text_add( $str );
			return;
		// R  メッセージ領域の文字列を改行する
		case 'R':
			$st++;
			trace("text CRLF");
			sco_text_add( "_CRLF_" );
			return;
		// T text_x,text_y:  文字の表示開始座標を指定する
		case 'T':
			$st++;
			$arg = sco_var_args(2, $file, $st);
			trace("text T %s", serialize($arg));
			$gp_pc["T"] = array( sco_var($arg[0]) , sco_var($arg[1]) );
			return;
		// X num:  指定の文字列を表示する
		case 'X':
			$st++;
			$num = sco_calli($file, $st);
			trace("text X  $num");
			sco_text_add( $gp_pc["X"][$num] );
			return;

		case ':': // 0x3a
			$st++;
			trace("NOP");
			return;
		case '!': // 0x21
			$st++;
			list($v,$e) = sco_varno($file, $st);
			$exp = sco_var( sco_calli($file, $st) );
			trace("var_$v+$e = $exp");
			sco_var_put( $v, $e, $exp );
			return;
		case ' ': // 0x20
			$jp = sco_sjis($file, $st);
			trace("text %s", $jp);
			sco_text_add( $jp );
			return;
		// $label$文字列$  選択肢を登録する
		case '$': // 0x24
			// if $run , select menu stop here
			// reuse $ajax as no animation will occur on select loop
			if ( $ajax ) // within select loop
			{
				$ajax = false;
				return;
			}
			else
			{
				$st++;
				$sel_jmp = str2int( $file, $st, 4 );
				trace("select loc_%x , TEXT", $sel_jmp);

				$bak = $gp_pc["div"];
				$gp_pc["div"] = array();
				$loop = true;
				$select = true;
				while ( $select )
				{
					trace("= select_%x : ", $gp_pc["pc"][1]);
					$func($gp_pc["pc"][0], $gp_pc["pc"][1], $loop, $select);
				}
				$sel_txt = "";
				foreach ( $gp_pc["div"] as $text )
				{
					if ( $text['t'] == "text" )
						$sel_txt .= $text['jp'];
				}
				$gp_pc["div"] = $bak;
					$gp_pc["pc"][1]++; // skip '$'

				trace("select TEXT = $sel_txt");
				$gp_pc["select"][] = array( $sel_jmp , $sel_txt );
			}
			return;
		// ]  選択肢を開く
		case ']': // 0x5d
			if ( isset( $gp_pc["select"]['B'] ) )
				unset( $gp_pc["select"]['B'] );

			$gp_pc["select"]['B'] = array(
				$st + 1,
				"&lt;&lt;&lt;",
			);

			if ( ! empty( $gp_input ) )
			{
				if ( $gp_input[0] == "select" )
				{
					$sel = $gp_input[1];
					if ( isset( $gp_pc["select"][$sel] ) )
					{
						trace("select $sel");
						$gp_pc["pc"][1] = $gp_pc["select"][$sel][0];
						$gp_pc["select"] = array();
						$gp_input = array();
						sco_text_add( "_NEXT_" );
						return;
					}
				}
			}
			trace("select menu");
			return;
		case '{': // 0x7b
			if ( sco_loop_inf( $file, $st ) )
				return;
			if ( sco_loop_IK0( $file, $st ) )
			{
				$st += 17;
				return;
			}

			$st++;
			$exp = sco_var( sco_calli($file, $st) );
			$end = str2int( $file, $st, 4 );
			trace("if not %d then goto loc_%x", $exp, $end);
			if ( ! $exp )
				$gp_pc["pc"][1] = $end;
			return;
		// <  var,start,end,sign,step:  FORループ開始
		// <@ cali:成立中実行コマンド  <@ Whileループ開始
		case '<': // 0x3c
			$type = ord( $file[$st+1] );
			$st += 2;
			if ( $type == 0 )
				$st += 2;
			$done = str2int( $file, $st, 4 );
			list($v,$e) = sco_varno($file, $st);
				$st++; // skip 0x7f
			$end  = sco_var( sco_calli($file, $st) );
			$sign = sco_var( sco_calli($file, $st) );
			$step = sco_var( sco_calli($file, $st) );

			if ( $sign ) // i++
			{
				if ( $type )
					$gp_pc["var"][$v+$e] += $step;

				$var = $gp_pc["var"][$v+$e];
				if ( $var <= $end )
					trace("for ($var += $step) < $end");
				else
				{
					trace("for end  loc_%x", $done);
					$gp_pc["pc"][1] = $done;
				}
				return;
			}
			else // i--
			{
				if ( $type )
					$gp_pc["var"][$v+$e] -= $step;

				$var = $gp_pc["var"][$v+$e];
				if ( $var >= $end )
					trace("for ($var -= $step) > $end");
				else
				{
					trace("for end  loc_%x", $done);
					$gp_pc["pc"][1] = $done;
				}
				return;
			}
			return;
		// >  FORループ終了
		// >  Whileループ終了
		case '>': // 0x3e
			$st++;
			$forloop = str2int( $file, $st, 4 );
			trace("for loop  loc_%x", $forloop);
			$gp_pc["pc"][1] = $forloop;
			return;
		// #label,number:  データを読み込む位置を指定する (関連 Fｺﾏﾝﾄﾞ)
		case '#': // 0x23
			$st++;
			$label  = str2int( $file, $st, 4 );
			$number = sco_var( sco_calli($file, $st) );

			if ( $number )
			{
				$table = $label + (($number-1) * 4);
				$label = str2int( $file, $table, 4 );
			}

			trace("array [ %d ] = loc_%x", $number, $label);
			$gp_pc["F"] = array($gp_pc["pc"][0], $label);
			return;
		case '~': // 0x7e , function call
			if ( sco_skip_func() )
			{
				trace("skip callfunc");
				$st += 7;
				return;
			}

			$st++;
			$page = str2int( $file, $st, 2 );
			switch ( $page )
			{
				case 0:
					$var = sco_calli($file, $st);
					trace("return func = $var");
					$gp_pc["return"] = $var;
					$jal = array_shift( $gp_pc["jal"] );
					$gp_pc["pc"] = $jal;
					return;
				case 0xffff:
					list($v,$e) = sco_varno($file, $st);
						$st++; // skip 0x7f
					trace("return $v+$e = var");
					sco_var_put($v, $e, $gp_pc["return"]);
					return;
				default:
					$label = str2int( $file, $st, 4 );
					trace("func sco_%d , loc_%x", $page, $label);

					array_unshift( $gp_pc["jal"], array($gp_pc["pc"][0], $gp_pc["pc"][1]) );
					$gp_pc["pc"] = array($page, $label);
					return;
			}
			return;
		// @mmmm:  ラベルジャンプ文
		case '@': // 0x40
			$st++;
			$label = str2int( $file, $st, 4 );
			trace("jump loc_%x", $label);
			$gp_pc["pc"][1] = $label;
			return;
		// &  page jump
		case '&': // 0x26
			$st++;
			$page = sco_var( sco_calli($file, $st) ) + 1;
			trace("jump sco_%d", $page);
			$gp_pc["pc"] = array($page, 0);
			return;
		// ¥mmmm:  ラベルコール文
		// \0  label return
		case '\\': // 0x5c
			$st++;
			$label = str2int( $file, $st, 4 );
			if ( $label == 0 )
			{
				trace("return label");
				$jal = array_shift( $gp_pc["jal"] );
				$gp_pc["pc"] = $jal;
			}
			else
			{
				trace("call loc_%x", $label);
				array_unshift( $gp_pc["jal"], array($gp_pc["pc"][0], $gp_pc["pc"][1]) );
				$gp_pc["pc"][1] = $label;
			}
			return;
		// %mmmm:  ページコール文
		// %0  page return
		case '%': // 0x25
			$st++;
			$page = sco_calli($file, $st) + 1;
			if ( $page == 1 )
			{
				trace("return page");
				$jal = array_shift( $gp_pc["jal"] );
				$gp_pc["pc"] = $jal;
			}
			else
			{
				trace("call sco_%d", $page);
				array_unshift( $gp_pc["jal"], array($gp_pc["pc"][0], $gp_pc["pc"][1]) );
				$gp_pc["pc"] = array($page, 0);
			}
			return;
		default:
			$b1 = ord( $file[$st] );
			if ( $b1 & 0x80 )
			{
				$jp = sco_sjis($file, $st);
				trace("text %s", $jp);
				sco_text_add( $jp );
				return;
			}
			return;
	}
	return;
}
