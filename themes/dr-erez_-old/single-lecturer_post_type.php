<?php
get_header();

if ( have_posts() ) :
  while ( have_posts() ) : the_post();
?>

<main class="lecturer-single">

  <section class="lecturer-hero">
    <h1 class="lecturer-name"><?php the_title(); ?></h1>

    <?php if ( has_post_thumbnail() ) : ?>
      <div class="lecturer-image">
        <?php the_post_thumbnail('large'); ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="lecturer-details">

    <?php if ( get_field('title') ) : ?>
      <p class="lecturer-title">
        <?php the_field('title'); ?>
      </p>
    <?php endif; ?>

  </section>

  <section class="lecturer-content">
    <?php the_content(); ?>
  </section>

</main>

<?php
  endwhile;
endif;

get_footer();
