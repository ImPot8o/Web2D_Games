<!doctype html>
<html><head>

<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Quad Perspective Test (SRC * M)</title>
@@<quad-inc.css>@@
@@<qdfn.js>@@

</head><body>
@@<mona_lisa.png>@@

@@<quad-inc.inc>@@
@@<quad-inc.js>@@

<script>
var vert_src = `
	attribute  highp  vec2  a_uv;
	uniform    highp  vec4  u_pxsize;
	uniform    highp  mat3  u_mat3;
	varying    highp  vec2  v_uv;

	highp  vec3  v3;
	void main(void){
		v_uv = vec2(a_uv.x * u_pxsize.z , a_uv.y * u_pxsize.w);

		v3 = vec3(a_uv, 1.0) * u_mat3;
			v3.x *= u_pxsize.x;
			v3.y *= u_pxsize.y;
			v3.xyz /= v3.z;
		gl_Position = vec4(v3, 1.0);
	}
`;

var frag_src = `
	uniform  sampler2D  u_tex;
	varying  highp  vec2  v_uv;

	void main(void){
		gl_FragColor = texture2D(u_tex, v_uv);
	}
`;

QDFN.set_shader_program(vert_src, frag_src);
QDFN.set_shader_loc('a_uv', 'u_pxsize', 'u_mat3', 'u_tex');

QDFN.set_tex_count('u_tex', 1);
var TEX_SIZE = [360,640];

SRC = [0,0 , TEX_SIZE[0],0 , TEX_SIZE[0],TEX_SIZE[1] , 0,TEX_SIZE[1]];
function quad_draw(){
	QDFN.canvas_resize();
	QDFN.set_vec4_size('u_pxsize', TEX_SIZE[0], TEX_SIZE[1]);
	var mat3 = get_perspective_mat3(SRC, DST, false);
	QDFN.set_mat3fv('u_mat3', mat3);

	var scx = find_intersect_point(SRC);
	var dcx = find_intersect_point(DST);

	// for simple and twisted
	if ( dcx !== -1 ){
		var uv = [
			scx[0],scx[1] , SRC[0],SRC[1] , SRC[2],SRC[3] ,
			scx[0],scx[1] , SRC[2],SRC[3] , SRC[4],SRC[5] ,
			scx[0],scx[1] , SRC[4],SRC[5] , SRC[6],SRC[7] ,
			scx[0],scx[1] , SRC[6],SRC[7] , SRC[0],SRC[1] ,
		];
		QDFN.v2_attrib('a_uv', uv);

		console.log('simple and twisted', dcx);
		return QDFN.draw(12);
	}

	// bended
	var area1 = quad_area(DST[0],DST[1] , DST[2],DST[3] , DST[4],DST[5] , DST[6],DST[7]);
	var area2 = quad_area(DST[2],DST[3] , DST[4],DST[5] , DST[6],DST[7] , DST[0],DST[1]);

	if ( area1 < area2 ){
		var uv = [
			SRC[0],SRC[1] , SRC[2],SRC[3] , SRC[4],SRC[5] ,
			SRC[0],SRC[1] , SRC[4],SRC[5] , SRC[6],SRC[7] ,
		];
	}
	else {
		var uv = [
			SRC[2],SRC[3] , SRC[4],SRC[5] , SRC[6],SRC[7] ,
			SRC[2],SRC[3] , SRC[6],SRC[7] , SRC[0],SRC[1] ,
		];
	}
	QDFN.v2_attrib('a_uv', uv);

	console.log('bended', dcx);
	return QDFN.draw(6);
}

function render(){
	if ( IS_CLICK ){
		get_dst_corner();
		quad_draw();
		IS_CLICK = false;
	}
	requestAnimationFrame(render);
}

QDFN.bind_tex2D_id(0, 'mona_lisa_png').then(function(){
	IS_CLICK = true;
	requestAnimationFrame(render);
});
</script>

</body></html>
