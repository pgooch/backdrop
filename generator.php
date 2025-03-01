<?php
/*
	Before we do anything lets get the wordpress blog header, allowing us to call the options and whatnot. Then we can 
	load the options and merge that with the defaults (better to be safe, I know the default should be passed but I've 
	noticed that it doesn't always do it with my themes).
*/

// Get and merge the options with the defaults and any currently customized ones that might be in the session
if(isset($_GET['use_session']) && isset($_GET['nonce']) && wp_verify_nonce(wp_kses(wp_unslash($_GET['nonce']),[]), 'backdrop-live-session-preview')){
	session_start();
	$session = (isset($_SESSION['currently_customized_backdrop_options'])?array_map('sanitize_text_field',$_SESSION['currently_customized_backdrop_options']):array());
	$session['session_id'] = session_id();
}else{
	$session = array();
}

// Load the primary options
$options = get_option('backdrop',array());

// Get a true/false for if were in advanced mode (to make future checks simpler)
$_in_advanced = (get_option('backdrop-advanced','disabled')=='enabled'?true:false);

// If they you have advanced mode on chagne the ones that overrite non advanced over
if($_in_advanced){
	foreach(array('image-position-adv','image-size-adv','element-include-adv') as $key){
		if(isset($session[$key])){
			$session[substr($key,0,-4)]=$session[$key];
		}
		if(isset($options[$key])){
			$options[substr($key,0,-4)]=$options[$key];
		}
	}
}

// Merge all the toption arrays together
$options = array_merge(
	array(
		'color' 				=> '#fafafa',
		'color-opacity' 		=> 100,
		'image' 				=> '',
	    'image-size' 			=> 'auto',
	    'image-repeat'			=> 'repeat',
	    'image-position'    	=> 'center center',
	    'background-effect'		=> 'fixed',
	    'parallax-speed'		=> '33',
	    'move-speed'			=> 100,
	    'move-direction'		=> 'right none',
	    'element-include'		=> true,
	    'display-on'			=> 'all',
	    // These are used by the advanced mode options
	    'element'				=> '#backdrop-element',
	    'element-include-adv'	=> true,
	    'image-position-adv'	=> 'center center', // overridden above, just here to be complete.
	    'additional-styles' 	=> '',
	    'css-wrapper'			=> '',
	    // Not updated by user
		'last_update' 			=> -1,
	),
	$options,
	$session
);

// And finally lets send some headers out
$time = round($options['last_update']/1000);//Because timestamps in JS are not in seconds
header("Last-Modified: ".gmdate("D, d M Y H:i:s",$time)." GMT");
header('Cache-Control: public');

/*
	We are going to pre-process some of the values into things that work a little better for the CSS and JS parts.
*/

// Determine the element we want to apply this to (which only changed if your in advanced mode)
if($_in_advanced){
	$el = $options['element'];
}else{
	$el = '#backdrop-element';
}

// Chek the display on settings to see if we need to add something to the el.
if($options['display-on']!='all'){
	if($options['display-on'] == 'home'){
		$el = 'body.home '.$el;
	}else if($options['display-on'] == 'not-home'){
		$el = 'body:not(.home) '.$el;
	}
}

// Process the color, if thre is an opacity convert to RGBA, otherwise forget it. Because this can have an impact on performance it only will convert if the background is not normal
if($options['color-opacity']!=0 && $options['color-opacity']!=100){
	$r = hexdec(substr($options['color'],1,2));
	$g = hexdec(substr($options['color'],3,2));
	$b = hexdec(substr($options['color'],5,2));
	$a = ($options['color-opacity']/100);
	$options['color'] = 'rgba('.$r.','.$g.','.$b.','.$a.')';

// If there opacity is 0 just use the "transparent" keyword
}else if($options['color-opacity']==0){
	$options['color'] = 'transparent';
}

// Get the image size (used in CSS and JS)
$upload_dir = wp_upload_dir();
$check_file = explode('/',$options['image']);
$check_file = $upload_dir['path'].'/'.$check_file[count($check_file)-1];
if(is_file($check_file)){
	$image_size = getimagesize($check_file);
}else{
	$image_size = array(0,0); // Fake image size so it will still find the array values it wants
}

// Check if the image-size is retina, adjust as needed.
if($options['image-size']=='retina' && $options['image']!='' && $options['image']!='none'){
	$options['image-size'] = round($image_size[0]/2).'px '.round($image_size[1]/2).'px';
	// Also adjust the values in $image_size so that there correct when the JS gets them
	$image_size[0] = round($image_size[0]/2);
	$image_size[1] = round($image_size[1]/2);
}

// Check if we need to wrap the image in a url, or if it's just none
if($options['image']!=''){
	$options['image'] = 'url("'.$options['image'].'")';
}

/*
	If get set to css, generate css
*/
if(isset($_GET['generate']) && $_GET['generate']=='css'){

	// output the header
	header('Content-Type: text/css');

	// Check if we have a CSS wrap and if we do do that
	if($options['css-wrapper']!=''){
		echo esc_html($options['css-wrapper']).'{';
	}

	// Open the styles for the element
	echo esc_html($el).'{';

		// The background color, which has already been processed
		echo 'background-color:'.esc_html($options['color']).';';

		// Skip all the image released ones if the image is none
		if($options['image']!=''){

			// All the image related styles, should be pretty much 1:1 with options
			echo 'background-image:'.wp_kses($options['image'],[]).'!important;';
			echo 'background-size:'.esc_html($options['image-size']).'!important;';
			echo 'background-repeat:'.esc_html($options['image-repeat']).'!important;';
			echo 'background-position:'.esc_html($options['image-position']).';';

			// Background attachment is always false, we fake all scrolling
			echo 'background-attachment:fixed!important;';

			// we tell it to transform3d to... the same, this si so it's faster
			echo 'transform:translate3d(0,0,0);';

		}

		// And any additional styles they may have added
		echo esc_html($options['additional-styles']);

		if($options['css-wrapper']!=''){
			echo ' }';
		}

	// close the css tag
	echo '}';

	// The backdrop-element needs to be fixed to take the entire display, thre are no chaning parts to this css
	echo '#backdrop-element{position:fixed;top:0;right:0;bottom:0;left:0;z-index:-10;width:100%;height:100%;}';


/*
	If get set to js, generate js
*/
}else if(isset($_GET['generate']) && $_GET['generate']=='js'){

	// output the header
	header('Content-Type: text/javascript');

	// If the background effect is slide scroll then convert that into a slide
	if($options['background-effect']=='scroll'){
		$options['move-speed'] = 100;
		$options['move-direction'] = 'up none';
	}

	// Parallax, thats a slide too, best to keep it simple as possible
	if($options['background-effect']=='parallax'){
		$options['move-speed'] = $options['parallax-speed'];
		$options['move-direction'] = 'up none';
	}

	// Adjust the move speed to be something we can easily multiple the scroll position by
	$options['move-speed'] = $options['move-speed']/100;

	// Now we can build out our function
	echo 'function _backdropSlide(){';

		// We want to do this on every animation frame for the smoothest animation possible
		echo 'requestAnimationFrame(_backdropSlide);';

		// Get the image size
		echo 'var imgW='.esc_js($image_size[0]).';';
		echo 'var imgH='.esc_js($image_size[1]).';';

		// Get the scroll position
		echo 'var s=(document.documentElement.scrollTop)?document.documentElement.scrollTop:window.pageYOffset;';

		// Get move speed (as parallax regardless of wether or not your using parallax) 
		echo 'var p='.esc_js($options['move-speed']).';';

		// Now we loop through the elements
		echo 'var elements=document.querySelectorAll("'.esc_js($el).'");';
		echo 'for(var element in elements){';

			// Make an array to sore the position in, and grab the specific element we want
			echo 'var el=elements[element];';

			// if we are working with an object (and not one of the strings attached to an array like length)
			echo 'if(typeof el == "object"){';

				// If the size is but Automatic or Retina we need to do more math, now to determine the size the browser is going to make the image
				if($options['image-size']=='cover' || $options['image-size']!='contain' || $options['image-size']=='100% auto' || $options['image-size']!='auto 100%'){

					// First we will determin the maximum image width if the container width option is selected
					echo 'var fullWidthAdj=el.offsetWidth/imgW;';

					// And the same thing for the height
					echo 'var fullHeightAdj=el.offsetHeight/imgH;';

					// Now we switch through the options, adjusting the stored image sized (W/H) as needed for the method chosen
					switch($options['image-size']){
						case 'cover':
							echo 'var adj=Math.max(fullWidthAdj,fullHeightAdj);';
							echo 'imgW=imgW*adj;';
							echo 'imgH=imgH*adj;';
						break;
						case 'contain':
							echo 'var adj=Math.min(fullWidthAdj,fullHeightAdj);';
							echo 'imgW=imgW*adj;';
							echo 'imgH=imgH*adj;';
						break;
						case '100% auto':
							echo 'imgW=imgW*fullWidthAdj;';
							echo 'imgH=imgH*fullWidthAdj;';
						break;
						case 'auto 100%':
							echo 'imgW=imgW*fullHeightAdj;';
							echo 'imgH=imgH*fullHeightAdj;';
						break;
					}

				}
				// Some special cases can apply to image-size if your rnning in advanced more
				else if($_in_advanced){
					$img_size = explode(' ',$options['image-size']);

					// If you have something set to auto we have to do math, thanks a lot.
					if($img_size[0]=='auto' || $img_size[1]=='auto'){

						// Loop through both looking for the non-auto one, getting how big we need to adjust the other
						foreach($img_size as $n => $size){
							if($size!='auto'){
								echo esc_js('var fullAdj='.preg_replace('~[^0-9]+~','',$img_size[$n]).'/img'.($n==0?'W':'H').';');
								echo esc_js('img'.($n==0?'W':'H').'='.preg_replace('~[^0-9]+~','',$img_size[$n]).';');
							}
						}

						// Now loop backthrough and adjust the auto one
						foreach($img_size as $n => $size){
							if($size!=='auto'){
								echo 'img'.($n==1?'W':'H').'=parseInt(img'.($n==1?'W':'H').'*fullAdj);';
							}
						}


					// No math, no effort
					}else{
						echo esc_js('imgW='.preg_replace('~[^0-9]+~','',$img_size[0]).';');
						echo esc_js('imgH='.preg_replace('~[^0-9]+~','',$img_size[1]).';');
					}

				}

				// First we get the inital image position using the $image_size and maths
				$xy=explode(' ',$options['image-position']);

				foreach($xy as $n => $pos){
					switch(preg_replace('~[^a-z%]+~','',strtolower($pos))){
						case 'left':
							echo 'var imgPosX=0;';
						break;
						case 'center':
							echo 'var imgPos'.($n==1?'X':'Y').'=(el.offset'.($n==1?'Width':'Height').'/2)-(img'.($n==1?'W':'H').'/2);';
						break;
						case 'right':
							echo 'var imgPosX=el.offsetWidth-imgW;';
						break;
						case 'top':
							echo 'var imgPosY=0;';
						break;
						case 'bottom':
							echo 'var imgPosY=el.offsetHeight-imgH;';
						break;
						case 'px':
							echo esc_js('var imgPos'.($n==1?'X':'Y').'='.preg_replace('~[^0-9-]+~','',$pos).';');
						break;
						case '%':
							echo esc_js('var imgPos'.($n==1?'X':'Y').'=(el.offset'.($n==1?'Width':'Height').'*'.(preg_replace('~[^0-9-]+~','',$pos)/100).')-(img'.($n==1?'W':'H').'/2);');
						break;

					}
				}

				// Add the element offset
				echo  'imgPosX+=Math.ceil(el.getBoundingClientRect().left);';
				echo  'imgPosY+=Math.ceil(el.getBoundingClientRect().top);';

				// Now that we have the initla position lets put that in the array were'll be using for output
				echo 'var pos=[imgPosX+"px",imgPosY+"px"];';

				// The position isn't moved it it's set to fixed (however it is still positiion via JS so it works more logically in sub-elements)
				if($options['background-effect']!='fixed'){

					// Depending on the move directions selected modify the position array
					$move=explode(' ',$options['move-direction']);
					foreach($move as $n => $dir){
						switch($dir){
							case 'left':
								echo 'pos[0]=(imgPosX-(s*p))+"px";';
							break;
							case 'right':
								echo 'pos[0]=(imgPosX+(s*p))+"px";';
							break;
							case 'up':
								echo 'pos[1]=(imgPosY-(s*p))+"px";';
							break;
							case 'down':
								echo 'pos[1]=(imgPosY+(s*p))+"px";';
							break;
							default: // none
								echo 'pos['.($n==1?'0':'1').']=imgPos'.($n==1?'X':'Y').'+"px";';
							break;
						}
					}

				}

				// and update the style
				echo 'el.style.backgroundPosition = pos.join(" ");';

			echo '};';

		echo '};';

	echo '};';

	// Call it for the first time
	echo '_backdropSlide();';

/*
	If get set to to something else, or nothing at all, 404
*/
}else{
	header("HTTP/1.0 404 Not Found");
}

// Output the settings into a comment (if in advanced, no real need in normal mode)
if($_in_advanced){
	echo '/*';
	echo "\n".'Backdrop Settings ';
	foreach($options as $k => $v){
		echo "\n ".esc_html($k).': '.wp_kses($v,[]);
	}
	echo "\n".'*/';
}
