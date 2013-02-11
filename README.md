=== WordPress EventAggregator ===
Contributors: dakoller, tailorvj
Donate link: http://example.com/
Tags: event, calendar, aggregation, socialnetwork, facebook, meetup, api
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The EventAggregator takes a curated list of Facebook pages, Facebook search terms (to be used for local public events), Meetup groups or Meetup search terms (to be used to search in local public events) and fills the calendar of events from WordPress installation for further presentation.
Based on this plugin you can create a topic-focussed list and/or local event list based on social network information.

== Description ==

The EventAggregator depends on an installation of the EventCalendar installation from Modern Tribes ( http://tri.be/wordpress-events-calendar/ ).

EventAggregator focusses and acquisition and aggregation of events form social networks and APIs and does not support specific means of presentation: the have to be enabled using the Modern Tribes plugins/settings.
The plugin can either directly publishes all fitting eventsdirectly on the page or can create 'pending' posts, which requires the approval of an editor to be published.
("take_all_events" setting, can be set on a per-page level)

EventAggregator supports initially Facebook and Meetup.

For Facebook, you can list events from Facebooks pages and you can take public event around a certain location. (given in WGS84 coordinates)
Filtering events is possible based on word in the event title or in the event description.

For Meetup, you can add a Meetup groups and you can also get in public events around a certain location (filtered by searchterms for event title and description).

For both event sources you need to authorize yourself:
*    For Facebook you need to create a Facebook application (take application id and application secret and enter them in the admin dialog). (see https://developers.facebook.com/apps --> "Create new app"  )
*    For Meetup you need to enter an API key, which you can retrieve via http://www.meetup.com/meetup_api/key/ . (oAuth authentication for Meetup is not supported)

Further possible event channels include e.g. EventBrite.

The plugin will provide some widgets for the admin and the enduser pages:
*    A (enduser visible) plugin to enter a new event source, event source has to be approved by admin in the admin page of the plugin.
*    A (admin visible) plugin containing imported but pending events, enabling immediate publishing of the event. 

As a technical description:
*    Events are added as posts of the type 'tribe_events'.
*    The sources for events are stored in a custom database table (called 'wp_EventA_sources').
*    The plugin contains an admin page for maintenance for API keys, location information and event sources.
*    Location information is entered as 'Location Longitude' and 'Location Latitude': you can find your coordinates e.g. via openstreetmap.org.
*    The event sync is a scheduled process, which takes place all 3 hours. (not yet implemented)

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the EventAggregator folder to the `/wp-content/plugins/` directory
1. Create the wp_EventA_sources table on your database. (this will be automated later)
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

The database table creation code is: 
<code>
CREATE TABLE IF NOT EXISTS `wp_EventA_sources` (
  `type` varchar(6) COLLATE latin1_german2_ci NOT NULL,
  `name` varchar(40) COLLATE latin1_german2_ci NOT NULL,
  `id_in_source` varchar(50) COLLATE latin1_german2_ci DEFAULT NULL,
  `take_all_events` tinyint(1) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tags` text COLLATE latin1_german2_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
</code>

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets 
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png` 
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.1 =
* Initial version
