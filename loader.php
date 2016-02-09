<?php

/*
Plugin Name: BuddyForms Posts in Groups
Plugin URI: http://buddyforms.com/downloads/buddyforms-posts-in-groups/
Description: BuddyForms Posts in Groups
Version: 1.0
Author: svenl77, buddyforms
Author URI: https://profiles.wordpress.org/svenl77
Licence: GPLv3
Network: false

*****************************************************************************
*
* This script is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
****************************************************************************
*/
function buddyforms_pig_init() {

  define('buddyforms_pig', '0.1');

  require (dirname(__FILE__) . '/includes/buddyforms-post-in-groups.php');
  require (dirname(__FILE__) . '/includes/functions.php');
	bp_register_group_extension( 'BuddyForms_Post_in_Groups' );

}
add_action('bp_loaded', 'buddyforms_pig_init');
