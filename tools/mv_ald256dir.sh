#!/bin/bash
<<'////'
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
////

for f in *; do
	[ -f "$f" ] || continue

	tit="${f%.*}"
	ext="${f##*.}"

	t1=$(echo "$tit" | bc)
	t2=$(echo "$tit/256" | bc)
	[[ "$t1" == "$t2" ]] && continue

	dn=$(printf "%03d" $t2)
	fn=$(printf "%03d/%05d" $t2  $t1)
	mkdir -p "$dn"

	mv -vf "$f" "$fn.$ext"
done
