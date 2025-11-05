<?php
/**
 * Template Name: Search Results
 * Description: Custom search results page using the Astra-Child card/grid styles.
 */
get_header();
$paged = get_query_var( 'paged' ) ?: 1;

// build our own search query, 12 per page
$search_query = new WP_Query( [
    's'              => get_search_query(),
    'posts_per_page' => 12,
    'paged'          => $paged,
] );
?>

<div class="producer-layout">
  <!-- No sidebar on search; main content only -->
  <div class="producer-content">
    <section class="products-section">
      
		
      <?php if ( $search_query->have_posts() ) : ?>
        <div class="producer-grid" id="search-results-container">
          <?php
          while ( $search_query->have_posts() ) :
            $search_query->the_post(); ?>
			
            <article <?php post_class( 'producer-card' ); ?>>
              <a href="<?php the_permalink(); ?>" class="card-link">
                
                <!-- Thumbnail -->
                <div class="card-image">
                  <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'medium' ); ?>
                  <?php else : ?>
                    <div class="no-thumb">
                      <svg class="placeholder-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21,15 16,10 5,21"></polyline>
                      </svg>
                    </div>
                  <?php endif; ?>
                </div>
                
                <!-- Title, excerpt and read-more button -->
                <div class="card-body">
                  <h3 class="card-title"><?php the_title(); ?></h3>
                  <div class="card-producers"><?php echo do_shortcode( '[show_producers]' ); ?></div>
                  <div class="producer-desc">
                    <?php the_excerpt(); ?>
                  </div>
                  
                  <div class="card-footer">
                    <span class="card-btn">
                      <span class="btn-text">המשך לקרוא</span>
                      <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M7 17L17 7M17 7H7M17 7V17"/>
                      </svg>
                    </span>
                  </div>
                </div>
              
              </a>
            </article>
          <?php endwhile; ?>
        </div>
        
        <!-- Pagination -->
       <div class="pagination-wrapper">
          <?php
          echo paginate_links( [
            'total'   => $search_query->max_num_pages,
            'current' => $paged,
            'mid_size'=> 2,
            'prev_text' => '« הקודם',
            'next_text' => 'הבא »',
          ] );
          ?>
        </div>
      
      <?php else : ?>
        <!-- Empty state -->
        <section class="empty-state">
          <div class="empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
              <line x1="9" y1="9" x2="9.01" y2="9"></line>
              <line x1="15" y1="9" x2="15.01" y2="9"></line>
            </svg>
          </div>
          <h3 class="empty-title">אין תוצאות</h3>
          <p class="empty-desc">
            לא נמצאו תוצאות עבור: "<?php echo esc_html( get_search_query() ); ?>"
          </p>
          <a href="<?php echo esc_url( home_url() ); ?>" class="empty-btn">חזור לעמוד הבית</a>
        </section>
      <?php endif; ?>
      
    </section>
  </div>
</div>

<?php get_footer(); ?>
