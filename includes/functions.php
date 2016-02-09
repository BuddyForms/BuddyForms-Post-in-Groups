<?php

function buddyforms_post_in_groups_load_constants() {

  if (!defined('BUDDYFORMS_PIG_INSTALL_PATH'))
    define('BUDDYFORMS_PIG_INSTALL_PATH', dirname(__FILE__) . '/');

  if (!defined('BUDDYFORMS_PIG_INCLUDES_PATH'))
    define('BUDDYFORMS_PIG_INCLUDES_PATH', BUDDYFORMS_PIG_INSTALL_PATH . 'includes/');

  if (!defined('BUDDYFORMS_PIG_TEMPLATE_PATH'))
    define('BUDDYFORMS_PIG_TEMPLATE_PATH', BUDDYFORMS_PIG_INSTALL_PATH . 'templates/');

}
add_action('init', 'buddyforms_post_in_groups_load_constants');

function buddyforms_post_in_groups_front_js_css_loader($fount){
    return true;
}
add_filter('buddyforms_front_js_css_loader', 'buddyforms_post_in_groups_front_js_css_loader', 10, 1 );

/**
 * Locate a template
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function buddyforms_post_in_groups_locate_template($file) {
	if (locate_template(array($file), false)) {
		locate_template(array($file), true);
	} else {
		include (BUDDYFORMS_PIG_TEMPLATE_PATH . $file);
	}
}

function buddyforms_pig_the_loop_actions($post_id){
  $group_permalink = bp_get_group_permalink() . bp_current_action();

  if (get_the_author_meta('ID') ==  get_current_user_id())
    return;

  $post_status = get_post_status($post_id);

  $post_status_css =  $post_status_name  = $post_status;

  if( $post_status == 'pending')
    $post_status_css = 'bf-pending';

  if( $post_status == 'publish')
    $post_status_name = 'published';

  ?>
  <div class="action">
    <div class="meta">
      <div class="item-status"><?php echo $post_status_name; ?></div>
      <?php
      echo '<a title="Edit" id="' . $post_id . '" class="bf_edit_post" href="' . $group_permalink . '/edit/' . $post_id . '">' . __( 'Edit', 'buddyforms' ) .'</a>';
      echo ' - <a title="Delete"  id="' . $post_id . '" class="bf_delete_post" href="#">' . __( 'Delete', 'buddyforms' ) . '</a>';
      ?>
    </div>
  </div>
  <?php
}
add_action('buddyforms_the_loop_li_last', 'buddyforms_pig_the_loop_actions', 99, 1);

function buddyforms_pig_user_can_delete(){
  return true;
}
add_filter('buddyforms_user_can_delete', 'buddyforms_pig_user_can_delete', 10, 1);

function bf_pig_loop_edit_post_link($link, $post_id){
echo 'das';
  return 'da';
}
add_filter('bf_loop_edit_post_link', 'bf_pig_loop_edit_post_link', 99999, 2);
