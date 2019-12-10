<?php
/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :
	class BuddyForms_Post_in_Groups extends BP_Group_Extension {
		/**
		 * Here you can see more customization of the config options
		 */
		function __construct() {
			global $buddyforms, $buddyforms_user_can;

			$buddyforms_pig = get_option( 'buddyforms_pig_options' );

			$this->post_in_group_form_slug = groups_get_groupmeta( bp_get_current_group_id(), '_bf_pig_form_slug', true );
			$this->buddyforms_pig          = groups_get_groupmeta( bp_get_current_group_id(), '_buddyforms_pig', true );
			$buddyforms_user_can           = false;
			//$this->enable_create_step       = false;

			$form_slug = $this->post_in_group_form_slug;

			$name = isset($buddyforms[ $form_slug ]['name']) ? $buddyforms[ $form_slug ]['name'] : '';

			if ( isset( $this->buddyforms_pig['create'] ) ) {
				switch ( $this->buddyforms_pig['create'] ) {
					case 'admin' :
						if ( groups_is_user_admin( bp_loggedin_user_id(), bp_get_current_group_id() ) ) {
							$buddyforms_user_can = true;
						}
						break;
					case 'mod' :
						if ( groups_is_user_mod( bp_loggedin_user_id(), bp_get_current_group_id() ) || groups_is_user_admin( bp_loggedin_user_id(), bp_get_current_group_id() ) ) {
							$buddyforms_user_can = true;
						}
						break;
					case 'member' :
					default :
						if ( groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() ) ) {
							$buddyforms_user_can = true;
						}
						break;
				}
			}
			$args = array(
				'slug' => $form_slug,
				'name' => $name,
			);


			switch ( $buddyforms_pig['permission'] ) {
				case 'all':
					add_action( 'bp_after_group_settings_admin', array(
						$this,
						'bp_pig_after_group_manage_members_admin'
					), 1, 1 );
					add_action( 'groups_group_settings_edited', array(
						$this,
						'bf_pig_groups_group_settings_edited'
					), 10, 1 );
					add_action( 'bp_after_group_settings_creation_step', array(
						$this,
						'bp_pig_after_group_manage_members_admin'
					), 1, 1 );
					add_action( 'groups_create_group_step_save_group-settings', array(
						$this,
						'bf_pig_groups_group_settings_edited'
					), 10, 1 );
					break;
				case 'group-admin':
					add_action( 'bp_after_group_settings_admin', array(
						$this,
						'bp_pig_after_group_manage_members_admin'
					), 1, 1 );
					add_action( 'groups_group_settings_edited', array(
						$this,
						'bf_pig_groups_group_settings_edited'
					), 10, 1 );
					break;
				case 'admin':
					if ( is_super_admin() ) {
						add_action( 'bp_after_group_settings_admin', array(
							$this,
							'bp_pig_after_group_manage_members_admin'
						), 1, 1 );
						add_action( 'groups_group_settings_edited', array(
							$this,
							'bf_pig_groups_group_settings_edited'
						), 10, 1 );
					}
					break;
			}

			parent::init( $args );
		}

		function display( $group_id = null ) {

			ob_start();
			buddyforms_post_in_groups_locate_template( 'group-display.php' );
			$tmp = ob_get_clean();
			echo $tmp;
		}

		function bp_pig_after_group_manage_members_admin() {
			global $buddyforms, $bp;

			$buddyforms_pig = get_option( 'buddyforms_pig_options' );

			if ( ! isset( $buddyforms_pig['forms'] ) ) {
				return;
			}

			$group_id = bp_get_group_id();

			if ( ! $group_id ) {
				$group_id = $bp->groups->new_group_id;
			}


			$form_slug = groups_get_groupmeta( $group_id, '_bf_pig_form_slug' ); ?>

			<h4><?php _e( 'Post in Group Options', 'buddyforms' ) ?></h4>
			<p><?php _e( 'Select the forms you like to integrate: ', 'buddyforms' ) ?>

				<select name="_bf_pig_form_slug">
					<option value="none"><?php _e( 'Select Form', 'buddyforms' ) ?></option>
					<?php foreach ( $buddyforms_pig['forms'] as $key => $slug ) { ?>
						<option <?php selected( $slug, $form_slug ) ?>
							value="<?php echo $slug ?>"><?php echo $buddyforms[ $slug ]['name'] ?></option>
					<?php } ?>
				</select>
			</p>
			<?php

			$settings = groups_get_groupmeta( $group_id, '_buddyforms_pig' );

			$can_create = empty( $settings['create'] ) ? false : $settings['create'];
			$can_edit   = empty( $settings['edit'] ) ? false : $settings['edit'];
			$can_delete = empty( $settings['delete'] ) ? false : $settings['delete'];

			?>
			<br>
			<p>Which members of this group are allowed to create/edit/delete posts?</p>

			<table class="_buddyforms_pig-options">
				<tr>
					<td class="groups-label">
						<label
							for="_buddyforms_pig-can-create"><?php _e( 'Minimum role to create posts:', 'buddyforms' ) ?></label>
					</td>

					<td>
						<select name="_buddyforms_pig[create]" id="_buddyforms_pig-can-create">
							<option value="admin" <?php selected( $can_create, 'admin' ) ?> />
							<?php _e( 'Group admin', 'buddyforms' ) ?></option>
							<option value="mod" <?php selected( $can_create, 'mod' ) ?> />
							<?php _e( 'Group moderator', 'buddyforms' ) ?></option>
							<option value="member" <?php selected( $can_create, 'member' ) ?> />
							<?php _e( 'Group member', 'buddyforms' ) ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="groups-label">
						<label
							for="_buddyforms_pig-can-create"><?php _e( 'Minimum role to edit posts:', 'buddyforms' ) ?></label>
					</td>

					<td>
						<select name="_buddyforms_pig[edit]" id="_buddyforms_pig-can-create">
							<option value="admin" <?php selected( $can_edit, 'admin' ) ?> />
							<?php _e( 'Group Admin', 'buddyforms' ) ?></option>
							<option value="mod" <?php selected( $can_edit, 'mod' ) ?> />
							<?php _e( 'Group Moderator', 'buddyforms' ) ?></option>
							<option value="member" <?php selected( $can_edit, 'member' ) ?> />
							<?php _e( 'Group Member', 'buddyforms' ) ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="groups-label">
						<label
							for="_buddyforms_pig-can-create"><?php _e( 'Minimum role to delete posts:', 'buddyforms' ) ?></label>
					</td>

					<td>
						<select name="_buddyforms_pig[delete]" id="_buddyforms_pig-can-create">
							<option value="admin" <?php selected( $can_delete, 'admin' ) ?> />
							<?php _e( 'Group Admin', 'buddyforms' ) ?></option>
							<option value="mod" <?php selected( $can_delete, 'mod' ) ?> />
							<?php _e( 'Group Moderator', 'buddyforms' ) ?></option>
							<option value="member" <?php selected( $can_delete, 'member' ) ?> />
							<?php _e( 'Group Member', 'buddyforms' ) ?></option>
						</select>
					</td>
				</tr>
			</table>
			<p>Please Note: The Post Author and Admin will always have all rights to edit or delete a post.</p>
			<?php
		}

		function bf_pig_groups_group_settings_edited( $group_id ) {
			global $bp;

			if ( ! $group_id ) {
				$group_id = $bp->groups->new_group_id;
			}

			if ( isset( $_POST['_bf_pig_form_slug'] ) ) {
				$form_slug = isset( $_POST['_bf_pig_form_slug'] ) ? $_POST['_bf_pig_form_slug'] : '';
				groups_update_groupmeta( $group_id, '_bf_pig_form_slug', $form_slug );
			}
			if ( isset( $_POST['_buddyforms_pig'] ) ) {
				$settings = ! empty( $_POST['_buddyforms_pig'] ) ? $_POST['_buddyforms_pig'] : array();
				groups_update_groupmeta( $group_id, '_buddyforms_pig', $settings );
			}
		}
	}
endif;
