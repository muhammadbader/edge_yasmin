<?php
/**
 * Template for /producer/{term}/ archives
 * Enhanced modern design for Kent products factory
 * Place this file in your Astra-Child theme.
 */
get_header();

/* ------------------------------------------------------------------
 *  Current term + a list of ALL producer terms
 * ---------------------------------------------------------------- */
$term          = get_queried_object(); // current WP_Term
$all_terms     = get_terms( [
	'taxonomy'   => 'producer',
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
] );
$current_slug  = $term ? $term->slug : '';
$product_count = $term ? $term->count : 0;
?>

<!-- ===== Modern Producer Layout ===== -->
<div class="producer-layout">
	
	<!-- === Enhanced Sidebar === -->
	<aside class="producer-sidebar">
		<div class="sidebar-header">
			<h2 class="sidebar-title">
				<svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
				</svg>
				<?php esc_html_e( 'כל היצרנים', 'astra-child' ); ?>
			</h2>
			<div class="producers-count"><?php echo count($all_terms); ?> יצרנים</div>
		</div>
		
		<div class="producer-search">
			<input  type="text"
        id="producer-filter"
        placeholder="חפש יצרן…"
        class="producer-search-input"
        dir="rtl"   
        style="text-align: right;"
>

			<svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<circle cx="11" cy="11" r="8"></circle>
				<path d="m21 21-4.35-4.35"></path>
			</svg>
		</div>
		
		<ul class="producer-list">
			<?php foreach ( $all_terms as $t ) : ?>
				<li class="producer-item <?php echo $t->slug === $current_slug ? 'current' : ''; ?>" data-name="<?php echo esc_attr(strtolower($t->name)); ?>">
					<a href="<?php echo esc_url( get_term_link( $t ) ); ?>" class="producer-link">
						<span class="producer-name"><?php echo esc_html( $t->name ); ?></span>
						<span class="producer-count"><?php echo $t->count; ?></span>
						<?php if ($t->slug === $current_slug) : ?>
							<svg class="current-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<polyline points="20,6 9,17 4,12"></polyline>
							</svg>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</aside>

	<!-- === Enhanced Main Content === -->
	<div class="producer-content">
		
		<!-- === Modern Hero Section === -->
		<section class="producer-hero">
			<div class="hero-background">
				<div class="hero-pattern"></div>
			</div>
			<div class="hero-content">
				<div class="hero-badge">מוצרי קנטים</div>
				<h1 class="producer-title"><?php echo esc_html( $term->name ); ?></h1>
				<?php if ( $desc = term_description() ) : ?>
					<div class="producer-desc"><?php echo wp_kses_post( $desc ); ?></div>
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

		<!-- === Products Section === -->
		<?php if ( have_posts() ) : ?>
			<section class="products-section">
				<div class="section-header">
					
					<div class="view-controls">
						<button class="view-btn active" data-view="grid" title="תצוגת רשת">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="3" width="7" height="7"></rect>
								<rect x="14" y="3" width="7" height="7"></rect>
								<rect x="14" y="14" width="7" height="7"></rect>
								<rect x="3" y="14" width="7" height="7"></rect>
							</svg>
						</button>
						<button class="view-btn" data-view="list" title="תצוגת רשימה">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<line x1="8" y1="6" x2="21" y2="6"></line>
								<line x1="8" y1="12" x2="21" y2="12"></line>
								<line x1="8" y1="18" x2="21" y2="18"></line>
								<line x1="3" y1="6" x2="3.01" y2="6"></line>
								<line x1="3" y1="12" x2="3.01" y2="12"></line>
								<line x1="3" y1="18" x2="3.01" y2="18"></line>
							</svg>
						</button>
					</div>
					<h2 class="section-title" dir="rtl" style="text-align: right">מוצרי <?php echo esc_html( $term->name ); ?></h2>
				</div>
				
				<div class="producer-grid" id="products-container">
					<?php while ( have_posts() ) : the_post(); ?>
						<article <?php post_class( 'producer-card' ); ?>>
							<a href="<?php the_permalink(); ?>" class="card-link">
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
									<div class="card-overlay">
										<span class="overlay-text">צפה במוצר</span>
									</div>
								</div>
								
								<div class="card-body">
									<h3 class="card-title"><?php the_title(); ?></h3>
									<div class="card-producers">
										<?php echo do_shortcode( '[show_producers]' ); ?>
									</div>
									<div class="card-footer">
										<span class="card-btn">
											<span class="btn-text">הצג כרטיס</span>
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
				
				<div class="pagination-wrapper">
					<?php the_posts_pagination(array(
						'mid_size' => 2,
						'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"></polyline></svg> הקודם',
						'next_text' => 'הבא <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"></polyline></svg>'
					)); ?>
				</div>
			</section>
		<?php else : ?>
			<section class="empty-state">
				<div class="empty-icon">
					<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
						<circle cx="12" cy="12" r="10"></circle>
						<path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
						<line x1="9" y1="9" x2="9.01" y2="9"></line>
						<line x1="15" y1="9" x2="15.01" y2="9"></line>
					</svg>
				</div>
				<h3 class="empty-title">אין מוצרים זמינים</h3>
				<p class="empty-desc">לא נמצאו מוצרים עבור יצרן זה כרגע.</p>
				<a href="<?php echo home_url(); ?>" class="empty-btn">חזור לעמוד הבית</a>
			</section>
		<?php endif; ?>
		
	</div><!-- /.producer-content -->
</div><!-- /.producer-layout -->

<script>
// Enhanced producer page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Producer search functionality
    const searchInput = document.getElementById('producer-filter');
    const producerItems = document.querySelectorAll('.producer-item');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            producerItems.forEach(function(item) {
                const producerName = item.getAttribute('data-name');
                if (producerName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // View toggle functionality
    const viewBtns = document.querySelectorAll('.view-btn');
    const container = document.getElementById('products-container');
    
    viewBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.getAttribute('data-view');
            container.className = container.className.replace(/view-\w+/, '');
            container.classList.add('producer-grid', 'view-' + view);
        });
    });
    
    // Card hover effects
    const cards = document.querySelectorAll('.producer-card');
    cards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>

<?php get_footer(); ?>