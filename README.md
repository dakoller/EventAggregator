EventAggregator
===============

WordPress EventAggregator

=== Plugin Name ===
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

As a technical description:
*    Events are added as posts of the type 'tribe_events'.
*    The sources for events are stored in a custom database table (called 'wp_EventA_sources').
*    The plugin contains an admin page for maintenance for API keys, location information and event sources.
*    Location information is entered as 'Location Longitude' and 'Location Latitude': you can find your coordinates e.g. via openstreetmap.org.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

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

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
