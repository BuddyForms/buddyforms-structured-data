<?php

add_action( 'wp_head', 'buddyforms_generate_structured_data', '999' );
function buddyforms_generate_structured_data() {
	global $buddyforms, $post, $wp_query;


	if ( bp_is_user_profile() ) {
		$member_type = bp_get_member_type( bp_displayed_user_id() );
		$form_slug   = buddyforms_members_get_form_by_member_type( $member_type );
	} else {
		$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );
	}

	if ( ! isset( $buddyforms[ $form_slug ]['structured_data'] ) ) {
		return;
	}

	$form_type = $buddyforms[ $form_slug ]['form_type'];
	$json      = $buddyforms[ $form_slug ]['structured_data_json'];

	if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $form_field ) {


			if ( $buddyforms[ $form_slug ]['form_type'] == 'post' ) {
				if ( $form_field['type'] == 'featured_image' ) {

//					$json = buddyforms_replace_shortcode_for_value( $json, '[featured_image]', get_the_post_thumbnail_url( $post->ID, 'full' ) );
//					$json = buddyforms_replace_shortcode_for_value( $json, '[featured_image_medium]', get_the_title( $post->ID ) );
//					$json = buddyforms_replace_shortcode_for_value( $json, '[featured_image_full]', get_the_title( $post->ID ) );
				}


				$permalink      = get_permalink( $post->ID );
				$author_name    = get_the_author_meta( 'display_name', $post->post_author );
				$date_published = get_the_time( DATE_ISO8601, $post->ID );
				$date_modified  = get_post_modified_time( DATE_ISO8601, __return_false(), $post->ID );


				$json = buddyforms_replace_shortcode_for_value( $json, '[permalink]', $permalink );
				$json = buddyforms_replace_shortcode_for_value( $json, '[author_name]', $author_name );
				$json = buddyforms_replace_shortcode_for_value( $json, '[date_published]', $date_published );
				$json = buddyforms_replace_shortcode_for_value( $json, '[date_modified]', $date_modified );

				//$parent_id = wp_get_post_parent_id($post->ID);


				$tispost   = get_post( $post->ID );
				$parent_id = $tispost->post_parent;

				$parent_post       = get_post( $parent_id );
				$parent_post_title = $parent_post->post_title;
				$json              = buddyforms_replace_shortcode_for_value( $json, '[projekte]', $parent_post_title );


				// DISPLAY Adresse
				$adresse = bf_geo_my_wp_get_content_address_by_component( $post->ID, 'location' );

				if ( isset( $adresse[0] ) ) :
					$json = buddyforms_replace_shortcode_for_value( $json, '[location]', strip_tags( $adresse[0] ) );
				endif;

				$value = buddyforms_get_field_with_meta( $form_slug, $post->ID, $form_field['slug'] );
				$json  = buddyforms_replace_shortcode_for_value( $json, '[' . $form_field['slug'] . ']', strip_tags( $value['value'] ) );
			}

			if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' ) {

				$profile_link = bp_core_get_userlink( bp_displayed_user_id() );
				$json         = buddyforms_replace_shortcode_for_value( $json, '[profile_link]', strip_tags( $profile_link ) );


				switch ( $form_field['type'] ) {
					case 'profile_picture':


						$url = bp_core_fetch_avatar(
							array(
								'item_id' => bp_displayed_user_id(), // id of user for desired avatar
								'type'    => 'full',
								'html'    => false     // FALSE = return url, TRUE (default) = return img html
							)
						);

						$json = buddyforms_replace_shortcode_for_value( $json, '[profile_picture]', strip_tags( $url ) );
						break;
					case 'xprofile_field':

						$value = xprofile_get_field_data( $form_field['name'], bp_displayed_user_id() );

						$value_array = $value;
						if ( is_array( $value_array ) ) {
							$value = '';
							foreach ( $value_array as $val ) {
								$value .= $val;
							}
						}

						$json = buddyforms_replace_shortcode_for_value( $json, '[' . $form_field['name'] . ']', strip_tags( $value ) );

						break;
					case 'xprofile_group':
						break;

					case 'geo_my_wp_address':
						if ( $form_type !== 'registration' ) {
							$location_data = bf_geo_my_wp_get_content_address_by_component( $post->ID, $form_field['slug'] );
						} else {
							$location_data = bf_geo_my_wp_get_user_address_by_component( bp_displayed_user_id(), $form_field['slug'] );
						}
						if ( isset( $location_data[0] ) ) {
							$json = buddyforms_replace_shortcode_for_value( $json, '[location]', strip_tags( $location_data[0] ) );
						}
						break;
				}

				$value = buddyforms_get_field_with_meta( $form_slug, $post->ID, $form_field['slug'] );
				$json  = buddyforms_replace_shortcode_for_value( $json, '[' . $form_field['slug'] . ']', strip_tags( $value['value'] ) );

			}


		}
	}

	echo '<script type="application/ld+json">' . $json . '</script>';

}

