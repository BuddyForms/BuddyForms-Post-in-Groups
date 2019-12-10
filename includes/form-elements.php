<?php

/*
 * Add CPUBLISHING form elementrs in the form elements select box
 */
//add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_cpublishing_elements_to_select', 1, 2 );
function buddyforms_cpublishing_elements_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}
	$elements_select_options['cpublishing']['label']                              = 'Colaburative Publishing';
	$elements_select_options['cpublishing']['class']                              = 'bf_show_if_f_type_post';
	$elements_select_options['cpublishing']['fields']['collaborative-publishing'] = array(
		'label' => __( 'Colaburative Publishing', 'buddyforms' ),
	);

	return $elements_select_options;
}

/*
 * Create the new CPUBLISHING Form Builder Form Elements
 *
 */
//add_filter( 'buddyforms_form_element_add_field', 'buddyforms_cpublishing_form_builder_form_elements', 1, 5 );
function buddyforms_cpublishing_form_builder_form_elements( $form_fields, $form_slug, $field_type, $field_id ) {
	global $field_position, $buddyforms;


	switch ( $field_type ) {
		case 'collaborative-publishing':

			//unset( $form_fields );

			$roles = get_editable_roles();

			$roles_array = array( 'all' => 'All Roles' );
			foreach ( $roles as $role_kay => $role ) {
				$roles_array[ $role_kay ] = $role['name'];
			}


			$cpublishing_editors = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors'] ) ) {
				$cpublishing_editors = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors'];
			}
			$form_fields['general']['cpublishing_editors']       = new Element_Select( '<b>' . __( 'Editors', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_editors]", $roles_array, array(
				'value'         => $cpublishing_editors,
				'data-field_id' => $field_id,
				'shortDesc'     => 'You can enable all users or filter the select for a specific user role'
			) );
//			$multiple_editors                                    = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_editors'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_editors'] : 'false';
//			$form_fields['general']['multiple_editors']          = new Element_Checkbox( '<b>' . __( 'Multiple Editors', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_editors]", array( 'multiple_editors' => '<b>' . __( 'Multiple Editors', 'buddyforms' ) . '</b>' ), array( 'value' => $multiple_editors ) );
			$cpublishing_editors_label                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors_label'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors_label'] ) : __( 'Select Editors', 'buddyforms' );
			$form_fields['general']['cpublishing_editors_label'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_editors_label]", array(
				'data'      => $field_id,
				'value'     => $cpublishing_editors_label,
				'shortDesc' => 'Allow one or multiple editors'
			) );


//			$enable_moderation                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_moderation'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_moderation'] : 'false';
//			$form_fields['general']['enable_moderation'] = new Element_Checkbox( '<b>' . __( 'Enable Moderation', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][enable_moderation]", array( 'enable_moderation' => '<b>' . __( 'Enable Moderation', 'buddyforms' ) . '</b>' ),
//				array(
//					'value'              => $enable_moderation,
//					'data'               => $field_id,
//					'class'              => 'bf_enable_moderation_hidden_checkbox',
//					'bf_hidden_checkbox' => 'bf_hide_if_not_multiple_moderators '
//				) );
//
//			$bf_hide_if_not_multiple_moderators = 'bf_hide_if_not_multiple_moderators';
//			if ( $enable_moderation == 'false' ) {
//				$bf_hide_if_not_multiple_moderators = 'bf_hide_if_not_multiple_moderators hidden';
//			}
//
//			$cpublishing_moderators = 'false';
//			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators'] ) ) {
//				$cpublishing_moderators = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators'];
//			}
//			$form_fields['general']['cpublishing_moderators']       = new Element_Select( '<b>' . __( 'Select Moderators', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_moderators]", $roles_array, array(
//				'value'         => $cpublishing_moderators,
//				'data-field_id' => $field_id,
//				'class'         => $bf_hide_if_not_multiple_moderators,
//				'shortDesc'     => 'You can enable all users or filter the select for a specific user role'
//			) );
//			$multiple_moderators                                    = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_moderators'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_moderators'] : 'false';
//			$form_fields['general']['multiple_moderators']          = new Element_Checkbox( '<b>' . __( 'Multiple Moderators', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_moderators]", array( 'multiple_moderators' => '<b>' . __( 'Multiple moderators', 'buddyforms' ) . '</b>' ), array(
//				'value'     => $multiple_moderators,
//				'class'     => $bf_hide_if_not_multiple_moderators,
//				'shortDesc' => 'Allow one or multiple moderators.'
//			) );
//			$cpublishing_moderators_label                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators_label'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators_label'] ) : __( 'Select Moderators', 'buddyforms' );
//			$form_fields['general']['cpublishing_moderators_label'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_moderators_label]", array(
//				'data'  => $field_id,
//				'value' => $cpublishing_moderators_label,
//				'class' => $bf_hide_if_not_multiple_moderators
//			) );


			$enable_teams                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_teams'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_teams'] : 'false';
			$form_fields['general']['enable_teams'] = new Element_Checkbox( '<b>' . __( 'Enable Teams', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][enable_teams]", array( 'enable_teams' => '<b>' . __( 'Enable Teams', 'buddyforms' ) . '</b>' ),
				array(
					'value'              => $enable_teams,
					'data'               => $field_id,
					'class'              => 'bf_enable_teams_hidden_checkbox',
					'bf_hidden_checkbox' => 'bf_hide_if_not_enable_teams '
				) );

			$bf_hide_if_not_enable_teams = 'bf_hide_if_not_enable_teams';
			if ( $enable_teams == 'false' ) {
				$bf_hide_if_not_enable_teams = 'bf_hide_if_not_enable_teams hidden';
			}

			$cpublishing_teams = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_teams'] ) ) {
				$cpublishing_teams = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_teams'];
			}
			$form_fields['general']['cpublishing_teams'] = new Element_Select( '<b>' . __( 'Select a Team Base', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_teams]", buddyforms_cpublishing_get_team_forms(), array(
				'value'         => $cpublishing_teams,
				'data-field_id' => $field_id,
				'class'         => $bf_hide_if_not_enable_teams,
				'shortDesc'     => 'Select a form to use the form post type'
			) );

			$cpublishing_team_label                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_team_label'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_team_label'] ) : __( 'Select a Team', 'buddyforms' );
			$form_fields['general']['cpublishing_team_label'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_team_label]", array(
				'data'  => $field_id,
				'value' => $cpublishing_team_label,
				'class' => $bf_hide_if_not_enable_teams,
			) );


			$invite_by_mail                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_by_mail'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_by_mail'] : 'false';
			$form_fields['general']['invite_by_mail'] = new Element_Checkbox( '<b>' . __( 'Invite by Mail', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][invite_by_mail]", array( 'invite_by_mail' => '<b>' . __( 'Invite by Mail', 'buddyforms' ) . '</b>' ),
				array(
					'value'     => $invite_by_mail,
					'data'      => $field_id,
					'shortDesc' => 'Display an invite by email button',
					'class'     => 'bf_invite_by_mail_hidden_checkbox',
					'bf_hidden_checkbox' => 'bf_hide_if_not_invite_by_mail '
				) );


			$bf_hide_if_not_invite_by_mail = 'bf_hide_if_not_invite_by_mail';
			if ( $invite_by_mail == 'false' ) {
				$bf_hide_if_not_invite_by_mail = 'bf_hide_if_not_invite_by_mail hidden';
			}


			// Get all allowed pages
			$all_pages = buddyforms_get_all_pages( 'id' );

			// After Submission Page
			$invite_register_page                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_register_page'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_register_page'] : 'false';
			$form_fields['general']['invite_register_page'] = new Element_Select( '<b>' . __( "Invite Register Page", 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][invite_register_page]", $all_pages, array(
				'value'     => $invite_register_page,
				'shortDesc' => __( 'Select the Page from where the content gets displayed. Will redirected to the page if ajax is disabled, otherwise display the content.', 'buddyforms' ),
				'class'     => $bf_hide_if_not_invite_by_mail,
			) );


			$invite_message                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_message'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_message'] ) : __( 'You got an invite to edit a post', 'buddyforms' );
			$form_fields['general']['invite_message'] = new Element_Textbox( '<b>' . __( 'Message Text', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][invite_message]", array(
				'data'  => $field_id,
				'value' => $invite_message,
				'class' => $bf_hide_if_not_invite_by_mail,
			) );

			$delete_request_message                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['delete_request_message'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['delete_request_message'] ) : __( 'We share a post I like to delete. Please follow the link to approve the delete request.', 'buddyforms' );
			$form_fields['general']['delete_request_message'] = new Element_Textbox( '<b>' . __( 'Delete Request Message Text', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][delete_request_message]", array(
				'data'  => $field_id,
				'value' => $delete_request_message,
			) );

//			$edit_request_message                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['edit_request_message'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['edit_request_message'] ) : __( 'I like to edit this post', 'buddyforms' );
//			$form_fields['general']['edit_request_message'] = new Element_Textbox( '<b>' . __( 'Edit Request Message Text', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][edit_request_message]", array(
//				'data'  => $field_id,
//				'value' => $edit_request_message,
//			) );


			$form_fields['general']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'CPUBLISHING_field_key' );
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
add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_cpublishing_frontend_form_elements', 1, 2 );
function buddyforms_cpublishing_frontend_form_elements( $form, $form_args ) {
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
		case 'collaborative-publishing':

//			$post_editors = wp_get_object_terms( $post_id, 'buddyforms_editors' );
//			$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts' );

//			ob_start();
//			echo 'Post Editors:<pre>';
//			print_r( $post_editors );
//			echo '</pre>';

//			echo '<br>User Posts<pre>';
//			print_r( $user_posts );
//			echo '</pre>';
//			$JHG = ob_get_clean();

//			$form->addElement( new Element_HTML( $JHG ) );


			if ( $customfield['cpublishing_editors'] == 'all' ) {
				$blogusers = get_users();
			} else {
				$blogusers = get_users( array(
					'role' => $customfield['cpublishing_editors']
				) );
			}
			// Array of WP_User objects.
			if ( ! (isset( $customfield['multiple_editors'] ) && is_array( $customfield['multiple_editors'] )) ) {
				$options['none'] = __( 'Select an Editor' );
			}
			foreach ( $blogusers as $user ) {
				$options[ $user->ID ] = $user->user_nicename;
			}

			if ( ! empty( $customfield['frontend_reset'][0] ) ) {
				$element_attr['data-reset'] = 'true';
			}

			$label = __( 'Select Editors', 'buddyforms' );
			if ( isset ( $customfield['cpublishing_editors_label'] ) ) {
				$label = $customfield['cpublishing_editors_label'];
			}

			$element_attr['class'] = $element_attr['class'] . ' bf-select2';
			$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_editors', true );
			$element_attr['id']    = 'col-lab-editors';


			$element = new Element_Select( $label, 'buddyforms_editors', $options, $element_attr );

			//if ( isset( $customfield['multiple_editors'] ) && is_array( $customfield['multiple_editors'] ) ) {
				$element->setAttribute( 'multiple', 'multiple' );
			//}

			BuddyFormsAssets::load_select2_assets();

			$form->addElement( $element );


//			if ( isset( $customfield['enable_moderation'] ) ) {
//
//				$label = __( 'Select Moderators', 'buddyforms' );
//				if ( isset ( $customfield['cpublishing_moderators_label'] ) ) {
//					$label = $customfield['cpublishing_moderators_label'];
//				}
//
//				$element_attr['class'] = $element_attr['class'] . ' bf-select2';
//				$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_moderators', true );
//				$element_attr['id']    = $customfield['slug'] . '-moderators';
//
//				$element = new Element_Select( $label, 'buddyforms_moderators', $options, $element_attr );
//
//				if ( isset( $customfield['multiple_moderators'] ) && is_array( $customfield['multiple_moderators'] ) ) {
//					$element->setAttribute( 'multiple', 'multiple' );
//				}
//
//				BuddyFormsAssets::load_select2_assets();
//
//				$form->addElement( $element );
//
//			}

			if ( isset( $customfield['enable_teams'] ) ) {
				$label = __( 'Select a Team', 'buddyforms' );
				if ( isset ( $customfield['cpublishing_team_label'] ) ) {
					$label = $customfield['cpublishing_team_label'];
				}

				$element_attr['class'] = $element_attr['class'] . ' bf-select2';
				$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_teams', true );
				$element_attr['id']    = $customfield['slug'] . '-teams';

				$team_forms['none'] = __('Select a Team');
				if ( isset( $customfield['cpublishing_teams'] ) ) {

					$args      = array(
						'post_type'      => $buddyforms[ $customfield['cpublishing_teams'] ][ $post_type ],
						'post_status'    => 'publish',
						'posts_per_page' => 100
					);
					$the_query = new WP_Query( $args );


 					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$team_forms[ get_the_ID() ] = get_the_title();
					}

				}

				$element = new Element_Select( $label, 'buddyforms_teams', $team_forms, $element_attr );


				BuddyFormsAssets::load_select2_assets();

				$form->addElement( $element );

			}

			if ( isset( $customfield['invite_by_mail'] ) ) {
				$element = new Element_HTML( do_shortcode( '[buddyforms_become_an_editor form_slug="' . $form_slug . '" post_id=' . $post_id . ']' ) );

				$form->addElement( $element );


			}

			break;

	}

	return $form;
}


/*
 * Save Fields
 *
 */
add_action( 'buddyforms_update_post_meta', 'buddyforms_cpublishing_update_post_meta', 10, 2 );
function buddyforms_cpublishing_update_post_meta( $customfield, $post_id ) {

	if ( $customfield['type'] == 'collaborative-publishing' ) {

		// Create a editors array to store all editors.
		$editors     = array();
		$old_editors = get_post_meta( $post_id, 'buddyforms_editors', true );
		$old_team    = get_post_meta( $post_id, 'buddyforms_teams', true );

		// Update the editors post meta
		if ( ! empty( $_POST['buddyforms_editors'] ) ) {
			update_post_meta( $post_id, 'buddyforms_editors', $_POST['buddyforms_editors'] );

			// Update the editors array
			foreach ( $_POST['buddyforms_editors'] as $key => $editor ) {
				$editors[ $editor ] = $editor;
			}
		}


		//
		// Update the teams and add all team members to the editors array
		//
		if ( ! empty( $_POST['buddyforms_teams'] ) ) {

			// Update the team post meta
			update_post_meta( $post_id, 'buddyforms_teams', $_POST['buddyforms_teams'] );


			// Make sure the team is not the same post
			if ( $_POST['buddyforms_teams'] != $post_id ) {

				// Get all editors form the team post
				$team_editors = get_post_meta( $_POST['buddyforms_teams'], 'buddyforms_editors', true );

				//Add all team editors to the editors array
				foreach ( $team_editors as $tkey => $teditor ) {
					$editors[ $teditor ] = $teditor;
				}
			}
		}

		// Add all editors to the buddyforms_editors taxonomy
		$term_editors = wp_set_post_terms( $post_id, $editors, 'buddyforms_editors', false );








		// Loop through the old editors and remove them from the buddyforms_user_posts taxonomy
		foreach ( $old_editors as $post_editor ) {
			if ( ! array_key_exists( $post_editor, $editors ) ) {
				wp_remove_object_terms( $post_editor, strval($post_id), 'buddyforms_user_posts', true );
			}
		}

		// Check if the team has changed and if so remove the old team members from the buddyforms_user_posts taxonomy
		if ( $old_team != $_POST['buddyforms_teams'] ) {

			// Gert the old team members
			$old_team_editors = get_post_meta( $_POST['buddyforms_teams'], 'buddyforms_editors', true );

			// Remove the post_id from the old team members
			foreach ( $old_team_editors as $old_post_editor ) {
				wp_remove_object_terms( $old_post_editor, strval($post_id), 'buddyforms_user_posts', true );
			}
		}

		// Loop thru all editors and add the post to the buddyforms_user_posts taxonomy
		foreach ( $editors as $editor_id ) {
			$user_posts = wp_set_object_terms( $editor_id, strval($post_id), 'buddyforms_user_posts', true );
		}

		// Update the Moderators post meta
//		if ( ! empty( $_POST['buddyforms_moderators'] ) ) {
//			$moderators = update_post_meta( $post_id, 'buddyforms_moderators', $_POST['buddyforms_moderators'] );
//		}

	}
}
