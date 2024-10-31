<?php
/*
 Plugin Name: schmie_LstFM2.0
 Plugin URI: http://schmiddi.co.cc/wordpress_plugins/
 Description: LastFM Widget 
 Author: Michael Schmitt (schmiddim@gmx.at)
 Version: 1.0
 Author URI: http://schmiddi.co.cc/wordpress_plugins/
 License: GPL 2.0, @see http://www.gnu.org/licenses/gpl-2.0.html
 @date 09.03.09
 @desc Wordpress plugin fuer Last.fm
 
 
 */

require_once 'lib/lstfm.php';
#wordpress
function schmie_lstfm_init() {

	#Ueberorueftt Wordpress-Funktion, Abbruch wenn nicht vorhanden
	if ( !function_exists('wp_register_sidebar_widget') )
		return;

	# Ausgabe Frontend
	function  schmie_lstfm($args) {
		extract($args);
		// Auslesen der Optionen
		$options = get_option('schmie_lstfm');
		$titel = htmlspecialchars($options['titel'], ENT_QUOTES);
		$username=htmlspecialchars($options['username'], ENT_QUOTES);
		$tracks=htmlspecialchars($options['tracks'], ENT_QUOTES);
		$show_link=htmlspecialchars($options['show_link'], ENT_QUOTES);
		// Ausgabe des Widgets
		echo $before_widget . $before_titel;
		echo "<h3>$titel</h3>";

	
		$last = new lastfm($username);
		echo $last->echoCurrentTrack();
		echo $last->echoRecentTracks_1($tracks);
		echo $after_widget;
		
		
	}

	// back end controller
	function schmie_lstfm_control() {

		// Auslesen der Optionen
		$options = get_option('schmie_lstfm');
		// Wenn Optionen nicht angegeben, Default-Werte setzen
		if ( empty($options) )  {
			$options = array('titel'=>'schmie_lstfm',                              	         				  
	         				'username'=>'username',
							'tracks'=>'10',
						#	'show_link'=>'show'
	         				  );
		}
				
		if ( $_POST['schmie_lstfm-submit'] ) {
			$options['titel'] = strip_tags(stripslashes($_POST['schmie_lstfm-titel']));
			$options['username'] = strip_tags(stripslashes($_POST['schmie_lstfm-username']));
			$options['tracks'] = strip_tags(stripslashes($_POST['schmie_lstfm-tracks']));
			$options['show_link'] = strip_tags(stripslashes($_POST['schmie_lstfm-show_link']));
			update_option('schmie_lstfm', $options);
		}

		$titel = htmlspecialchars($options['titel'], ENT_QUOTES);		
		$tracks = htmlspecialchars($options['tracks'], ENT_QUOTES);
		$username = htmlspecialchars($options['username'], ENT_QUOTES);
		$show_link = htmlspecialchars($options['show_link'], ENT_QUOTES);
	

		echo '
<p style="text-align:right;"><label for="schmie_lstfm-titel">Titel
<input style="width: 150px;" id="schmie_lstfm-titel" name="schmie_lstfm-titel" type="text" value="'.$titel.'" /></label>
 
';

		echo '
<p style="text-align:right;"><label for="schmie_lstfm-username">username:
<input style="width: 150px;" id="schmie_lstfm-username" name="schmie_lstfm-username" type="text" value="'.$username.'" /></label>
 
';		
		echo '
<p style="text-align:right;"><label for="schmie_lstfm-tracks">How many tracks?:
<input style="width: 150px;" id="schmie_lstfm-tracks" name="schmie_lstfm-tracks" type="text" value="'.$tracks.'" /></label>
 
';
		

		
###########################		
		echo '
<input type="hidden" id="schmie_lstfm-submit" name="schmie_lstfm-submit" value="1" />';
	}

	wp_register_sidebar_widget('schmie_lstfm', 'schmie_lstfm',
                                   'schmie_lstfm',
	array(
                                         'classname' => 'schmie_lstfm',
        	                             'description' =>'enter your Lastfm Username' ) );
	wp_register_widget_control('schmie_lstfm', 'On Last FM',
                                   'schmie_lstfm_control',
	array( 'width' => 300  ) );

}

add_action('widgets_init', 'schmie_lstfm_init');

?>
