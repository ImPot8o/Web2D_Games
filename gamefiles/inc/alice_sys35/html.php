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
echo <<<_HTML
<div id="key_input">
<table><tr>

<td>
	<table>
	<tr>
		<td>&nbsp;</td>
		<td><button data="{$gp_key['up']}">UP</button></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><button data="{$gp_key['left']}">LT</button></td>
		<td>&nbsp;</td>
		<td><button data="{$gp_key['right']}">RT</button></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><button data="{$gp_key['down']}">DN</button></td>
		<td>&nbsp;</td>
	</tr>
	</table>
</td>

<td style="width:100%;">
	<button data="0">SKIP</button>
</td>

<td>
	<table>
	<tr>
		<td>&nbsp;</td>
		<td><button data="{$gp_key['esc']}">C</button></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><button data="{$gp_key['tab']}">D</button></td>
		<td>&nbsp;</td>
		<td><button data="{$gp_key['enter']}">A</button></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><button data="{$gp_key['space']}">B</button></td>
		<td>&nbsp;</td>
	</tr>
	</table>
</td>

</tr></table>
<p><span id="clickgpad">GAMEPAD</span></p>
</div>
_HTML;
?>

<style>
	#key_input {
		position:fixed;
		bottom:0;
		width:100%;
	}
	#key_input button {
		font-size:1em;
		padding:0.5em;
		cursor:pointer;
	}
	#key_input table {
		margin:0 auto 0 auto;
		text-align:center;
	}
	#key_input p {
		text-align:center;
		cursor:pointer;
	}
	#clickgpad {
		position:fixed;
		bottom:0;
		text-align:center;
	}
</style>

<script>
var auto_skip = false;

function listener()
{
	if ( auto_skip )
	{
		window_update( "&resume&input=key,0" );
		setTimeout(listener, 100);
	}
}

jq("#key_input").on("click", "button", function(){
	var data = jq(this).attr("data");
	if ( data == 0 )
	{
		auto_skip = ! auto_skip;
		if ( auto_skip )
			jq(this).empty().append("AUTO");
		else
			jq(this).empty().append("SKIP");
		listener();
	}
	else
		window_update( "&resume&input=key,"+data );
});

jq("#clickgpad").click(function(){
	jq("#key_input table").toggle();
});
</script>
