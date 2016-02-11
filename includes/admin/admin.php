<?php

function bf_pig_admin_tabs($tabs){
  $tabs['buddypress-pig'] = 'BuddyPress Post in Groups';
  return $tabs;
}
add_filter('bf_admin_tabs', 'bf_pig_admin_tabs', 10, 1);

function buddyforms_pig_settings_page_tab($tab){
  global $buddyforms;
  if($tab == 'buddypress-pig'){


    if(isset($_POST['buddyforms_pig_options'])){
       update_option( 'buddyforms_pig_options', $_POST['buddyforms_pig_options'] );
    }
    $buddyforms_pig 	= get_option( 'buddyforms_pig_options' );

    // echo '<pre>';
    // print_r($buddyforms_pig);
    // echo '</pre>';

    ?>

    <h2><?php _e('Post in Groups options'); ?></h2>
    <form method="post" action="">

        <?php //settings_fields('buddyforms_pig_options'); ?>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('Enable Post in Groups'); ?>
                    <?php _e('How is alowed to enable the Post in Groups Feature in a Grop'); ?>
                </th>
                <td>
                  <select name="buddyforms_pig_options[permission]" class="regular-radio">
                      <option value="none">Disabled</option>
                      <option <?php echo selected($buddyforms_pig['permission'], 'all', true) ?>
                        value="all">All Users</option>
                      <option <?php echo selected($buddyforms_pig['permission'], 'admin', true) ?>
                        value="all2">Admins Only</option>
                  </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('Select the Forms you like to make available as Group Forms'); ?>
                </th>
                <td>
                  <select name="buddyforms_pig_options[forms][]" multiple="multiple" class="bf-select2 regular-radio">
                      <?php foreach ($buddyforms as $form_slug => $buddyform) { ?>
                        <option  <?php echo in_array($form_slug, $buddyforms_pig['forms']) ? 'selected' : '' ?>
                          value="<?php echo $form_slug ?>"><?php echo $buddyform['name'] ?></option>
                      <?php } ?>

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
