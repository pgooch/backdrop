<?php
/*
Plugin Name: Backdrop
Plugin URI: http://fatfolderdesign.com/wordpress-plugins
Description: Backdrop is an improved site background customizer allowing for all manner of fancy things.
Version: 2.3.5
Author: Phillip Gooch
Author URI: https://pgooch.com
License: GNU General Public License v2
*/

class backdrop {

	public function __construct(){
		// Hook into the customizer action
		add_action('customize_register',array(&$this,'customizer'));
		add_action('wp_enqueue_scripts',array(&$this,'enque'));
		add_action('wp_footer',			array(&$this,'element'));
		// Add the help page
		add_action('admin_menu',		array(&$this,'help'));
		add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(&$this,'plugin_page_help_link'));
		// Filtering for the custom generator
		add_action('parse_request',     array(&$this,'catch_generator_requests'));
	}

	/*
		We don't actually do anything on activate, but we don't want there to be an error output about it
	*/
	static function __activate(){}

	/*
		This will populate the customizer with the new section and options.
	*/
	public function customizer($wp_customize){

		// Everything for Backdrop will go into 1 section for convience.
		$wp_customize->add_section('backdrop',array(
			'title'        => __( 'Backdrop','backdrop' ),
			'description'	=> 'For more details on the options below read the <a href="themes.php?page=backdrop-help" target="_blank">Backdrop Help</a> Page. <i>Note: Animations appear more sluggish in the preview than on the live site. Complex themes may be slow to refresh background.</i>'.(get_option('backdrop-advanced','disabled')=='enabled'?'<br/><br/>View the <a href="/backdrop.css?/backdrop.css?use_session=true" target="_blank">Generated CSS</a> or <a href="/backdrop.css?/backdrop.js?use_session=true" target="_blank">Generated JS</a> files as used in the preview with their corresponding settings.<br/><br/>':''),
			'priority'     => 1,
		) );

		// ADV: Element that backdrop will be applied to
		if(get_option('backdrop-advanced','disabled')=='enabled'){
			$wp_customize->add_setting('backdrop[element]', array(
				'default'        	=> '#backdrop-element',
				'capability'     	=> 'edit_theme_options',
				'type'           	=> 'option',
				'sanitize_callback' => 'sanitize_fake',
			));
			$wp_customize->add_control('background_element', array(
				'label'      => __('Applied To','backdrop'),
				'section'    => 'backdrop',
				'settings'   => 'backdrop[element]',
				'type'       => 'text',
			));
		}

		// ADV: To show the backdrop element or to not show, that is the question
		if(get_option('backdrop-advanced','disabled')=='enabled'){
			$wp_customize->add_setting('backdrop[element-include-adv]', array(
				'default'        	=> true,
				'capability'     	=> 'edit_theme_options',
				'type'           	=> 'option',
				'sanitize_callback' => 'sanitize_fake',
			));
			$wp_customize->add_control('background_element_include', array(
				'label'      => __('Include the #backdrop-element','backdrop'),
				'section'    => 'backdrop',
				'settings'   => 'backdrop[element-include-adv]',
				'type'       => 'checkbox',
			));
		}

		// Color & Color Opacity
		$wp_customize->add_setting('backdrop[color]', array(
			'default'           => '#FAFAFA',
			'sanitize_callback' => 'sanitize_hex_color',
			'capability'        => 'edit_theme_options',
			'type'           	=> 'option',
		));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize,'color',array(
			'label'    => __('Color','backdrop'),
			'section'  => 'backdrop',
			'settings' => 'backdrop[color]',
		)));
		$wp_customize->add_setting('backdrop[color-opacity]', array(
			'default'           => '100',
			'sanitize_callback' => 'sanitize_percentage',
			'capability'        => 'edit_theme_options',
			'type'           	=> 'option',
		));
		$wp_customize->add_control(new backdrop_custom_percentage_control($wp_customize,'color-opacity',array(
			'label'     => __('Color Opacity','backdrop'),
			'section'   => 'backdrop',
			'settings'  => 'backdrop[color-opacity]',
			'type' 		=> 'percentage'
		)));

		// Image
		$wp_customize->add_setting('backdrop[image]', array(
			'default'    		=> '',
			'capability' 		=> 'edit_theme_options',
			'type'       		=> 'option',
			'sanitize_callback' => 'sanitize_file',
		));
		$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'image', array(
			'label'    => __('Image','backdrop'),
			'section'  => 'backdrop',
			'settings' => 'backdrop[image]',
		)));

		// ADV: Image Size
		if(get_option('backdrop-advanced','disabled')=='enabled'){
			$wp_customize->add_setting('backdrop[image-size-adv]', array(
				'default'        	=> 'auto',
				'capability'     	=> 'edit_theme_options',
				'type'           	=> 'option',
				'sanitize_callback' => 'sanitize_fake',
			));
			$wp_customize->add_control('background_image-size', array(
				'label'      => __('Image Size','backdrop'),
				'section'    => 'backdrop',
				'settings'   => 'backdrop[image-size-adv]',
				'type'       => 'text',
			));

		// Image Size
		}else{
			$wp_customize->add_setting('backdrop[image-size]', array(
				'default'        	=> 'auto',
				'capability'     	=> 'edit_theme_options',
				'type'           	=> 'option',
				'sanitize_callback' => 'sanitize_fake',
			));
			$wp_customize->add_control('image-size', array(
				'label'      => __('Image Size','backdrop'),
				'section'    => 'backdrop',
				'settings'   => 'backdrop[image-size]',
				'type'       => 'select',
				'choices'    => array(
					'auto' 		=> 'Automatic',
					'retina'	=> 'HiDPI scaled (1/2)',
					'cover' 	=> 'Cover',
					'contain' 	=> 'Contain',
					'100% auto' => 'Container Width',
					'auto 100%'	=> 'Container Height',
				),
			));
		}

		// ADV: Image Position
		if(get_option('backdrop-advanced','disabled')=='enabled'){
			$wp_customize->add_setting('backdrop[image-position-adv]', array(
				'default'        	=> 'center center',
				'capability'     	=> 'edit_theme_options',
				'type'           	=> 'option',
				'sanitize_callback' => 'sanitize_fake',
			));
			$wp_customize->add_control('background_image-position', array(
				'label'      => __('Image Position','backdrop'),
				'section'    => 'backdrop',
				'settings'   => 'backdrop[image-position-adv]',
				'type'       => 'text',
			));

		// Image Position (Both) (standard)
		}else{
			$wp_customize->add_setting('backdrop[image-position]', array(
				'default'           => 'center center',
				'sanitize_callback' => 'sanitize_percentage',
				'capability'        => 'edit_theme_options',
				'type'           	=> 'option',
			));
			$wp_customize->add_control(new backdrop_custom_image_position_control($wp_customize,'image-position',array(
				'label'     => __('Image Position','backdrop'),
				'section'   => 'backdrop',
				'settings'  => 'backdrop[image-position]',
				'type' 		=> 'image-position'
			)));
		}


		// Image Repeat
		$wp_customize->add_setting('backdrop[image-repeat]', array(
			'default'        	=> 'repeat',
			'capability'     	=> 'edit_theme_options',
			'type'           	=> 'option',
			'sanitize_callback' => 'sanitize_fake',
		));
		$wp_customize->add_control('image-repeat', array(
			'label'      => __('Image Repeat','backdrop'),
			'section'    => 'backdrop',
			'settings'   => 'backdrop[image-repeat]',
			'type'       => 'select',
			'choices'    => array(
				'repeat' 	=> 'Both Directions',
				'repeat-x'	=> 'Horizontally Only',
				'repeat-y' 	=> 'Vertically Only',
				'no-repeat'	=> 'No Repeat',
			),
		));

		// Background Effect
		$wp_customize->add_setting('backdrop[background-effect]', array(
			'default'        	=> 'fixed',
			'capability'     	=> 'edit_theme_options',
			'type'           	=> 'option',
			'sanitize_callback' => 'sanitize_fake',
		));
		$wp_customize->add_control('background-effect', array(
			'label'      => __('Background Effect','backdrop'),
			'section'    => 'backdrop',
			'settings'   => 'backdrop[background-effect]',
			'type'       => 'select',
			'choices'    => array(
				'fixed' 	=> 'Fixed',
				'scroll'	=> 'Scroll',
				// 'local'		=> 'Local', //!\\ Another potential support headache, again, if you understand how CSS and backgrounds are really attached feel free to uncomment it and use it.
				'parallax' 	=> 'Parallax',
				'slide'		=> 'Slide',
			),
		));

		// Parallax Speed
		$wp_customize->add_setting('backdrop[parallax-speed]', array(
			'default'           => '33',
			'sanitize_callback' => 'sanitize_speed',
			'capability'        => 'edit_theme_options',
			'type'           	=> 'option',
		));
		$wp_customize->add_control(new backdrop_custom_speed_control($wp_customize,'parallax-speed',array(
			'label'     => __('Parallax Speed','backdrop'),
			'section'   => 'backdrop',
			'settings'  => 'backdrop[parallax-speed]',
			'type' 		=> 'speed'
		)));

		// Slide Speed
		$wp_customize->add_setting('backdrop[move-speed]', array(
			'default'           => '100',
			'sanitize_callback' => 'sanitize_speed',
			'capability'        => 'edit_theme_options',
			'type'           	=> 'option',
		));
		$wp_customize->add_control(new backdrop_custom_speed_control($wp_customize,'move-speed',array(
			'label'     => __('Slide Speed','backdrop'),
			'section'   => 'backdrop',
			'settings'  => 'backdrop[move-speed]',
			'type' 		=> 'speed'
		)));

		// Slide Direction
		$wp_customize->add_setting('backdrop[move-direction]', array(
			'default'           => 'none right',
			'sanitize_callback' => 'sanitize_fake',
			'capability'        => 'edit_theme_options',
			'type'           	=> 'option',
		));
		$wp_customize->add_control(new backdrop_custom_move_direction_control($wp_customize,'move-direction',array(
			'label'     => __('Slide Direction','backdrop'),
			'section'   => 'backdrop',
			'settings'  => 'backdrop[move-direction]',
			'type' 		=> 'move-direction'
		)));

		// Display On Page
		$wp_customize->add_setting('backdrop[display-on]', array(
			'default'           => 'all',
			'sanitize_callback' => 'sanitize_fake',
			'capability'        => 'edit_theme_options',
			'type'           	=> 'option',
		));
		$wp_customize->add_control('display-on',array(
			'label'     => __('Pages That Have Backdrop ','backdrop'),
			'section'   => 'backdrop',
			'settings'  => 'backdrop[display-on]',
			'type'       => 'select',
			'choices'    => array(
				'home'		=> 'Homepage Only',
				'all' 		=> 'All Pages',
				'not-home'	=> 'All Pages Except Homepage',
			),
		));

		// Selector Wrapper
		if(get_option('backdrop-advanced','disabled')=='enabled'){
			$wp_customize->add_setting('backdrop[css-wrapper]', array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_fake',
				'capability'        => 'edit_theme_options',
				'type'           	=> 'option',
			));
			$wp_customize->add_control(new backdrop_custom_css_wrapper_control($wp_customize,'css-wrapper',array(
				'label'     => __('Media Query Wrapper','backdrop'),
				'section'   => 'backdrop',
				'settings'  => 'backdrop[css-wrapper]',
				'type' 		=> 'css_wrapper'
			)));

		}

		// Additional Styles
		if(get_option('backdrop-advanced','disabled')=='enabled'){
			$wp_customize->add_setting('backdrop[additional-styles]', array(
				'default'        	=> '',
				'capability'     	=> 'edit_theme_options',
				'type'           	=> 'option',
				'sanitize_callback' => 'sanitize_text',
			));
			$wp_customize->add_control('backdrop_additional_styles', array(
				'label'     => __('Additional Styles','backdrop'),
				'section'   => 'backdrop',
				'settings'  => 'backdrop[additional-styles]',
				'type' 		=> 'textarea'
			));
		}

		// Hidden Time catching input
		$wp_customize->add_setting('backdrop[last_update]', array(
			'default'           => '0',
			'sanitize_callback' => 'sanitize_fake',
			'capability'        => 'edit_theme_options',
			'type'           	=> 'option',
		));
		$wp_customize->add_control(new backdrop_custom_last_update_control($wp_customize,'last_update',array(
			'label'     => __('Last Update','backdrop'),
			'section'   => 'backdrop',
			'settings'  => 'backdrop[last_update]',
			'type' 		=> 'last_update',
		)));
	}

	/*
		This function will enque both the style and the script. We have to do something special if your in the 
		customizer and store the values that get_option is giving us into a session varible so the generator can load 
		it. Would be nicer if we didn't have to and the generator could just grab the temp values but I couldent find 
		any API access to do that.
	*/
	public function enque(){

		// The URL modifier for if we should use the session stored data
		$use_session = '';

		// Get the options (specifically for the last update time)
		$options = get_option('backdrop');

		// Check for the last update time and use it as a version number or all back
		$ver = $options['last_update'];
		if( is_null($ver) ){
			$ver = time();
		}

		// Just in case someone has sessions disabled on a server level (it might solve a problem or two).
		if(is_callable('session_start')){

			// If we are in the customize preview we need to tell the generator to use what we put in the session, otherwise we can pass on that
			if(is_customize_preview()){

				// Before we create a session lets make sure ones not already going on
				if(!isset($_SESSION)){
					session_start();
				}

				// Update the session and the $use_options string
				$_SESSION['currently_customized_backdrop_options'] = get_option('backdrop');
				$use_session = '?use_session=true&nonce='.wp_create_nonce('backdrop-live-session-preview');
				$options['last_update'] = preg_replace('~[^0-9]+~','',microtime());

			}

		}

		// Enque the script and style
		wp_enqueue_script('Backdrop Scripts','/backdrop.js'.$use_session,array(),$ver,'all');
		wp_enqueue_style('Backdrop Styles','/backdrop.css'.$use_session,array(),$ver,'all');
	}

	/*
		This will add an element to the footer, this element will then be streached to cover the entire page, and given 
		a z-index below the content (but above the body). This is used for all backdrops in 2.0 (and was used for all 
		the interesteing ones in 1.X).
	*/
	public function element(){

		// Get the options (specifically for the last update time)
		$options = get_option('backdrop',array('element-include'=>true,'element-include-adv'=>true));

		// If were in advanced mode change the adv to the normal
		if(get_option('backdrop-advanced','disabled')=='enabled'){
			$options['element-include'] = true;//$options['element-include-adv'];
		}

		// If were are going to show it show it.
		if(isset($options['element-include'])===false || $options['element-include']===true){
			echo '<div id="backdrop-element"></div>';
		}

	}

	/*
		This will watch and check the page requests and if it's the backdrop css or js file it'll deal with that instead 
		of letting wordpress do it's thing.
	*/
	public function catch_generator_requests($query){
		if( $query->request === 'backdrop.css' ){
			$_GET['generate'] = 'css';
			require 'generator.php';
			exit;
		}else if( $query->request === 'backdrop.js' ){
			$_GET['generate'] = 'js';
			require 'generator.php';
			exit;
		}
		return $query;
	}

	/*
		The help page functions
	*/
	public function help(){
		// Lets add a new (sub) menu item;
		add_submenu_page(null,'Backdrop','Backdrop','edit_theme_options','backdrop-help',array(&$this,'help_page'));
	}
	public function help_page(){
		// Loads the other page, it's much simpler this way.
		require_once('help.php');
	}
	public function plugin_page_help_link($links){
		$links[] = '<a href="./themes.php?page=backdrop-help">Help</a>';
		return $links;
	}
}
// The final peice(s) of the puzzle.
$backdrop=new backdrop();
register_activation_hook(__FILE__,array('backdrop','__activate'));

/*
	These are sanitizers for the WP_customize inputs
*/
if(!function_exists('sanitize_fake')){
	function sanitize_fake($value){
		// Seems sanitize_option now returns a object, which, you know, not expected. This should be fine for dropdowns.
		return $value;
	}
}
if(!function_exists('sanitize_percentage')){
	function sanitize_percentage($value){
		if($value>=0 && $value<=100){
			return $value;
		}else{
			return 100;
		}
	}
}
if(!function_exists('sanitize_speed')){
	function sanitize_speed($value){
		return preg_replace('~[^0-9-\.]+~','',$value);
	}
}
if(!function_exists('sanitize_file')){
	function sanitize_file($file){
		// Repath it so we know where to check
		$upload_dir = wp_upload_dir();
		$check_file = explode('/',$file);
		$check_file = $upload_dir['path'].'/'.$check_file[count($check_file)-1];
		// Check, return as needed
		if(is_file($check_file)){
			return $file;
		}else{
			return '';
		}
	}
}

/*
	These are additional controllers that are called in the customizer
*/
function backdrop_add_custom_controllers($wp_customize){

	/*
		A Percentage controller, a number between 0 and 100
	*/
	class backdrop_custom_percentage_control extends WP_Customize_Control {
		public $type = 'percentage';
		public function render_content(){ ?>
			<label>
				<span class="customize-control-title"><?php echo esc_html($this->label) ?></span>
				<input type="number" min="0" max="100" <?php $this->link() ?> value="<?php echo esc_attr($this->value()) ?>" />
			</label>
		<?php }
	}

	/*
		A Speed controller, this is just a num without a min or max and with a note
	*/
	class backdrop_custom_speed_control extends WP_Customize_Control {
		public $type = 'speed';
		public function render_content(){ ?>
			<label>
				<span class="customize-control-title"><?php echo esc_html($this->label) ?></span>
				<small>0 is stationary, 100 is 1:1 with scroll speed.</small>
				<input type="number" <?php $this->link() ?> value="<?php echo esc_attr($this->value()) ?>" />
			</label>
		<?php }
	}

	/*
		A CSS Wrapper controller, this is just a text with a note
	*/
	class backdrop_custom_css_wrapper_control extends WP_Customize_Control {
		public $type = 'css_wrapper';
		public function render_content(){ ?>
			<label>
				<span class="customize-control-title"><?php echo esc_html($this->label) ?></span>
				<small>Will wrap CSS rules, ads curly brackets.</small>
				<input type="text" <?php $this->link() ?> value="<?php echo esc_attr($this->value()) ?>" />
			</label>
		<?php }
	}

	/*
		A Image Position controller, has a grid of 9 radio buttons, click where you want it to show. Values are for CSS background-position.
	*/
	class backdrop_custom_image_position_control extends WP_Customize_Control {
		public $type = 'image-position';
		public function render_content(){ ?>
			<label for="image-position-default">
				<span class="customize-control-title"><?php echo esc_html($this->label) ?></span>
				<input type="radio" name="image-position" <?php $this->link() ?> value="top left" /><input type="radio" name="image-position" <?php $this->link() ?> value="top center" /><input type="radio" name="image-position" <?php $this->link() ?> value="top right" /><br/>
				<input type="radio" name="image-position" <?php $this->link() ?> value="center left" /><input type="radio" id="image-position-default" name="image-position" <?php $this->link() ?> value="center center" /><input type="radio" name="image-position" <?php $this->link() ?> value="center right" /><br/>
				<input type="radio" name="image-position" <?php $this->link() ?> value="bottom left" /><input type="radio" name="image-position" <?php $this->link() ?> value="bottom center" /><input type="radio" name="image-position" <?php $this->link() ?> value="bottom right" />
			</label>
		<?php }
	}

	/*
		A Move Direction controller, same 9 grid as position, but the middle is invisible and disabled.
	*/
	class backdrop_custom_move_direction_control extends WP_Customize_Control {
		public $type = 'move-direction';
		public function render_content(){ ?>
			<label for="image-position-default">
				<span class="customize-control-title"><?php echo esc_html($this->label) ?></span>
				<input type="radio" name="move-direction" <?php $this->link() ?> value="up left" /><input type="radio" name="move-direction" <?php $this->link() ?> value="up none" /><input type="radio" name="move-direction" <?php $this->link() ?> value="up right" /><br/>
				<input type="radio" name="move-direction" <?php $this->link() ?> value="none left" /><input type="radio" name="move-direction" <?php $this->link() ?> value="none none" disabled style="opacity:0;"/><input type="radio" name="move-direction" id="move-direction-default" <?php $this->link() ?> value="none right" /><br/>
				<input type="radio" name="move-direction" <?php $this->link() ?> value="down left" /><input type="radio" name="move-direction" <?php $this->link() ?> value="down none" /><input type="radio" name="move-direction" <?php $this->link() ?> value="down right" />
			</label>
		<?php }
	}

	/*
		A "last update" controller, grabs the time when the save button sees mouedown.
		*** BONUS *** Also has the JS to make sections show and hide (it's noted below, you can rip that part out)
		*** EXTRA BONUS *** Also got some styles in there to make things a tad more compact and clean.
	*/
	class backdrop_custom_last_update_control extends WP_Customize_Control {
		public $type = 'last_update';
		public function render_content(){ ?>
			<input type="text" id="last_update" <?php $this->link() ?> value="<?php echo esc_attr($this->value()) ?>" style="display:none;"/>
			<script>
				jQuery('html').on('mousedown','input[type="submit"][name="save"]',function(){
					jQuery('input#last_update').val(new Date().getTime()).trigger('keyup');
				});
				/*
					This is what controlls toggle effects for the inputs that get hidden/shown in specific situations.
				*/
				// This is for the custom selector input, only seen when type is custom
				jQuery('html').on('change','[data-customize-setting-link="backdrop[type]"]',function(){
					if(jQuery(this).val()=='custom'){
						jQuery('[data-customize-setting-link="backdrop[custom_selector]"]').closest('li').show();
					}else{
						jQuery('[data-customize-setting-link="backdrop[custom_selector]"]').closest('li').hide();
					}
					if(jQuery(this).val()=='normal'){
						jQuery('[data-customize-setting-link="backdrop[opacity]"]').closest('li').hide();
					}else{
						jQuery('[data-customize-setting-link="backdrop[opacity]"]').closest('li').show();
					}
				});
				// This is for the Background Effect and whcih details need to show
				jQuery('html').on('change','[data-customize-setting-link="backdrop[background-effect]"]',function(){
					if(jQuery(this).val()=='parallax'){
						jQuery('[data-customize-setting-link="backdrop[parallax-speed]"]').closest('li').show();
					}else{
						jQuery('[data-customize-setting-link="backdrop[parallax-speed]"]').closest('li').hide();
					}
					if(jQuery(this).val()=='slide'){
						jQuery('[data-customize-setting-link="backdrop[move-speed]"]').closest('li').show();
						jQuery('[data-customize-setting-link="backdrop[move-direction]"]').closest('li').show();
					}else{
						jQuery('[data-customize-setting-link="backdrop[move-speed]"]').closest('li').hide();
						jQuery('[data-customize-setting-link="backdrop[move-direction]"]').closest('li').hide();
					}
				});

				// This triggers all the... triggers... when you click on the Backddrop li
				jQuery('html').on('mousedown','li#accordion-section-backdrop',function(){
					jQuery('[data-customize-setting-link="backdrop[type]"]').trigger('change');
					jQuery('[data-customize-setting-link="backdrop[background-effect]"]').trigger('change');
				})
			</script>
			<style>
			#accordion-section-backdrop span.customize-control-title{
				margin-bottom: -3px;
			}
			#accordion-section-backdrop small{
				display: block;
				margin-top: -6px;
			}
			#accordion-section-backdrop .customize-control-image .actions{
				margin-bottom:3px;
			}
			</style>
		<?php }
	}
}
add_action('customize_register','backdrop_add_custom_controllers',0);
