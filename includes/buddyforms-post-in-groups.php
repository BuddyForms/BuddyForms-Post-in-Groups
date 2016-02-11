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
      global $buddyforms;

      $this->post_in_group_form_slug	= groups_get_groupmeta( bp_get_current_group_id(), '_bf_pig_form_slug', true );
      $this->buddyforms_pig           = groups_get_groupmeta( bp_get_current_group_id(), '_buddyforms_pig', true );
      $this->buddyforms_user_can	    = false;
      $this->enable_create_step       = false;

      $form_slug = $this->post_in_group_form_slug;


//      foreach ($this->post_in_group_form_slug as $key => $form_slug) {
        $name = $buddyforms[$form_slug]['name'];

        if ( isset( $this->buddyforms_pig['create'] ) ) {
          switch ( $this->buddyforms_pig['create'] ) {
            case 'admin' :
              if ( groups_is_user_admin( bp_loggedin_user_id(), bp_get_current_group_id() ) )
                $this->buddyforms_user_can = true;
              break;
            case 'mod' :
              if ( groups_is_user_mod(bp_loggedin_user_id(), bp_get_current_group_id()) || groups_is_user_admin(bp_loggedin_user_id(), bp_get_current_group_id()) )
                $this->buddyforms_user_can = true;
              break;
            case 'member' :
            default :
              if ( groups_is_user_member(bp_loggedin_user_id(), bp_get_current_group_id()) )
                $this->buddyforms_user_can = true;
              break;
          }
        }
        $args = array(
            'slug' => $form_slug,
            'name' => $name,
          //  'nav_item_position' => 105,
        );
        add_action('bp_after_group_settings_admin', array($this, 'bp_pig_after_group_manage_members_admin'), 1, 1);
        add_action('groups_group_settings_edited' , array($this, 'bf_pig_groups_group_settings_edited'), 10, 1 );
        add_action('bp_after_group_settings_creation_step', array($this, 'bp_pig_after_group_manage_members_admin'), 1, 1);
        add_action('groups_create_group_step_save_group-settings' , array($this, 'bf_pig_groups_group_settings_edited'), 10, 1 );


        parent::init( $args );
  //    }

    }

    function display( $group_id = NULL ) {
      $group_id = bp_get_group_id();
      $group_permalink = bp_get_group_permalink() . bp_current_action();
      ob_start(); ?>
      <div class="item-list-tabs no-ajax" id="subnav" role="navigation">
        <ul>
        <li id="view-post-details-groups-li" class="<?php echo bp_action_variable() == '' ? 'current selected' : ''; ?>"><a id="view-post-details" class="bf_show_pig" href=" <?php echo $group_permalink ?>">View</a></li>
        <?php if( $this->buddyforms_user_can ){ ?>
          <li id="edit-post-details-groups-li" class="<?php echo bp_action_variable() == 'create' ? 'current selected' : ''; ?>"><a id="edit-post-details" class="bf_show_pig" href="<?php echo $group_permalink ?>/create">Create</a></li>
        <?php } ?>
        <?php if( bp_action_variable() == 'edit'){ ?>
          <li id="edit-post-details-groups-li" class="<?php echo bp_action_variable() == 'edit' ? 'current selected' : ''; ?>"><a id="edit-post-details" class="bf_show_pig" href="#">Edit</a></li>
        <?php } ?>
        </ul>
      </div>
      <?php
      switch (bp_action_variable()) {
        case 'create':

          if( $this->buddyforms_user_can ){
            $args = array(
               'form_slug' => $this->post_in_group_form_slug,
            );
            echo buddyforms_create_edit_form($args);
          }

          break;
        case 'edit':

            echo bp_action_variable(1);
            add_filter('buddyforms_user_can_edit', 'buddyforms_post_in_groups_front_js_css_loader', 10, 1);
            if( $this->buddyforms_user_can ){
              $args = array(
                 'form_slug' => $this->post_in_group_form_slug,
                 'post_id'  => bp_action_variable(1),
              );
              echo buddyforms_create_edit_form($args);
            }

            break;
        default:
          global $wp_query, $current_user, $the_lp_query, $bp, $buddyforms, $buddyforms_member_tabs, $form_slug, $paged;

          $temp_query         = $the_lp_query;
          $form_slug          = $bp->current_action;
          $post_type          = $buddyforms[$form_slug]['post_type'];
          $list_posts_option  = $buddyforms[$form_slug]['list_posts_option'];

          $query_args = array(
            'post_type'			=> $post_type,
            'form_slug'         => $form_slug,
            'post_status'		=> array('publish'),
            'posts_per_page'	=> 5,
            'post_parent'		=> 0,
            'paged'				=> $paged,
            'author'			=> $bp->displayed_user->id,
            'meta_key'          => '_bf_form_slug',
            'meta_value'        => $form_slug
          );

          if(isset($list_posts_option) && $list_posts_option == 'list_all'){
            unset($query_args['meta_key']);
            unset($query_args['meta_value']);
          }

          if ($bp->displayed_user->id == $current_user->ID){
            $query_args['post_status'] = array('publish', 'pending', 'draft');
          }

          $query_args =  apply_filters('bf_post_to_display_args',$query_args);
          $the_lp_query = new WP_Query( $query_args );
          buddyforms_locate_template('buddyforms/the-loop.php');

          // Support for wp_pagenavi
          if(function_exists('wp_pagenavi')){
            wp_pagenavi( array( 'query' => $the_lp_query) );
          }
          $the_lp_query = $temp_query;
          break;
      }

      $tmp = ob_get_clean();
      echo $tmp;
    }

    function bp_pig_after_group_manage_members_admin(){
      global $buddyforms, $bp;

      $group_id = bp_get_group_id();

      if(!$group_id)
        $group_id = $bp->groups->new_group_id;


      $form_slug = groups_get_groupmeta( $group_id, '_bf_pig_form_slug' );

print_r($form_slug);
      ?>
      <h2><?php _e( 'Post in Group Options', 'buddyforms' ) ?></h2>
      <label for="_bf_pig_form_slug"><?php _e( 'Select the forms you like to integrate', 'buddyforms' ) ?></label>

      <select name="_bf_pig_form_slug">
         <option value="none"><?php _e('Select Form', 'buddyforms') ?></option>
        <?php foreach ($buddyforms as $key => $buddyform) { ?>
          <option <?php selected($key, $form_slug) ?> value="<?php echo $key ?>"><?php echo $buddyform['name'] ?></option>
        <?php } ?>
      </select>


      <?php

      $settings	= groups_get_groupmeta( $group_id, '_buddyforms_pig' );

      $can_create  = empty( $settings['create'] ) ? false : $settings['create'];
      $can_edit    = empty( $settings['edit'] )   ? false : $settings['edit'];
      $can_delete  = empty( $settings['delete'] ) ? false : $settings['delete'];

      ?>

      <div id="_buddyforms_pig-options" <?php if ( $form_slug ) : ?>class="hidden"<?php endif ?>>

        <table class="_buddyforms_pig-options">
          <tr>
            <td class="label">
              <label for="_buddyforms_pig-can-create"><?php _e( 'Minimum role to create posts:', 'buddyforms' ) ?></label>
            </td>

            <td>
              <select name="_buddyforms_pig[create]" id="_buddyforms_pig-can-create">
                <option value="admin" <?php selected( $can_create, 'admin' ) ?> /><?php _e( 'Group admin', 'buddyforms' ) ?></option>
                <option value="mod" <?php selected( $can_create, 'mod' ) ?> /><?php _e( 'Group moderator', 'buddyforms' ) ?></option>
                <option value="member" <?php selected( $can_create, 'member' ) ?> /><?php _e( 'Group member', 'buddyforms' ) ?></option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="label">
              <label for="_buddyforms_pig-can-create"><?php _e( 'Minimum role to edit posts:', 'buddyforms' ) ?></label>
            </td>

            <td>
              <select name="_buddyforms_pig[edit]" id="_buddyforms_pig-can-create">
                <option value="author" <?php selected( $can_edit, 'author' ) ?> /><?php _e( 'Post Author', 'buddyforms' ) ?></option>
                <option value="admin" <?php selected( $can_edit, 'admin' ) ?> /><?php _e( 'Group Admin', 'buddyforms' ) ?></option>
                <option value="mod" <?php selected( $can_edit, 'mod' ) ?> /><?php _e( 'Group Moderator', 'buddyforms' ) ?></option>
                <option value="member" <?php selected( $can_edit, 'member' ) ?> /><?php _e( 'Group Member', 'buddyforms' ) ?></option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="label">
              <label for="_buddyforms_pig-can-create"><?php _e( 'Minimum role to delete posts:', 'buddyforms' ) ?></label>
            </td>

            <td>
              <select name="_buddyforms_pig[delete]" id="_buddyforms_pig-can-create">
                <option value="author" <?php selected( $can_delete, 'author' ) ?> /><?php _e( 'Post Author', 'buddyforms' ) ?></option>
                <option value="admin" <?php selected( $can_delete, 'admin' ) ?> /><?php _e( 'Group Admin', 'buddyforms' ) ?></option>
                <option value="mod" <?php selected( $can_delete, 'mod' ) ?> /><?php _e( 'Group Moderator', 'buddyforms' ) ?></option>
                <option value="member" <?php selected( $can_delete, 'member' ) ?> /><?php _e( 'Group Member', 'buddyforms' ) ?></option>
              </select>
            </td>
          </tr>
        </table>
      </div>
      <?php
    }
    function bf_pig_groups_group_settings_edited($group_id){
      global $bp;

      if(!$group_id)
        $group_id = $bp->groups->new_group_id;

      if(isset($_POST['_bf_pig_form_slug'])){
        $form_slug = isset( $_POST['_bf_pig_form_slug'] ) ? $_POST['_bf_pig_form_slug'] : '';
        groups_update_groupmeta( $group_id, '_bf_pig_form_slug', $form_slug );
      }
      if(isset($_POST['_bf_pig_form_slug'])){
        $settings = !empty( $_POST['_buddyforms_pig'] ) ? $_POST['_buddyforms_pig'] : array();
        groups_update_groupmeta( $group_id, '_buddyforms_pig', $settings );
      }
    }
  }
endif;
