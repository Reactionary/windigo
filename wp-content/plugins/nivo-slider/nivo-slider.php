<?php
/*
Plugin Name: Nivo Slider WordPress Plugin
Plugin URI: http://nivo.dev7studios.com/wordpress
Description: The official WordPress plugin for the <a href="http://nivo.dev7studios.com">Nivo Slider</a>.
Version: 0.9
Author: Gilbert Pellegrom
Author URI: http://gilbert.pellegrom.me
*/

$wordpress_nivo_slider = new WordpressNivoSlider();

class WordpressNivoSlider {

    function __construct() {	
        add_action('init', array(&$this, 'init'));
        add_filter('post_updated_messages', array(&$this, 'updated_messages'));
        add_action('manage_edit-nivoslider_columns', array(&$this, 'edit_columns'));
        add_action('manage_posts_custom_column', array(&$this, 'custom_columns'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_print_styles', array(&$this, 'admin_print_styles'));
        add_action('admin_print_scripts', array(&$this, 'admin_print_scripts'));
        add_action('save_post', array(&$this, 'save_post'));
        add_action('wp_ajax_nivoslider_load_images', array(&$this, 'load_images'));
        add_action('wp_ajax_nivoslider_upload', array(&$this, 'upload_image'));
        add_action('wp_ajax_nivoslider_load_meta', array(&$this, 'load_image_meta'));
        add_action('wp_ajax_nivoslider_edit', array(&$this, 'edit_image'));
        add_action('wp_ajax_nivoslider_remove', array(&$this, 'remove_image'));
        add_action('wp_ajax_nivoslider_order_save', array(&$this, 'save_order'));
        add_action('wp_print_styles', array(&$this, 'print_styles'));
        add_action('wp_print_scripts', array(&$this, 'print_scripts'));
        add_shortcode('nivoslider', array(&$this, 'shortcode'));
        
        load_plugin_textdomain( 'nivo-slider', false, dirname( plugin_basename( __FILE__ ) ) .'/lang/' );
	}
    
    function init() {
        $labels = array(
            'name' => _x( 'Nivo Slider', 'post type general name' ),
            'singular_name' => _x( 'Nivo Slider', 'post type singular name' ),
            'add_new' => __( 'Add New', 'nivo-slider' ),
            'add_new_item' => __( 'Add New Slider', 'nivo-slider' ),
            'edit_item' => __( 'Edit Slider', 'nivo-slider' ),
            'new_item' => __( 'New Slider', 'nivo-slider' ),
            'view_item' => __( 'View Slider', 'nivo-slider' ),
            'search_items' => __( 'Search Sliders', 'nivo-slider' ),
            'not_found' =>  __( 'No Sliders found', 'nivo-slider' ),
            'not_found_in_trash' => __( 'No Sliders found in Trash', 'nivo-slider' ), 
            'parent_item_colon' => ''
        );
        
        register_post_type(
            'nivoslider',
            array(
                'labels' => $labels,
                'public' => true,
                'show_ui' => true,
                'menu_position' => 100,
                'supports' => array(
                    'title'
                )
            )
        );
        
        if( current_user_can('edit_posts') && current_user_can('edit_pages') && get_user_option('rich_editing') == 'true' ){  
            add_filter('mce_external_plugins', array(&$this, 'mce_add_plugin'));  
            add_filter('mce_buttons_2', array(&$this, 'mce_register_button'));  
        }  
    }
    
    function updated_messages( $messages ) {
        global $post, $post_ID;

        $messages['nivoslider'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __('Slider updated.', 'nivo-slider'),
            2 => __('Custom field updated.', 'nivo-slider'),
            3 => __('Custom field deleted.', 'nivo-slider'),
            4 => __('Slider updated.', 'nivo-slider'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf( __('Slider restored to revision from %s', 'nivo-slider'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => __('Slider published.', 'nivo-slider'),
            7 => __('Slider saved.', 'nivo-slider'),
            8 => __('Slider submitted.', 'nivo-slider'),
            9 => sprintf( __('Slider scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview book</a>', 'nivo-slider'),
                // translators: Publish box date format, see http://php.net/date
                date_i18n( __( 'M j, Y @ G:i', 'nivo-slider' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
            10 => __('Slider draft updated.', 'nivo-slider')
        );

        return $messages;
    }
    
    function edit_columns( $columns ) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Title', 'nivo-slider' ),
            'shortcode' => __( 'Shortcode', 'nivo-slider' ),
            'author' => __( 'Author', 'nivo-slider' ),
            'images' => __( 'Images', 'nivo-slider' ),
            'date' => __( 'Date', 'nivo-slider' )
        );

        return $columns;
    }

    function custom_columns( $column ) {
        global $post;
        switch ( $column )
        {
            case 'images':     
                $limit = 5;
                if(isset($_GET['mode']) && $_GET['mode'] == 'excerpt') $limit = 20;
                $images = $this->get_slider_images( $post->ID, array(32, 32), $limit );
                if ( $images ) {
                    echo '<ul class="nivoslider-thumbs">';
                    foreach( $images as $image ){
                        echo '<li><img src="'. $image['image_src'] .'" alt="" style="width:32px;height:32px;" /></li>';
                    }
                    echo '</ul>'; 
                }
                break;
            case 'shortcode':  
                echo '<code>[nivoslider id="'. $post->ID .'"]</code>';
                if($post->post_name != '') echo '<br /><code>[nivoslider slug="'. $post->post_name .'"]</code>';
                break;
        }
    }
    
    function admin_init() {
        add_meta_box( 'nivoslider_upload_box', __( 'Upload Images', 'nivo-slider' ), array(&$this, 'meta_box_upload'), 'nivoslider', 'normal' );
        add_meta_box( 'nivoslider_settings_box', __( 'Settings', 'nivo-slider' ), array(&$this, 'meta_box_settings'), 'nivoslider', 'normal' );
        add_meta_box( 'nivoslider_shortcode_box', __( 'Using this Slider', 'nivo-slider' ), array(&$this, 'meta_box_shortcode'), 'nivoslider', 'side' );
    }
    
    function admin_print_styles() {
        global $post;

        if(isset($post->post_type) && $post->post_type == 'nivoslider'){
            wp_enqueue_style( 'nivoslider-fileupload-css', WP_PLUGIN_URL .'/nivo-slider/scripts/uploader/fileuploader.css' );
            wp_enqueue_style( 'nivoslider-admin-css', WP_PLUGIN_URL .'/nivo-slider/styles/nivo-admin.css' );
        }
    }
    
    function admin_print_scripts() {
        global $post;

        if(isset($post->post_type) && $post->post_type == 'nivoslider'){
            wp_register_script( 'nivoslider-fileupload', WP_PLUGIN_URL .'/nivo-slider/scripts/uploader/fileuploader.js' );
            wp_enqueue_script( 'nivoslider-fileupload' ); 
            wp_register_script( 'jquery-simplemodal', WP_PLUGIN_URL .'/nivo-slider/scripts/jquery.simplemodal.1.4.1.min.js', array('jquery') );
            wp_enqueue_script( 'jquery-simplemodal' );
            wp_register_script( 'nivoslider-admin-js', WP_PLUGIN_URL .'/nivo-slider/scripts/nivo-admin.js', array('jquery') );
            wp_enqueue_script( 'nivoslider-admin-js' );
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-sortable');
        }
        
        // Sliders list for TinyMCE dropdown
        $sliders = get_posts( array('post_type' => 'nivoslider', 'posts_per_page' => -1) );
        $list = array();
        foreach( $sliders as $slider ){
            $list[] = array(
                'id' => $slider->ID, 
                'name' => $slider->post_title
            );
        }
        wp_localize_script( 'jquery', 'NivoSlider', array('sliders' => json_encode($list)) );
    }

    function meta_box_settings() {
        global $post;
        $options = get_post_meta( $post->ID, 'nivo_settings', true );
    
        wp_nonce_field( plugin_basename( __FILE__ ), 'nivoslider_noncename' );
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Slider Type', 'nivo-slider'); ?></th>
                <td><select name="nivo_settings[type]">
                    <?php $slider_type = $this->default_val($options, 'type', 'manual'); ?>
                    <option value="manual"<?php if($slider_type == 'manual') echo ' selected="selected"'; ?>><?php _e('Manual', 'nivo-slider'); ?></option>
                    <option value="category"<?php if($slider_type == 'category') echo ' selected="selected"'; ?>><?php _e('Category', 'nivo-slider'); ?></option>
                    <option value="sticky"<?php if($slider_type == 'sticky') echo ' selected="selected"'; ?>><?php _e('Sticky Posts', 'nivo-slider'); ?></option>
                </select><br />
                <span class="description"><?php _e('Choose to manually upload images or use post thumbnails', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top" id="nivo_type_category">
                <th scope="row">- <?php _e('Category', 'nivo-slider'); ?></th>
                <td><select name="nivo_settings[type_category]">
                    <?php 
                    $categories = get_categories();
                    foreach($categories as $category){
                        echo '<option value="'. $category->cat_ID .'"';
                        if($this->default_val($options, 'type_category') == $category->cat_ID) echo ' selected="selected"';
                        echo '>'. $category->name .'</option>';
                    }
                    ?>
                </select><br />
                <span class="description"><?php _e('Select the category you want to use for post thumbnails', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top" id="nivo_type_captions">
                <th scope="row">- <?php _e('Enable Captions', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[enable_captions]" value="off" />
                <input type="checkbox" name="nivo_settings[enable_captions]" value="on"<?php if($this->default_val($options, 'enable_captions', 'on') == 'on') echo 'checked="checked"'; ?>/><br />
                <span class="description"><?php _e('Enable automatic captions from post titles', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Slider Size', 'nivo-slider'); ?></th>
                <td><input type="text" name="nivo_settings[dim_x]" value="<?php echo $this->default_val($options, 'dim_x', 400); ?>" /> x 
                <input type="text" name="nivo_settings[dim_y]" value="<?php echo $this->default_val($options, 'dim_y', 150); ?>" /><br />
                <span class="description"><?php _e('(Size in px) Images will be cropped to these dimensions (eg 400 x 150)', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Slider Theme', 'nivo-slider'); ?></th>
                <td><select name="nivo_settings[theme]">
                    <option value="">None</option>
                    <?php 
                    $slider_theme = $this->default_val($options, 'theme'); 
                    $themes = $this->get_themes();
                    foreach($themes as $theme){
                        echo '<option value="'. $theme['theme_name'] .'"';
                        if($slider_theme == $theme['theme_name']) echo ' selected="selected"'; 
                        echo '>'. $theme['theme_details']['SkinName'] .'</option>';
                    }
                    ?>
                </select><br />
                <span class="description"><?php _e('Use a pre-built theme or provide your own styles. <a href="http://nivo.dev7studios.com/theme-demos/" target="_blank">See theme demos</a>', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Transition Effect', 'nivo-slider'); ?></th>
                <td><select name="nivo_settings[effect]">
                    <?php $transition_effect = $this->default_val($options, 'effect', 'random'); ?>
                    <option value="random"<?php if($transition_effect == 'random') echo ' selected="selected"'; ?>>Random</option>
                    <option value="fade"<?php if($transition_effect == 'fade') echo ' selected="selected"'; ?>>Fade</option>
                    <option value="fold"<?php if($transition_effect == 'fold') echo ' selected="selected"'; ?>>Fold</option>
                    <option value="sliceDown"<?php if($transition_effect == 'sliceDown') echo ' selected="selected"'; ?>>Slice Down</option>
                    <option value="sliceDownLeft"<?php if($transition_effect == 'sliceDownLeft') echo ' selected="selected"'; ?>>Slice Down (Left)</option>
                    <option value="sliceUp"<?php if($transition_effect == 'sliceUp') echo ' selected="selected"'; ?>>Slice Up</option>
                    <option value="sliceUpLeft"<?php if($transition_effect == 'sliceUpLeft') echo ' selected="selected"'; ?>>Slice Up (Left)</option>
                    <option value="sliceUpDown"<?php if($transition_effect == 'sliceUpDown') echo ' selected="selected"'; ?>>Slice Up/Down</option>
                    <option value="sliceUpDownLeft"<?php if($transition_effect == 'sliceUpDownLeft') echo ' selected="selected"'; ?>>Slice Up/Down (Left)</option>
                    <option value="slideInRight"<?php if($transition_effect == 'slideInRight') echo ' selected="selected"'; ?>>Slide In (Right)</option>
                    <option value="slideInLeft"<?php if($transition_effect == 'slideInLeft') echo ' selected="selected"'; ?>>Slide In (Left)</option>
                    <option value="boxRandom"<?php if($transition_effect == 'boxRandom') echo ' selected="selected"'; ?>>Box Random</option>
                    <option value="boxRain"<?php if($transition_effect == 'boxRain') echo ' selected="selected"'; ?>>Box Rain</option>
                    <option value="boxRainReverse"<?php if($transition_effect == 'boxRainReverse') echo ' selected="selected"'; ?>>Box Rain (Reverse)</option>
                    <option value="boxRainGrow"<?php if($transition_effect == 'boxRainGrow') echo ' selected="selected"'; ?>>Box Rain Grow</option>
                    <option value="boxRainGrowReverse"<?php if($transition_effect == 'boxRainGrowReverse') echo ' selected="selected"'; ?>>Box Rain Grow (Reverse)</option>
                </select></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Slices', 'nivo-slider'); ?></th>
                <td><input type="text" name="nivo_settings[slices]" value="<?php echo $this->default_val($options, 'slices', 15); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('The number of slices to use in the "Slice" transitions (eg 15)', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Box (Cols x Rows)', 'nivo-slider'); ?></th>
                <td><input type="text" name="nivo_settings[boxCols]" value="<?php echo $this->default_val($options, 'boxCols', 8); ?>" /> x 
                <input type="text" name="nivo_settings[boxRows]" value="<?php echo $this->default_val($options, 'boxRows', 4); ?>" /><br />
                <span class="description"><?php _e('The number of columns and rows to use in the "Box" transitions (eg 8 x 4)', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Animation Speed', 'nivo-slider'); ?></th>
                <td><input type="text" name="nivo_settings[animSpeed]" value="<?php echo $this->default_val($options, 'animSpeed', 500); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('The speed of the transition animation in milliseconds (eg 500)', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Enable Thumbnail Navigation', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[controlNavThumbs]" value="off" />
                <input type="checkbox" name="nivo_settings[controlNavThumbs]" value="on"<?php if($this->default_val($options, 'controlNavThumbs', 'off') == 'on') echo 'checked="checked"'; ?>/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Thumbnail Size', 'nivo-slider'); ?></th>
                <td><input type="text" name="nivo_settings[thumbSizeWidth]" value="<?php echo $this->default_val($options, 'thumbSizeWidth', 70); ?>" /> x 
                <input type="text" name="nivo_settings[thumbSizeHeight]" value="<?php echo $this->default_val($options, 'thumbSizeHeight', 50); ?>" /><br />
                <span class="description"><?php _e('The width and height of the thumbnails', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Pause Time', 'nivo-slider'); ?></th>
                <td><input type="text" name="nivo_settings[pauseTime]" value="<?php echo $this->default_val($options, 'pauseTime', 3000); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('The amount of time to show each slide in milliseconds (eg 3000)', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Start Slide', 'nivo-slider'); ?></th>
                <td><input type="text" name="nivo_settings[startSlide]" value="<?php echo $this->default_val($options, 'startSlide', 0); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('Set which slide the slider starts from (zero based index: usually 0)', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Enable Direction Navigation', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[directionNav]" value="off" />
                <input type="checkbox" name="nivo_settings[directionNav]" value="on"<?php if($this->default_val($options, 'directionNav', 'on') == 'on') echo 'checked="checked"'; ?>/>
                <span class="description"><?php _e('Prev &amp; Next arrows', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Hide Direction Navigation on Hover', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[directionNavHide]" value="off" />
                <input type="checkbox" name="nivo_settings[directionNavHide]" value="on"<?php if($this->default_val($options, 'directionNavHide', 'on') == 'on') echo 'checked="checked"'; ?>/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Enable Control Navigation', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[controlNav]" value="off" />
                <input type="checkbox" name="nivo_settings[controlNav]" value="on"<?php if($this->default_val($options, 'controlNav', 'on') == 'on') echo 'checked="checked"'; ?>/>
                <span class="description"><?php _e('eg 1,2,3...', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Enable Keyboard Navigation', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[keyboardNav]" value="off" />
                <input type="checkbox" name="nivo_settings[keyboardNav]" value="on"<?php if($this->default_val($options, 'keyboardNav', 'on') == 'on') echo 'checked="checked"'; ?>/>
                <span class="description"><?php _e('Press Left or Right', 'nivo-slider'); ?></span></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Pause the Slider on Hover', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[pauseOnHover]" value="off" />
                <input type="checkbox" name="nivo_settings[pauseOnHover]" value="on"<?php if($this->default_val($options, 'pauseOnHover', 'on') == 'on') echo 'checked="checked"'; ?>/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Manual Transitions', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[manualAdvance]" value="off" />
                <input type="checkbox" name="nivo_settings[manualAdvance]" value="on"<?php if($this->default_val($options, 'manualAdvance', 'off') == 'on') echo 'checked="checked"'; ?>/> 
                <span class="description"><?php _e('Slider is always paused', 'nivo-slider'); ?></span></td>
            </tr>   
            <tr valign="top">
                <th scope="row"><?php _e('Random Start Slide', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[randomStart]" value="off" />
                <input type="checkbox" name="nivo_settings[randomStart]" value="on"<?php if($this->default_val($options, 'randomStart', 'off') == 'on') echo 'checked="checked"'; ?>/> 
                <span class="description"><?php _e('Overrides Start Slide value', 'nivo-slider'); ?></span></td>
            </tr>             
            <tr valign="top">
                <th scope="row"><?php _e('Disable Automatic Cropping', 'nivo-slider'); ?></th>
                <td><input type="hidden" name="nivo_settings[disableCrop]" value="off" />
                <input type="checkbox" name="nivo_settings[disableCrop]" value="on"<?php if($this->default_val($options, 'disableCrop', 'off') == 'on') echo 'checked="checked"'; ?>/> 
                <span class="description"><?php _e('Disables automatic cropping and resizing of images (i.e. disables TimThumb)', 'nivo-slider'); ?></span></td>
            </tr>              
        </table>
        <script type="text/javascript">
        jQuery(document).ready(function($){ 
            var nivo_themes = <?php echo json_encode($themes); ?>;
            $('select[name="nivo_settings[theme]"]').change(function(){
                nivo_theme_set_size();
            });
            nivo_theme_set_size();
            
            function nivo_theme_set_size(){
                var current_theme = $('select[name="nivo_settings[theme]"] option:selected').val();
                var dim_x = $('input[name="nivo_settings[dim_x]"]');
                var dim_y = $('input[name="nivo_settings[dim_y]"]');
                    
                if(nivo_themes[current_theme] != undefined){
                    if(nivo_themes[current_theme].theme_details.SkinType == 'fixed'){
                        dim_x.addClass('disabled').attr('readonly', true); 
                        dim_y.addClass('disabled').attr('readonly', true); 
                        dim_x.val(nivo_themes[current_theme].theme_details.ImageWidth);
                        dim_y.val(nivo_themes[current_theme].theme_details.ImageHeight);
                    } else {
                        dim_x.removeClass('disabled').removeAttr('readonly'); 
                        dim_y.removeClass('disabled').removeAttr('readonly'); 
                    }
                } else {
                    dim_x.removeClass('disabled').removeAttr('readonly'); 
                    dim_y.removeClass('disabled').removeAttr('readonly'); 
                }
            }
        });
        </script>
        <?php
    }
    
    function default_val( $options, $value, $default = '' ){
        if( !isset($options[$value]) ) return $default;
        else return $options[$value];
    }
    
    function save_post( $post_id ){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

        if ( !isset($_POST['nivoslider_noncename']) || !wp_verify_nonce( $_POST['nivoslider_noncename'], plugin_basename( __FILE__ ) ) )
            return;

        // Check permissions
        if ( 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) )
                return;
        } else {
            if ( !current_user_can( 'edit_post', $post_id ) )
                return;
        }

        // Good to go
        $settings = $_POST['nivo_settings'];
        
        if( !is_numeric($settings['dim_x']) || $settings['dim_x'] <= 0 ) $settings['dim_x'] = 400;
        if( !is_numeric($settings['dim_y']) || $settings['dim_y'] <= 0 ) $settings['dim_y'] = 150;
        if( !is_numeric($settings['slices']) || $settings['slices'] <= 0 ) $settings['slices'] = 15;
        if( !is_numeric($settings['boxCols']) || $settings['boxCols'] <= 0 ) $settings['boxCols'] = 8;
        if( !is_numeric($settings['boxRows']) || $settings['boxRows'] <= 0 ) $settings['boxRows'] = 4;
        if( !is_numeric($settings['animSpeed']) || $settings['animSpeed'] <= 0 ) $settings['animSpeed'] = 500;
        if( !is_numeric($settings['pauseTime']) || $settings['pauseTime'] <= 0 ) $settings['pauseTime'] = 3000;
        if( !is_numeric($settings['startSlide']) || $settings['startSlide'] < 0 ) $settings['startSlide'] = 0;
        if( !is_numeric($settings['thumbSizeWidth']) || $settings['thumbSizeWidth'] <= 0 ) $settings['thumbSizeWidth'] = 70;
        if( !is_numeric($settings['thumbSizeHeight']) || $settings['thumbSizeHeight'] <= 0 ) $settings['thumbSizeHeight'] = 50;
        
        update_post_meta( $post_id, 'nivo_settings', $settings );
    }
    
    function meta_box_upload() {
        global $post;
        ?>
        <ul id="nivoslider-images"></ul>
        <div id="file-uploader">       
            <noscript>          
                <p><?php _e('Please enable JavaScript to use the file uploader.', 'nivo-slider'); ?></p>
            </noscript>         
        </div>
        <div id="nivo-edit-image">
            <p><strong>Edit Image</strong></p>
            <table class="form-table">
            	<tr valign="top">
                    <th scope="row"><?php _e('Image Caption', 'nivo-slider'); ?></th>
                    <td><input type="text" name="nivo_meta_caption" id="nivo_meta_caption" value="" class="regular-text" /><br />
                    <span class="description"><?php _e('e.g. Example caption (Certain HTML allowed)', 'nivo-slider'); ?></span></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Image Link', 'nivo-slider'); ?></th>
                    <td><input type="text" name="nivo_meta_link" id="nivo_meta_link" value="" class="regular-text" /><br />
                    <span class="description"><?php _e('e.g. http://www.example.com', 'nivo-slider'); ?></span></td>
                </tr>
            </table>
            <p class="submit"><input type="button" name="nivo_meta_submit" id="nivo_meta_submit" class="button-primary" value="<?php _e( 'Save Changes', 'nivo-slider' ); ?>"></p>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function($){ 
            // File Uploader
            var uploader = new qq.FileUploader({
                element: document.getElementById('file-uploader'),
                action: '<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php',
                params: { action:'nivoslider_upload', post_id:'<?php echo $post->ID; ?>', 
                          nonce:'<?php echo wp_create_nonce('nivoslider_upload'); ?>' },
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],        
                multiple: true,
                debug: false,
                showMessage: function(message){}, 
                onComplete: function(id, fileName, response){
                    if(response.error){
                        alert(response.message);
                    } else {
                        $('#nivoslider-images').append('<li id="attachment-' + response.attachment_id + '">' +
                            '<img src="' + response.upload_path + '/' + response.file + '" alt="" class="attachment-thumbnail" />' +
                            '<a href="#" class="edit-image" rel="' + response.attachment_id + '" title="Edit Caption">Edit</a>' +
                            '<a href="#" class="remove-image" rel="' + response.attachment_id + '" title="Remove Image">Remove</a></li>');
                            
                        // Remove item from upload list
                        $('.qq-upload-list li').each(function(){
                            if($('.qq-upload-file', this).text() == fileName){
                                $(this).remove();
                            }
                        });
                    }
                }
            });
            $('.qq-upload-button').addClass('button-secondary');
            $('.qq-upload-button').bind('click', function(){
                $('.qq-upload-list li').remove();
                $('.qq-upload-drop-area').hide();
            });
            
            // Edit Caption
            $('#nivoslider-images .edit-image').live('click', function(){
                var edit = $(this);
                $('#nivo_meta_link').val('');
                $('#nivo_meta_caption').val('');
                $('#nivo-edit-image').data('attach_id', edit.attr('rel'));
                $('#nivo-edit-image').modal();
                
                $('#nivo-edit-image strong').addClass('loading');
                $.post('<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php', 
                    { action:'nivoslider_load_meta', id:$('#nivo-edit-image').data('attach_id'), 
                      nonce:'<?php echo wp_create_nonce('nivoslider_load_meta'); ?>' }, 
                    function(data){
                        $('#nivo_meta_link').val(data.link);
                        $('#nivo_meta_caption').val(data.caption);
                        $('#nivo-edit-image strong').removeClass('loading');
                    }
                , 'json');
                return false;
            });
            $('#nivo_meta_submit').live('click', function(){
                $('#nivo_meta_submit').val('Saving...');
                $.post('<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php', 
                    { action:'nivoslider_edit', id:$('#nivo-edit-image').data('attach_id'), 
                      link:$('#nivo_meta_link').val(), caption:$('#nivo_meta_caption').val(),
                      nonce:'<?php echo wp_create_nonce('nivoslider_edit'); ?>' }, 
                    function(data){
                        $('#nivo_meta_submit').val('Save Changes');
                        $.modal.close();
                    }
                );
            });
            
            // Remove Image
            $('#nivoslider-images .remove-image').live('click', function(){
                var remove = $(this);
                $.post('<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php', 
                    { action:'nivoslider_remove', data:remove.attr('rel'),
                      nonce:'<?php echo wp_create_nonce('nivoslider_remove'); ?>' }, 
                    function(data){
                        remove.parent().fadeOut(500, function(){
                            remove.remove();
                        });
                    }
                );
                
                return false;
            });
            
            // Drag & Drop sort images
            $('#nivoslider-images').sortable({
                update: function(event, ui){
                    $.post('<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php', 
                    $(this).sortable('serialize') + '&action=nivoslider_order_save&nonce=<?php echo wp_create_nonce('nivoslider_order_save'); ?>', 
                    function(response){
                        response = jQuery.parseJSON(response);
                        if(response.error){
                            alert(response.message);
                        }
                    });
                }
            });
            
            // Change Slider Type
            loadImages($('select[name="nivo_settings[type]"] option:selected').val()); // Initial load
            $('select[name="nivo_settings[type]"]').change(function(){
                var type = $('select[name="nivo_settings[type]"] option:selected').val();
                loadImages(type);
                if(type == 'manual'){
                    $('#nivoslider-images').sortable('enable');
                    $('.qq-upload-drop-area').hide(); // Strange bug
                } else {
                    $('#nivoslider-images').sortable('disable');
                }
            });
            $('select[name="nivo_settings[type_category]"]').change(function(){
                loadImages($('select[name="nivo_settings[type]"] option:selected').val());
            });
            
            function loadImages(type){
                $('#nivoslider-images').html('<li class="loading">Loading...</li>');
                $.ajax({
                    url: '<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php', 
                    type: 'POST',
                    dataType: 'json',
                    data: { action:'nivoslider_load_images', id:'<?php echo $post->ID; ?>', 
                      slider_type:type, category:$('select[name="nivo_settings[type_category]"] option:selected').val(),
                      nonce:'<?php echo wp_create_nonce('nivoslider_load_images'); ?>' }, 
                    success: function(response){
                        if(response.error){
                            alert(response.message);
                        } else {
                            $('#nivoslider-images').html('');
                            for(var i in response.images){
                                var image = response.images[i];
                                var output = '<li id="attachment-' + image.id + '">' +
                                    '<img src="' + image.src + '" alt="" class="attachment-thumbnail" />';
                                if(type == 'manual'){
                                    output += '<a href="#" class="edit-image" rel="' + image.id + '" title="Edit Caption">Edit</a>' +
                                              '<a href="#" class="remove-image" rel="' + image.id + '" title="Remove Image">Remove</a></li>';
                                }
                                $('#nivoslider-images').append(output);
                            }
                        }
                    },
                    error: function(response, status, error){
                        alert('Error: ' + error.replace(/(<([^>]+)>)/ig,""));
                    }
                });
            }
        });
        </script>
        <?php 
    }
    
    function load_images(){
        // Verify this came from the our screen and with proper authorization
        if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], plugin_basename('nivoslider_load_images') ))
            return 0;
            
        $response['error'] = false;
        $response['message'] = '';
        $response['images'] = array();
        $images = array();
        
        if($_POST['slider_type'] == 'manual'){
            $args = array(
                'orderby'        => 'menu_order ID',
                'order'          => 'ASC',
                'post_type'      => 'attachment',
                'post_parent'    => $_POST['id'],
                'post_mime_type' => 'image',
                'post_status'    => null,
                'numberposts'    => -1
            );
            $attachments = get_posts( $args );
            if( $attachments ){
                foreach( $attachments as $attachment ){
                    $image = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' ); 
                    $images[] = array(
                        'id' => $attachment->ID,
                        'src' => $image[0]
                    );
                }
            }
        }
        if($_POST['slider_type'] == 'category'){
            $args = array(
                'post_type'      => 'post',
                'numberposts'    => -1,
                'category'       => $_POST['category']
            );
            $posts = get_posts( $args );
            if( $posts ){
                foreach( $posts as $post ){
                    if( has_post_thumbnail($post->ID) ) {
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
                        $images[] = array(
                            'id' => get_post_thumbnail_id($post->ID),
                            'src' => $image[0]
                        );
                    }
                }
            }
        }
        if($_POST['slider_type'] == 'sticky'){
            $sticky = get_option( 'sticky_posts' );
            $args = array(
                'post_type'      => 'post',
                'numberposts'    => -1,
                'post__in'       => $sticky
            );
            $posts = get_posts( $args );
            if( $posts ){
                foreach( $posts as $post ){
                    if( has_post_thumbnail($post->ID) ) {
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
                        $images[] = array(
                            'id' => get_post_thumbnail_id($post->ID),
                            'src' => $image[0]
                        );
                    }
                }
            }
        }
        
        $response['images'] = $images;
        
        echo json_encode($response);
        die;
    }
    
    function upload_image(){
        // Verify this came from the our screen and with proper authorization
        if ( !isset($_GET['nonce']) || !wp_verify_nonce( $_GET['nonce'], plugin_basename('nivoslider_upload') ))
            return 0;
        
        require_once(WP_PLUGIN_DIR .'/nivo-slider/scripts/uploader/uploader.php');
        $response['error'] = false;
        $response['message'] = '';
        
        // Upload file
        $wp_uploads = wp_upload_dir();
        if( isset($wp_uploads['error']) && $wp_uploads['error'] != false ){
            $response['error'] = true;
            $response['message'] = $wp_uploads['error'];
            echo htmlspecialchars( json_encode($response), ENT_NOQUOTES );
            exit;
        }
        $uploader = new qqFileUploader();
        $result = $uploader->handleUpload( $wp_uploads['path'] .'/', false );
        
        if( is_array($result) && $result[0] == 'success' ){  
            // Attach image to the post
            $uploadfile = $result[1];
            $wp_filetype = wp_check_filetype( basename($uploadfile), null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($uploadfile)),
                'post_content' => '',
                'post_status' => 'inherit',
                'menu_order' => 1
            );
            $attach_id = wp_insert_attachment( $attachment, $uploadfile, $_GET['post_id'] );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $uploadfile );
            wp_update_attachment_metadata( $attach_id,  $attach_data );
            
            $response['upload_path'] = $wp_uploads['url'];
            if(!empty($attach_data['sizes'])){
                $response['file'] = $attach_data['sizes']['thumbnail']['file'];
                $response['file_medium'] = $attach_data['sizes']['medium']['file'];
            } else {
                $response['file'] = basename($attach_data['file']);
                $response['file_medium'] = basename($attach_data['file']);
            }
            $response['file_full'] = basename($attach_data['file']);
            $response['attachment_id'] = $attach_id;
            $response['success'] = true;
        } else {
            $response['error'] = true;
            $response['message'] = $result; 
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars( json_encode($response), ENT_NOQUOTES );
        die;
    }
    
    function load_image_meta() {
        // Verify this came from the our screen and with proper authorization
        if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], plugin_basename('nivoslider_load_meta') ))
            return 0;
            
        $response['error'] = false;
        $response['message'] = '';

        $meta = wp_get_attachment_metadata($_POST['id']);        
        $response['caption'] = $meta['image_meta']['caption'];
        $response['link'] = '';
        if( isset($meta['link']) ) $response['link'] = $meta['link'];
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    function edit_image() {
        // Verify this came from the our screen and with proper authorization
        if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], plugin_basename('nivoslider_edit') ))
            return 0;
            
        $response['error'] = false;
        $response['message'] = '';
            
        $meta = wp_get_attachment_metadata($_POST['id']);
        $meta['image_meta']['caption'] = strip_tags($_POST['caption'], '<a><strong><em><h1><h2><h3><h4><h5><h6><code>');
        $meta['link'] = strip_tags($_POST['link']);
        wp_update_attachment_metadata( $_POST['id'], $meta );
        
        $response['caption'] = $meta['image_meta']['caption'];
        $response['link'] = $meta['link'];
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    function remove_image() {
        // Verify this came from the our screen and with proper authorization
        if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], plugin_basename('nivoslider_remove') ))
            return 0;
            
        $response['error'] = false;
        $response['message'] = '';
            
        wp_delete_attachment( $_POST['data'] );
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }

    function save_order() {    
        // Verify this came from the our screen and with proper authorization
        if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], plugin_basename('nivoslider_order_save') ))
            return 0;
            
        $response['error'] = false;
        $response['message'] = '';
            
        $i = 0;
        $data = $_POST['attachment'];
        foreach( $data as $attach_id ){
            $attachment = array();
            $attachment['ID'] = $attach_id;
            $attachment['menu_order'] = $i;
            wp_update_post( $attachment );
            $i++;
        }
        
        $response['data'] = $data;
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    function meta_box_shortcode() {
        global $post;
        
        echo '<p>'. __('To use this slider in your posts or pages use the following shortcode:', 'nivo-slider') .'</p>
        <p><code>[nivoslider id="'. $post->ID .'"]</code>';
        if($post->post_name != '') echo 'or</p><p><code>[nivoslider slug="'. $post->post_name .'"]</code>';
        echo '</p>';
    }
    
    function print_styles() {
        $themes = $this->get_themes();
        foreach($themes as $theme){
            wp_enqueue_style( 'nivoslider-theme-'. $theme['theme_name'], $theme['theme_url'] );
        }
        
        wp_enqueue_style( 'nivoslider', WP_PLUGIN_URL .'/nivo-slider/scripts/nivo-slider/nivo-slider.css' );
    }
    
    function print_scripts() {
        wp_register_script( 'nivoslider', WP_PLUGIN_URL .'/nivo-slider/scripts/nivo-slider/jquery.nivo.slider.pack.js', array('jquery') );
        wp_enqueue_script( 'nivoslider' );
        wp_enqueue_script( 'jquery' );
    }
    
    function shortcode( $atts ) {
        extract( shortcode_atts( array(
            'id' => 0,
            'slug' => ''
        ), $atts ) );
        
        if(!$id && !$slug){
            return __('Invalid Slider', 'nivo-slider');
        }
        
        if(!$id){
            $slider = get_page_by_path( $slug, OBJECT, 'nivoslider' );
            if($slider){
                $id = $slider->ID;
            } else {
                return __('Invalid Slider Slug', 'nivo-slider');
            }
        }
        
        $output = '';
        $options = get_post_meta( $id, 'nivo_settings', true );
        $images = $this->get_slider_images( $id );
        
        if( $images ){
        	$captions = array();
            $output .= '<div class="slider-wrapper';
            if(isset($options['theme']) && $options['theme'] != '') $output .= ' theme-'. $options['theme'];
            if(isset($options['controlNavThumbs']) && $options['controlNavThumbs'] == 'on') $output .= ' controlnav-thumbs';
            $output .='"><div class="ribbon"></div>';
            $output .= '<div id="nivoslider-'. $id .'" class="nivoSlider" style="width:'. $options['dim_x'] .'px;height:'. $options['dim_y'] .'px;">';
            $i = 0;
            foreach( $images as $image ){
                if(isset($image['post_permalink']) && $image['post_permalink'] != '') $output .= '<a href="'. $image['post_permalink'] .'">';
                if(isset($options['disableCrop']) && $options['disableCrop'] == 'on'){ 
                    $output .= '<img src="'. $image['image_src'] .'" ';
                } else { 
                    // WPMultisite support
                    global $blog_id;
                    $theImageSrc = $image['image_src'];
                    if (isset($blog_id) && $blog_id > 0) {
                        $imageParts = explode('/files/', $theImageSrc);
                        if (isset($imageParts[1])) {
                            $theImageSrc = WP_CONTENT_URL .'/blogs.dir/'. $blog_id .'/files/'. $imageParts[1];
                        }
                        $image['image_src'] = $theImageSrc;
                    }
                    $output .= '<img src="'. WP_PLUGIN_URL .'/nivo-slider/timthumb.php?src='. $image['image_src'] .'&amp;h='. $options['dim_y'] .'&amp;w='. $options['dim_x'] .'&amp;zc=1&amp;q=100" ';
                }
                
                if($options['type'] != 'manual' && $options['enable_captions'] == 'on' && isset($image['post_title'])){ 
                	$captions[] = $image['post_title'];
                	$output .= 'title="#nivoslider-'. $id .'-caption-'. $i .'" ';
                	$i++;
                }
                if($options['type'] == 'manual' && isset($image['post_title']) && $image['post_title'] != ''){
                	$captions[] = $image['post_title'];
                	$output .= 'title="#nivoslider-'. $id .'-caption-'. $i .'" ';
                	$i++;
                }
                
                if(isset($options['controlNavThumbs']) && $options['controlNavThumbs'] == 'on'){
                    if(isset($options['disableCrop']) && $options['disableCrop'] == 'on'){ 
                        $output .= 'rel="'. $image['image_src'] .'" ';
                    } else {
                        // WPMultisite support
                        global $blog_id;
                        $theImageSrc = $image['image_src'];
                        if (isset($blog_id) && $blog_id > 0) {
                            $imageParts = explode('/files/', $theImageSrc);
                            if (isset($imageParts[1])) {
                                $theImageSrc = WP_CONTENT_URL .'/blogs.dir/'. $blog_id .'/files/'. $imageParts[1];
                            }
                            $image['image_src'] = $theImageSrc;
                        }
                        $output .= 'rel="'. WP_PLUGIN_URL .'/nivo-slider/timthumb.php?src='. $image['image_src'] .'&amp;h='. $options['thumbSizeHeight'] .'&amp;w='. $options['thumbSizeWidth'] .'&amp;zc=1&amp;q=90" ';
                    }
                }
                
                $output .= 'alt="" />';
                if(isset($image['post_permalink']) && $image['post_permalink'] != '') $output .= '</a>';
            }
            $output .= '</div></div>';
            
            $i = 0;
            foreach( $captions as $caption ){
            	$output .= '<div id="nivoslider-'. $id .'-caption-'. $i .'" class="nivo-html-caption">';
            	$output .= $caption;
            	$output .= '</div>';
            	$i++;
            }
            
            if( count($images) > 1){
                $output .= '<script type="text/javascript">' ."\n";
                $output .= 'jQuery(window).load(function(){' ."\n";
                $output .= '    jQuery("#nivoslider-'. $id .'").nivoSlider({' ."\n";
                $output .= '        effect:"'. $options['effect'] .'",' ."\n";
                $output .= '        slices:'. $options['slices'] .',' ."\n";
                $output .= '        boxCols:'. $options['boxCols'] .',' ."\n";
                $output .= '        boxRows:'. $options['boxRows'] .',' ."\n";
                $output .= '        animSpeed:'. $options['animSpeed'] .',' ."\n";
                $output .= '        pauseTime:'. $options['pauseTime'] .',' ."\n";
                if($options['randomStart'] == 'on') $output .= '        startSlide:'. floor(rand(0, count($images))) .',' ."\n";
                else $output .= '        startSlide:'. $options['startSlide'] .',' ."\n";
                $output .= '        directionNav:'. (($options['directionNav'] == 'on') ? 'true' : 'false') .',' ."\n";
                $output .= '        directionNavHide:'. (($options['directionNavHide'] == 'on') ? 'true' : 'false') .',' ."\n";
                $output .= '        controlNav:'. (($options['controlNav'] == 'on') ? 'true' : 'false') .',' ."\n";
                $output .= '        controlNavThumbs:'. (($options['controlNavThumbs'] == 'on') ? 'true' : 'false') .',' ."\n";
                $output .= '        controlNavThumbsFromRel:true,' ."\n";
                $output .= '        keyboardNav:'. (($options['keyboardNav'] == 'on') ? 'true' : 'false') .',' ."\n";
                $output .= '        pauseOnHover:'. (($options['pauseOnHover'] == 'on') ? 'true' : 'false') .',' ."\n";
                $output .= '        manualAdvance:'. (($options['manualAdvance'] == 'on') ? 'true' : 'false') ."\n";
                $output .= '    });' ."\n";
                $output .= '});' ."\n";
                $output .= '</script>' ."\n";
            } else {
                $output .= '<script type="text/javascript">' ."\n";
                $output .= 'jQuery(window).load(function(){' ."\n";
                $output .= '    jQuery("#nivoslider-'. $id .' img").show();' ."\n";
                $output .= '});' ."\n";
                $output .= '</script>' ."\n";
            }
        }
        
        return $output;
    }
    
    function get_slider_images( $post_id, $size = 'full', $limit = -1 ) {
        $options = get_post_meta( $post_id, 'nivo_settings', true );
        if(!$options) return;
        $images = array();
        
        if($options['type'] == 'manual'){
            $args = array(
                'orderby'        => 'menu_order ID',
                'order'          => 'ASC',
                'post_type'      => 'attachment',
                'post_parent'    => $post_id,
                'post_mime_type' => 'image',
                'post_status'    => null,
                'numberposts'    => $limit
            );
            $attachments = get_posts( $args );
            if( $attachments ){
                foreach( $attachments as $attachment ){
                    $image = wp_get_attachment_image_src( $attachment->ID, $size );
                    $meta = wp_get_attachment_metadata( $attachment->ID );        
                    $caption = $meta['image_meta']['caption'];
                    $link = '';
                    if( isset($meta['link']) ) $link = $meta['link'];
                    
                    $images[] = array(
                        'image_src' => $image[0],
                        'post_permalink' => $link,
                        'post_title' => $caption
                    );
                }
            }
        }
        if($options['type'] == 'category'){
            $args = array(
                'post_type'      => 'post',
                'numberposts'    => $limit,
                'category'       => $options['type_category']
            );
            $posts = get_posts( $args );
            if( $posts ){
                foreach( $posts as $post ){
                    if( has_post_thumbnail($post->ID) ) {
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $size );
                        $images[] = array(
                            'image_src' => $image[0],
                            'post_permalink' => get_permalink($post->ID),
                            'post_title' => get_the_title($post->ID)
                        );
                    }
                }
            }
        }
        if($options['type'] == 'sticky'){
            $sticky = get_option( 'sticky_posts' );
            $args = array(
                'post_type'      => 'post',
                'numberposts'    => $limit,
                'post__in'       => $sticky
            );
            $posts = get_posts( $args );
            if( $posts ){
                foreach( $posts as $post ){
                    if( has_post_thumbnail($post->ID) ) {
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $size );
                        $images[] = array(
                            'image_src' => $image[0],
                            'post_permalink' => get_permalink($post->ID),
                            'post_title' => get_the_title($post->ID)
                        );
                    }
                }
            }
        }
        
        return $images;
    }

    function mce_add_plugin( $plugin_array ) {
        $plugin_array['nivoslider'] = WP_PLUGIN_URL .'/nivo-slider/scripts/mce-nivoslider/nivoslider.js';
        return $plugin_array;
    }
    
    function mce_register_button( $buttons ) {
        array_push($buttons, '|', 'nivoslider');
        return $buttons;
    }
    
    function get_themes(){
        $nivo_theme_specs = array(
            'SkinName' => 'Skin Name',
            'SkinURI' => 'Skin URI',
            'SkinType' => 'Skin Type',
            'ImageWidth' => 'Image Width',
            'ImageHeight' => 'Image Height',
            'Description' => 'Description',
            'Version' => 'Version',
            'Author' => 'Author',
            'AuthorURI' => 'Author URI'
        );
        $nivo_themes = glob(WP_PLUGIN_DIR .'/nivo-slider/scripts/nivo-slider/themes/*', GLOB_ONLYDIR);
        $themes = array();
        
        foreach($nivo_themes as $theme_dir){
            $theme_name = basename($theme_dir);
            $theme_path = WP_PLUGIN_DIR .'/nivo-slider/scripts/nivo-slider/themes/'. $theme_name .'/'. $theme_name .'.css';
            if( file_exists($theme_path) ){
                $themes[$theme_name] = array(
                    'theme_name' => $theme_name,
                    'theme_path' => $theme_path,
                    'theme_url' => WP_PLUGIN_URL .'/nivo-slider/scripts/nivo-slider/themes/'. $theme_name .'/'. $theme_name .'.css',
                    'theme_details' => get_file_data($theme_path, $nivo_theme_specs)
                );
            }
        }
        
        return $themes;
    }
    
}

?>