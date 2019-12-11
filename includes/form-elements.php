<?php

/*
 * Add CPUBLISHING form elementrs in the form elements select box
 */
add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_buddypress_post_in_groups_elements_to_select', 1, 2 );
function buddyforms_buddypress_post_in_groups_elements_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return $elements_select_options;
	}
	$elements_select_options['buddypress']['label']                       = 'BuddyPress';
	$elements_select_options['buddypress']['class']                       = 'bf_show_if_f_type_post';
	$elements_select_options['buddypress']['fields']['group_select']      = array(
		'label'  => __( 'Groups Select', 'buddyforms' ),
		'unique' => 'unique',
	);
	$elements_select_options['buddypress']['fields']['group_post_author'] = array(
		'label'  => __( 'Author Select', 'buddyforms' ),
		'unique' => 'unique',
	);

	return $elements_select_options;
}

/*
 * Create the new CPUBLISHING Form Builder Form Elements
 *
 */
add_filter( 'buddyforms_form_element_add_field', 'buddyforms_buddypress_post_in_groups_form_builder_form_elements', 1, 5 );
function buddyforms_buddypress_post_in_groups_form_builder_form_elements( $form_fields, $form_slug, $field_type, $field_id ) {
	global $field_position, $buddyforms;


	switch ( $field_type ) {
		case 'group_elect':

			unset( $form_fields );
			break;
		case 'group_post_author':
			unset( $form_fields );
			$name                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['name'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['name'] ) : __( 'Post Author', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$description                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['description'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['description'] ) : __( 'If you change the post author and you are not member of a group with edit rights you will lose the rights to edit and delete the post.', 'buddyforms' );
			$form_fields['general']['description'] = new Element_Textbox( '<b>' . __( 'Description:', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][description]", array( 'value' => $description ) );

			$form_fields['general']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'buddyforms_buddypress_group_author' );
			$form_fields['general']['type']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			$form_fields['general']['order'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][order]", $field_position, array( 'id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order' ) );

			break;
	}

	return $form_fields;
}

/*
 * Display the new CPUBLISHING Fields in the frontend form
 *
 */
add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_buddypress_post_in_groups_frontend_form_elements', 1, 2 );
function buddyforms_buddypress_post_in_groups_frontend_form_elements( $form, $form_args ) {
	global $buddyforms, $nonce;

	extract( $form_args );

	$post_type = $buddyforms[ $form_slug ]['post_type'];

	if ( ! $post_type ) {
		return $form;
	}

	if ( ! isset( $customfield['type'] ) ) {
		return $form;
	}

	switch ( $customfield['type'] ) {
		case 'group_elect':

			$groups_array = array( 'all' => 'All Members' );

			if ( bp_has_groups() ) :

				while ( bp_groups() ) : bp_the_group();

					$groups_array[ bp_the_profile_group_id() ] = bp_the_profile_group_name();

				endwhile;

			endif;

			break;
		case 'group_post_author':


			$blogusers = get_users();

			// Array of WP_User objects.
//			if ( ! ( isset( $customfield['multiple_editors'] ) && is_array( $customfield['multiple_editors'] ) ) ) {
//				$options['none'] = __( 'Select an Editor' );
//			}
			foreach ( $blogusers as $user ) {
				$options[ $user->ID ] = $user->user_nicename;
			}

//			if ( ! empty( $customfield['frontend_reset'][0] ) ) {
//				$element_attr['data-reset'] = 'true';
//			}

			$label = __( 'Select an Author', 'buddyforms' );
//			if ( isset ( $customfield['buddypress_post_in_groups_editors_label'] ) ) {
//				$label = $customfield['buddypress_post_in_groups_editors_label'];
//			}

			$element_attr['class'] = $element_attr['class'] . ' bf-select2';
			$element_attr['value'] = get_post_field( 'post_author', $post_id );
			$element_attr['id']    = 'group-post-authors';


			$element = new Element_Select( $label, 'buddyforms_buddypress_group_author', $options, $element_attr );

			//if ( isset( $customfield['multiple_editors'] ) && is_array( $customfield['multiple_editors'] ) ) {
//			$element->setAttribute( 'multiple', 'multiple' );
			//}

			BuddyFormsAssets::load_select2_assets();

			$form->addElement( $element );


			break;

	}

	return $form;
}


/*
 * Save Fields
 *
 */
//dd_action( 'buddyforms_update_post_meta', 'buddyforms_buddypress_post_in_groups_update_post_meta', 9999, 2 );
function buddyforms_buddypress_post_in_groups_update_post_meta( $customfield, $post_id ) {

	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	if ( $customfield['type'] == 'group_post_author' ) {


		if ( ! isset( $_POST['buddyforms_buddypress_group_author'] ) ) {
			return;
		}
		if ( $_POST['buddyforms_buddypress_group_author'] == get_post_field( 'post_author', $post_id ) ) {
			return;
		}

		$arg         = array(
			'ID'          => $post_id,
			'post_author' => $_POST['buddyforms_buddypress_group_author'],
		);
		$update_post = wp_update_post( $arg, true );
//		update_post_meta( $post_id, 'post_author', $_POST['buddyforms_buddypress_group_author'] );

	}
}

add_action( 'buddyforms_process_submission_end', 'buddyforms_buddypress_group_post_author_process_submission_end' );

function buddyforms_buddypress_group_post_author_process_submission_end( $args ) {

	if ( isset( $args['customfields'] ) && is_array( $args['customfields'] ) ) {
		foreach ( $args['customfields'] as $customfield ) {


			if ( $customfield['type'] == 'group_post_author' ) {


				if ( ! isset( $_POST['buddyforms_buddypress_group_author'] ) ) {
					return;
				}
				if ( $_POST['buddyforms_buddypress_group_author'] == get_post_field( 'post_author', $args['post_id'] ) ) {
					return;
				}

				$arg         = array(
					'ID'          => $args['post_id'],
					'post_author' => $_POST['buddyforms_buddypress_group_author'],
				);
				$update_post = wp_update_post( $arg, true );

			}

		}
	}


}
