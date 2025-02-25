<div id="icon-themes" class="icon32"><br></div><h2>Backdrop Help</h2>

<?php
/*
	Toggles the "Advanced Settings" option
*/
if( check_admin_referer( 'backdrop-help-page-setting ') ){
	if(isset($_POST['enable_advanced'])){
		update_option('backdrop-advanced','enabled');
		echo '<div id="message" class="updated"><p><strong>Advanced Settings have been Enabled.</strong></p></div>';
	}else if(isset($_POST['disable_advanced'])){
		update_option('backdrop-advanced','disabled');
		echo '<div id="message" class="updated"><p><strong>Advanced Settings have been Disabled.</strong></p></div>';
	}
}
/*
	Clears the settings
*/
if( check_admin_referer( 'backdrop-help-page-setting ') ){
	if(isset($_POST['clear_settings'])){
		delete_option('backdrop');
		echo '<div id="message" class="updated"><p><strong>Backdrop Settings Cleared.</strong></p></div>';
	}
}
?>

<p>
	This page describes the options of Backdrop, as well as any quirks or oddities that individual options may have. 
</p>

<table class="form-table">
	<tbody>

		<tr valign="top">
			<th scope="row">Color</th>
			<td>
				<p>
					This is the color of the background selected using the built in WordPress color picker. This color 
					will be applied to the backdrop element, beneath any image you use. This color will be used in 
					standard 6 digit hex, unless a color opacity is applied, in which case it will be converted into an 
					rgba() value.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Color Opacity</th>
			<td>
				<p>
					This is a percentage value, from 0 to 100, for the opacity of the background color. This opacity is 
					only applied to the background color. If the opacity is 100 the hex value given by the color picker 
					will be used. If the opacity is 0 then the "transparent" keyword will be used. For all other values 
					the color hex cod e will be converted into an rgba() color.
				</p>
				<p class="description">
					Some browsers have noticeable, sometimes severe, performance impacts when using semi-transparent 
					backgrounds. Keep this in mind if you choose to you a value other than 0 or 100.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Image</th>
			<td>
				<p>
					An image used by backdrop. This image can be further resized and positioned with the below controls.
				</p>
				<p class="description">
					Large images can have severe performance impacts on both page load and the smoothness of the 
					scrolling animation. Large images should be resized prior to upload.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Image Size</th>
			<td>
				<p>
					Adjusts the display size of the image, this does not change the actual image loaded. The available 
					options are:
				</p>
				<table class="form-sub-table">
					<tbody>
						<tr>
							<th scope="row">Automatic</th>
							<td>
								The image will be displayed at it's natural size.
							</td>
						</tr>

						<tr>
							<th scope="row">HiDPI scaled (1/2)</th>
							<td>
								The image will be displayed at half it's natural size, this will produce a sharper, 
								clearer image on high DPI devices like mobile phones and laptops with high resolutions 
								displays.
							</td>
						</tr>
						<tr>
							<th scope="row">Cover</th>
							<td>
								The image will be stretched proportionally to cover the entire screen, regardless of the 
								size of the original image.
							</td>
						</tr>
						<tr>
							<th scope="row">Contain</th>
							<td>
								The image will be stretched proportionally to be as large as possible while completely 
								fitting within the screen.
							</td>
						</tr>
						<tr>
							<th scope="row">Full Width</th>
							<td>
								The image will be stretched proportionally to be as wide as the screen.
							</td>
						</tr>
						<tr>
							<th scope="row">Full Height</th>
							<td>
								The image will be stretched proportionally to be as tall as the screen.
							</td>
						</tr>
					</tbody>
				</table>
				<p class="description">
					Because this does not resize the actual image file it is not a substitute for uploading a smaller 
					file. If you have having problems with studding while scrolling try uploading a smaller image.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Image Position</th>
			<td>
				<p>
					Where you would like the image to be placed. This is the starting position for all the effects that 
					may move the image around the page. Click the radio button in the position you would like the image. 
					With most themes it will not be possible to initially see the image when it is placed in the center 
					of the page, however it will animate out of the center as you scroll if you have set it to do so.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Image Repeat</th>
			<td>
				<p>
					How you would like the image to repeat or tile across the page. 
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Background Effect</th>
			<td>
				<p>
					What type of effect or animation you would like on the background image. The available effects are:
				</p>
				<table class="form-sub-table">
					<tbody>
						<tr>
							<th scope="row">Fixed</th>
							<td>
								The image will not move from it's initial position.
							</td>
						</tr>

						<tr>
							<th scope="row">Scroll</th>
							<td>
								The image will scroll like a normal background image with the default CSS background 
								attachment.
							</td>
						</tr>
						<tr>
							<th scope="row">Parallax</th>
							<td>
								Will scroll like a normal background image, although not usually at a 1:1 ratio 
								(although if the parallax speed is set to 100, it will have a 1:1 ratio). The speed of 
								this can be set with the Parallax Speed option, although something around 33 is 
								generally nice. More details about parallax speed are below.
							</td>
						</tr>
						<tr>
							<th scope="row">Slide</th>
							<td>
								The background will slide on the page with your scrolling. If the slide direction is set 
								to straight up it will have the same effect as scroll or parallax depending on slide 
								speed. More information about slide speed and direction can be found below.
							</td>
						</tr>
					</tbody>
				</table>
				<p class="description">
					All background effects except for "Fixed" require javascript to be active and only support IE 
					versions 10 and above, most other browser should work without any problems.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Parallax Speed</th>
			<td>
				<p class="description">
					Only visible when the "Parallax" Background Effect is selected.
				</p>
				<p>
					The speed at which the background moves relative to the scroll position. If this is set to 0 then it 
					will not move relative to the scroll position and it will effectively be fixed. If this is set to 100 
					then it will move at exactly the same speed as the scroll position and it will effectively be set to 
					scroll. Generally, a number around 30 produces a nice effect. Parallax speed can be any number, 
					positive or negative. Decimals are allowed, however images cannot be placed at sub-pixel levels in 
					browsers and so they will round it to the nearest pixel. Large numbers over 100 will produce a 
					studdering effect, this is because they are skipping pixels when being animated.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Slide Speed</th>
			<td>
				<p class="description">
					Only visible when the "Slide" Background Effect is selected.
				</p>
				<p>
					The speed at which the background slides relative to the scroll position. If this is set to 0 then it 
					will not move relative to the scroll position and it will effectively be fixed. If this is set to 100 
					then it will move at exactly the same speed as the user scrolls. This can be set to any number,
					positive or negative. Decimals are allowed, however images cannot be placed at sub-pixel levels in 
					browsers and so they will round it to the nearest pixel. Large numbers over 100 will produce a 
					studdering effect.
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Slide Direction</th>
			<td>
				<p class="description">
					Only visible when the "Slide" Background Effect is selected.
				</p>
				<p>
					The direction you want the image to slide. You can select from the 8 cardinal directions by clicking 
					the box at the direction you want the image to move. Clicking Directly up will produce the same 
					effect as Scroll or parallax, depending on the slide speed set. All sliding will take place from the 
					images initial point, set with the image position option.
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Pages That Have Backdrop</th>
			<td>
				<p class="description">
					Where will the backdrop display.
				</p>
				<p>
					Controls where the backdrop is displayed, whether you want it only on the homepage or want to disable 
					it on your homepage. Defaults to displaying on all pages.
				</p>
			</td>
		</tr>

	</tbody>
</table>

<h2 class="title">Advanced Settings</h2>
<p>
	Backdrop has some additional, more advanced options you can adjust. Sometimes when adjusting these options things 
	may behave in an unexpected manner. Support for the advanced settings is limited, however since they interface more 
	directly with the style sheet if you are familiar  with CSS you should have little trouble with it. You can enable or 
	disable advanced settings at any time with the button below.
</p>
<form method="post" action="">
	<?php wp_nonce_field( 'backdrop-help-page-setting' ); ?>
	<p class="submit">
		<?php if(get_option('backdrop-advanced','disabled')=='disabled'){
			echo '<input type="submit" name="enable_advanced" class="button button-secondary" value="Enable Advanced Settings" />';
		}else{
			echo '<input type="submit" name="disable_advanced" class="button button-secondary" value="Disable Advanced Settings" />';
		} ?>
	</p>
</form>
<p>
	At the top of the Backdrop panel there is a new message about how to view the generated style sheets. These links 
	generate the stylesheets and currently being used on your live site, not the ones being used to generate the preview 
	to the right of the customizer. This can be changed by adding <code>&use_session=true</code> to the URL. If you are 
	having in problems with Backdrop please include both your Generated CSS and Generated JS files when contating me so 
	I can better diagnose the problem.
</p>
<?php if(get_option('backdrop-advanced','disabled')!='disabled'){?>
	<table class="form-table">
		<tbody>

			<tr valign="top">
				<th scope="row">Applied To</th>
				<td>
					<p>
						The element you want the backdrop styles applied to. By default they are applied to <code>#backdrop-element</code>, 
						a div added into the page by the plugin. You can apply the styles and effects to multiple 
						elements if you'd like the same way you would apply a style to multiple elements. When the 
						javascript is run each element is grabbed with <code>document.querySelectorAll()</code>. 
						positioning is applied on an element by element basis, adjusting for the elements relative 
						position on the page with <code>getBoundingClientRect()</code>. Keep in mind that because the 
						position is calculated for each element selecting a large number of elements may have a 
						performance impact. Also, even though the initial background position is calculated on an 
						element by element basis the scroll position is determined at the window level, not an 
						individual scroll position that a given element may have.
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Include the #backdrop-element</th>
				<td>
					<p>
						IF you are not using the default backdrop element (a div with the id "backdrop-element") you can 
						opt to not have it inlcuded on your page. This element is a single empty div placed at the bottom 
						of the page and has no discernable impact on page loading, so leaving it on at all time should 
						not pose a problem. If you disable advnaced mode it will automatically be re-enabled.
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Image Position</th>
				<td>
					<p class="description">
						The image position input differs when in advanced mode returning to a standard text input. Your 
						advanced option is stored independently of the basic option.
					</p>
					<p>
						The starting position of the image on the page. <u>This must be a two value entry</u> (ie center 
						center) regardless of whether the CSS spec allows for a single entry value. The available options 
						are:
					</p>
					<table class="form-sub-table">
						<tbody>
							<tr>
								<th scope="row">top</th>
								<td>
									Set as 0px
								</td>
							</tr>

							<tr>
								<th scope="row">bottom</th>
								<td>
									Set as element height minus image height.
								</td>
							</tr>
							<tr>
								<th scope="row">left</th>
								<td>
									Set as 0px
								</td>
							</tr>
							<tr>
								<th scope="row">right</th>
								<td>
									Set as element width minus image width.
								</td>
							</tr>
							<tr>
								<th scope="row">center</th>
								<td>
									Set as 1/2 the element width or height minus 1/2 the images width or height.
								</td>
							</tr>
							<tr>
								<th scope="row"><em>###</em>px</th>
								<td>
									The position from the top left in pixels
								</td>
							</tr>
							<tr>
								<th scope="row"><em>###</em>%</th>
								<td>
									The position distance from the center of the image to the % distance from the top 
									left of the element. <span class="description">Note that this is different than the 
									stock CSS way to handle percentage positions. While standard CSS adjusts for the 
									image size Backdrop does not. This means that if you apple a position of 0% 0% only 
									the bottom right quarter of the image will be visible, as opposed tot he entire image.
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Image Size</th>
				<td>
					<p class="description">
						The image size input differs when in advanced mode returning to a standard text input. Your 
						advanced option is stored independently of the basic option. 
					</p>
					<p>
						The size of the image in the background. There are several pre-set values and the option to 
						apply a custom value in pixels. The possible values are: Like with image position <u>if you 
						are using a value that in not a keyword below then you must provide a two value entry</u> (ie 
						50px auto) regardless of whether the CSS spec allows for a single entry value. The available 
						options are:
					</p>
					<table class="form-sub-table">
						<tbody>
							<tr>
								<th scope="row">auto</th>
								<td>
									The image will not be resized.
								</td>
							</tr>
							<tr>
								<th scope="row">retina</th>
								<td>
									The image will be resized to 1.2 it's original size.
								</td>
							</tr>
							<tr>
								<th scope="row">cover</th>
								<td>
									Image is enlarged to cover the entire element.
								</td>
							</tr>
							<tr>
								<th scope="row">contain</th>
								<td>
									Image is enlarged to be as large as possible while fitting entirely withing the element.
								</td>
							</tr>
							<tr>
								<th scope="row">100% auto</th>
								<td>
									Image will be made the full width of the element.
								</td>
							</tr>
							<tr>
								<th scope="row">auto 100%</th>
								<td>
									Image will be made the full height of the element.
								</td>
							</tr>
						</tbody>
					</table>
					<p>
						If you are using a custom size both a width and height must be passed. <u>Only pixel values are 
						supported</u> at this time. If you only want to provide a width or height you may use the <code>auto</code> 
						keyword for the missing value.
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Media Query Wrapper</th>
				<td>
					<p>
						A media query wrapper that all other CSS rules generated by the plug-in will be placed in.
					</p>
					<p class="description">
						Curly brackets will be added automatically, the @media will not (to future proof for level 3 
						nesting when it becomes commonplace). This only effects the CSS and does nothing with the 
						generated JavaScript. 
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Additional Styles</th>
				<td>
					<p>
						Additional style attributes you would like to apply to the elements set in Applied To. These 
						will be added after any Backdrop styles allowing you to override them as desired.
					</p>
					<p class="description">
						Background position will be updated via JS directly at the element level, most likely 
						overwriting any position styles you add here.
					</p>
				</td>
			</tr>

		</tbody>
	</table>

	<p>
		Advanced settings have been tested, although not as thoroughly as the basic ones. If you experience an issue 
		when using the advanced settings please let me know and I will try to look into a solution. If you experience 
		any odd behavior when using the advanced settings you can reset to a basic stock installation with the button 
		below. This will not disable advanced mode, simply reset it. Advanced mode can be disabled using the same button 
		you use to enable it.
	</p>

	<form method="post" action="" id="clear-form">
		<?php wp_nonce_field( 'backdrop-help-page-setting' ); ?>
		<p class="submit">
			<input type="submit" name="clear_settings" class="button button-secondary" value="Clear Backdrop Settings" />
		</p>
	</form>

<?php } ?>

<h2 class="title">Miscellaneous</h2>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="nothing">Have a problem</label></th>
		<td>
			Having trouble getting the plug-in working? Expected results? Feel like direction your repentant rage at someone far far away? Just have a general usage question? You can try the <a href="http://wordpress.org/support/plugin/backdrop" target="_blank">plug-ins support form</a> or, if you want an answer from the source, feel free to email me at <a href="mailto:phillip.gooch@gmail.com" target="_blank">phillip.gooch@gmail.com</a>.
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="nothing">Check out the code</label></th>
		<td>
			Want to see how it all works, you can check out the on the <a href="http://plugins.svn.wordpress.org/backdrop/trunk/" target="_blank">WordPress SVN</a> or, even better, <a href="https://github.com/pgooch/backdrop" target="_blank">fork it on GitHub</a>. Feel free to make changes and submit pull requests, good ideas will be added to the master branch.
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Donate</th>
		<td>
			Like the plug-in and want to support further development? Thanks, thats just awesome! 
			Consider <a href="https://buymeacoffee.com/pgooch" target="_blank">buying me a coffee</a> to support further open source development or if you're looking to get some work done yourself <a href="mailto:phillip.gooch@gmail.com" target="_blank">get in touch</a> and we'll talk code.
		</td>
	</tr>
</table>

<style>
/* There is not enough that needs styling to really justify having a included CSS file */
.form-sub-table th {
	white-space: nowrap;
}
.form-sub-table th, .form-sub-table td{
	  padding: 5px 10px 5px 0px;
	  width:auto;
}
</style>
<script>
/* Same as the CSS, there is not enough that needs styling to really justify having a included JS file */
jQuery('form#clear-form').on('submit',function(e){
	if(!confirm("Are you sure you want to clear all backdrop settings? You can't undo this.")){
		e.preventDefault();
	}
});
</script>
