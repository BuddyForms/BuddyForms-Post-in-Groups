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
		if ( $buddyforms_user_can ) {
			$args = array(
				'form_slug' => $bp->current_action,
				'post_id'   => bp_action_variable( 1 ),
			);
			print_r( $args );
			echo buddyforms_create_edit_form( $args );
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
			'author'         => $bp->displayed_user->id,
			'meta_key'       => '_bf_form_slug',
			'meta_value'     => $form_slug
		);

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

		// Support for wp_pagenavi
		if ( function_exists( 'wp_pagenavi' ) ) {
			wp_pagenavi( array( 'query' => $the_lp_query ) );
		}
		$the_lp_query = $temp_query;
		break;
}
