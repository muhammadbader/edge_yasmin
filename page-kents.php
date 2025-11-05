<?php
/**
 * Template Name: Kent Products Archive RTL
 * Description: Displays the "product" CPT filtered by the first word of the Page title with RTL support.
 */

get_header();

// 1) Figure out the substring from the Page title
$page   = get_queried_object();
$title  = $page->post_title;               
$words  = preg_split( '/\s+/', trim( $title ) );
$substr = $words[0] ?? '';

// 2) Grab all categories, then filter by that substring
$all_cats = get_terms([
  'taxonomy'   => 'category',
  'hide_empty' => false,
  'orderby'    => 'name',
  'order'      => 'ASC',
]);

$matched = array_filter( $all_cats, function( $c ) use ( $substr ) {
  return stripos( $c->name, $substr ) !== false;
});

// collect term_taxonomy_ids & sum up counts
$tt_ids        = wp_list_pluck( $matched, 'term_taxonomy_id' );
$product_count = array_reduce( $matched, function( $sum, $t ) {
  return $sum + $t->count;
}, 0 );

// 3) Query the "product" CPT
$paged    = max(1, get_query_var('paged'));
$products = new WP_Query([
  'post_type'      => 'product',
  'posts_per_page' => 16,
  'paged'          => $paged,
  'tax_query'      => [[
    'taxonomy'         => 'category',
    'field'            => 'term_taxonomy_id',
    'terms'            => $tt_ids,
    'include_children' => false,
    'operator'         => 'IN',
  ]],
]);
?>

<div class="kent-archive producer-layout rtl-layout" style="background: var(--secondary-color); min-height: 100vh;">

  <!-- Main content -->
  <div class="producer-content">

    <!-- Compact Hero -->
    <section class="producer-hero-compact">
      <div class="hero-background"><div class="hero-pattern"></div></div>
      <div class="hero-content">
        <div class="hero-badge">מוצרי קנטים</div>
        <h1 class="producer-title"><?php echo esc_html( $title ); ?></h1>
        <?php if ( $page->post_content ) : ?>
          <div class="producer-desc">
            <?php echo wp_kses_post( apply_filters( 'the_content', $page->post_content ) ); ?>
          </div>
        <?php endif; ?>
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-number"><?php echo $product_count; ?></span>
            <span class="stat-label">מוצרים</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">✓</span>
            <span class="stat-label">איכות מובטחת</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Products Grid -->
    <?php if ( $products->have_posts() ) : ?>
      <section class="products-section">
        <div class="section-header">
          <div class="view-controls">
            <button class="view-btn active" data-view="grid" title="תצוגת רשת">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
              </svg>
            </button>
            <button class="view-btn" data-view="list" title="תצוגת רשימה">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="8" y1="6" x2="21" y2="6" />
                <line x1="8" y1="12" x2="21" y2="12" />
                <line x1="8" y1="18" x2="21" y2="18" />
                <line x1="3" y1="6" x2="3.01" y2="6" />
                <line x1="3" y1="12" x2="3.01" y2="12" />
                <line x1="3" y1="18" x2="3.01" y2="18" />
              </svg>
            </button>
          </div>
        </div>

        <div class="producer-grid" id="products-container">
          <?php while ( $products->have_posts() ) : $products->the_post(); ?>
            <article <?php post_class( 'producer-card' ); ?>>
              <a href="<?php the_permalink(); ?>" class="card-link">
                <div class="card-image">
                  <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'medium' ); ?>
                  <?php else : ?>
                    <div class="no-thumb">
                      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21,15 16,10 5,21" />
                      </svg>
                    </div>
                  <?php endif; ?>
                  <div class="card-overlay">
                    <span class="overlay-text">צפה במוצר</span>
                  </div>
                </div>
                <div class="card-body">
                  <h3 class="card-title"><?php the_title(); ?></h3>
                  <div class="card-producers"><?php echo do_shortcode( '[show_producers]' ); ?></div>
                  <div class="card-footer">
                    <span class="card-btn">
                      <span class="btn-text">הצג כרטיס</span>
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M7 17L17 7M17 7H7M17 7V17"/>
                      </svg>
                    </span>
                  </div>
                </div>
              </a>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <div class="pagination-wrapper">
          <?php
            echo paginate_links( [
              'total'     => $products->max_num_pages,
              'current'   => $paged,
              'prev_text' => '< הקודם',
              'next_text' => 'הבא >',
            ] );
          ?>
        </div>
      </section>
    <?php else : ?>
      <section class="empty-state">
        <div class="empty-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <circle cx="12" cy="12" r="10" />
            <path d="M16 16s-1.5-2-4-2-4 2-4 2" />
            <line x1="9" y1="9" x2="9.01" y2="9" />
            <line x1="15" y1="9" x2="15.01" y2="9" />
          </svg>
        </div>
        <h3 class="empty-title">אין מוצרים זמינים</h3>
        <p class="empty-desc">לא נמצאו מוצרים עבור "<?php echo esc_html( $title ); ?>" כרגע.</p>
        <a href="<?php echo esc_url( home_url() ); ?>" class="empty-btn">חזור לעמוד הבית</a>
      </section>
    <?php endif; ?>

  </div><!-- /.producer-content -->

  <!-- Sidebar -->
  <aside class="producer-sidebar">
    <!-- Header Title -->
    <div class="sidebar-header">
      <h2 class="sidebar-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        כל התפריט
      </h2>
    </div>

    <!-- 1) Live Filter/Search -->
    <div class="producer-search">
      <input 
        id="producer-filter"
        class="producer-search-input"
        type="text"
        placeholder="חפש בתפריט…" 
        dir="rtl"
      >
      <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
    </div>

    <?php
    // 2) Grab your Secondary menu by location key:
    $locations = get_nav_menu_locations();
    if ( ! empty( $locations['secondary_menu'] ) ) {
      $items = wp_get_nav_menu_items( $locations['secondary_menu'] );
      echo '<ul class="producer-list">';
      $current_id = get_queried_object_id();

      foreach ( $items as $item ) {
        // only top-level
        if ( $item->menu_item_parent != 0 ) {
          continue;
        }
        // skip יצרנים
        if ( trim( $item->title ) === 'יצרנים' ) {
          continue;
        }

        // mark current page
        $active = ( intval( $item->object_id ) === $current_id ) ? ' current' : '';

        printf(
          '<li class="producer-item%s" data-name="%s">',
          $active,
          esc_attr( strtolower( $item->title ) )
        );
        printf(
          '<a href="%s" class="producer-link"><span class="producer-name">%s</span></a>',
          esc_url( $item->url ),
          esc_html( $item->title )
        );
        echo '</li>';
      }

      echo '</ul>';
    }
    ?>
  </aside>

</div><!-- /.kent-archive -->

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('producer-filter');
  const items       = document.querySelectorAll('.producer-item');
  searchInput?.addEventListener('input', e => {
    const term = e.target.value.toLowerCase();
    items.forEach(i => i.style.display = i.dataset.name.includes(term) ? 'block' : 'none');
  });

  const viewBtns = document.querySelectorAll('.view-btn');
  const container = document.getElementById('products-container');
  viewBtns.forEach(btn => btn.addEventListener('click', function() {
    viewBtns.forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    container.classList.toggle('view-list', this.dataset.view === 'list');
  }));

  document.querySelectorAll('.producer-card').forEach(c => {
    c.addEventListener('mouseenter', () => c.style.transform = 'translateY(-8px) scale(1.02)');
    c.addEventListener('mouseleave', () => c.style.transform = '');
  });
});
</script>

<?php get_footer(); ?>