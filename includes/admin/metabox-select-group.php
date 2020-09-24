<?php

add_action('add_meta_boxes', 'buddyforms_add_groups_selector_metabox');
function buddyforms_add_groups_selector_metabox() {
	global $buddyforms;

	$group_forms = array();
	$buddyforms_pig = get_option( 'buddyforms_pig_options' );

	if ( isset( $buddyforms_pig['forms'] ) && is_array( $buddyforms_pig['forms'] ) ) {
		$group_forms = $buddyforms_pig['forms'];
	}
	
	foreach ( $group_forms as $group_form ) {

		$form = $buddyforms[$group_form];
		add_meta_box(
			'wporg_box_id',
			__( 'Group', 'buddyforms' ),
			'buddyforms_group_selector_metabox_callback',
			$form['post_type'],
			'side',
			'high'
		);
	}
}

function buddyforms_group_selector_metabox_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'buddyforms_bp_group_selector', 'buddyforms_bp_group_selector_nonce' );

	$all_groups = groups_get_groups( array( 'per_page' => -1 ) );
	$selected_group = (int) get_post_meta( $post->ID, 'buddyforms_buddypress_group', true );

	echo '<label for="buddyforms_buddypress_group">';
	_e( 'Select a group', 'buddyforms' );
	echo '</label> ';

	echo ' <p><select name="buddyforms_buddypress_group" id="buddyforms_buddypress_group">';
	echo ' <option value="none">' . __( 'None', 'buddyforms' ) . '</option>';

	if ( $all_groups['total'] > 0 ) {
		foreach ($all_groups['groups'] as $group) {

			$selected = '';
			if ( $selected_group === $group->id ) {
				$selected = ' selected';
			}

			echo '<option ' . $selected .  ' value="' . $group->id . '">' . $group->name . '</option>';	
		}
	}

	echo '</select></p>';

}


add_action( 'save_post', 'buddyforms_save_group_selector_metadata' );
function buddyforms_save_group_selector_metadata( $post_id ) {

    if ( ! is_admin() ) {
		return $post_id;
    }
    
    if ( ! isset( $_POST['buddyforms_bp_group_selector_nonce'] ) ) {
		return $post_id;
    }
    
    $nonce = $_POST['buddyforms_bp_group_selector_nonce'];

    // Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'buddyforms_bp_group_selector' ) ) {
		return $post_id;
    }
    
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
    }
    
    // Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
    }

    /** OK, it's safe now */
    
    $form_slug = sanitize_text_field( $_POST['buddyforms_buddypress_group'] );

    // Update the form slug for this post
	update_post_meta( $post_id, 'buddyforms_buddypress_group', $form_slug );

	return $post_id;

}