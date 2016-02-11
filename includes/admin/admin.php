<?php

function bf_pig_admin_tabs($tabs){
  $tabs['buddypress-pig'] = 'BuddyPress Post in Groups';
  return $tabs;
}
add_filter('bf_admin_tabs', 'bf_pig_admin_tabs', 10, 1);

function buddyforms_pig_settings_page_tab($tab){
  if($tab == 'buddypress-pig'){


    if(isset($_POST['buddyforms_pig_options'])){
       update_option( 'buddyforms_pig_options', $_POST['buddyforms_pig_options'] );
    }
    $buddyforms_pig 	= get_option( 'buddyforms_pig_options' );

    ?>

    <h2><?php _e('Post in Groups options'); ?></h2>
    <form method="post" action="">

        <?php //settings_fields('buddyforms_pig_options'); ?>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('Enable Post in Groups'); ?>
                </th>
                <td>
                  <select name="buddyforms_pig_options[permission]" class="regular-radio">
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
