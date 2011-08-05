<?php
/*
Plugin Name: RoboHash Avatar
Plugin URI: http://trepmal.com/plugins/robohash-avatar/
Description: Robohash characters as default avatars 
Author: Kailey Lampert
Version: 0.2
Author URI: http://kaileylampert.com/

Copyright (C) 2011  Kailey Lampert

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class robohashavatar {
	var $url = 'http://robohash.org/emailhash.png';
	function robohashavatar( ) {
		add_filter( 'avatar_defaults' , array( &$this , 'addavatar' ) );
		add_filter( 'get_avatar', array( &$this, 'insert_hash' ), 10, 5 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'scripts' ) );
		add_action( 'load-options.php', array( &$this, 'update' ) );
	}
	function addavatar ( $avatar_defaults ) {
		$options = get_option( 'robohash_options', array( 'bot' => 'set1', 'bg' => 'bg1' ) );

		$bots = '<label for="robohash_bot">Body</label> <select id="robohash_bot" name="robohash_bot">';
		$bots .= '<option value="set1" '.selected( $options['bot'], 'set1', false ).'>Robots</option>';
		$bots .= '<option value="set2"'.selected( $options['bot'], 'set2', false ).'>Monsters</option>';
		$bots .= '<option value="set3"'.selected( $options['bot'], 'set3', false ).'>Robot Heads</option>';
		$bots .= '<option value="any"'.selected( $options['bot'], 'any', false ).'>Any</option>';
		$bots .= '</select> ';

		$bgs = '<label for="robohash_bg">Background</label> <select id="robohash_bg" name="robohash_bg">';
		$bgs .= '<option value="" '.selected( $options['bg'], '', false ).'>None</option>';
		$bgs .= '<option value="bg1" '.selected( $options['bg'], 'bg1', false ).'>Scene</option>';
		$bgs .= '<option value="bg2" '.selected( $options['bg'], 'bg2', false ).'>Abstract</option>';
		$bgs .= '<option value="any" '.selected( $options['bg'], 'any', false ).'>Any</option>';
		$bgs .= '</select>';
		$hidden = '<input type="hidden" id="spinner" value="'. admin_url('images/wpspin_light.gif') .'" />';

		$avatar_defaults[ $this->url . "?set={$options['bot']}&bgset={$options['bg']}" ] = 	$bots.$bgs.$hidden;

		return $avatar_defaults;
	}
	function insert_hash( $avatar, $id_or_email, $size, $default, $alt ) {
		if ( strpos( $default, $this->url ) !== false && is_object( $id_or_email ) ) {
			$email = $id_or_email->comment_author_email;
			$email = empty( $email ) ? 'nobody' : md5( $email );
			$avatar = str_replace( 'emailhash', $email, $avatar );
		}
		return $avatar;
	}	
	function scripts( $hook ) {
		if ( $hook != 'options-discussion.php' ) return;
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'robohash', plugins_url( 'robohash.js', __FILE__ ), array('jquery') );
	}
	function update( ) {
		if ( isset($_POST['robohash_bot']) && isset($_POST['robohash_bg']) ) {
			$options = array(
				'bot' => esc_attr( $_POST['robohash_bot'] ),
				'bg' => esc_attr( $_POST['robohash_bg'] )
			);
			update_option( 'robohash_options', $options );
		}
	}
}
new robohashavatar( );