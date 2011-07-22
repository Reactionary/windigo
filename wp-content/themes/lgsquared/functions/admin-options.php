<?php

	// Theme Setting
	$themename 				= "My Framework";
	$shortname 				= "my_framework";
	
	
	
	
	// Options Page Functions

	function themeoptions_admin_menu() 
	{
		// here's where we add our theme options page link to the dashboard sidebar
		add_theme_page("Theme Options", "Theme Options", 'edit_themes', basename(__FILE__), 'themeoptions_page');
	}
	
	function themeoptions_page() 
	{
		// here's the main function that will generate our options page
		
		if ( $_POST['update_themeoptions'] == 'true' ) { themeoptions_update(); }
		
		//if ( get_option() == 'checked'
		
		?>
		<div class="wrap">
			<div id="icon-themes" class="icon32"><br /></div>
			<h2>Theme Options</h2>
		
			<form method="POST" action="">
				<input type="hidden" name="update_themeoptions" value="true" />
        
        <h3>Logo Image</h3>
				<p><input type="text" name="logoimage" id="logoimage" size="60" value="<?php echo get_option('mytheme_logoimage'); ?>"/> Path to Logo Image</p>
        <h3>Favicon Image</h3>
        <p><input type="text" name="favicon" id="favicon" size="60" value="<?php echo get_option('mytheme_favicon'); ?>"/> Path to Favicon</p>
        <h3 style="padding-top:30px;">Slider settings</h3>
        <h5 style="margin-bottom:7px;">Slider effects</h5>
        <select name="effect">
					<?php $effect = get_option('mytheme_effect'); ?>
          <option value="random" <?php if ($effect=='random') { echo 'selected'; } ?> >random</option>
          <option value="fold" <?php if ($effect=='fold') { echo 'selected'; } ?>>fold</option>
          <option value="fade" <?php if ($effect=='fade') { echo 'selected'; } ?>>fade</option>
          <option value="sliceDown" <?php if ($effect=='sliceDown') { echo 'selected'; } ?>>sliceDown</option>
          <option value="sliceDownLeft" <?php if ($effect=='sliceDownLeft') { echo 'selected'; } ?>>sliceDownLeft</option>
          <option value="sliceUp" <?php if ($effect=='sliceUp') { echo 'selected'; } ?>>sliceUp</option>
          <option value="sliceUpLeft" <?php if ($effect=='sliceUpLeft') { echo 'selected'; } ?>>sliceUpLeft</option>
          <option value="sliceUpDown" <?php if ($effect=='sliceUpDown') { echo 'selected'; } ?>>sliceUpDown</option>
          <option value="sliceUpDownLeft" <?php if ($effect=='sliceUpDownLeft') { echo 'selected'; } ?>>sliceUpDownLeft</option>
          <option value="slideInRight" <?php if ($effect=='slideInRight') { echo 'selected'; } ?>>slideInRight</option>
          <option value="slideInLeft" <?php if ($effect=='slideInLeft') { echo 'selected'; } ?>>slideInLeft</option>
        </select> specify effect parameter
        <p><input type="text" name="slices" id="slices" size="4" value="<?php echo get_option('mytheme_slices'); ?>"/> number of slices</p>
        <p><input type="text" name="animSpeed" id="animSpeed" size="4" value="<?php echo get_option('mytheme_animSpeed'); ?>"/> slide transition speed (ms)</p>
        <p><input type="text" name="pauseTime" id="pauseTime" size="4" value="<?php echo get_option('mytheme_pauseTime'); ?>"/> time between slide animation (ms)</p>
        <p><input type="text" name="startSlide" id="startSlide" size="4" value="<?php echo get_option('mytheme_startSlide'); ?>"/> set starting Slide (0 index)</p>
				
        <h5 style="margin-bottom:7px; padding-top:12px;">Slider navigation</h5>
        <p><select name="directionNav">
					<?php $directionNav = get_option('mytheme_directionNav'); ?>
          <option value="false" <?php if ($directionNav=='false') { echo 'selected'; } ?> >false</option>
          <option value="true" <?php if ($directionNav=='true') { echo 'selected'; } ?>>true</option>
        </select> show navigation (Prev &amp; Next)</p>
        <p><select name="directionNavHide">
					<?php $directionNavHide = get_option('mytheme_directionNavHide'); ?>
          <option value="false" <?php if ($directionNavHide=='false') { echo 'selected'; } ?> >false</option>
          <option value="true" <?php if ($directionNavHide=='true') { echo 'selected'; } ?>>true</option>
        </select> false - always visible, true - only on hover</p>
        <p><select name="controlNav">
					<?php $controlNav = get_option('mytheme_controlNav'); ?>
          <option value="false" <?php if ($controlNav=='false') { echo 'selected'; } ?> >false</option>
          <option value="true" <?php if ($controlNav=='true') { echo 'selected'; } ?>>true</option>
        </select> show pagination</p>
        <p><select name="controlNavThumbs">
					<?php $controlNavThumbs = get_option('mytheme_controlNavThumbs'); ?>
          <option value="false" <?php if ($controlNavThumbs=='false') { echo 'selected'; } ?> >false</option>
          <option value="true" <?php if ($controlNavThumbs=='true') { echo 'selected'; } ?>>true</option>
        </select> use thumbnails for Control Nav</p>
        
        <h5 style="margin-bottom:7px; padding-top:12px;">Misc</h5>
        <p><select name="keyboardNav">
					<?php $keyboardNav = get_option('mytheme_keyboardNav'); ?>
          <option value="false" <?php if ($keyboardNav=='false') { echo 'selected'; } ?> >false</option>
          <option value="true" <?php if ($keyboardNav=='true') { echo 'selected'; } ?>>true</option>
        </select> use keyboard navigation</p>
        <p><select name="pauseOnHover">
					<?php $pauseOnHover = get_option('mytheme_pauseOnHover'); ?>
          <option value="false" <?php if ($pauseOnHover=='false') { echo 'selected'; } ?> >false</option>
          <option value="true" <?php if ($pauseOnHover=='true') { echo 'selected'; } ?>>true</option>
        </select> stop animation while hovering</p>
        <p><input type="text" name="captionOpacity" id="captionOpacity" size="4" value="<?php echo get_option('mytheme_captionOpacity'); ?>"/> universal caption opacity (0.8 by default)</p>
				
				
				<p><input type="submit" name="search" value="Update Options" class="button" /></p>
			</form>
		
		</div>
		<?php
	}
	
	function themeoptions_update()
	{
		// this is where validation would go
		
		update_option('mytheme_effect', 	$_POST['effect']);
		update_option('mytheme_animSpeed', 	$_POST['animSpeed']);
		update_option('mytheme_pauseTime', 	$_POST['pauseTime']);
		update_option('mytheme_startSlide', 	$_POST['startSlide']);
		update_option('mytheme_slices', 	$_POST['slices']);
		
		update_option('mytheme_directionNav', 	$_POST['directionNav']);
		update_option('mytheme_directionNavHide', 	$_POST['directionNavHide']);
		update_option('mytheme_controlNav', 	$_POST['controlNav']);
		update_option('mytheme_controlNavThumbs', 	$_POST['controlNavThumbs']);
		
		update_option('mytheme_keyboardNav', 	$_POST['keyboardNav']);
		update_option('mytheme_pauseOnHover', 	$_POST['pauseOnHover']);
		update_option('mytheme_captionOpacity', 	$_POST['captionOpacity']);
		
		
		update_option('mytheme_logoimage', 	$_POST['logoimage']);
		update_option('mytheme_favicon', 	$_POST['favicon']);
		
	
	}
	
	add_action('admin_menu', 'themeoptions_admin_menu');

?>
