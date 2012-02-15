<?php
/*
Plugin Name: New Simple Gallery
Plugin URI: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
Description: Want to display images as an automatic slideshow that can also be explicitly played or paused by the user? then use this New Simple Gallery. <strong>In future back up your existing new simple gallery XML files before update this plugin.</strong> 
Author: Gopi.R
Version: 3.0
Author URI: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
Donate link: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
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
	  $nsg_package = $nsg_package .'["'.$path.'", "'.$link.'", "'.$target.'", "'.$title.'"],';
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
	<script type="text/javascript" src="<?php echo $nsg_pluginurl; ?>/new-simple-gallery.js"></script>
	<script type="text/javascript" src="<?php echo $nsg_pluginurl; ?>/jquery-1.2.6.pack.js"></script>
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


add_filter('the_content','nsg_show_filter');

function nsg_show_filter($content){
	return 	preg_replace_callback('/\[new-simple-gallery=(.*?)\]/sim','nsg_show_filter_Callback',$content);
}

function nsg_show_filter_Callback($matches) 
{
	//echo $matches[1];
	$var = $matches[1];
	parse_str($var, $output);
	
	//echo "--".$output['filename']."--";
	$filename = $output['filename'];
	if($filename==""){$filename = "new-simple-gallery.xml";}
	
	//echo "--".$output['amp;width']."--";
	//echo "--".$output['width']."--";
	$width = $output['amp;width'];
	if($width==""){$width = $output['width'];}
	if($width==""){$width = $output['width'];}
	if(!is_numeric($width)){$width = 200;} 
	//echo "--".$output['width']."--";
	//echo $width;
	
	$height = $output['amp;height'];
	if($height==""){$height = $output['height'];}
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
	$nsg_pp = $nsg_pp . '<script type="text/javascript" src="'. $nsg_pluginurl.'new-simple-gallery.js"></script>';
	$nsg_pp = $nsg_pp . '<script type="text/javascript" src="'.$nsg_pluginurl.'jquery-1.2.6.pack.js"></script>';
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
	echo "<div class='wrap'>";
	echo "<h2>"; 
	echo wp_specialchars( "New Simple Gallery" ) ;
	echo "</h2>";
    
	$nsg_xml_file = get_option('nsg_xml_file');
	$nsg_random = get_option('nsg_random');
	$nsg_title = get_option('nsg_title');
	$nsg_dir = get_option('nsg_dir');
	$nsg_width = get_option('nsg_width');
	$nsg_height = get_option('nsg_height');
	$nsg_pause = get_option('nsg_pause');
	$nsg_duration = get_option('nsg_duration');
	$nsg_cycles = get_option('nsg_cycles');
	
	if ($_POST['nsg_submit']) 
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
	<table width="100%" border="0" cellspacing="0" cellpadding="3"><tr><td align="left">
	<?php
	echo '<p>XML File:<br><input  style="width: 200px;" maxlength="500" type="text" value="';
	echo $nsg_xml_file . '" name="nsg_xml_file" id="nsg_xml_file" /><br>(Enter Name of the XML file)</p>';

	echo '<p>Random:<br><input  style="width: 100px;" maxlength="1" type="text" value="';
	echo $nsg_random . '" name="nsg_random" id="nsg_random" />(Y/N)</p>';
	
	echo '<p>Title:<br><input  style="width: 200px;" maxlength="200" type="text" value="';
	echo $nsg_title . '" name="nsg_title" id="nsg_title" /></p>';
	
	echo '<p>Width:<br><input  style="width: 100px;" maxlength="4" type="text" value="';
	echo $nsg_width . '" name="nsg_width" id="nsg_width" />Only Number, This not for page and post gallery.</p>';
	
	echo '<p>Height:<br><input  style="width: 100px;" maxlength="4" type="text" value="';
	echo $nsg_height . '" name="nsg_height" id="nsg_height" />Only Number, This not for page and post gallery.</p>';
	
	echo '<p>Pause:<br><input maxlength="4" style="width: 100px;" type="text" value="';
	echo $nsg_pause . '" name="nsg_pause" id="nsg_pause" />Only Number<br>';
	echo 'Pause between slides</p>';
	
	echo '<p>Fade Duration:<br><input maxlength="4" style="width: 100px;" type="text" value="';
	echo $nsg_duration . '" name="nsg_duration" id="nsg_duration" />Only Number<br>';
	echo 'The duration of the fade effect when transitioning from one image to the next, in milliseconds.</p>';
	
	echo '<p>Cycles:<br><input maxlength="1" style="width: 100px;" type="text" value="';
	echo $nsg_cycles . '" name="nsg_cycles" id="nsg_cycles" />Only Number<br>';
	echo 'The cycles option when set to 0 will cause the slideshow to rotate perpetually,';
	echo 'while any number larger than 0 means it will stop after N cycles.</p>';

	echo '<input name="nsg_submit" id="nsg_submit" class="button-primary" value="Submit" type="submit" />';
	?>
	</td><td align="left" valign="top">  </td></tr></table>
	</form>
	<h2><?php echo wp_specialchars( 'We can use this plug-in in three different way.!' ); ?></h2>
	1.	Go to widget menu and drag and drop the "New Simple Gallery" widget to your sidebar location. or <br /><br />
	2.	Copy and past the below mentioned code to your desired template location.<br /><br />
	&lt;?php if (function_exists (nsg_show)) nsg_show(); ?&gt; <br /><br />
	3.	Past the given code to post or page.<br /><br />
	[new-simple-gallery=filename=600x400.xml&width=600&height=400]
	<br /><br />
	<span style="color: #FF0000;font-weight: bold;">In future back up your existing new simple gallery XML files before update this plugin.</span>
	<h2><?php echo wp_specialchars( 'About Plugin!' ); ?></h2>
    Plug-in created by <a target="_blank" href='http://www.gopiplus.com/work/'>Gopi</a>.<br />
	<a target="_blank" href='http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/'>Click here</a> to see more information.<br />
    <a target="_blank" href='http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/'>Click here</a> to post suggestion or comments or feedback.<br />
    <a target="_blank" href='http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/'>Click here</a> to see live demo & more info.<br />
    <a target="_blank" href='http://www.gopiplus.com/work/wordpress-plugin-download/'>Click here</a> to download my other plugins.<br />
	<br />
	<?php
	echo "</div>";
}

function nsg_control()
{
	echo '<p>To change the setting goto New Simple Gallery link under Setting menu.<br>';
	echo '<a href="options-general.php?page=new-simple-gallery/new-simple-gallery.php">';
	echo 'Click here</a></p>';
}

function nsg_widget_init() 
{
  	register_sidebar_widget(__('New Simple Gallery'), 'nsg_widget');   
	if(function_exists('register_sidebar_widget')) 	
	{
		register_sidebar_widget('New Simple Gallery', 'nsg_widget');
	}
	if(function_exists('register_widget_control')) 	
	{
		register_widget_control(array('New Simple Gallery', 'widgets'), 'nsg_control');
	} 
}

function nsg_deactivation() 
{

}

function nsg_add_to_menu() 
{
	add_options_page('New Simple Gallery', 'New Simple Gallery', 7, __FILE__, 'nsg_admin_option' );
}

add_action('admin_menu', 'nsg_add_to_menu');
add_action("plugins_loaded", "nsg_widget_init");
register_activation_hook(__FILE__, 'nsg_install');
register_deactivation_hook(__FILE__, 'nsg_deactivation');
add_action('init', 'nsg_widget_init');
?>
