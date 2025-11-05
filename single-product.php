<?php
/**
 * single-product.php
 * Single product layout - RTL optimized
 * Now includes Product Details and Description sections
 */
get_header();

$code = function_exists('get_field')
    ? get_field('product_code')
    : get_post_meta(get_the_ID(), 'product_code', true);

// Get custom fields
$product_details = get_post_meta(get_the_ID(), '_product_details', true);
$product_description = get_post_meta(get_the_ID(), '_product_description', true);

// Check if product has producers
$producers = get_the_terms(get_the_ID(), 'producer');
$has_producers = ($producers && !is_wp_error($producers) && count($producers) > 0);

// Add class to layout based on whether producers exist
$layout_class = $has_producers ? 'single-product producer-layout' : 'single-product producer-layout no-sidebar';
?>

<div class="<?php echo esc_attr($layout_class); ?>">
  
  <!-- SIDEBAR (Right in RTL) - Only show if producers exist -->
  <?php if ($has_producers) : ?>
    <aside class="producer-sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          יצרן
        </h2>
      </div>
      
      <ul class="producer-list">
        <?php foreach ($producers as $p) : ?>
          <li class="producer-item">
            <a href="<?php echo esc_url(get_term_link($p)); ?>" class="producer-link">
              <span class="producer-name"><?php echo esc_html($p->name); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>
  <?php endif; ?>

  <!-- MAIN CONTENT (Left in RTL) -->
  <div class="producer-content">
    
    <!-- Title & Short Description -->
    <header class="single-header">
      <h1 class="single-title"><?php the_title(); ?></h1>
    </header>
    
    <?php if (get_the_content()) : ?>
      <div class="single-description">
        <?php the_content(); ?>
      </div>
    <?php endif; ?>
    
    <!-- Product Image -->
    <?php if (has_post_thumbnail()) : 
      $full_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
    ?>
      <div class="single-card">
        <div class="clickable-image" onclick="openImageModal('<?php echo esc_js($full_url); ?>')">
          <?php the_post_thumbnail('large', ['class' => 'single-image']); ?>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Producer Info & Code -->
    <div class="single-meta-top">
      <?php 
      // Only show producer details if producers exist
      if ($has_producers) {
        echo do_shortcode('[show_producers_full]'); 
      }
      ?>
      
      <?php if ($code) : ?>
        <div class="single-code">קוד: <?php echo esc_html($code); ?></div>
      <?php endif; ?>
    </div>
    
    <!-- NEW: Product Details Section -->
    <?php if (!empty($product_details)) : ?>
      <section class="product-details-section">
        <h2 class="section-heading">
          <svg class="section-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
          </svg>
          פרטי מוצר
        </h2>
        <div class="product-details-content">
          <?php echo nl2br(esc_html($product_details)); ?>
        </div>
      </section>
    <?php endif; ?>
    
    <!-- NEW: Product Description Section -->
    <?php if (!empty($product_description)) : ?>
      <section class="product-description-section">
        <h2 class="section-heading">
          <svg class="section-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
          תיאור מפורט
        </h2>
        <div class="product-description-content">
          <?php echo wp_kses_post($product_description); ?>
        </div>
      </section>
    <?php endif; ?>
    
  </div>

</div>

<!-- Image Modal (moved outside main layout) -->
<div class="image-modal" id="imageModal" onclick="closeImageModal()">
  <button class="modal-close" onclick="closeImageModal()" aria-label="Close">×</button>
  <img src="" alt="" class="modal-img" onclick="event.stopPropagation()">
</div>

<script>
function openImageModal(imageUrl) {
  const modal = document.getElementById('imageModal');
  const img = modal.querySelector('.modal-img');
  img.src = imageUrl;
  modal.classList.add('show');
  document.body.classList.add('modal-open');
}

function closeImageModal() {
  const modal = document.getElementById('imageModal');
  modal.classList.remove('show');
  document.body.classList.remove('modal-open');
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeImageModal();
});
</script>

<?php get_footer(); ?>