<?php

//add_filter( 'buddyforms_formbuilder_fields_options', 'buddyforms_structured_data_formbuilder_fields_options', 10, 4 );
function buddyforms_structured_data_formbuilder_fields_options( $form_fields, $field_type, $field_id, $form_slug = '' ) {
	global $buddyforms;

	$form_fields['StructuredData']['structured_field_type'] = new Element_Textbox( '<b>' . __( 'Field Type', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][structured_field_type]", array(
		'value'    => 'etwas',
		'class'    => '',
		'field_id' => $field_id,
		'id'       => 'buddyforms_structured_data_' . $field_id,
	) );


	return $form_fields;
}


function buddyforms_structured_data_admin_settings_sidebar_metabox() {
	add_meta_box( 'buddyforms_structured_data', __( "Structured Data", 'buddyforms' ), 'buddyforms_structured_data_admin_settings_sidebar_metabox_html', 'buddyforms', 'normal', 'low' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_structured_data', 'buddyforms_metabox_class' );
	//add_filter( 'postbox_classes_buddyforms_buddyforms_structured_data', 'buddyforms_metabox_hide_if_form_type_register' );
	//add_filter( 'postbox_classes_buddyforms_buddyforms_structured_data', 'buddyforms_metabox_hide_if_form_type_register' );
}


function buddyforms_structured_data_admin_settings_sidebar_metabox_html() {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );

	$form_setup = array();

	$structured_data = false;
	if ( isset( $buddyform['structured_data'] ) ) {
		$structured_data = $buddyform['structured_data'];
	}


	ob_start();
	?>{
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
    }<?php

	$structured_data_json = ob_get_clean();
	if ( isset( $buddyform['structured_data_json'] ) ) {
		$structured_data_json = $buddyform['structured_data_json'];
	}


	$form_setup[] = new Element_Checkbox( "<b>" . __( 'Enable Structured Data for this form entries', 'buddyforms' ) . "</b>", "buddyforms_options[structured_data]", array( "structured_data" => "Enable Structured Data" ), array(
		'value'     => $structured_data,
		'shortDesc' => __( 'Find detailed documentation and examples of Structured Data and how google display it in the search result here: <a <a target="_blank" href="https://developers.google.com/search/docs/guides/search-gallery?hl=en" >Google Documentation</a><br><br>' .
		                   ' Test your result with the Google Structured Data Testing Tool <a <a target="_blank" href="https://developers.google.com/search/docs/guides/search-gallery?hl=en" >Google Structured Data Testing</a>', 'buddyforms' )
	) );
	$form_setup[] = new Element_Textarea( "<br><b>" . __( 'json', 'buddyforms' ) . "</b>", "buddyforms_options[structured_data_json]", array(
		'value'     => $structured_data_json,
		'shortDesc' => __( 'This is the default Blog Post Markup from <a target="_blank" href="https://search.google.com/structured-data/testing-tool?utm_campaign=devsite&utm_medium=jsonld&utm_source=article"> here</a>. Adjust it to your needs. You can use any form element value by using the form element slug as shortcode [slug]', 'buddyforms' ),
		'cols'      => '70',
		'rows'      => '25'
	) );

	buddyforms_display_field_group_table( $form_setup );

}

add_filter( 'add_meta_boxes', 'buddyforms_structured_data_admin_settings_sidebar_metabox' );
