<?php
/**
 * Template Name: All Kents
 * Description: Shows every Kent product in random order with the standard card/grid styling plus the "Secondary" menu sidebar.
 */
get_header();
?>

<div class="producer-layout">

<!-- === Sidebar (Kent pages: show secondary menu + open submenus) === -->
<aside class="producer-sidebar">
  <!-- Header Title -->
  <div class="sidebar-header">
    <h2 class="sidebar-title">
      <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
      </svg>
      כל התפריט
    </h2>
  </div>

  <!-- Live Filter/Search -->
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
  $locations = get_nav_menu_locations();
  if ( ! empty( $locations['secondary_menu'] ) ) {
    $items      = wp_get_nav_menu_items( $locations['secondary_menu'] );
    $current_id = get_queried_object_id();

    // collect only top-level items
    $parents = array_filter( $items, function( $i ) { return (int) $i->menu_item_parent === 0; } );

    echo '<ul class="producer-list">';

    foreach ( $parents as $item ) {
      // find children of this item
      $children = array_filter( $items, function( $i ) use ( $item ) {
        return (int) $i->menu_item_parent === (int) $item->ID;
      } );

      $has_kids = ! empty( $children );

      // build classes
      $li_classes = [ 'producer-item' ];
      if ( $has_kids )  $li_classes[] = 'has-children';
      if ( (int) $item->object_id === $current_id ) $li_classes[] = 'current';

      printf(
        '<li class="%s" data-name="%s">',
        implode( ' ', $li_classes ),
        esc_attr( strtolower( $item->title ) )
      );

      // the link
      printf(
        '<a href="%s" class="producer-link"><span class="producer-name">%s</span></a>',
        esc_url( $item->url ),
        esc_html( $item->title )
      );

      // if there are sub-items, render them
      if ( $has_kids ) {
        echo '<ul class="producer-sublist">';
        foreach ( $children as $child ) {
          $child_classes = [ 'producer-item' ];
          if ( (int) $child->object_id === $current_id ) $child_classes[] = 'current';

          printf(
            '<li class="%s" data-name="%s"><a href="%s" class="producer-link"><span class="producer-name">%s</span></a></li>',
            implode( ' ', $child_classes ),
            esc_attr( strtolower( $child->title ) ),
            esc_url( $child->url ),
            esc_html( $child->title )
          );
        }
        echo '</ul>';
      }

      echo '</li>';
    }

    echo '</ul>';
  }
  ?>
</aside>

  <!-- === Main Content === -->
  <div class="producer-content">
    <section class="products-section">

      <!-- Page Title -->
      <div class="section-header" dir="rtl">
        <h2 class="section-title" style="text-align: right">
          כל מוצרי קנטים
        </h2>
      </div>

      <?php
      // Fetch all Kent products in random order
      $all_kents = new WP_Query( [
        'post_type'      => 'product', // adjust this if your CPT is different
        'posts_per_page' => -1,
        'orderby'        => 'rand',
      ] );
      ?>

      <?php if ( $all_kents->have_posts() ) : ?>
        <div class="producer-grid" id="all-kents-container">
          <?php while ( $all_kents->have_posts() ) : $all_kents->the_post(); ?>
            <article <?php post_class( 'producer-card' ); ?>>
              <a href="<?php the_permalink(); ?>" class="card-link">

                <!-- Thumbnail / Placeholder -->
                <div class="card-image">
                  <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'medium' ); ?>
                  <?php else : ?>
                    <div class="no-thumb">
                      <svg class="placeholder-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21,15 16,10 5,21"/>
                      </svg>
                    </div>
                  <?php endif; ?>
                </div>

                <!-- Title, Producers, Excerpt & Button -->
                <div class="card-body">
                  <h3 class="card-title"><?php the_title(); ?></h3>

                  <!-- your existing shortcode -->
                  <div class="card-producers">
                    <?php echo do_shortcode( '[show_producers]' ); ?>
                  </div>

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
          <?php endwhile; wp_reset_postdata(); ?>
        </div>

      <?php else : ?>
        <section class="empty-state">
          <div class="empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <circle cx="12" cy="12" r="10"/>
              <path d="M16 16s-1.5-2-4-2-4 2-4 2"/>
              <line x1="9" y1="9" x2="9.01" y2="9"/>
              <line x1="15" y1="9" x2="15.01" y2="9"/>
            </svg>
          </div>
          <h3 class="empty-title">אין מוצרים להצגה</h3>
          <p class="empty-desc">כרגע אין מוצרים זמינים להצגה.</p>
        </section>
      <?php endif; ?>

    </section>
  </div><!-- /.producer-content -->

</div><!-- /.producer-layout -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const input = document.getElementById('producer-filter');
  if (!input) return;

  input.addEventListener('input', function() {
    const term = this.value.trim().toLowerCase();

    // 1) Show/hide every <li class="producer-item">
    document.querySelectorAll('.producer-sidebar .producer-item').forEach(item => {
      const name = (item.dataset.name || '').toLowerCase();
      item.style.display = name.includes(term) ? '' : 'none';
    });

    // 2) For each parent with children, check if any child remains visible
    document.querySelectorAll('.producer-sidebar .producer-item.has-children').forEach(parent => {
      const sublist = parent.querySelector('.producer-sublist');
      if (!sublist) return;

      if (term) {
        // Are there any sub‐items still visible?
        const anyChildVisible = Array.from(
          sublist.querySelectorAll('.producer-item')
        ).some(child => child.style.display !== 'none');

        if (anyChildVisible) {
          // Show the parent and force‐open its submenu
          parent.style.display = '';
          sublist.style.display = 'block';
        } else {
          // No child matches: hide parent, reset submenu
          parent.style.display = 'none';
          sublist.style.display = '';
        }
      } else {
        // Empty search: reset everything
        parent.style.display = '';
        sublist.style.display = '';
      }
    });
  });
});
</script>
<?php get_footer(); ?>
