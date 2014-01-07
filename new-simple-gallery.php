<?php
/*
Plugin Name: New Simple Gallery
Plugin URI: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
Description: Want to display images as an automatic slideshow that can also be explicitly played or paused by the user? then use this New Simple Gallery. <strong>In future back up your existing new simple gallery XML files before update this plugin.</strong> 
Author: Gopi.R
Version: 6.1
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
	//$nsg_displaydesc = get_option('nsg_displaydesc');
	
	if(!is_numeric($nsg_width)){$nsg_width = 200;} 
	if(!is_numeric($nsg_height)){$nsg_height = 150;} 
	if(!is_numeric($nsg_pause)){$nsg_pause = 2500;}
	if(!is_numeric($nsg_duration)){$nsg_duration = 500;}
	if(!is_numeric($nsg_cycles)){$nsg_cycles = 0;}
	
	$xmldir = dirname(__FILE__);
	if (file_exists($xmldir. "\\" . $nsg_xml_file)) 
	{
		// No action required
	}
	else
	{
		echo "The file or folder does not exist<br>";
		echo $xmldir. "\\" . $nsg_xml_file;
		return true;
	}
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
		wrapperid: "nsggallerywidget",
		dimensions: [<?php echo $nsg_width; ?>, <?php echo $nsg_height; ?>],
		imagearray: [<?php echo $nsg_package; ?>],
		autoplay: [true, <?php echo $nsg_pause; ?>, <?php echo $nsg_cycles; ?>],
		persist: false,
		fadeduration: <?php echo $nsg_duration; ?>,
		oninit:function(){ 
		},
		onslide:function(curslide, i){
		}
	})
	</script>
	<div id="nsggallerywidget"></div>
	<?php
}

add_shortcode( 'new-simple-gallery', 'nsg_show_filter_shortcode' );

function nsg_show_filter_shortcode( $atts )
{
	$nsg_pp = "";
	$nsg_package = "";
	
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
	//$nsg_displaydesc = get_option('nsg_displaydesc');
	
	if(!is_numeric($nsg_pause)){$nsg_pause = 2500;}
	if(!is_numeric($nsg_duration)){$nsg_duration = 500;}
	if(!is_numeric($nsg_cycles)){$nsg_cycles = 0;}
	
	$xmldir = dirname(__FILE__);
	if (file_exists($xmldir. "\\" . $filename)) 
	{
		// No action required
	}
	else
	{
		$nsg_pp = $nsg_pp . "The file or folder does not exist<br>";
		$nsg_pp = $nsg_pp . $xmldir. "\\" . $filename;
		return $nsg_pp;
	}

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
		<h2><?php _e('New Simple Gallery', 'new-simple-gallery'); ?></h2>
		<?php
		$nsg_xml_file = get_option('nsg_xml_file');
		$nsg_random = get_option('nsg_random');
		$nsg_title = get_option('nsg_title');
		//$nsg_dir = get_option('nsg_dir');
		$nsg_width = get_option('nsg_width');
		$nsg_height = get_option('nsg_height');
		$nsg_pause = get_option('nsg_pause');
		$nsg_duration = get_option('nsg_duration');
		$nsg_cycles = get_option('nsg_cycles');
		
		if (isset($_POST['nsg_submit'])) 
		{
			$nsg_xml_file = stripslashes($_POST['nsg_xml_file']);
			$nsg_random = stripslashes($_POST['nsg_random']);
			$nsg_title = stripslashes($_POST['nsg_title']);
			//$nsg_dir = stripslashes($_POST['nsg_dir']);
			$nsg_width = stripslashes($_POST['nsg_width']);
			$nsg_height = stripslashes($_POST['nsg_height']);
			$nsg_pause = stripslashes($_POST['nsg_pause']);
			$nsg_duration = stripslashes($_POST['nsg_duration']);
			$nsg_cycles = stripslashes($_POST['nsg_cycles']);
			
			update_option('nsg_xml_file', $nsg_xml_file );
			update_option('nsg_random', $nsg_random );
			update_option('nsg_title', $nsg_title );
			//update_option('nsg_dir', $nsg_dir );
			update_option('nsg_width', $nsg_width );
			update_option('nsg_height', $nsg_height );
			update_option('nsg_pause', $nsg_pause );
			update_option('nsg_duration', $nsg_duration );
			update_option('nsg_cycles', $nsg_cycles );
		}
		?>
		<form name="nsg_form" method="post" action="">
		<h3><?php _e('Gallery setting', 'new-simple-gallery'); ?></h3>
		
		<label for="tag-title"><?php _e('XML File (For widget)', 'new-simple-gallery'); ?></label>
		<input name="nsg_xml_file" type="text" id="nsg_xml_file" size="75" value="<?php echo $nsg_xml_file; ?>" />
		<p><?php _e('Enter name of the XML file. XML file should available in the plugin directory.', 'new-simple-gallery'); ?> (Example: new-simple-gallery.xml)</p>
		
		<label for="tag-title"><?php _e('Random', 'new-simple-gallery'); ?></label>
		<select name="nsg_random" id="nsg_random">
            <option value='Y' <?php if($nsg_random == 'Y') { echo 'selected' ; } ?>>Yes</option>
            <option value='N' <?php if($nsg_random == 'N') { echo 'selected' ; } ?>>No</option>
          </select>
		<p><?php _e('Random image display.', 'new-simple-gallery'); ?></p>
		
		<label for="tag-title"><?php _e('Title (For widget)', 'new-simple-gallery'); ?></label>
		<input name="nsg_title" type="text" id="nsg_title" value="<?php echo $nsg_title; ?>" size="40" />
		<p><?php _e('Enter enter widget title, Only for widget.', 'new-simple-gallery'); ?></p>
		
		<label for="tag-title"><?php _e('Width (For widget)', 'new-simple-gallery'); ?></label>
		<input name="nsg_width" type="text" id="nsg_width" value="<?php echo $nsg_width; ?>" maxlength="4" />
		<p><?php _e('Enter width of the gallery, Only for width. (Example: 200)', 'new-simple-gallery'); ?></p>
		
		<label for="tag-title"><?php _e('Height (For widget)', 'new-simple-gallery'); ?></label>
		<input name="nsg_height" type="text" id="nsg_height" value="<?php echo $nsg_height; ?>" maxlength="4" />
		<p><?php _e('Enter height of the gallery, Only for height. (Example: 200)', 'new-simple-gallery'); ?></p>
		
		<label for="tag-title"><?php _e('Pause (Global setting)', 'new-simple-gallery'); ?></label>
		<input name="nsg_pause" type="text" id="nsg_pause" value="<?php echo $nsg_pause; ?>" maxlength="4" />
		<p><?php _e('Pause between slides. (Example: 2500)', 'new-simple-gallery'); ?></p>
		
		<label for="tag-title"><?php _e('Fade duration (Global setting)', 'new-simple-gallery'); ?></label>
		<input name="nsg_duration" type="text" id="nsg_duration" value="<?php echo $nsg_duration; ?>" maxlength="4" />
		<p><?php _e('The duration of the fade effect when transitioning from one image to the next, in milliseconds. (Example: 500)', 'new-simple-gallery'); ?></p>
		
		<label for="tag-title"><?php _e('Cycles (Global setting)', 'new-simple-gallery'); ?></label>
		<input name="nsg_cycles" type="text" id="nsg_cycles" value="<?php echo $nsg_cycles; ?>" maxlength="2" />
		<p><?php _e('The cycles option when set to 0 will cause the slideshow to rotate perpetually,<br />while any number larger than 0 means it will stop after N cycles. (Example: 2)', 'new-simple-gallery'); ?></p>
		<div style="height:10px;"></div>
		<input name="nsg_submit" id="nsg_submit" class="button add-new-h2" value="<?php _e('Submit', 'new-simple-gallery'); ?>" type="submit" />
		
		</form>
		</div>
		<h3><?php _e('Plugin configuration option', 'new-simple-gallery'); ?></h3>
		<ol>
			<li><?php _e('Add the plugin in the posts or pages using short code.', 'new-simple-gallery'); ?></li>
			<li><?php _e('Add directly in to the theme using PHP code.', 'new-simple-gallery'); ?></li>
			<li><?php _e('Drag and drop the widget to your sidebar.', 'new-simple-gallery'); ?></li>
		</ol>
	    <p class="description"><?php _e('Check official website for more information', 'new-simple-gallery'); ?> 
		<a target="_blank" href="http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/"><?php _e('click here', 'new-simple-gallery'); ?></a></p>
	</div>
	<?php
}

function nsg_control()
{
	echo '<p><b>';
	 _e('New Simple Gallery', 'new-simple-gallery');
	echo '.</b> ';
	_e('Check official website for more information', 'new-simple-gallery');
	?> <a target="_blank" href="http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/"><?php _e('click here', 'new-simple-gallery'); ?></a></p><?php
}

function nsg_widget_init()
{
	if(function_exists('wp_register_sidebar_widget')) 	
	{
		wp_register_sidebar_widget( __('New Simple Gallery','new-simple-gallery'), 'New Simple Gallery', 'nsg_widget');
	}
	if(function_exists('wp_register_widget_control')) 	
	{
		wp_register_widget_control( __('New Simple Gallery','new-simple-gallery'), array('New Simple Gallery', 'widgets'), 'nsg_control');
	} 
}

function nsg_deactivation()
{
	// No action required.
}

function nsg_add_to_menu()
{
	add_options_page(__('New Simple Gallery','new-simple-gallery'),
						__('New Simple Gallery','new-simple-gallery'), 'manage_options', 'new-simple-gallery', 'nsg_admin_option');  
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

function nsg_textdomain() 
{
	  load_plugin_textdomain( 'new-simple-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('plugins_loaded', 'nsg_textdomain');
add_action('init', 'nsg_add_javascript_files');
add_action("plugins_loaded", "nsg_widget_init");
register_activation_hook(__FILE__, 'nsg_install');
register_deactivation_hook(__FILE__, 'nsg_deactivation');
add_action('init', 'nsg_widget_init');
?>