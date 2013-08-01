<?php
/*
Plugin Name: New Simple Gallery
Plugin URI: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
Description: Want to display images as an automatic slideshow that can also be explicitly played or paused by the user? then use this New Simple Gallery. <strong>In future back up your existing new simple gallery XML files before update this plugin.</strong> 
Author: Gopi.R
Version: 6.0
Author URI: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
Donate link: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function nsg_show() 
{
	$nsg_siteurl = get_option('siteurl');
	$nsg_pluginurl = $nsg_siteurl . "/wp-content/plugins/new-simple-gallery/";
	
	$nsg_width = get_option('nsg_width');
	
	$nsg_xml_file = get_option('nsg_xml_file');
	if($nsg_xml_file==""){$nsg_xml_file = "new-simple-gallery.xml";}
	
	$nsg_width = get_option('nsg_width');
	$nsg_height = get_option('nsg_height');
	$nsg_pause = get_option('nsg_pause');
	$nsg_duration = get_option('nsg_duration');
	$nsg_cycles = get_option('nsg_cycles');
	$nsg_displaydesc = get_option('nsg_displaydesc');
	
	if(!is_numeric($nsg_width)){$nsg_width = 200;} 
	if(!is_numeric($nsg_height)){$nsg_height = 150;} 
	if(!is_numeric($nsg_pause)){$nsg_pause = 2500;}
	if(!is_numeric($nsg_duration)){$nsg_duration = 500;}
	if(!is_numeric($nsg_cycles)){$nsg_cycles = 0;}
	
	$doc = new DOMDocument();
	$doc->load( $nsg_pluginurl . $nsg_xml_file );
	$images = $doc->getElementsByTagName( "image" );
	foreach( $images as $image )
	{
	  $paths = $image->getElementsByTagName( "path" );
	  $path = $paths->item(0)->nodeValue;
	  $targets = $image->getElementsByTagName( "target" );
	  $target = $targets->item(0)->nodeValue;
	  $titles = $image->getElementsByTagName( "title" );
	  $title = $titles->item(0)->nodeValue;
	  $links = $image->getElementsByTagName( "link" );
	  $link = $links->item(0)->nodeValue;
	  $nsg_package = @$nsg_package .'["'.$path.'", "'.$link.'", "'.$target.'", "'.$title.'"],';
	}
	$nsg_random = get_option('nsg_random');
	if($nsg_random==""){$nsg_random = "Y";}
	if($nsg_random=="Y")
	{
		$nsg_package = explode("[", $nsg_package);
		shuffle($nsg_package);
		$nsg_package = implode("[", $nsg_package);
		$nsg_package = '[' . $nsg_package;
		$nsg_package = explode("[[", $nsg_package);
		$nsg_package = implode("[", $nsg_package); // ugly hack to get rid of stray [[
	}
	
	$nsg_package = substr($nsg_package,0,(strlen($nsg_package)-1));
	?>
	<script type="text/javascript">
	var mygallery=new newsimplegallery({
		wrapperid: "nsggallerywidget", //ID of main gallery container,
		dimensions: [<?php echo $nsg_width; ?>, <?php echo $nsg_height; ?>], //width/height of gallery in pixels. Should reflect dimensions of the images exactly
		imagearray: [<?php echo $nsg_package; ?>],
		autoplay: [true, <?php echo $nsg_pause; ?>, <?php echo $nsg_cycles; ?>], //[auto_play_boolean, delay_btw_slide_millisec, cycles_before_stopping_int]
		persist: false, //remember last viewed slide and recall within same session?
		fadeduration: <?php echo $nsg_duration; ?>, //transition duration (milliseconds)
		oninit:function(){ //event that fires when gallery has initialized/ ready to run
			//Keyword "this": references current gallery instance (ie: try this.navigate("play/pause"))
		},
		onslide:function(curslide, i){ //event that fires after each slide is shown
			//Keyword "this": references current gallery instance
			//curslide: returns DOM reference to current slide's DIV (ie: try alert(curslide.innerHTML)
			//i: integer reflecting current image within collection being shown (0=1st image, 1=2nd etc)
		}
	})
	</script>
	<div id="nsggallerywidget"></div>
	<?php
}

//dd_filter('the_content','nsg_show_filter');

//function nsg_show_filter($content)
//{
	//return 	preg_replace_callback('/\[new-simple-gallery=(.*?)\]/sim','nsg_show_filter_Callback',$content);
//}

add_shortcode( 'new-simple-gallery', 'nsg_show_filter_shortcode' );

function nsg_show_filter_shortcode( $atts )
{
	//echo $matches[1];
	//$var = $matches[1];
	//parse_str($var, $output);
	
	//echo "--".$output['filename']."--";
	//$filename = $output['filename'];
	
	
	$nsg_pp = "";
	$nsg_package = "";
	//echo "--".$output['amp;width']."--";
	//echo "--".$output['width']."--";
	//$width = $output['amp;width'];
	//if($width==""){$width = $output['width'];}
	//if($width==""){$width = $output['width'];}
	//if(!is_numeric($width)){$width = 200;} 
	//echo "--".$output['width']."--";
	//echo $width;
	
	//$height = $output['amp;height'];
	//if($height==""){$height = $output['height'];}

	
	//[new-simple-gallery filename="new-simple-gallery.xml" width="400" height="300"]
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$filename = $atts['filename'];
	$width = $atts['width'];
	$height = $atts['height'];
	
	if($filename==""){$filename = "new-simple-gallery.xml";}
	if(!is_numeric($width)){$width = 200;} 
	if(!is_numeric($height)){$height = 200;} 
		
	$nsg_siteurl = get_option('siteurl');
	$nsg_pluginurl = $nsg_siteurl . "/wp-content/plugins/new-simple-gallery/";
	
	$nsg_width = $width;
	$nsg_height = $height;

	$nsg_pause = get_option('nsg_pause');
	$nsg_duration = get_option('nsg_duration');
	$nsg_cycles = get_option('nsg_cycles');
	$nsg_displaydesc = get_option('nsg_displaydesc');
	
	if(!is_numeric($nsg_pause)){$nsg_pause = 2500;}
	if(!is_numeric($nsg_duration)){$nsg_duration = 500;}
	if(!is_numeric($nsg_cycles)){$nsg_cycles = 0;}
	
	$doc = new DOMDocument();
	$doc->load( $nsg_pluginurl . $filename );
	$images = $doc->getElementsByTagName( "image" );
	foreach( $images as $image )
	{
	  $paths = $image->getElementsByTagName( "path" );
	  $path = $paths->item(0)->nodeValue;
	  $targets = $image->getElementsByTagName( "target" );
	  $target = $targets->item(0)->nodeValue;
	  $titles = $image->getElementsByTagName( "title" );
	  $title = $titles->item(0)->nodeValue;
	  $links = $image->getElementsByTagName( "link" );
	  $link = $links->item(0)->nodeValue;
	  $nsg_package = $nsg_package .'["'.$path.'", "'.$link.'", "'.$target.'", "'.$title.'"],';
	}
	$nsg_package = substr($nsg_package,0,(strlen($nsg_package)-1));
	
	$nsg_wrapperid = str_replace(".","_",$filename);
	$nsg_wrapperid = str_replace("-","_",$nsg_wrapperid);
	$nsg_pp = $nsg_pp . '<script type="text/javascript">';
	$nsg_pp = $nsg_pp . 'var mygallery=new newsimplegallery({wrapperid: "'.$nsg_wrapperid.'", dimensions: ['.$nsg_width.', '. $nsg_height.'], imagearray: ['. $nsg_package.'],autoplay: [true, "'.$nsg_pause.'", "'.$nsg_duration.'"],persist: false, fadeduration: "'.$nsg_duration.'",oninit:function(){},onslide:function(curslide, i){}})';
	$nsg_pp = $nsg_pp . '</script>';
	$nsg_pp = $nsg_pp . '<div style="padding-top:5px;"></div>';
	$nsg_pp = $nsg_pp . '<div id="'.$nsg_wrapperid.'"></div>';
	$nsg_pp = $nsg_pp . '<div style="padding-top:5px;"></div>';
	return $nsg_pp;
}

function nsg_install()
{
	add_option('nsg_xml_file', "new-simple-gallery.xml");
	add_option('nsg_random', "Y");
	add_option('nsg_title', "Slideshow");
	add_option('nsg_dir', "wp-content/plugins/new-simple-gallery/images/");
	add_option('nsg_width', "200");
	add_option('nsg_height', "200");
	add_option('nsg_pause', "2500");
	add_option('nsg_duration', "500");
	add_option('nsg_cycles', "2");
}

function nsg_widget($args)
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('nsg_title');
	echo $after_title;
	nsg_show();
	echo $after_widget;
}

function nsg_admin_option()
{
	?>
	<div class="wrap">
	  <div class="form-wrap">
		<div id="icon-edit" class="icon32 icon32-posts-post"><br>
		</div>
		<h2>New simple gallery</h2>
		<?php
		$nsg_xml_file = get_option('nsg_xml_file');
		$nsg_random = get_option('nsg_random');
		$nsg_title = get_option('nsg_title');
		$nsg_dir = get_option('nsg_dir');
		$nsg_width = get_option('nsg_width');
		$nsg_height = get_option('nsg_height');
		$nsg_pause = get_option('nsg_pause');
		$nsg_duration = get_option('nsg_duration');
		$nsg_cycles = get_option('nsg_cycles');
		
		if (@$_POST['nsg_submit']) 
		{
			$nsg_xml_file = stripslashes($_POST['nsg_xml_file']);
			$nsg_random = stripslashes($_POST['nsg_random']);
			$nsg_title = stripslashes($_POST['nsg_title']);
			$nsg_dir = stripslashes($_POST['nsg_dir']);
			$nsg_width = stripslashes($_POST['nsg_width']);
			$nsg_height = stripslashes($_POST['nsg_height']);
			$nsg_pause = stripslashes($_POST['nsg_pause']);
			$nsg_duration = stripslashes($_POST['nsg_duration']);
			$nsg_cycles = stripslashes($_POST['nsg_cycles']);
			
			update_option('nsg_xml_file', $nsg_xml_file );
			update_option('nsg_random', $nsg_random );
			update_option('nsg_title', $nsg_title );
			update_option('nsg_dir', $nsg_dir );
			update_option('nsg_width', $nsg_width );
			update_option('nsg_height', $nsg_height );
			update_option('nsg_pause', $nsg_pause );
			update_option('nsg_duration', $nsg_duration );
			update_option('nsg_cycles', $nsg_cycles );
		}
		?>
		<form name="nsg_form" method="post" action="">
		<h3>Gallery setting</h3>
		
		<label for="tag-title">XML File</label>
		<input name="nsg_xml_file" type="text" id="nsg_xml_file" size="75" value="<?php echo $nsg_xml_file; ?>" />
		<p>Enter name of the XML file. (Example: new-simple-gallery.xml)</p>
		
		<label for="tag-title">Random</label>
		<select name="nsg_random" id="nsg_random">
            <option value='Y' <?php if($nsg_random == 'Y') { echo 'selected' ; } ?>>Yes</option>
            <option value='N' <?php if($nsg_random == 'N') { echo 'selected' ; } ?>>No</option>
          </select>
		<p>Random image display.</p>
		
		<label for="tag-title">Title</label>
		<input name="nsg_title" type="text" id="nsg_title" value="<?php echo $nsg_title; ?>" size="40" />
		<p>Enter enter widget title, Only for widget.</p>
		
		<label for="tag-title">Width</label>
		<input name="nsg_width" type="text" id="nsg_width" value="<?php echo $nsg_width; ?>" maxlength="4" />
		<p>Enter width of the gallery, Only for width. (Example: 200)</p>
		
		<label for="tag-title">Height</label>
		<input name="nsg_height" type="text" id="nsg_height" value="<?php echo $nsg_height; ?>" maxlength="4" />
		<p>Enter height of the gallery, Only for height). (Example: 200)</p>
		
		<label for="tag-title">Pause</label>
		<input name="nsg_pause" type="text" id="nsg_pause" value="<?php echo $nsg_pause; ?>" maxlength="4" />
		<p>Pause between slides. (Example: 2500)</p>
		
		<label for="tag-title">Fade duration</label>
		<input name="nsg_duration" type="text" id="nsg_duration" value="<?php echo $nsg_duration; ?>" maxlength="4" />
		<p>The duration of the fade effect when transitioning from one image to the next, in milliseconds. (Example: 500)</p>
		
		<label for="tag-title">Cycles</label>
		<input name="nsg_cycles" type="text" id="nsg_cycles" value="<?php echo $nsg_cycles; ?>" maxlength="2" />
		<p>The cycles option when set to 0 will cause the slideshow to rotate perpetually,<br />while any number larger than 0 means it will stop after N cycles. (Example: 2)</p>
		<div style="height:10px;"></div>
		<input name="nsg_submit" id="nsg_submit" class="button add-new-h2" value="Submit" type="submit" />
		
		</form>
		</div>
		<h3>Plugin configuration option</h3>
		<ol>
			<li>Add the plugin in the posts or pages using short code.</li>
			<li>Add directly in to the theme using PHP code.</li>
			<li>Drag and drop the widget to your sidebar.</li>
		</ol>
	    <p class="description">Check official website for more information <a target="_blank" href="http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/">click here</a></p>
	</div>
	<?php
}

function nsg_control()
{
	echo '<p>To change the setting goto New Simple Gallery link under Setting menu.<br>';
	echo '<a href="options-general.php?page=new-simple-gallery">';
	echo 'Click here</a></p>';
}

function nsg_widget_init()
{
	if(function_exists('wp_register_sidebar_widget')) 	
	{
		wp_register_sidebar_widget('New Simple Gallery', 'New Simple Gallery', 'nsg_widget');
	}
	if(function_exists('wp_register_widget_control')) 	
	{
		wp_register_widget_control('New Simple Gallery', array('New Simple Gallery', 'widgets'), 'nsg_control');
	} 
}

function nsg_deactivation()
{
	// No action required.
}

function nsg_add_to_menu()
{
	add_options_page('New simple gallery','New simple gallery','manage_options', 'new-simple-gallery','nsg_admin_option');  
}

function nsg_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('new-simple-gallery', get_option('siteurl').'/wp-content/plugins/new-simple-gallery/new-simple-gallery.js');
	}	
}

if (is_admin())
{
	add_action('admin_menu', 'nsg_add_to_menu');
}

add_action('init', 'nsg_add_javascript_files');
add_action("plugins_loaded", "nsg_widget_init");
register_activation_hook(__FILE__, 'nsg_install');
register_deactivation_hook(__FILE__, 'nsg_deactivation');
add_action('init', 'nsg_widget_init');
?>