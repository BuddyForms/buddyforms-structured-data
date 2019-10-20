<?php

add_action( 'wp_head', 'buddyforms_generate_structured_data', '999' );
function buddyforms_generate_structured_data() {
	global $buddyforms, $post;

	ob_start();


	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $form_field ) {

			echo '<pre>';
			print_r( $form_field );
			echo '</pre>';
		}
	}


	?>
    <script type="application/ld+json">
	    {
		    "@context": "https://schema.org",
		    "@type": "NewsArticle",
		    "mainEntityOfPage": {
			    "@type": "WebPage",
			    "@id": "https://google.com/article"
		    },
		    "headline": "Article headline",
		    "image": [
			    "https://example.com/photos/1x1/photo.jpg",
			    "https://example.com/photos/4x3/photo.jpg",
			    "https://example.com/photos/16x9/photo.jpg"
		    ],
		    "datePublished": "2015-02-05T08:00:00+08:00",
		    "dateModified": "2015-02-05T09:20:00+08:00",
		    "author": {
			    "@type": "Person",
			    "name": "John Doe"
		    },
		    "publisher": {
			    "@type": "Organization",
			    "name": "Google",
			    "logo": {
				    "@type": "ImageObject",
				    "url": "https://google.com/logo.jpg"
			    }
		    },
		    "description": "A most wonderful article"
	    }
    </script>
	<?php

	$tmp = ob_get_clean();
	echo $tmp;
}