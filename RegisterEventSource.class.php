<?php

class RegisterEventSourceWidget extends WP_Widget {
  function RegisterEventSourceWidget() {
    parent::WP_Widget( false, $name = 'Register New Event Source Widget' );
  }
  
  function update( $new_instance, $old_instance ) {
	$instance = $old_instance;

	//Strip tags from title and name to remove HTML
	$instance['title'] = strip_tags( $new_instance['title'] );
	$instance['source_name'] = strip_tags( $new_instance['source_name'] );
	$instance['contact_person'] = $new_instance['contact_person'];
        $instance['id_in_source'] = strip_tags( $new_instance['id_in_source'] );

	return $instance;
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

      if( isset( $_POST ) ) {
        
        $source_name= sanitize_text_field($_POST['source_name']);
        $id_in_source= sanitize_text_field($_POST['id_in_source']);
        $contact_person= sanitize_email($_POST['contact_person']);

        $wpres= $wpdb->insert('wp_EventA_sources',
                      array(
                            'name' => $source_name,
                            'id_in_source' => $id_in_source,
                            'enabled' => False,
                            'take_all_events' => False, 
                            'contact_email' => $contact_person,
                            'type' => 'tbd'),
                      array(
                            '%s', '%s','%d','%d','%s','%s'));
        
        if ($wpres != False) {
            print '<p>Thank you for adding '.$source_name.' to the list.</p> ';
            print '<p>We will notify you at '.$contact_person.' about our decision.</p> ';
        } else {
            print '<p>Error during adding the new event source.</p>';
        }
        
        

        print'<hr/>';
      } 
      
    ?>

    <p>You can recommend new event sources for inclusion in our calendar.</p>
    <b>Please enter the name of the site, the URL (e.g. Facebook Page URL) and the email address of a contact person in the form below!</b>
    <form method="post">
        <div class="new_event_source">
          <label for="source_name">Name of Event Source:</label>
          <input type="text" name="source_name" id="source_name_id"/>
          <label for="source_name">URL to Event source (e.g. Facebook link):</label>
          <input type="text" name="id_in_source" id="id_in_source_id"/>
          <label for="source_name">Contact person eMail address:</label>
          <input type="text" name="contact_person" id="contact_person_id" value="<?php echo $contact_person; ?>"/>
          
          
          <input type='submit' value='Submit' id='my_text_submit_id'/>
        </div>
    </form>

     <?php
      
       
       echo $after_widget;
  }
}
?>