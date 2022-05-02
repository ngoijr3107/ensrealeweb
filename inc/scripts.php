
<!-- JS FILES --> 
<script src="js/jquery.min.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script src="js/swiper.min.js"></script> 
<script src="js/tilt.jquery.js"></script> 
<script src="js/wow.min.js"></script> 
<script src="js/odometer.min.js"></script> 
<script src="js/jquery.typewriter.js"></script> 
<script src="js/fancybox.min.js"></script> 
<script src="js/app.js"></script>
<script src="js/scripts.js"></script>

<script  id="vertex-shader" type="x-shader/x-vertex">
	vec4 mod289(vec4 x)
    {
      return x - floor(x * (1.0 / 289.0)) * 289.0;
    }

    vec4 permute(vec4 x)
    {
      return mod289(((x*34.0)+1.0)*x);
    }

    vec4 taylorInvSqrt(vec4 r)
    {
      return 1.79284291400159 - 0.85373472095314 * r;
    }

    vec2 fade(vec2 t) {
      return t*t*t*(t*(t*6.0-15.0)+10.0);
    }

    // Classic Perlin noise
    float cnoise(vec2 P)
    {
      vec4 Pi = floor(P.xyxy) + vec4(0.0, 0.0, 1.0, 1.0);
      vec4 Pf = fract(P.xyxy) - vec4(0.0, 0.0, 1.0, 1.0);
      Pi = mod289(Pi); // To avoid truncation effects in permutation
      vec4 ix = Pi.xzxz;
      vec4 iy = Pi.yyww;
      vec4 fx = Pf.xzxz;
      vec4 fy = Pf.yyww;

      vec4 i = permute(permute(ix) + iy);

      vec4 gx = fract(i * (1.0 / 41.0)) * 2.0 - 1.0 ;
      vec4 gy = abs(gx) - 0.5 ;
      vec4 tx = floor(gx + 0.5);
      gx = gx - tx;

      vec2 g00 = vec2(gx.x,gy.x);
      vec2 g10 = vec2(gx.y,gy.y);
      vec2 g01 = vec2(gx.z,gy.z);
      vec2 g11 = vec2(gx.w,gy.w);

      vec4 norm = taylorInvSqrt(vec4(dot(g00, g00), dot(g01, g01), dot(g10, g10), dot(g11, g11)));
      g00 *= norm.x;
      g01 *= norm.y;
      g10 *= norm.z;
      g11 *= norm.w;

      float n00 = dot(g00, vec2(fx.x, fy.x));
      float n10 = dot(g10, vec2(fx.y, fy.y));
      float n01 = dot(g01, vec2(fx.z, fy.z));
      float n11 = dot(g11, vec2(fx.w, fy.w));

      vec2 fade_xy = fade(Pf.xy);
      vec2 n_x = mix(vec2(n00, n01), vec2(n10, n11), fade_xy.x);
      float n_xy = mix(n_x.x, n_x.y, fade_xy.y);
      return 2.3 * n_xy;
    }

    float map(float value, float oldMin, float oldMax, float newMin, float newMax) {
        return newMin + (newMax - newMin) * (value - oldMin) / (oldMax - oldMin);
    }

    varying vec3 vUv;
    varying float vTime;
    varying float vZ;
    uniform float time;
    void main()
    {
        vUv = position;
        vTime = time;
        vec3 newPos = position;
        vec2 peak = vec2(1.0 - abs(.5 - uv.x), 1.0 - abs(.5 - uv.y));
        vec2 noise = vec2(
            map(cnoise(vec2(0.3 * time + uv.x * 5., uv.y * 5.)), 0., 1., -2., (peak.x * peak.y * 30.)),
            map(cnoise(vec2(-0.3 * time + uv.x * 5., uv.y * 5.)), 0., 1., -2., 25.)
        );

        //newPos.x += noise.x * 10.;
        newPos.z += noise.x * .06 * noise.y;
        vZ = newPos.z;
        vec4 mvPosition = modelViewMatrix * vec4( newPos, 1.0 );
        gl_PointSize = 10.0;
        gl_Position = projectionMatrix * mvPosition;
    }
	</script>

    <script id="fragment-shader" type="x-shader/x-fragment">
	   varying vec3 vUv;
		varying float vTime;
		varying float vZ;
		uniform sampler2D texture;

		float map(float value, float oldMin, float oldMax, float newMin, float newMax) {
			return newMin + (newMax - newMin) * (value - oldMin) / (oldMax - oldMin);
		}


		void main()
		{
			vec3 colorA = vec3(.6, 0.17, 0.17);
			vec3 colorB = vec3(0.17, 0.8, .7); 
			//vec3 color = mix(colorA, colorB, vUv.x * vUv.y);
			float alpha = map(vZ / 2., -1. / 2., 30. / 2., 0.17, 1.); 
			vec3 color = vec3(.5, .5, .6);

			gl_FragColor = vec4( color, alpha);
			gl_FragColor = gl_FragColor * texture2D( texture, gl_PointCoord );
		}
    </script>
<script src='js/three.min.js'></script>
<!-- <script  src="js/particles.js"></script> -->