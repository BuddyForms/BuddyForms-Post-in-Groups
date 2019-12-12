<?php

global $buddyforms_user_can, $wp_query, $current_user, $the_lp_query, $bp, $buddyforms, $buddyforms_member_tabs, $form_slug, $paged;
$group_id        = bp_get_group_id();
$group_permalink = bp_get_group_permalink() . bp_current_action();

?>

    <nav class="bp-navs bp-subnavs no-ajax group-subnav" id="subnav" role="navigation"
         aria-label="Group administration menu">

        <ul class="subnav">

            <li id="view-post-details-groups-li"
                class="<?php echo bp_action_variable() == '' ? 'current selected' : ''; ?> bp-groups-admin-tab"><a
                        id="view-post-details"
                        class="bf_show_pig"
                        href=" <?php echo $group_permalink ?>">View</a>
            </li>
			<?php if ( $buddyforms_user_can ) { ?>
                <li id="edit-post-details-groups-li"
                    class="<?php echo bp_action_variable() == 'create' ? 'current selected' : ''; ?> bp-groups-admin-tab">
                    <a
                            id="edit-post-details" class="bf_show_pig"
                            href="<?php echo $group_permalink ?>/create">Create</a></li>
			<?php } ?>
			<?php if ( bp_action_variable() == 'edit' ) { ?>
                <li id="edit-post-details-groups-li"
                    class="<?php echo bp_action_variable() == 'edit' ? 'current selected' : ''; ?> bp-groups-admin-tab">
                    <a
                            id="edit-post-details" class="bf_show_pig" href="#">Edit</a></li>
			<?php } ?>


        </ul>


    </nav>

<?php
switch ( bp_action_variable() ) {
	case 'create':

		if ( $buddyforms_user_can ) {
			$args = array(
				'form_slug' => bp_current_action(),
			);
			echo buddyforms_create_edit_form( $args );
		}

		break;
	case 'edit':
		add_filter( 'buddyforms_user_can_edit', 'buddyforms_post_in_groups_front_js_css_loader', 10, 1 );



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
		if ( $can_edit ) {
			$args = array(
				'form_slug' => $bp->current_action,
				'post_id'   => bp_action_variable( 1 ),
			);
			echo buddyforms_create_edit_form( $args );
		} else {
		    echo __('You have not the needed rights to edit this post', 'buddyforms');
        }

		break;
	default:


		$temp_query        = $the_lp_query;
		$form_slug         = $bp->current_action;
		$post_type         = $buddyforms[ $form_slug ]['post_type'];
		$list_posts_option = $buddyforms[ $form_slug ]['list_posts_option'];

		$query_args = array(
			'post_type'      => $post_type,
			'form_slug'      => $form_slug,
			'post_status'    => array( 'publish' ),
			'posts_per_page' => 5,
			'post_parent'    => 0,
			'paged'          => $paged,
		//	'author'         => $bp->displayed_user->id,

		);

		$buddyforms_pig = groups_get_groupmeta( $group_id, '_buddyforms_pig' );

		if ( isset( $buddyforms_pig['view'] ) && $buddyforms_pig['view'] == 'form'  ) {
		    $query_args['meta_key'] = '_bf_form_slug';
			$query_args['meta_value'] = $form_slug;
		}
		if ( isset( $buddyforms_pig['view'] ) && $buddyforms_pig['view'] == 'assigned'  ) {
			$query_args['meta_key']   = 'buddyforms_buddypress_group';
			$query_args['meta_value'] = $group_id;
		}

		if ( isset( $buddyforms_pig['view'] ) && $buddyforms_pig['view'] == 'group_members'  ) {



		    $group_members = array();
			// An array of optional arguments.
			$args = array(
				'group_id' => $group_id,
			);


			$admins = groups_get_group_admins($group_id);
//			echo '<pre>';
//			print_r($admins);
//			echo '</pre>';
			foreach ($admins as $key =>  $admin){
				$group_members[$admin->user_id] = $admin->user_id;
            }

			$members = groups_get_group_members($args);
			foreach ($members['members'] as $key => $member){
				$group_members[$member->ID] = $member->ID;
            }
//			echo '<pre>';
//			print_r($members['members']);
//			echo '</pre>';

			$query_args['author__in'] = array_keys($group_members);
		}




		if ( isset( $list_posts_option ) && $list_posts_option == 'list_all' ) {
			unset( $query_args['meta_key'] );
			unset( $query_args['meta_value'] );
		}

		if ( $bp->displayed_user->id == $current_user->ID ) {
			$query_args['post_status'] = array( 'publish', 'pending', 'draft' );
		}

		$query_args   = apply_filters( 'bf_post_to_display_args', $query_args );
		$the_lp_query = new WP_Query( $query_args );
		buddyforms_locate_template( 'the-loop', $form_slug );

		$the_lp_query = $temp_query;
		break;
}
