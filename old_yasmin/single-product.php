<?php
/**
 * single-product.php
 * Modern single‐product layout for CPT "product" - RTL Version
 */
get_header();
// get your product code
$code = function_exists('get_field')
    ? get_field('product_code')
    : get_post_meta( get_the_ID(), 'product_code', true );
?>


<div class="single-product kent-archive producer-layout">
	<!-- SIDEBAR: ONLY the producer links - Appears on right in RTL -->
  <aside class="producer-sidebar">
    <div class="sidebar-header">
      <h2 class="sidebar-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 
                   1.18 6.88L12 17.77l-6.18 3.25L7 
                   14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        יצרן
      </h2>
    </div>
    <?php
    $producers = get_the_terms( get_the_ID(), 'producer' );
    if ( $producers && ! is_wp_error( $producers ) ) : ?>
      <ul class="producer-list">
        <?php foreach ( $producers as $p ) : ?>
          <li class="producer-item">
            <a href="<?php echo esc_url( get_term_link( $p ) ); ?>" class="producer-link">
              <?php echo esc_html( $p->name ); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </aside>
  <!-- MAIN CONTENT - Comes first in HTML for RTL -->
  <div class="producer-content">
    <!-- — title & content — -->
    <header class="single-header">
      <h1 class="single-title"><?php the_title(); ?></h1>
    </header>
    <div class="single-description">
      <?php the_content(); ?>
    </div>
    
    <!-- — single card wrapper — -->
    <div class="single-card">
      <!-- — clickable image — -->
      <?php if ( has_post_thumbnail() ) :
        $full = get_the_post_thumbnail_url( get_the_ID(), 'full' );
      ?>
        <div class="clickable-image" onclick="openImageModal('<?php echo esc_js( $full ); ?>')">
          <?php the_post_thumbnail( 'large', ['class'=>'single-image'] ); ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- — your producers shortcode — -->
    <div class="single-meta-top">
      <?php echo do_shortcode('[show_producers_full]'); ?>
      <?php if ( $code ) : ?>
        <div class="single-code">קוד: <?php echo esc_html( $code ); ?></div>
      <?php endif; ?>
    </div>
  </div>

  
</div>  <!-- /.producer-layout -->

<?php get_footer(); ?>