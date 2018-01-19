<?php
function should_delete( $post_id ) {
	$query = new WP_Query( array(
		'post_type'      => 'any',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'post_parent'    => $post_id,
	) );

	$should_delete = empty( $query->posts );

	return $should_delete;
}

$url  = 'latinobuzz.entravision.com';
$path = '/srv/www/wp';

$_SERVER['HTTP_HOST']   = $url;
$_SERVER['SERVER_NAME'] = $url;

include sprintf( '%s/wp-load.php', $path );
        
printf( "Deleting posts from %s...\n", get_bloginfo('name') );
        
$totals  = wp_count_posts();
$current = 0;
$errors  = 0;
$skipped = 0;

while( true ) {         
	$query = new WP_Query( array(
		'post_type'      => 'post',
		'post_status'    => 'pending',
		'posts_per_page' => 200,
	) );
                
	if ( empty( $query->posts ) ) {
		break;
	}

	foreach ( $query->posts as $post ) {
		$current++;

		if ( 1 === $current ||
			0 === $current % 100 ||
			$current === count( $query->posts ) ) {
			$print = true;
		} else {
			$print = false;
		}

		if ( ! should_delete( $post->ID ) ) {
			$skipped++;

			$status = 'Skipped';
		} else if ( ! wp_delete_post( $post->ID, true ) ) {
			$errors++;
		} 

		if ( $print ) {
			printf( "Analyzed %s/%s (%s skipped, %s error/s)...\n", $current, $totals->pending, $skipped, $errors );
		}
	}
}               

printf( "Finished.\n" );
