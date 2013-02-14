<?php

/*
Plugin Name: EventAggregator
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: EventAggregator collects events from Facebook, Meetup and EventBrite Pages and show them in the calendar
Version: 0.1
Author: Daniel Koller, daniel@dakoller.net
Author URI: http://twitter.com/dakoller

  Copyright 2013  Daniel Koller  (email : daniel@dakoller.net

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$plugin_pref = 'EventAggregator_';

require_once('Meetup-API-client-for-PHP-master/Meetup.php');
require_once('Meetup-API-client-for-PHP-master/MeetupGroups.class.php');
require_once('Meetup-API-client-for-PHP-master/MeetupEvents.class.php');
require_once('Meetup-API-client-for-PHP-master/MeetupExceptions.class.php');

require_once("facebook.php");

add_option($plugin_pref . 'Facebook App ID', '', '', 'yes');
add_option($plugin_pref . 'Facebook App Secret', '', '', 'yes');

add_option($plugin_pref . 'Facebook App Access Token', '479573022079563|e0EPd7hLj-Lc1FWuGjJffghvp-0', '', 'yes');

add_option($plugin_pref . 'Meetup API Key', '', '', 'yes');

add_option($plugin_pref . 'Location Longitude', 11.579, '', 'yes');
add_option($plugin_pref . 'Location Latitude', 48.13, '', 'yes');
add_option($plugin_pref . 'Location Radius (in km)', 50, '', 'yes');

$fb_app_id = get_option($plugin_pref . 'Facebook App ID');
$fb_app_secret = get_option($plugin_pref .'Facebook App Secret');
$meetup_api_key = get_option($plugin_pref .'Meetup API Key');

global $jal_db_version;
$jal_db_version = "0.1";

function startsWith($haystack,$needle) {
  if  (strpos($haystack,$needle)===0)
    return true;
  else
    return false;
}

function hourly_sync() {
  call_user_func('sync_sources');
}

function on_activation() {
   global $wpdb;
   global $jal_db_version;

   $table_name = $wpdb->prefix . "EventA_sources";
      
   $sql = "CREATE TABLE $table_name (
  id int(11) NOT NULL AUTO_INCREMENT,
  type varchar(6) COLLATE latin1_german2_ci NOT NULL,
  name varchar(40) COLLATE latin1_german2_ci NOT NULL,
  id_in_source varchar(50) COLLATE latin1_german2_ci DEFAULT NULL,
  take_all_events tinyint(1) DEFAULT NULL,
  enabled tinyint(1) DEFAULT NULL,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tags text COLLATE latin1_german2_ci,
  contact_email varchar(100) COLLATE latin1_german2_ci DEFAULT NULL,
  last_fetch timestamp NULL DEFAULT NULL,
  last_fetch_status tinyint(4) DEFAULT NULL,
  UNIQUE KEY  id (id)
);";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
 
   add_option("jal_db_version", $jal_db_version);
   
   wp_schedule_event( time(), 'hourly', 'hourly_sync');
}

function on_deactivation() {
   global $wpdb;
   global $jal_db_version;

   $table_name = $wpdb->prefix . "EventA_sources";
      
   $sql = "DROP TABLE $table_name;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
 
   add_option("jal_db_version", $jal_db_version);
   
   wp_clear_scheduled_hook('hourly_sync_event');
}

class MyWidget extends WP_Widget {
  function MyWidget() {
    parent::WP_Widget( false, $name = 'EventAggregator Widget' );
  }

  function widget( $args, $instance ) {
    global $wpdb;
    extract( $args );
    $title = apply_filters( 'widget_title', $instance['title'] );
    ?>

    <?php
	echo $before_widget;
    ?>

    <?php
      if ($title) {
	echo $before_title . $title . $after_title;
      }
      
      call_user_func('sync_sources');
      
    
      
    ?>

    <div class="my_textbox">
      <input type='text' name='my_text' id='my_text_id'/>
      <input type='button' value='Submit' id='my_text_submit_id'/>
    </div>

     <?php
       echo $after_widget;
     ?>
     <?php
  }

  function update( $new_instance, $old_instance ) {
    return $new_instance;
  }

  function form( $instance ) {
    $title = esc_attr( $instance['title'] );
    ?>

    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
      </label>
    </p>
    <?php
  }
}

function sync_sources() {
  global $wpdb;
  
  $sources = $wpdb->get_results('select * from wp_EventA_sources where enabled=1');
      foreach ($sources as $source) {
	echo '<p>'. $source->name .'</p>';
	
	if (startsWith($source->type, 'fb')) {
	  call_user_func(sync_fb,$source);
	}
	
	if (startsWith($source->type, 'mt')) {
	  call_user_func(sync_meetup,$source);
	}
	
	
	
	
	
      }
  
}

function sync_fb($row) {
  global $wpdb;
  echo 'now syncing '. $row->name;
  
  $plugin_pref = 'EventAggregator_';

  require_once("facebook.php");
  
  $fb_app_id = get_option($plugin_pref . 'Facebook App ID');
  $fb_app_secret = get_option($plugin_pref .'Facebook App Secret');
  
  $facebook = new Facebook($config);
  $token = get_option($plugin_pref . 'Facebook App Access Token');
  $facebook->setAccessToken($token);
  
  if ($row->type =='fbpage') {
    $events= $facebook->api('/'. $row->id_in_source .'/events');
  } elseif ( $row->type == 'fbuser') {
    $events = $facebook->api('/'.$row->id_in_source.'/events');
  }
  foreach ($events['data'] as $event) {
    //echo '<b>' . $event['name'] . '</b>';
    
    $fb_id = 'fb_' . $event['id'];
    //print('('.$fb_id.')<br/>');

    //$existing_event= get_posts(array('post_name' => $fb_id));
    $existing_event= get_page_by_path($fb_id,OBJECT,'tribe_events');
    //print(var_dump($existing_event));
    
    $post = array(
                    'comment_status' => 'open',
                    'ping_status'    => 'open',
                    'post_author'    => 1,
                    'post_content'   => 'test',

                    'post_excerpt'   => 'dfgdge',
                    'post_name'      => $fb_id,
                    'post_title'     => $event['name'],
                    'post_type'      => 'tribe_events',
                    'tags_input'     => $row->tags,
                    'to_ping'        => ''
                  );

    
    if ($existing_event != NULL ) {

      #update event

      $post_id= $existing_event->ID;
      

      $post['ID'] = $post_id;
      $post['post_status'] = $existing_event->post_status;
      
      

      //echo '(existing event)<br/>';
      
      //echo '( '.$post_id.' )<br/>';
    } else {
      #preserve #pending staus
      if ($row->take_all_events) {
	$post['post_status'] = 'publish';
      } else {
	$post['post_status'] = 'publish';
      }
      //echo '(new event)<br/>';
    
    }
    
    #print(var_dump($post));
    
    
                
    $id = wp_insert_post($post,True);
    //print(var_dump($id));
                
    add_post_meta($id,'_EventStartDate',$event['start_time'],True);
    add_post_meta($id,'_EventEndDate',$event['end_time'],True);
                
    add_post_meta($id,'_EventOrganizerID',$row->id_in_source ,True);
    add_post_meta($id,'_EventVenueID',$event['location'],True);
                
    add_post_meta($id,'_EventSource',$row->id,True);
    add_post_meta($id,'_EventSourceID',$row->id_in_source ,True);
    add_post_meta($id,'_EventIDinSource',$event['id'],True);

    
  }
  
}

function sync_meetup($row) {
  global $wpdb;
  global $plugin_pref;
  
  
  $api_key = get_option($plugin_pref . 'Meetup API Key','');
  if ($api_key != '') {
    print('<b>'.$api_key.'</b><br/>');
    $con = new MeetupKeyAuthConnection($api_key);
    
    if ($row->type =='mtgrou') {
    
      $mgroups= new MeetupGroups($con);
      
      $groups= $mgroups->getGroups(array('group_urlname' => $row->id_in_source));
      $group_id= $groups[0]['id'];      
      $mevents = new MeetupEvents($con);
      $events= $mevents->getEvents(array('group_id' => $group_id));
    } elseif ( $row->type == 'mtsear') {
      print(var_dump($con));
      
      $mevents = new MeetupEvents($con);

      
      $lat = get_option($plugin_pref .'Location Latitude');
      $lon = get_option($plugin_pref .'Location Longitude');
      
      $text= join(explode (',', $row->id_in_source),' ');
      echo var_dump($text);
      $events = $mevents->getOpenEvents(array('lon' => $lon, 'lat' => $lat, 'text' => $text));
      
      print(var_dump($events));
      
    }
    foreach ($events as $event) {
      //print(var_dump($event));
      $mt_id ='mt_'.$event['id'];
      
      //print($mt_id);
      
      $existing_event= get_page_by_path($mt_id,OBJECT,'tribe_events');
      
      $post = array(
                    'comment_status' => 'open',
                    'ping_status'    => 'open',
                    'post_author'    => 1,
                    'post_content'   => $event['description'],

                    'post_excerpt'   => 'dfgdge',
                    'post_name'      => $mt_id,
                    'post_title'     => $event['name'],
                    'post_type'      => 'tribe_events',
                    'tags_input'     => $row->tags,
                    'to_ping'        => ''
                  );
      
      $location= array(
		       
		       );
      
      if ($existing_event != NULL) {
	// existing event
	$post_id= $existing_event->ID;
	$post['ID'] = $post_id;
	$post['publish_status'] = $existing_event->publish_status;
      } else {
	// new event
	#preserve #pending staus
	if ($row->take_all_events) {
	  $post['post_status'] = 'publish';
	} else {
	  $post['post_status'] = 'pending';
	}
	
      }
      
      $id = wp_insert_post($post,True);
      print(var_dump($id));
		  
      add_post_meta($id,'_EventStartDate',date('', $event['time']),True);
    
      add_post_meta($id,'_EventStartHour',2);
      add_post_meta($id,'_EventStartMinute',29);
      add_post_meta($id,'_EventStartMeridian','pm');

      
		  
      add_post_meta($id,'_EventOrganizerID',$row->id_in_source ,True);
      add_post_meta($id,'_EventVenueID',$event['venue']['name'],True);
		  
      add_post_meta($id,'_EventSource',$row->id,True);
      add_post_meta($id,'_EventSourceID',$row->id_in_source ,True);
      add_post_meta($id,'_EventIDinSource',$event['id'],True);
      
      
    }   
    
    
  } else {
    print('No API key for meetup given!<br/>');
  }
}

class RegisterEventSourceWidget extends WP_Widget {
  function RegisterEventSourceWidget() {
    parent::WP_Widget( false, $name = 'Register New Event Source Widget' );
  }

  function widget( $args, $instance ) {
    global $wpdb;
    extract( $args );
    $title = apply_filters( 'widget_title', $instance['title'] );
    ?>

    <?php
	echo $before_widget;
    ?>

    <?php
      if ($title) {
	echo $before_title . $title . $after_title;
      }
      
      
    ?>

    <div class="nw_source">
      <label for="source_name">Name of Event Source:</label>
      <input type="text" name="source_name" id="event_site_id"/>
      <label for="kind_of_event_source">Kind of Event Source:</label>
      <select id="kind_of_event_site_id" name="kind_of_event_source">
	<option value="fbpage">Facebook Page</option>
	<option value="mtupgr">Meetup Group</option>
      </select>
      <label for="source_name">URL to Event source (e.g. Facebook link):</label>
      <input type="text" name="id_in_source" id="id_in_source_id"/>
      
      <label for="categories">Categories for loaded events: (comma separated)</label>
      <input type="text" name="categories" id="categories_id"></input>
      <input type='button' value='Submit' id='my_text_submit_id'/>
    </div>

     <?php
       echo $after_widget;
     ?>
     <?php
  }
}

add_action('admin_menu', 'register_admin_page');

function register_admin_page() {
   add_menu_page('EventAggregator', 'EventAggregator', 'add_users', 'EventAggregator/admin.php', '',   plugins_url('EventAggregator/images/icon.png'));
}

add_action( 'widgets_init', 'MyWidgetInit' );
function MyWidgetInit() {
  register_widget( 'MyWidget' );
  register_widget( 'RegisterEventSourceWidget' );
}

register_activation_hook(__FILE__,'on_activation');
register_deactivation_hook(__FILE__,'on_deactivation');

add_action('hourly_sync_event', 'hourly_sync');

?>
