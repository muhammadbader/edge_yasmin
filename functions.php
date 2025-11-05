<?php
/**
 * Yasmin Child – core functions.
 *
 * Loads the parent theme stylesheet, the child stylesheet,
 * the NEW organized CSS architecture (8 files),
 * and other custom functionality for the theme.
 */

/* -----------------------------------------------------------
 * CRITICAL: New CSS Architecture - Load in EXACT ORDER
 * ---------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', function () {
    /* 1. Parent theme style (keeps Astra's CSS intact) */
    wp_enqueue_style(
        'astra-parent',
        get_template_directory_uri() . '/style.css',
        [],
        defined( 'ASTRA_THEME_VERSION' ) ? ASTRA_THEME_VERSION : null
    );

    /* 2. Child theme main style.css */
    wp_enqueue_style(
        'astra-child',
        get_stylesheet_uri(),
        [ 'astra-parent' ],
        '1.0'
    );

    /* === NEW ORGANIZED CSS ARCHITECTURE === */
    
    /* 3. Design System Variables (FOUNDATION) */
    wp_enqueue_style(
        'kent-variables',
        get_stylesheet_directory_uri() . '/css/1-variables.css',
        [ 'astra-child' ],
        filemtime( get_stylesheet_directory() . '/css/1-variables.css' )
    );
    
    /* 4. Layout Structure */
    wp_enqueue_style(
        'kent-layout',
        get_stylesheet_directory_uri() . '/css/2-layout.css',
        [ 'kent-variables' ],
        filemtime( get_stylesheet_directory() . '/css/2-layout.css' )
    );
    
    /* 5. Reusable Components */
    wp_enqueue_style(
        'kent-components',
        get_stylesheet_directory_uri() . '/css/3-components.css',
        [ 'kent-variables' ],
        filemtime( get_stylesheet_directory() . '/css/3-components.css' )
    );
    
    /* 6. Single Product Pages */
    wp_enqueue_style(
        'kent-single-product',
        get_stylesheet_directory_uri() . '/css/4-single-product.css',
        [ 'kent-variables', 'kent-components' ],
        filemtime( get_stylesheet_directory() . '/css/4-single-product.css' )
    );
    
    /* 7. Producer/Category Archives */
    wp_enqueue_style(
        'kent-producer-archive',
        get_stylesheet_directory_uri() . '/css/5-producer-archive.css',
        [ 'kent-variables', 'kent-components' ],
        filemtime( get_stylesheet_directory() . '/css/5-producer-archive.css' )
    );
    
    /* 8. Header & Navigation */
    wp_enqueue_style(
        'kent-header',
        get_stylesheet_directory_uri() . '/css/6-header.css',
        [ 'kent-variables' ],
        filemtime( get_stylesheet_directory() . '/css/6-header.css' )
    );
    
    /* 9. Product Slider */
    wp_enqueue_style(
        'kent-slider',
        get_stylesheet_directory_uri() . '/css/7-slider.css',
        [ 'kent-variables' ],
        filemtime( get_stylesheet_directory() . '/css/7-slider.css' )
    );
    
    /* 10. Responsive Styles (MUST BE LAST) */
    wp_enqueue_style(
        'kent-responsive',
        get_stylesheet_directory_uri() . '/css/8-responsive.css',
        [ 
            'kent-variables', 
            'kent-layout', 
            'kent-components', 
            'kent-single-product', 
            'kent-producer-archive', 
            'kent-header', 
            'kent-slider' 
        ],
        filemtime( get_stylesheet_directory() . '/css/8-responsive.css' )
    );

    /* 11. Search page styles (conditional) */
    if ( is_search() ) {
        wp_enqueue_style(
            'yasmin-search-css',
            get_stylesheet_directory_uri() . '/css/search.css',
            [ 'kent-responsive' ],
            filemtime( get_stylesheet_directory() . '/css/search.css' )
        );
    }
}, 11 );

/* -----------------------------------------------------------
 * RTL Support - Now handled in 1-variables.css
 * ---------------------------------------------------------- */

// Force RTL body class
function yasmin_add_rtl_body_class($classes) {
    $classes[] = 'rtl';
    return $classes;
}
add_filter('body_class', 'yasmin_add_rtl_body_class');

/* -----------------------------------------------------------
 * Slick Slider Assets
 * ---------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', 'kent_enqueue_slider_assets' );
function kent_enqueue_slider_assets() {
    // Slick CSS
    wp_enqueue_style(
        'kent-slick-css',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
        array(),
        '1.8.1'
    );
    
    // Slick theme (arrows/dots styling)
    wp_enqueue_style(
        'kent-slick-theme',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
        array( 'kent-slick-css' ),
        '1.8.1'
    );
    
    // Slick JS (depends on jQuery)
    wp_enqueue_script(
        'kent-slick-js',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
        array( 'jquery' ),
        '1.8.1',
        true
    );
    
    // Initialize slider
    $init = <<<JS
jQuery(function($){
    $('.kent-product-slider').slick({
        slidesToShow: 3,
        centerMode: true,
        centerPadding: '40px',
        arrows: true,
        dots: true,
        infinite: true,
        speed: 500,
        autoplay: true,
        autoplaySpeed: 3000,
        prevArrow: '<button type="button" class="slick-prev" aria-label="Previous"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#291901" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>',
        nextArrow: '<button type="button" class="slick-next" aria-label="Next"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#291901" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>',
        responsive: [
            { breakpoint: 1024, settings: { slidesToShow: 2, centerPadding: '30px' } },
            { breakpoint: 600,  settings: { slidesToShow: 1, centerPadding: '20px' } }
        ]
    });
});
JS;
    wp_add_inline_script( 'kent-slick-js', $init );
}

/* -----------------------------------------------------------
 * Make sure shortcodes are rendered in REST API responses
 * ---------------------------------------------------------- */
add_filter('the_content', 'do_shortcode');

function apply_shortcodes_in_rest_content( $response, $post, $request ) {
    if ( isset( $response->data['content']['rendered'] ) ) {
        $response->data['content']['rendered'] = apply_filters( 'the_content', $post->post_content );
    }
    return $response;
}
add_filter( 'rest_prepare_page', 'apply_shortcodes_in_rest_content', 10, 3 );

/* -----------------------------------------------------------
 * Producer Taxonomy Registration
 * ---------------------------------------------------------- */
function wpw_register_producer_tax() {
    $labels = [
        'name'          => 'Producers',
        'singular_name' => 'Producer',
        'search_items'  => 'Search Producers',
        'all_items'     => 'All Producers',
        'edit_item'     => 'Edit Producer',
        'update_item'   => 'Update Producer',
        'add_new_item'  => 'Add New Producer',
        'new_item_name' => 'New Producer Name',
        'menu_name'     => 'Producers',
    ];

    /* Attach to BOTH 'product' and 'products' */
    register_taxonomy(
        'producer',
        [ 'product', 'products' ],
        [
            'labels'            => $labels,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'hierarchical'      => false,
            'rewrite'           => [ 'slug' => 'producer' ],
        ]
    );
}
add_action( 'init', 'wpw_register_producer_tax', 20 );

/* -----------------------------------------------------------
 * [show_producers] Shortcode - Compact Version
 * ---------------------------------------------------------- */
function custom_models_debug_output() {
    global $post;
    if ( ! $post ) {
        return '';
    }

    $items = [];
    for ( $i = 0; $i <= 14; $i++ ) {
        $manufacturer = get_post_meta( $post->ID, "models_{$i}_manufacture", true );
        $code         = get_post_meta( $post->ID, "models_{$i}_code", true );
        if ( $manufacturer || $code ) {
            $items[] = [
                'code' => $code ?: '—',
                'manufacturer' => $manufacturer ?: '—'
            ];
        }
    }

    if ( empty( $items ) ) {
        return '';
    }

    $output = '<div class="producers-container" dir="rtl">';

    foreach ( $items as $index => $item ) {
        $output .= sprintf(
            '<div class="producer-item" data-index="%d">
                <div class="producer-row">
                    <div class="producer-field">
                        <span class="producer-label">קוד:</span>
                        <span class="producer-value producer-code">%s</span>
                    </div>
                    <div class="producer-field">
                        <span class="producer-label">יצרן:</span>
                        <span class="producer-value producer-manufacturer">%s</span>
                    </div>
                </div>
            </div>',
            $index,
            esc_html( $item['code'] ),
            esc_html( $item['manufacturer'] )
        );
    }

    $output .= '</div>';
    return $output;
}
add_shortcode( 'show_producers', 'custom_models_debug_output' );

/* -----------------------------------------------------------
 * [show_producers_full] Shortcode - Full Height Version
 * ---------------------------------------------------------- */
function custom_models_full_height_output() {
    global $post;
    if ( ! $post ) {
        return '';
    }
    
    $items = [];
    for ( $i = 0; $i <= 14; $i++ ) {
        $manufacturer = get_post_meta( $post->ID, "models_{$i}_manufacture", true );
        $code         = get_post_meta( $post->ID, "models_{$i}_code", true );
        if ( $manufacturer || $code ) {
            $items[] = [
                'code' => $code ?: '—',
                'manufacturer' => $manufacturer ?: '—'
            ];
        }
    }
    
    if ( empty( $items ) ) {
        return '';
    }
    
    $output = '<div class="producers-container-full" dir="rtl">';
    
    foreach ( $items as $index => $item ) {
        $output .= sprintf(
            '<div class="producer-item" data-index="%d">
                <div class="producer-row">
                    <div class="producer-field">
                        <span class="producer-label">קוד:</span>
                        <span class="producer-value producer-code">%s</span>
                    </div>
                    <div class="producer-field">
                        <span class="producer-label">יצרן:</span>
                        <span class="producer-value producer-manufacturer">%s</span>
                    </div>
                </div>
            </div>',
            $index,
            esc_html( $item['code'] ),
            esc_html( $item['manufacturer'] )
        );
    }
    
    $output .= '</div>';
    return $output;
}
add_shortcode( 'show_producers_full', 'custom_models_full_height_output' );

/* -----------------------------------------------------------
 * Tell Unlimited Elements where to find templates
 * ---------------------------------------------------------- */
add_filter( 'ue_get_twig_templates', function ( $templates ) {
    $templates['producers'] = '[show_producers]';
    $templates['producers_full'] = '[show_producers_full]';
    return $templates;
} );

/* -----------------------------------------------------------
 * [kent_product_slider] Shortcode
 * Shows only products with featured images
 * ---------------------------------------------------------- */
add_shortcode( 'kent_product_slider', 'kent_product_slider_shortcode' );
function kent_product_slider_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'posts_per_page' => 6,
        'orderby'        => 'rand',
    ], $atts, 'kent_product_slider' );

    $query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => intval( $atts['posts_per_page'] ),
        'orderby'        => sanitize_text_field( $atts['orderby'] ),
        'meta_query'     => [
            [
                'key'     => '_thumbnail_id',
                'compare' => 'EXISTS',
            ],
        ],
        'cache_results'  => false,
        'no_found_rows'  => true,
    ]);

    if ( ! $query->have_posts() ) {
        return '<p>לא נמצאו מוצרים עם תמונות להצגה.</p>';
    }

    $html  = '<div class="kent-product-slider">';
    while ( $query->have_posts() ) {
        $query->the_post();
        $html .= '<div class="slider-item">';
        $html .= get_the_post_thumbnail( get_the_ID(), 'medium' );
        $html .= '<h3 class="slide-title"><a href="' . get_permalink() . '">' 
               . get_the_title() . '</a></h3>';
        $html .= '</div>';
    }
    $html .= '</div>';

    wp_reset_postdata();
    return $html;
}

/* -----------------------------------------------------------
 * [kent_test_products] Shortcode - Testing
 * ---------------------------------------------------------- */
function kent_test_products_shortcode() {
    $products = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => 4,
    ]);

    if ( ! $products->have_posts() ) {
        return '<p>No products found.</p>';
    }

    ob_start();
    echo '<div class="kent-test-grid">';
    while ( $products->have_posts() ) {
        $products->the_post();
        echo '<div class="kent-test-item">';
        echo '<h3>' . get_the_title() . '</h3>';
        if ( has_post_thumbnail() ) {
            the_post_thumbnail('medium');
        }
        echo '</div>';
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'kent_test_products', 'kent_test_products_shortcode' );

/* -----------------------------------------------------------
 * Category Grid with Images
 * ---------------------------------------------------------- */

// Add image field to category editor
function my_category_image_field($term) {
    $image_id  = get_term_meta($term->term_id, 'category_image_id', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="category-image">Category Image</label></th>
        <td>
            <input type="hidden" id="category-image" name="category-image" value="<?php echo esc_attr($image_id); ?>">
            <div id="category-image-preview" style="margin-bottom:10px;">
                <?php if ($image_url): ?>
                    <img src="<?php echo esc_url($image_url); ?>" style="max-width:150px;height:auto;">
                <?php endif; ?>
            </div>
            <button class="upload-category-image button">Upload/Add Image</button>
            <button class="remove-category-image button">Remove Image</button>
        </td>
    </tr>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('.upload-category-image').on('click', function(e){
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: 'Select or Upload Category Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#category-image').val(attachment.id);
                $('#category-image-preview').html('<img src="'+attachment.url+'" style="max-width:150px;height:auto;">');
            });
            frame.open();
        });

        $('.remove-category-image').on('click', function(e){
            e.preventDefault();
            $('#category-image').val('');
            $('#category-image-preview').html('');
        });
    });
    </script>
    <?php
}
add_action('category_edit_form_fields', 'my_category_image_field');

// Save category image
function my_save_category_image($term_id) {
    if (isset($_POST['category-image']) && $_POST['category-image'] !== '') {
        update_term_meta($term_id, 'category_image_id', intval($_POST['category-image']));
    } else {
        delete_term_meta($term_id, 'category_image_id');
    }
}
add_action('edited_category', 'my_save_category_image');

// [category_grid] Shortcode
function my_category_grid($atts) {
    $atts = shortcode_atts([
        'parent' => 0,
    ], $atts);
    
    $args = [
        'taxonomy'   => 'category',
        'parent'     => $atts['parent'],
        'hide_empty' => false,
    ];
    
    $categories = get_terms($args);
    
    if (empty($categories) || is_wp_error($categories)) {
        return '<p>No categories found.</p>';
    }
    
    ob_start();
    echo '<div class="category-grid">';
    
    foreach ($categories as $cat) {
        $image_id  = get_term_meta($cat->term_id, 'category_image_id', true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
        $url       = get_category_link($cat->term_id);
        
        echo '<article class="category-card">';
        echo '<a href="' . esc_url($url) . '" class="card-link">';
        
        // Image
        echo '<div class="card-image">';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($cat->name) . '" loading="lazy">';
        } else {
            echo '<div class="no-thumb">📷</div>';
        }
        echo '</div>';
        
        // Title
        echo '<div class="card-body">';
        echo '<h3 class="card-title">' . esc_html($cat->name) . '</h3>';
        echo '</div>';
        
        echo '</a>';
        echo '</article>';
    }
    
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('category_grid', 'my_category_grid');

/* -----------------------------------------------------------
 * Custom Search Placeholder
 * ---------------------------------------------------------- */
function yasmin_custom_search_form( $form ) {
    $form = str_replace(
        'placeholder="Search..."',
        'placeholder="חפש יצרנים, קטגוריות, שמות קנטים"',
        $form
    );
    return $form;
}
add_filter( 'get_search_form', 'yasmin_custom_search_form' );

// Add meta boxes to product edit screen
add_action('add_meta_boxes', 'yasmin_add_product_details_meta_boxes');
function yasmin_add_product_details_meta_boxes() {
    // Product Details meta box
    add_meta_box(
        'product_details',
        'פרטי מוצר',  // Hebrew: Product Details
        'yasmin_product_details_callback',
        'product',  // Your product post type
        'normal',
        'high'
    );
    
    // Product Description meta box
    add_meta_box(
        'product_description',
        'תיאור מוצר',  // Hebrew: Product Description
        'yasmin_product_description_callback',
        'product',  // Your product post type
        'normal',
        'high'
    );
}

// Product Details field callback (simple textarea)
function yasmin_product_details_callback($post) {
    wp_nonce_field('yasmin_save_product_details', 'yasmin_product_details_nonce');
    $value = get_post_meta($post->ID, '_product_details', true);
    ?>
    <div style="direction: rtl;">
        <p style="font-size: 14px; color: #666;">
            הזן פרטים טכניים, מידות, משקל, חומרים וכו'
        </p>
        <textarea 
            name="product_details" 
            id="product_details" 
            rows="6" 
            style="width: 100%; direction: rtl; font-size: 14px; padding: 10px;"
        ><?php echo esc_textarea($value); ?></textarea>
        <p style="font-size: 12px; color: #999;">
            טיפ: השתמש בשורות חדשות לפרטים נפרדים
        </p>
    </div>
    <?php
}

// Product Description field callback (WYSIWYG editor)
function yasmin_product_description_callback($post) {
    wp_nonce_field('yasmin_save_product_description', 'yasmin_product_description_nonce');
    $value = get_post_meta($post->ID, '_product_description', true);
    ?>
    <div style="direction: rtl;">
        <p style="font-size: 14px; color: #666;">
            כתוב תיאור מפורט של המוצר, יתרונות, שימושים וכו'
        </p>
        <?php
        wp_editor($value, 'product_description', array(
            'textarea_name' => 'product_description',
            'media_buttons' => false,
            'textarea_rows' => 10,
            'teeny' => false,
            'tinymce' => array(
                'directionality' => 'rtl',
                'toolbar1' => 'bold,italic,underline,strikethrough,|,bullist,numlist,|,link,unlink,|,undo,redo',
                'toolbar2' => '',
            )
        ));
        ?>
    </div>
    <?php
}

// Save meta box data
add_action('save_post', 'yasmin_save_product_details_meta');
function yasmin_save_product_details_meta($post_id) {
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    // Save Product Details
    if (isset($_POST['yasmin_product_details_nonce']) && 
        wp_verify_nonce($_POST['yasmin_product_details_nonce'], 'yasmin_save_product_details')) {
        
        if (isset($_POST['product_details'])) {
            $details = sanitize_textarea_field($_POST['product_details']);
            update_post_meta($post_id, '_product_details', $details);
        }
    }
    
    // Save Product Description
    if (isset($_POST['yasmin_product_description_nonce']) && 
        wp_verify_nonce($_POST['yasmin_product_description_nonce'], 'yasmin_save_product_description')) {
        
        if (isset($_POST['product_description'])) {
            $description = wp_kses_post($_POST['product_description']);
            update_post_meta($post_id, '_product_description', $description);
        }
    }
}

// Allow contributors/editors to see the meta boxes (not just admins)
add_filter('user_has_cap', 'yasmin_allow_editors_product_fields', 10, 3);
function yasmin_allow_editors_product_fields($allcaps, $caps, $args) {
    // Allow editors and contributors to edit custom fields
    if (isset($args[0]) && $args[0] == 'edit_post') {
        if (isset($allcaps['edit_posts']) && $allcaps['edit_posts']) {
            $allcaps['edit_post_meta'] = true;
            $allcaps['add_post_meta'] = true;
            $allcaps['delete_post_meta'] = true;
        }
    }
    return $allcaps;
}
