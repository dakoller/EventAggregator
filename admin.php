<?php

$plugin_pref = 'EventAggregator_';

$admin_path = 'admin.php?page=EventAggregator/admin.php';
$admin_url = admin_url($admin_path);
echo $admin_url;
//check new input
if (isset($_POST)) {
    echo '<ul>';
    foreach (array_keys($_POST) as $p1) {
        echo '<li>'.$_POST[$p1].'</li>';
    }
    echo '</ul><hr/>';
}
print(var_dump($_POST));

?>
<h3>EventAggregator Settings</h3>

<h4>Event Sources</h4>
<table>
    <thead>
        <tr>
            <td>ID</td>
            <td>Source Name</td>
            <td>Type of Event Source</td>
            <td>Link to Event Page</td>
            <td>Take all events</td>
            <td>Event Tags</td>
            <td>Contact eMail</td>
            <td>Actions</td>
            
            
        </tr>
    </thead>
    <tbody>
    <?php
        global $wpdb;
  
        $sources = $wpdb->get_results('select * from wp_EventA_sources where enabled=1');
        foreach ($sources as $source) {
    ?>
        <tr>
            <td><?php if ($source->enabled) { echo '<b>';} ?>
                <?php echo $source->id; ?>
                <?php if ($source->enabled) { echo '</b>';} ?>
            </td>
            <td>
                <?php if ($source->enabled) { echo '<b>';} ?>
                <?php echo $source->name; ?>
                <?php if ($source->enabled) { echo '</b>';} ?>
            </td>
            <td><?php if ($source->enabled) { echo '<b>';} ?>
                <?php echo $source->type; ?>
                <?php if ($source->enabled) { echo '</b>';} ?></td>
            
            <td>
                <?php
                if ($source->enabled) { echo '<b>';}
                if ($source->type == 'fbpage') {
                    $link= 'https://www.facebook.com/' . $source->id_in_source . '/events';
                    echo '<a href="'. $link .'" target="_new">'.$link.'</a>';
                }
                if ($source->enabled) { echo '</b>';}
                ?>
            </td>
            
            <td>
                <?php
                if ($source->enabled) { echo '<b>';}
                if ($source->take_all_events) {
                    echo 'Yes';
                } else {
                    echo 'No';
                }
                if ($source->enabled) { echo '</b>';}
                ?>
            </td>
            
            <td>
                <?php
                if ($source->enabled) { echo '<b>';}
                if ($source->tags != '') {
                    $tags= explode(',',$source->tags);
                    echo '<ul>';
                    foreach ($tags as $tag) {
                        echo '<li>'. $tag. '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo 'No tags assigned.';
                }
                if ($source->enabled) { echo '</b>';}
                ?>
            </td>
            
            <td>
                <?php
                if ($source->enabled) { echo '<b>';} 
                if ($source->contact_email != '') {
                    $adr= $source->contact_email;
                    echo '<a href="mailto:'.$adr.'">'. $adr .'</a>';
                    
                } else {
                    echo 'No contact eMail assigned.';
                };
                if ($source->enabled) { echo '</b>';}
                ?>
            </td>
            
            <td>
                <?php
                    if ($source->enabled) {
                        echo '<a href="">Disable event source</a>';
                    } else {
                        echo '<a href="">Enable event source</a>';
                        echo ' | ';
                        echo '<a href="">Delete event source</a>';
                        
                    }
                    echo ' | ';
                    
                    if ($source->take_all_events) {
                        echo '<a href="">Require editor approval</a>';
                    } else {
                        echo '<a href="">Publish all events immediately</a>';
                    }
                ?>
                
            </td>
            
            
            
        </tr>
    
    <?php
        }
    ?>
    </tbody>
    
</table>

<hr/>
<h4>API Keys & oAuth Information</h4>
<p>Updating values does not work yet!!!</p>
<form  action="#" method="post">
<table>
    <tr>
        <td colspan=2>
            <h5>Facebook App Settings</h5>
        </td>
        <td>
            Enter new settings & click 'Update settings'
        </td>
    </tr>
    <tr>
        <td>App ID:</td>
        <td><?php echo get_option($plugin_pref . 'Facebook App ID'); ?></td>
        <td><input type="text" id="new_fb_app_id"/></td>
    </tr>
    <tr>
        <td>App Secret:</td>
        <td><?php echo get_option($plugin_pref .'Facebook App Secret'); ?></td>
        <td><input type="text" id="new_fb_app_secret"/></td>
    </tr>
    <tr>
        <td>App Access Token:</td>
        <td><?php echo get_option($plugin_pref .'Facebook App Access Token'); ?></td>
        <td><input type="text" id="new_fb_app_access_token"/></td>
    </tr>
    
    <tr>
        <td colspan=2>
            <h5>Meetup API Settings</h5>
        </td>
        <td>
            Enter new settings & click 'Update settings'
        </td>
    </tr>
    <tr>
        <td>API Key:</td>
        <td><?php echo get_option($plugin_pref . 'Meetup API Key'); ?></td>
        <td><input type="text" id="new_meetup_api_key"/></td>
    </tr>
    
    <tr>
        <td colspan=2>
            <input type="submit" value="Update settings"/></td>
        </td>
    </tr>
    
</table>
</form>