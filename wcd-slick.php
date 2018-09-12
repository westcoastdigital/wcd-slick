<?php
/*
Plugin Name: Slick Slider
Plugin URI: https://github.com/WestCoastDigital/wcd-slick
Description: Convert any element into a slider using Slick by Ken Wheeler
Version: 1.0.0
Author: West Coast Digital
Author URI: https://github.com/WestCoastDigital
Text Domain: wcd
Domain Path: /languages
*/

/**
 * Enqueue slick styles and scripts
 */
function wcd_enqueue_slick() {
    wp_enqueue_style( 'slick', plugin_dir_url( __FILE__ ) . 'slick/slick.css' );
    wp_enqueue_style( 'slick-theme', plugin_dir_url( __FILE__ ) . 'slick/slick-theme.css' );
    wp_enqueue_script( 'jquery'  );
    wp_enqueue_script( 'slick', plugin_dir_url( __FILE__ ) . 'slick/slick.min.js' );
}
add_action( 'wp_enqueue_scripts', 'wcd_enqueue_slick' );


// Setup Slick Options
if ( ! function_exists('wcd_slick_sliders') ) {

    // Register Custom Post Type
    function wcd_slick_sliders() {
    
        $labels = array(
            'name'                  => _x( 'Slick Sliders', 'Post Type General Name', 'wcd' ),
            'singular_name'         => _x( 'Slick Slider', 'Post Type Singular Name', 'wcd' ),
            'menu_name'             => __( 'Slick Setup', 'wcd' ),
            'name_admin_bar'        => __( 'Slick Config', 'wcd' ),
            'archives'              => __( 'Slick Slides', 'wcd' ),
            'attributes'            => __( 'Slide', 'wcd' ),
            'parent_item_colon'     => __( 'Parent Item:', 'wcd' ),
            'all_items'             => __( 'All Items', 'wcd' ),
            'add_new_item'          => __( 'Add New Item', 'wcd' ),
            'add_new'               => __( 'Add New', 'wcd' ),
            'new_item'              => __( 'New Item', 'wcd' ),
            'edit_item'             => __( 'Edit Item', 'wcd' ),
            'update_item'           => __( 'Update Item', 'wcd' ),
            'view_item'             => __( 'View Item', 'wcd' ),
            'view_items'            => __( 'View Items', 'wcd' ),
            'search_items'          => __( 'Search Item', 'wcd' ),
            'not_found'             => __( 'Not found', 'wcd' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'wcd' ),
            'featured_image'        => __( 'Featured Image', 'wcd' ),
            'set_featured_image'    => __( 'Set featured image', 'wcd' ),
            'remove_featured_image' => __( 'Remove featured image', 'wcd' ),
            'use_featured_image'    => __( 'Use as featured image', 'wcd' ),
            'insert_into_item'      => __( 'Insert into item', 'wcd' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'wcd' ),
            'items_list'            => __( 'Items list', 'wcd' ),
            'items_list_navigation' => __( 'Items list navigation', 'wcd' ),
            'filter_items_list'     => __( 'Filter items list', 'wcd' ),
        );
        $args = array(
            'label'                 => __( 'Slick Slider', 'wcd' ),
            'description'           => __( 'Slick Sliders', 'wcd' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 75,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( 'slick', $args );
        flush_rewrite_rules();
    }
    add_action( 'init', 'wcd_slick_sliders', 0 );
    
    }

    class WCD_Slick_Meta_box {

        public function __construct() {
    
            if ( is_admin() ) {
                add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
                add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
            }
    
        }
    
        public function init_metabox() {
    
            add_action( 'add_meta_boxes',        array( $this, 'add_metabox' )         );
            add_action( 'save_post',             array( $this, 'save_metabox' ), 10, 2 );
    
        }
    
        public function add_metabox() {
    
            add_meta_box(
                'slick',
                __( 'Slick Setting', 'wcd' ),
                array( $this, 'render_metabox' ),
                'slick',
                'advanced',
                'default'
            );
    
        }
    
        public function render_metabox( $post ) {
    
            // Add nonce for security and authentication.
            wp_nonce_field( 'wcd_nonce_action', 'wcd_nonce' );
    
            // Retrieve an existing value from the database.
            $wcd_slick_class = get_post_meta( $post->ID, 'wcd_slick_class', true );
            $wcd_inifinte = get_post_meta( $post->ID, 'wcd_inifinte', true );
            $wcd_arrows = get_post_meta( $post->ID, 'wcd_arrows', true );
            $wcd_dots = get_post_meta( $post->ID, 'wcd_dots', true );
            $wcd_autoplay = get_post_meta( $post->ID, 'wcd_autoplay', true );
            $wcd_fade = get_post_meta( $post->ID, 'wcd_fade', true );
            $wcd_custom = get_post_meta( $post->ID, 'wcd_custom', true );
    
            // Set default values.
            if( empty( $wcd_slick_class ) ) $wcd_slick_class = '';
            if( empty( $wcd_inifinte ) ) $wcd_inifinte = '';
            if( empty( $wcd_arrows ) ) $wcd_arrows = '';
            if( empty( $wcd_dots ) ) $wcd_dots = '';
            if( empty( $wcd_autoplay ) ) $wcd_autoplay = '';
            if( empty( $wcd_fade ) ) $wcd_fade = '';
            if( empty( $wcd_custom ) ) $wcd_custom = '';
    
            // Form fields.
            echo '<table class="form-table">';
    
            echo '	<tr>';
            echo '		<th><label for="wcd_slick_class" class="wcd_slick_class_label">' . __( 'Class', 'wcd' ) . '</label></th>';
            echo '		<td>';
            echo '			<input type="text" id="wcd_slick_class" name="wcd_slick_class" class="wcd_slick_class_field" placeholder="' . esc_attr__( '', 'wcd' ) . '" value="' . esc_attr( $wcd_slick_class ) . '">';
            echo '			<p class="description">' . __( 'Class to assign the slick', 'wcd' ) . '</p>';
            echo '		</td>';
            echo '	</tr>';
    
            echo '	<tr>';
            echo '		<th><label for="wcd_inifinte" class="wcd_inifinte_label">' . __( 'Inifinte', 'wcd' ) . '</label></th>';
            echo '		<td>';
            echo '			<label><input type="checkbox" id="wcd_inifinte" name="wcd_inifinte" class="wcd_inifinte_field" value="checked" ' . checked( $wcd_inifinte, 'checked', false ) . '> ' . __( '', 'wcd' ) . '</label>';
            echo '			<span class="description">' . __( 'Infinite loop?', 'wcd' ) . '</span>';
            echo '		</td>';
            echo '	</tr>';
    
            echo '	<tr>';
            echo '		<th><label for="wcd_arrows" class="wcd_arrows_label">' . __( 'Arrows', 'wcd' ) . '</label></th>';
            echo '		<td>';
            echo '			<label><input type="checkbox" id="wcd_arrows" name="wcd_arrows" class="wcd_arrows_field" value="checked" ' . checked( $wcd_arrows, 'checked', false ) . '> ' . __( '', 'wcd' ) . '</label>';
            echo '			<span class="description">' . __( 'Show arrows?', 'wcd' ) . '</span>';
            echo '		</td>';
            echo '	</tr>';
    
            echo '	<tr>';
            echo '		<th><label for="wcd_dots" class="wcd_dots_label">' . __( 'Dots', 'wcd' ) . '</label></th>';
            echo '		<td>';
            echo '			<label><input type="checkbox" id="wcd_dots" name="wcd_dots" class="wcd_dots_field" value="checked" ' . checked( $wcd_dots, 'checked', false ) . '> ' . __( '', 'wcd' ) . '</label>';
            echo '			<span class="description">' . __( 'Show dots?', 'wcd' ) . '</span>';
            echo '		</td>';
            echo '	</tr>';
    
            echo '	<tr>';
            echo '		<th><label for="wcd_autoplay" class="wcd_autoplay_label">' . __( 'Autoplay', 'wcd' ) . '</label></th>';
            echo '		<td>';
            echo '			<label><input type="checkbox" id="wcd_autoplay" name="wcd_autoplay" class="wcd_autoplay_field" value="checked" ' . checked( $wcd_autoplay, 'checked', false ) . '> ' . __( '', 'wcd' ) . '</label>';
            echo '			<span class="description">' . __( 'Autoplay slides?', 'wcd' ) . '</span>';
            echo '		</td>';
            echo '	</tr>';
    
            echo '	<tr>';
            echo '		<th><label for="wcd_fade" class="wcd_fade_label">' . __( 'Fade', 'wcd' ) . '</label></th>';
            echo '		<td>';
            echo '			<label><input type="checkbox" id="wcd_fade" name="wcd_fade" class="wcd_fade_field" value="checked" ' . checked( $wcd_fade, 'checked', false ) . '> ' . __( '', 'wcd' ) . '</label>';
            echo '			<span class="description">' . __( 'Fade slides instead of scroll', 'wcd' ) . '</span>';
            echo '		</td>';
            echo '	</tr>';
    
            echo '	<tr>';
            echo '		<th><label for="wcd_custom" class="wcd_custom_label">' . __( 'Custom JS', 'wcd' ) . '</label></th>';
            echo '		<td>';
            echo '			<textarea id="wcd_custom" name="wcd_custom" class="wcd_custom_field" placeholder="' . esc_attr__( '', 'wcd' ) . '">' . $wcd_custom . '</textarea>';
            echo '			<p class="description">' . __( 'Add custom js', 'wcd' ) . '</p>';
            echo '		</td>';
            echo '	</tr>';
    
            echo '</table>';
    
        }
    
        public function save_metabox( $post_id, $post ) {
    
            // Add nonce for security and authentication.
            $nonce_name   = isset( $_POST['wcd_nonce'] ) ? $_POST['wcd_nonce'] : '';
            $nonce_action = 'wcd_nonce_action';
    
            // Check if a nonce is set.
            if ( ! isset( $nonce_name ) )
                return;
    
            // Check if a nonce is valid.
            if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
                return;
    
            // Sanitize user input.
            $wcd_new_slick_class = isset( $_POST[ 'wcd_slick_class' ] ) ? sanitize_text_field( $_POST[ 'wcd_slick_class' ] ) : '';
            $wcd_new_inifinte = isset( $_POST[ 'wcd_inifinte' ] ) ? 'checked'  : '';
            $wcd_new_arrows = isset( $_POST[ 'wcd_arrows' ] ) ? 'checked'  : '';
            $wcd_new_dots = isset( $_POST[ 'wcd_dots' ] ) ? 'checked'  : '';
            $wcd_new_autoplay = isset( $_POST[ 'wcd_autoplay' ] ) ? 'checked'  : '';
            $wcd_new_fade = isset( $_POST[ 'wcd_fade' ] ) ? 'checked'  : '';
            $wcd_new_custom = isset( $_POST[ 'wcd_custom' ] ) ? sanitize_text_field( $_POST[ 'wcd_custom' ] ) : '';
    
            // Update the meta field in the database.
            update_post_meta( $post_id, 'wcd_slick_class', $wcd_new_slick_class );
            update_post_meta( $post_id, 'wcd_inifinte', $wcd_new_inifinte );
            update_post_meta( $post_id, 'wcd_arrows', $wcd_new_arrows );
            update_post_meta( $post_id, 'wcd_dots', $wcd_new_dots );
            update_post_meta( $post_id, 'wcd_autoplay', $wcd_new_autoplay );
            update_post_meta( $post_id, 'wcd_fade', $wcd_new_fade );
            update_post_meta( $post_id, 'wcd_custom', $wcd_new_custom );
    
        }
    
    }
    
    new WCD_Slick_Meta_box;


    function wcd_add_custom_scripts() {

        $args = array(
            'post_type'              => array( 'slick' ),
            'post_status'            => array( 'publish' ),
            'posts_per_page'         => '-1',
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $wcd_slick_class = get_post_meta( get_the_ID(), 'wcd_slick_class', true );
                $wcd_inifinte = get_post_meta( get_the_ID(), 'wcd_inifinte', true );
                if ($wcd_inifinte) { $infinite = 'true'; } else { $infinite = 'false'; }
                $wcd_arrows = get_post_meta( get_the_ID(), 'wcd_arrows', true );
                if ($wcd_arrows) { $arrows = 'true'; } else { $arrows = 'false'; }
                $wcd_dots = get_post_meta( get_the_ID(), 'wcd_dots', true );
                if ($wcd_dots) { $dots = 'true'; } else { $dots = 'false'; }
                $wcd_autoplay = get_post_meta( get_the_ID(), 'wcd_autoplay', true );
                if ($wcd_autoplay) { $autoplay = 'true'; } else { $autoplay = 'false'; }
                $wcd_fade = get_post_meta( get_the_ID(), 'wcd_fade', true );
                if ($wcd_fade) { $fade = 'true'; } else { $fade = 'false'; }
                $wcd_custom = get_post_meta( get_the_ID(), 'wcd_custom', true );
                if ($wcd_custom) { $custom = $wcd_custom; } else { $custom = ''; }
                echo '<script>
                jQuery( document ).ready(function() {
                    jQuery( "' . $wcd_slick_class . '").slick({
                        dots: ' . $dots .',
                        arrows: ' . $arrows .',
                        infinite: ' . $infinite .',
                        autoplay: ' . $autoplay .',
                        fade: ' . $fade .',
                        ' . $custom . '
                    });
                });
                </script>';
            }
        }
        wp_reset_postdata();

    }
    add_action('wp_footer', 'wcd_add_custom_scripts');