<?php
/**
 * Search Results Template
 * Displays search results in grid layout
 */
get_header();

$search_term = get_search_query();
$paged = get_query_var('paged') ?: 1;

$search_query = new WP_Query([
  's'              => $search_term,
  'posts_per_page' => 12,
  'paged'          => $paged,
]);
?>

<div class="producer-layout">
  
  <!-- MAIN CONTENT (Full Width - No Sidebar) -->
  <div class="producer-content">
    
    <!-- Hero Section -->
    <div class="producer-hero-compact">
      <div class="hero-background"></div>
      <div class="hero-pattern"></div>
      <div class="hero-content">
        <div class="hero-badge">חיפוש</div>
        <h1 class="producer-title">תוצאות חיפוש עבור: "<?php echo esc_html($search_term); ?>"</h1>
        <?php if ($search_query->found_posts > 0) : ?>
          <p class="producer-desc">נמצאו <?php echo $search_query->found_posts; ?> תוצאות</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Results Section -->
    <div class="products-section">
      
      <?php if ($search_query->have_posts()) : ?>
        <div class="producer-grid">
          <?php while ($search_query->have_posts()) : $search_query->the_post(); ?>
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
                  <?php if (has_excerpt()) : ?>
                    <div class="card-excerpt"><?php the_excerpt(); ?></div>
                  <?php endif; ?>
                </div>
              </a>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
          <?php
          echo paginate_links([
            'total'     => $search_query->max_num_pages,
            'current'   => $paged,
            'mid_size'  => 2,
            'prev_text' => '« הקודם',
            'next_text' => 'הבא »',
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
          <h2 class="empty-title">אין תוצאות</h2>
          <p class="empty-desc">לא נמצאו תוצאות עבור: "<?php echo esc_html($search_term); ?>"</p>
          <a href="<?php echo home_url('/'); ?>" class="empty-btn">חזרה לדף הבית</a>
        </div>
      <?php endif; ?>
      
    </div>
  </div>

</div>

<?php get_footer(); ?>