<?php
/*
Plugin Name: iCal List - w3lab
Plugin URI: http://www.w3lab.com.br/ics-list/
Description: Display a list from iCal feeds (ics).
Author: Artus Rocha
Author URI: http://www.w3lab.com.br/
Version: 0.0.1
Text Domain: w3lab_ics_list
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/*
 * Este plugin usa https://github.com/u01jmg3/ics-parser
*/

require_once 'ics-parser/vendor/autoload.php';
use ICal\ICal;

function w3lab_ics_list($atts,$ctts){
 if( empty($atts['ics']) )
  return ;
 // ================= Parametros =================== //
 $atts = shortcode_atts(
  array(
   'date_format' => 'd/m/Y',
   'link_target' => '_self',
   'count' => 5,
   'ics' => '',
   'print_r' => false, //for debug
   'css_url' => __DIR__ . '/basic.css',
   'css' => '',
   'offset' => 0,
  )
  , $atts
 );
 // ================================================ // 

 $out = array('<ul class="w3lab-ics-list">');
 $out[] = '<style>'.file_get_contents($atts['css_url']).'</style>';
 if( !empty($atts['css']) )
  $out[] = '<style>'.$atts['css'].'</style>';

 $ical = new ICal( $atts['ics'] );
 $events = $ical->events();
 $events = $ical->sortEventsWithOrder($events,SORT_DESC);
 $n = $atts['offset'];
 foreach ($events as $event) {
		// == TO-DO: Templates suport
  $out[] = '<li id="event-'.crc32( $event->uid ).'" class="w3lab-event">';
  $date = date($atts['date_format'], $ical->iCalDateToUnixTimestamp($event->dtstart)) ;
  $out[] = '<a target="' . $atts['link_target']
         . '" href="' . $event->url
         . '"><span class="dtstart">' . $date
         . '</span><spam class="summary">' . $event->summary . '</spam></a>';
  if( $atts['print_r'] )
   $out[] = '<pre>' . print_r($event, true) . '</pre>';
  $out[] = '</li>';
  $n++ ;
  if( $n >= $atts['count'] )
   break;
 }
 $out[] = '<div class="appended_contents">'.do_shortcode($ctts).'</div>';
 $out[] = '</ul>';
 return implode('',$out);
}
add_shortcode('w3lab_ics_list','w3lab_ics_list');

