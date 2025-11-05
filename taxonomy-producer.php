<?php
/**
 * Producer Taxonomy Archive Template
 * Displays products by producer
 */
get_header();

$producer = get_queried_object();
?>

<div class="producer-layout">
  
  <!-- SIDEBAR (Right in RTL) -->
  <aside class="producer-sidebar">
    <div class="sidebar-header">
      <h2 class="sidebar-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        כל היצרנים
      </h2>
    </div>
    
    <!-- Search Box -->
    <div class="producer-search">
      <input
        type="text"
        id="producer-filter"
        class="producer-search-input"
        placeholder="חפש יצרן..."
        dir="rtl"
      >
      <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
    </div>
    
    <?php
    // Get all producers
    $producers = get_terms([
      'taxonomy'   => 'producer',
      'hide_empty' => false,
      'orderby'    => 'name',
      'order'      => 'ASC',
    ]);
    
    if ($producers && !is_wp_error($producers)) : ?>
      <ul class="producer-list">
        <?php foreach ($producers as $prod) : 
          $is_current = ($prod->term_id === $producer->term_id) ? 'current' : '';
        ?>
          <li class="producer-item <?php echo $is_current; ?>" data-name="<?php echo esc_attr(strtolower($prod->name)); ?>">
            <a href="<?php echo esc_url(get_term_link($prod)); ?>" class="producer-link">
              <span class="producer-name"><?php echo esc_html($prod->name); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </aside>

  <!-- MAIN CONTENT (Left in RTL) -->
  <div class="producer-content">
    
    <!-- Hero Section -->
    <div class="producer-hero-compact">
      <div class="hero-background"></div>
      <div class="hero-pattern"></div>
      <div class="hero-content">
        <div class="hero-badge">יצרן</div>
        <h1 class="producer-title"><?php echo esc_html($producer->name); ?></h1>
        <?php if ($producer->description) : ?>
          <p class="producer-desc"><?php echo esc_html($producer->description); ?></p>
        <?php endif; ?>
        <div class="hero-stats">
          <div class="stat-item">
            <div class="stat-number"><?php echo $producer->count; ?></div>
            <div class="stat-label">מוצרים</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Section -->
    <div class="products-section">
      <div class="section-header">
        <h2 class="section-title">מוצרי <?php echo esc_html($producer->name); ?></h2>
      </div>

      <?php if (have_posts()) : ?>
        <div class="producer-grid">
          <?php while (have_posts()) : the_post(); ?>
            <article class="producer-card">
              <a href="<?php the_permalink(); ?>" class="card-link">
                
                <!-- Image -->
                <div class="card-image">
                  <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium'); ?>
                  <?php else : ?>
                    <div class="no-thumb">
                      <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="m21 15-5-5L5 21"/>
                      </svg>
                    </div>
                  <?php endif; ?>
                  <div class="card-overlay">
                    <span class="overlay-text">צפה במוצר</span>
                  </div>
                </div>

                <!-- Body -->
                <div class="card-body">
                  <h3 class="card-title"><?php the_title(); ?></h3>
                  <?php echo do_shortcode('[show_producers]'); ?>
                </div>
              </a>
            </article>
          <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
          <?php
          the_posts_pagination([
            'mid_size'  => 2,
            'prev_text' => '→',
            'next_text' => '←',
          ]);
          ?>
        </div>

      <?php else : ?>
        <div class="empty-state">
          <div class="empty-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <h2 class="empty-title">אין מוצרים</h2>
          <p class="empty-desc">אין מוצרים של יצרן זה כרגע.</p>
          <a href="<?php echo home_url('/'); ?>" class="empty-btn">חזרה לדף הבית</a>
        </div>
      <?php endif; ?>
      
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('producer-filter');
  const items = document.querySelectorAll('.producer-item');
  
  if (searchInput && items.length > 0) {
    searchInput.addEventListener('input', function() {
      const term = this.value.toLowerCase().trim();
      
      items.forEach(function(item) {
        const name = item.getAttribute('data-name') || '';
        item.style.display = name.includes(term) ? '' : 'none';
      });
    });
  }
});
</script>

<?php get_footer(); ?>