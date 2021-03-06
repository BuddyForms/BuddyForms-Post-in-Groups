<?php


function buddyforms_post_in_groups_front_js_css_loader( $fount ) {
	return true;
}

add_filter( 'buddyforms_front_js_css_loader', 'buddyforms_post_in_groups_front_js_css_loader', 10, 1 );

/**
 * Locate a template
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function buddyforms_post_in_groups_locate_template( $file ) {
	if ( locate_template( array( $file ), false ) ) {
		locate_template( array( $file ), true );
	} else {
		include( BUDDYFORMS_PIG_TEMPLATE_PATH . $file );
	}
}

function buddyforms_pig_the_loop_actions( $post_id ) {
	if ( ! is_user_logged_in() ) {
		return;
	}

	global $groups_template;
	if ( ! function_exists( 'groups_get_groupmeta' ) || empty( $groups_template ) ) {
		return;
	}

	$group_id = bp_get_group_id();
	//$buddyforms_pig = get_option( 'buddyforms_pig_options' );

	if ( ! $group_id ) {
		return;
	}

	$can_edit   = false;
	$can_delete = false;

	$settings = groups_get_groupmeta( $group_id, '_buddyforms_pig' );

	switch ( $settings['edit'] ) {
		case 'admin':
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			break;
		case 'mod':
			if ( groups_is_user_mod( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			break;
		case 'member':
			if ( groups_is_user_member( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			if ( groups_is_user_mod( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			break;
	}

	switch ( $settings['delete'] ) {
		case 'admin':
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$can_delete = true;
			}
			break;
		case 'mod':
			if ( groups_is_user_mod( get_current_user_id(), $group_id ) ) {
				$can_delete = true;
			}
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$can_delete = true;
			}
			break;
		case 'member':
			if ( groups_is_user_member( get_current_user_id(), $group_id ) ) {
				$can_delete = true;
			}
			if ( groups_is_user_mod( get_current_user_id(), $group_id ) ) {
				$can_delete = true;
			}
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$can_delete = true;
			}
			break;
	}

//	if ( get_the_author_meta( 'ID' ) == get_current_user_id() ) {
//		$can_delete = true;
//		$can_edit   = true;
//	}


	$group_permalink = bp_get_group_permalink() . bp_current_action();
	$post_status     = get_post_status( $post_id );
	$post_status_css = $post_status_name = $post_status;

	if ( $post_status == 'pending' ) {
		$post_status_css = 'bf-pending';
	}

	if ( $post_status == 'publish' ) {
		$post_status_name = 'published';
	}

	?>
	<div class="action">
		<div class="meta">
			<?php if ( ! ( $can_edit == false && $can_delete == false ) ) { ?>
				<div class="item-status"><?php echo $post_status_name; ?></div>
			<?php }
			if ( $can_edit ) {
				echo '<a title="Edit" id="' . $post_id . '" class="bf_edit_post" href="' . $group_permalink . '/edit/' . $post_id . '">' . __( 'Edit', 'buddyforms' ) . '</a>';
			}
			if ( $can_delete ) {
				echo ' - <a title="Delete"  id="' . $post_id . '" class="bf_delete_post" href="#">' . __( 'Delete', 'buddyforms' ) . '</a>';
			}
			?>
		</div>
	</div>
	<?php
}

add_action( 'buddyforms_the_loop_li_last', 'buddyforms_pig_the_loop_actions', 99, 1 );

function buddyforms_pig_the_loop_meta_html( $meta_tmp ) {
	if ( ! bp_is_group_single() ) {
		return $meta_tmp;
	}

	global $groups_template;
	if ( ! function_exists( 'groups_get_groupmeta' ) || empty( $groups_template ) ) {
		return $meta_tmp;
	}

	$group_id = bp_get_group_id();

	if ( ! $group_id ) {
		return $meta_tmp;
	}

	return '';
}

add_filter( 'buddyforms_the_loop_meta_html', 'buddyforms_pig_the_loop_meta_html', 10, 1 );

function buddyforms_pig_user_can_delete( $user_can_delete ) {
	if ( ! bp_is_group_single() ) {
		return $user_can_delete;
	}

	global $groups_template;
	if ( ! function_exists( 'groups_get_groupmeta' ) || empty( $groups_template ) ) {
		return $user_can_delete;
	}

	$group_id = bp_get_group_id();

	if ( ! $group_id ) {
		return $user_can_delete;
	}


	if ( ! is_user_logged_in() ) {
		return $user_can_delete;
	}

	if ( get_the_author_meta( 'ID' ) == get_current_user_id() ) {
		return $user_can_delete;
	}


	$settings = groups_get_groupmeta( $group_id, '_buddyforms_pig' );

	switch ( $settings['delete'] ) {
		case 'admin':
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$user_can_delete = true;
			}
			break;
		case 'mod':
			if ( groups_is_user_mod( get_current_user_id(), $group_id ) ) {
				$user_can_delete = true;
			}
			break;
		case 'member':
			if ( groups_is_user_member( get_current_user_id(), $group_id ) ) {
				$user_can_delete = true;
			}
			break;
	}

	return $user_can_delete;

}

add_filter( 'buddyforms_user_can_delete', 'buddyforms_pig_user_can_delete', 10, 1 );

function bf_pig_loop_edit_post_link( $link, $post_id ) {
	if ( ! is_user_logged_in() ) {
		return $link;
	}

	if ( get_the_author_meta( 'ID' ) != get_current_user_id() ) {
		return $link;
	}

	global $groups_template;
	if ( ! function_exists( 'groups_get_groupmeta' ) || empty( $groups_template ) ) {
		return $link;
	}

	$group_id = bp_get_group_id();

	if ( ! $group_id ) {
		return $link;
	}

	$can_edit = false;

	$settings = groups_get_groupmeta( $group_id, '_buddyforms_pig' );

	switch ( $settings['edit'] ) {
		case 'admin':
			if ( groups_is_user_admin( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			break;
		case 'mod':
			if ( groups_is_user_mod( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			break;
		case 'member':
			if ( groups_is_user_member( get_current_user_id(), $group_id ) ) {
				$can_edit = true;
			}
			break;
	}

	$group_permalink = bp_get_group_permalink() . bp_current_action();

	if ( $can_edit ) {
		return '<a title="Edit" id="' . $post_id . '" class="bf_edit_post" href="' . $group_permalink . '/edit/' . $post_id . '">' . __( 'Edit', 'buddyforms' ) . '</a>';
	}

	return $link;
}

add_filter( 'bf_loop_edit_post_link', 'bf_pig_loop_edit_post_link', 99999, 2 );


/**
 * Redirect to group single posts list, if form settup -> after submit is set to display posts list
 *
 * @param $permalink
 *
 * @return string
 */
function buddyforms_pig_after_save_post_redirect( $permalink ) {
	global $buddyforms;

	$form_slug = buddyforms_sanitize_slug( $_POST['form_slug'] );

	if ( empty( $form_slug ) ) {
		return $permalink;
	}

	if ( ! bp_is_group_single() ) {
		return $permalink;
	}

	if ( isset( $buddyforms[ $form_slug ]['after_submit'] ) ) {
		if ( $buddyforms[ $form_slug ]['after_submit'] == 'display_posts_list' ) {
			$permalink = buddyforms_pig_get_url( '', $form_slug );
		}
	}

	return $permalink;

}

add_filter( 'buddyforms_after_save_post_redirect', 'buddyforms_pig_after_save_post_redirect', 10, 1 );


//add_filter( 'buddyforms_current_user_can', 'buddyforms_pig_current_user_can', 10, 4 );
add_filter( 'buddyforms_user_can_edit', 'buddyforms_pig_current_user_can_edit', 10, 3 );

function buddyforms_pig_current_user_can( $current_user_can, $form_slug, $post, $type ) {
	global $buddyforms_user_can, $groups_template;

	if ( function_exists( 'groups_get_groupmeta' ) && ! empty( $groups_template ) ) {
		$settings = groups_get_groupmeta( bp_get_group_id(), '_buddyforms_pig' );

		if ( $type == 'edit' ) {
			$buddyforms_user_can = empty( $settings['edit'] ) ? false : $settings['edit'];
		}
		if ( $type == 'delete' ) {
			$buddyforms_user_can = empty( $settings['delete'] ) ? false : $settings['delete'];
		}
		if ( $type == 'all' ) {
			$buddyforms_user_can = empty( $settings['create'] ) ? false : $settings['create'];
		}
		if ( get_the_author_meta( 'ID' ) == get_current_user_id() ) {
			$buddyforms_user_can = $current_user_can;
		}
	}

	return $buddyforms_user_can;
}

function buddyforms_pig_current_user_can_edit( $current_user_can ) {
	global $groups_template;
	if ( function_exists( 'groups_get_groupmeta' ) && ! empty( $groups_template ) ) {
		$group_id = bp_get_group_id();
		if ( ! empty( $group_id ) ) {
			$settings = groups_get_groupmeta( $group_id, '_buddyforms_pig' );

			$current_user_can = empty( $settings['edit'] ) ? false : true;

			if ( get_the_author_meta( 'ID' ) == get_current_user_id() ) {
				$current_user_can = true;
			}
		}
	}

	return $current_user_can;
}

function buddyforms_pig_create_submission_link( $default_link, $form_slug, $args ) {
	global $groups_template;
	if ( ! function_exists( 'groups_get_groupmeta' ) || empty( $groups_template ) ) {
		return $default_link;
	}

	if ( empty( $form_slug ) ) {
		return $default_link;
	}

	$buddyforms_pig = get_option( 'buddyforms_pig_options' );
	if ( empty( $buddyforms_pig ) ) {
		return $default_link;
	}

	$create_inside_group = buddyforms_pig_get_url( 'create', $form_slug );
	if ( empty( $create_inside_group ) ) {
		return $default_link;
	}

	return $create_inside_group;
}

add_filter( 'buddyforms_create_submission_link', 'buddyforms_pig_create_submission_link', 10, 3 );

function buddyforms_pig_get_url( $action = '', $form_slug = '' ) {
	$bp_action = bp_current_action();
	if ( empty( $bp_action ) && ! empty( $form_slug ) ) {
		$bp_action = $form_slug;
	}
	$group_permalink = bp_get_groups_directory_permalink();
	$current_group   = groups_get_current_group();
	if ( ! empty( $current_group ) ) {
		$group_permalink .= $current_group->slug . '/';
	}
	$group_permalink .= $bp_action;
	if ( ! empty( $action ) ) {
		$group_permalink .= '/' . $action;
	}

	return esc_url_raw( $group_permalink );
}