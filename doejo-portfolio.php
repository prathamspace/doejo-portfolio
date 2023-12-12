<?php
/**
 * Plugin Name: Doejo Portfolio
 * Description: Custom portfolio post type.
 * Version: 1.0
 * Author: Your Name
 */

// Register custom post type

/**
 * Importing Styles
 */


// Enqueue your styles
function enqueue_custom_styles()
{
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_style('custom-styles', $plugin_url . 'assets/css/styles.css', array(), '1.0', 'all');
}

// Hook into WordPress to enqueue the styles
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');



function doejo_portfolio_register_post_type()
{
    $labels = array(
        'name' => _x('Portfolio Items', 'post type general name', 'doejo-portfolio'),
        'singular_name' => _x('Portfolio Item', 'post type singular name', 'doejo-portfolio'),
        'menu_name' => _x(' Portfolio', 'admin menu', 'doejo-portfolio'),
        'name_admin_bar' => _x('Portfolio Item', 'add new on admin bar', 'doejo-portfolio'),
        'add_new' => _x('Add New', 'portfolio', 'doejo-portfolio'),
        'add_new_item' => __('Add New Portfolio Item', 'doejo-portfolio'),
        'new_item' => __('New Portfolio Item', 'doejo-portfolio'),
        'edit_item' => __('Edit Portfolio Item', 'doejo-portfolio'),
        'view_item' => __('View Portfolio Item', 'doejo-portfolio'),
        'all_items' => __('All Portfolio Items', 'doejo-portfolio'),
        'search_items' => __('Search Portfolio Items', 'doejo-portfolio'),
        'not_found' => __('No portfolio items found', 'doejo-portfolio'),
        'not_found_in_trash' => __('No portfolio items found in trash', 'doejo-portfolio'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'doejo-portfolio'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
    );

    register_post_type('doejo_portfolio', $args);
}

// Hook into the 'init' action
add_action('init', 'doejo_portfolio_register_post_type');



/**
 * Portfolio URL
 */

// Add meta box for portfolio URL
function doejo_portfolio_add_meta_box()
{
    add_meta_box(
        'doejo_portfolio_url',
        'Portfolio URL',
        'doejo_portfolio_url_callback',
        'doejo_portfolio',
        'normal',
        'default'
    );
}

// Callback function for portfolio URL meta box
function doejo_portfolio_url_callback($post)
{
    // Get existing value of the portfolio URL
    $portfolio_url = get_post_meta($post->ID, '_doejo_portfolio_url', true);

    // Display the input field
    ?>
    <label for="doejo_portfolio_url">Portfolio URL:</label>
    <input type="text" id="doejo_portfolio_url" name="doejo_portfolio_url" value="<?php echo esc_attr($portfolio_url); ?>"
        style="width: 100%;">
    <?php
}

// Save portfolio URL when the post is saved
function doejo_portfolio_save_post($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['doejo_portfolio_url'])) {
        update_post_meta($post_id, '_doejo_portfolio_url', sanitize_text_field($_POST['doejo_portfolio_url']));
    }
}

// Hook into the 'add_meta_boxes' action
add_action('add_meta_boxes', 'doejo_portfolio_add_meta_box');

// Hook into the 'save_post' action
add_action('save_post', 'doejo_portfolio_save_post');
















/**
 * Portfolio Query
 */
function doejo_portfolio_shortcode()
{
    ob_start();

    // Custom query to get doejo_portfolio posts
    $portfolio_query = new WP_Query(array(
        'post_type' => 'doejo_portfolio',
        'posts_per_page' => -1, // Display all posts
        'orderby' => 'date',
        'order' => 'ASC',
    ));


    ?>

    <div class="doejo-plugin-portfolio ">
        <?php
        // Check if there are any posts
        if ($portfolio_query->have_posts()):
            while ($portfolio_query->have_posts()):
                $portfolio_query->the_post();
                // Get the portfolio URL
                $portfolio_url = get_post_meta(get_the_ID(), '_doejo_portfolio_url', true);

                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');


                // Display the image with a hyperlink
                if ($thumbnail_url && $portfolio_url) {
                    ?>
                    <div class="doejo-portfolio-img ">
                        <a href="<?php echo esc_url($portfolio_url); ?>" target="_blank">
                            <img class="doejo_plugin_portfolio" src="<?php echo esc_url($thumbnail_url); ?>"
                                alt="<?php the_title_attribute(); ?>">
                        </a>
                    </div>
                    <?php
                }
                ?>

                <?php
            endwhile;
            // Reset post data
            wp_reset_postdata();
        else:
            // If no posts are found
            echo 'No portfolio items found.';
        endif;
        ?>
    </div>
    <?php
    return ob_get_clean();
}


// Register the shortcode
add_shortcode('doejo_portfolio', 'doejo_portfolio_shortcode');