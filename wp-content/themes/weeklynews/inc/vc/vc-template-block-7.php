<?php
/*
Plugin Name: Extend Visual Composer Plugin Example
Plugin URI: http://wpbakery.com/vc
Description: Extend Visual Composer with your own set of shortcodes.
Version: 0.1.1
Author: WPBakery
Author URI: http://wpbakery.com
License: GPLv2 or later
*/

if ( ! class_exists( 'MipTheme_VCExtendAddonClass_Block7' ) ) {

    class MipTheme_VCExtendAddonClass_Block7 {
        function __construct() {
            // We safely integrate with VC with this hook
            add_action( 'init', array( $this, 'integrateWithVC' ) );

            // Use this when creating a shortcode addon
            add_shortcode( 'mp_block_7', array( $this, 'renderMyBlock7' ) );

            // Register CSS and JS
            //add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
        }

        public function integrateWithVC() {
            // Check if Visual Composer is installed
            if ( ! defined( 'WPB_VC_VERSION' ) ) {
                return;
            }

            /*
            Add your Visual Composer logic here.
            Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

            More info: http://kb.wpbakery.com/index.php?title=Vc_map
            */
            vc_map( array(
                "name" => __("Review Block", 'vc_extend'),
                "base" => "mp_block_7",
                "class" => "",
                "controls" => "full",
                "icon" => 'mp_icon', // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
                "category" => __('Content', 'js_composer'),
                "params" => array(

                    array(
                        "param_name" => "section_title",
                        "type" => "textfield",
                        "value" => 'Latest Reviews',
                        "holder" => "div",
                        "heading" => __("Block Title:", 'vc_extend'),
                        "class" => ""
                    ),

                    array(
                        "param_name" => "section_link",
                        "type" => "textfield",
                        "value" => '',
                        "holder" => "div",
                        "heading" => __("Block Title Link:", 'vc_extend'),
                        "description" => __("This is an optional field", 'vc_extend'),
                        "class" => ""
                    ),

                    array(
                        "param_name" => "post_sort",
                        "type" => "dropdown",
                        "value" => array(
                            'Latest' => 'date',
                            'Best score reviews' => 'meta_value_num',
                            'Random posts' => 'rand',
                            'By name' => 'name',
                            'By Post Type' => 'type',
                            'Last Modified' => 'modified',
                            'Most Commented' => 'comment_count'
                        ),
                        "heading" => __("Sort order:", 'vc_extend'),
                        "description" => "",
                        "holder" => "div",
                        "class" => ""
                    ),

                    array(
                        "param_name" => "post_tag_slug",
                        "type" => "textfield",
                        "value" => '',
                        "heading" => __("Filter by tag slug:", 'vc_extend'),
                        "description" => "(e.g.: tag1, tag1, tag3); One or more tags separated by commas",
                        "holder" => "div",
                        "class" => ""
                    ),

                    array(
                        "param_name" => "post_limit",
                        "type" => "textfield",
                        "value" => '4',
                        "heading" => __("Posts per page:", 'vc_extend'),
                        "description" => "e.g.: 4; a integer number, used to display the number of posts per page",
                        "holder" => "div",
                        "class" => ""
                    ),

                    array(
                        "param_name" => "post_offset",
                        "type" => "textfield",
                        "value" => '0',
                        "heading" => __("Offset:", 'vc_extend'),
                        "description" => "e.g.: 3; a integer number of post to displace or pass over",
                        "holder" => "div",
                        "class" => ""
                    ),

                    array(
                        "param_name" => "post_paging",
                        "type" => "dropdown",
                        "value" => array(
                            'No paging' => 'none',
                            'Prev/next in block header' => 'header_paging',
                            'Prev/next in block footer' => 'footer_paging'
                        ),
                        "heading" => __("Allow paging:", 'vc_extend'),
                        "description" => "",
                        "holder" => "div",
                        "class" => ""
                    ),

                )
            ) );
        }

        /*
        Shortcode logic how it should be rendered
        */
        public function renderMyBlock7( $atts, $content = null ) {

            global $post, $mp_weeklynews;
            $page_sidebar_pos           = redux_post_meta(THEMEREDUXNAME, $post->ID, '_mp_page_sidebar_position_single')        ? redux_post_meta(THEMEREDUXNAME, $post->ID, '_mp_page_sidebar_position_single')        : (isset($mp_weeklynews['_mp_page_sidebar_position']) ? $mp_weeklynews['_mp_page_sidebar_position'] : '');
            $page_sidebar_pos           = ( $page_sidebar_pos == 'multi-sidebar mid-left' )                                         ? 'multi-sidebar' : $page_sidebar_pos;
            $page_sidebar_pos           = ( ($page_sidebar_pos == 'left-sidebar') || ($page_sidebar_pos == 'right-sidebar') )       ? 'sidebar' : $page_sidebar_pos;

            $first_image_attr           = new MipTheme_Image();
            $first_image                = $first_image_attr->get_image_attr_block11($page_sidebar_pos .'-1');

            $image_post_format_first    = $first_image[0];
            $image_post_format_second   = '';
            $image_post_first_width     = $first_image[1];
            $image_post_first_height    = $first_image[2];
            $image_post_second_width    = 0;
            $image_post_second_height   = 0;
            $shorten_text_chars         = 0;

            $image_post_dummy_first = ''. $image_post_first_width .'x'. $image_post_first_height .'';

            extract( shortcode_atts( array(
                'post_sort' => 'date',
                'post_limit' => '4',
                'post_tag_slug' => '',
                'post_offset' => '0',
                'section_title' => 'Latest Reviews',
                'section_link' => '',
                'post_paging' => ''
            ), $atts ) );

            $args = array(
                        'posts_per_page'        => $post_limit,
                        'offset'                => $post_offset,
                        'tag'                   => $post_tag_slug,
                        'post_status'           => 'publish',
                        'ignore_sticky_posts'   => true,
                        'orderby'               => $post_sort,
                        'meta_key'              => '_mp_review_post_total_score'
                    );

            // set unique posts if enabled
            if ( (bool)MipTheme_UniquePosts::$unique_posts_enabled ) $args = array_merge($args, array('post__not_in' => MipTheme_UniquePosts::$unique_posts_ids));

            $r = new WP_Query( apply_filters( 'block7_posts_args', $args ) );

            $category_multiple_id   = '';
            $category_display       = '';
            $output                 = '';

            if ($r->have_posts()) :
                $post_counter = 0;

                $ajax_data      = 'data-block="block-07" data-cat="'. $category_multiple_id .'" data-count="'. $post_limit .'" data-max-pages="'. ($r->max_num_pages + 1) .'" data-offset="'. $post_offset .'" data-tag="'. $post_tag_slug .'" data-sort="'. $post_sort .'" data-display="'. $category_display .'" data-img-format-1="'. $image_post_format_first .'" data-img-format-2="'. $image_post_format_second .'" data-img-width-1="'. $image_post_first_width .'" data-img-width-2="'. $image_post_second_width .'" data-img-height-1="'. $image_post_first_height .'" data-img-height-2="'. $image_post_second_height .'" data-text="'. $shorten_text_chars .'"';
                $ajax_block_id  = uniqid('mip-ajax-block-');

                $output .= '<section id="'. $ajax_block_id .'" '. $ajax_data .' class="section-full top-padding cat-reviews news-layout news-lay-2 clearfix">'. ( ( ($section_title != '') || ( isset($post_paging) && ($post_paging == 'header_paging')  && ($r->max_num_pages > 1) ) ) ? '<header><h2>'. ( ( $section_link != '' ) ? '<a href="'. $section_link .'">'. $section_title .'</a>' : $section_title ) .'</h2><span class="borderline"></span>'. ( ( isset($post_paging) && ($post_paging == 'header_paging') && ($r->max_num_pages > 1) ) ? MipTheme_Ajax::setAjaxNav( $ajax_block_id, 'ajax-nav-header' ) : '' ) .'</header>' : '' );
                $output .= '<div class="articles relative clearfix">';

                //$output .= '<section class="section-full top-padding cat-reviews news-layout news-lay-2">'. ( ( $section_title != '' ) ? '<header><h2>'. ( ( $section_link != '' ) ? '<a href="'. $section_link .'">'. $section_title .'</a>' : $section_title ) .'</h2><span class="borderline"></span></header>' : '' );

                $post_ajax                              = new MipTheme_Ajax();
                $post_ajax->ajax_query                  = $r;
                $post_ajax->post_id                     = $post->ID;
                $post_ajax->image_post_format_first     = MipTheme_Ajax::checkImgBfi($image_post_format_first);
                $post_ajax->image_post_format_second    = $image_post_format_second;
                $post_ajax->image_post_first_width      = $image_post_first_width;
                $post_ajax->image_post_first_height     = $image_post_first_height;
                $post_ajax->image_post_second_width     = $image_post_second_width;
                $post_ajax->image_post_second_height    = $image_post_second_height;
                $post_ajax->shorten_text_chars          = $shorten_text_chars;
                $post_ajax->category_multiple_id        = $category_multiple_id;
                $post_ajax->category_display            = $category_display;

                $output .= $post_ajax->formatBlock7();

                $output .= '</div>';

                if ( isset($post_paging) && ($post_paging == 'footer_paging') && ($r->max_num_pages > 1) ) {
                    $output .= MipTheme_Ajax::setAjaxNav( $ajax_block_id );
                }

                $output .= '</section>';
            endif;
            wp_reset_postdata();

            return $output;
        }

        /*
        Load plugin css and javascript files which you may need on front end of your site
        */
        /*public function loadCssAndJs() {
          wp_register_style( 'vc_extend_style', plugins_url('assets/vc_extend.css', __FILE__) );
          wp_enqueue_style( 'vc_extend_style' );

          // If you need any javascript files on front end, here is how you can load them.
          //wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
        }*/

        /*
        Show notice if your plugin is activated but Visual Composer is not
        */
        public function showVcVersionNotice() {
            $plugin_data = get_plugin_data(__FILE__);
            echo '
            <div class="updated">
              <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
            </div>';
        }
    }
    // Finally initialize code
    new MipTheme_VCExtendAddonClass_Block7();

}
