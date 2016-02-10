<?php

function bf_pig_admin_tabs($tabs){
  $tabs['buddypress_pig'] = 'BuddyPress Post in Groups';
  return $tabs;
}
add_filter('bf_admin_tabs', 'bf_pig_admin_tabs', 10, 1);

function buddyforms_pig_settings_page_tab($tab){
  if($tab == 'buddypress_pig'){
    $buddyforms_pig 	= get_option( 'buddyforms_pig' );?>

    <h2><?php _e('Post in Groups options'); ?></h2>
    <form method="post" action="options.php">

        <?php settings_fields('buddyforms_pig'); ?>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('How is alowed to add form to a Group'); ?>
                </th>
                <td>
                  <select name="buddyforms_pig" class="regular-radio">
                      <option value="none">None</option>
                      <option <?php echo selected($buddyforms_pig['permission'], 'all', true) ?>
                        value="all">All</option>
                        <option <?php echo selected($buddyforms_pig['permission'], 'all2', true) ?>
                          value="all2">All2</option>
                  </select>
                </td>
            </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
    <?php
  }
}
add_action('buddyforms_settings_page_tab', 'buddyforms_pig_settings_page_tab');

function buddyforms_pig_register_option() {
    // creates our settings in the options table
    register_setting('buddyforms_pig_options', 'buddyforms_pig', 'buddyforms_pig_sanitize' );

}
add_action('admin_init', 'buddyforms_pig_register_option');

function buddyforms_pig_sanitize(){
  return true;
}
