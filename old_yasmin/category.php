<?php
/**
 * Category Archive Template
 * Displays products in selected category
 */
get_header();

$category = get_queried_object();
?>

<div class="producer-layout">
  
  <!-- SIDEBAR (Right in RTL) -->
  <aside class="producer-sidebar">
    <div class="sidebar-header">
      <h2 class="sidebar-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        </svg>
        כל התפריט
      </h2>
    </div>
    
    <!-- Search Box -->
    <div class="producer-search">
      <input
        type="text"
        id="producer-filter"
        class="producer-search-input"
        placeholder="חפש בתפריט..."
        dir="rtl"
      >
      <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
    </div>
    
    <?php
    // Get only parent categories (top level)
    $parent_cats = get_categories([
      'taxonomy'   => 'category',
      'parent'     => 0,
      'hide_empty' => false,
      'orderby'    => 'name',
      'order'      => 'ASC',
    ]);
    
    if ($parent_cats) : ?>
      <ul class="producer-list">
        <?php foreach ($parent_cats as $parent) : 
          // Get children of this parent
          $children = get_categories([
            'taxonomy'   => 'category',
            'parent'     => $parent->term_id,
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
          ]);
          
          $has_children = !empty($children);
          $is_current = ($parent->term_id === $category->term_id) ? 'current' : '';
          $parent_classes = ['producer-item'];
          if ($has_children) $parent_classes[] = 'has-children';
          if ($is_current) $parent_classes[] = 'current';
        ?>
          <li class="<?php echo implode(' ', $parent_classes); ?>" data-name="<?php echo esc_attr(strtolower($parent->name)); ?>">
            <a href="<?php echo esc_url(get_category_link($parent->term_id)); ?>" class="producer-link">
              <span class="producer-name"><?php echo esc_html($parent->name); ?></span>
            </a>
            
            <?php if ($has_children) : ?>
              <ul class="producer-sublist">
                <?php foreach ($children as $child) : 
                  $is_child_current = ($child->term_id === $category->term_id) ? 'current' : '';
                ?>
                  <li class="producer-item <?php echo $is_child_current; ?>" data-name="<?php echo esc_attr(strtolower($child->name)); ?>">
                    <a href="<?php echo esc_url(get_category_link($child->term_id)); ?>" class="producer-link">
                      <span class="producer-name"><?php echo esc_html($child->name); ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
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
        <h1 class="producer-title"><?php echo esc_html($category->name); ?></h1>
        <?php if ($category->description) : ?>
          <p class="producer-desc"><?php echo esc_html($category->description); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Products Section -->
    <div class="products-section">
      <div class="section-header">
        <h2 class="section-title">מוצרים</h2>
      </div>

      <?php
      // Query products in this category
      $products = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'orderby'        => 'rand',
        'tax_query'      => [[
          'taxonomy'         => 'category',
          'field'            => 'term_id',
          'terms'            => $category->term_id,
          'include_children' => true,
        ]],
      ]);
      
      if ($products->have_posts()) : ?>
        <div class="producer-grid">
          <?php while ($products->have_posts()) : $products->the_post(); ?>
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
          <?php endwhile; wp_reset_postdata(); ?>
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
          <p class="empty-desc">אין מוצרים בקטגוריה זו כרגע.</p>
          <a href="<?php echo home_url('/'); ?>" class="empty-btn">חזרה לדף הבית</a>
        </div>
      <?php endif; ?>
      
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('producer-filter');
  const items = document.querySelectorAll('.producer-sidebar .producer-item');
  
  if (searchInput && items.length > 0) {
    searchInput.addEventListener('input', function() {
      const term = this.value.toLowerCase().trim();
      
      items.forEach(function(item) {
        const name = item.getAttribute('data-name') || '';
        
        // Check if item has children
        const hasChildren = item.classList.contains('has-children');
        const sublist = item.querySelector('.producer-sublist');
        
        if (term) {
          // Show/hide based on search
          if (name.includes(term)) {
            item.style.display = '';
            // If parent matches, show all children
            if (hasChildren && sublist) {
              sublist.style.display = 'block';
              sublist.querySelectorAll('.producer-item').forEach(child => {
                child.style.display = '';
              });
            }
          } else {
            // Check if any children match
            if (hasChildren && sublist) {
              const childMatches = Array.from(sublist.querySelectorAll('.producer-item')).some(child => {
                const childName = child.getAttribute('data-name') || '';
                return childName.includes(term);
              });
              
              if (childMatches) {
                item.style.display = '';
                sublist.style.display = 'block';
                // Show only matching children
                sublist.querySelectorAll('.producer-item').forEach(child => {
                  const childName = child.getAttribute('data-name') || '';
                  child.style.display = childName.includes(term) ? '' : 'none';
                });
              } else {
                item.style.display = 'none';
              }
            } else {
              item.style.display = 'none';
            }
          }
        } else {
          // Reset when search is empty
          item.style.display = '';
          if (hasChildren && sublist) {
            sublist.style.display = '';
          }
        }
      });
    });
  }
});
</script>

<?php get_footer(); ?>