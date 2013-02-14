<?php

require_once('Meetup-API-client-for-PHP-master/Meetup.php');


	
$m_con = new MeetupKeyAuthConnection("80401a73f2e222326b633a55217d37");

$m_events = new MeetupEvents($m_con);

foreach ($m_events->getOpenEvents(array("lat" => 43.13, "lon" => 11.5, text="startup")) as $m_event ) {
    echo "<p>.</p>";
}





?>
kjhkh
