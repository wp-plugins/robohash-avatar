<?php
/*
Plugin Name: RoboHash Avatar
Plugin URI: http://trepmal.com/plugins/robohash-avatar/
Description: Robohash characters as default avatars 
Author: Kailey Lampert
Version: 0.4
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
		add_filter( 'get_avatar', array( &$this, 'insert_hash' ), 11, 5 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'scripts' ) );
		add_action( 'load-options.php', array( &$this, 'update' ) );
	}
	function addavatar ( $avatar_defaults ) {
		//create the extra avatar option, with options!
		//js is used to create a live preview
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

		//current avatar, based on saved options
		$avatar_url = "{$this->url}?set={$options['bot']}&bgset={$options['bg']}";

		$avatar_defaults[ $avatar_url ] = 	$bots.$bgs.$hidden;

		return $avatar_defaults;
	}
	function insert_hash( $avatar, $id_or_email, $size, $default, $alt ) {
		
		//determine email address
		if ( is_numeric($id_or_email) ) 
			$email = get_userdata( $id_or_email )->user_email;
		elseif ( is_object($id_or_email) ) 
			$email = $id_or_email->comment_author_email;
		else
			$email = $id_or_email;
		
		//since we're hooking directly into get_avatar,
		//we need to make sure another avatar hasn't been selected
		if ( strpos( $default, $this->url ) !== false ) {
			$email = empty( $email ) ? 'nobody' : md5( $email );
			
			//in rare cases were there is no email associated with the comment (like Mr WordPress)
			//we have to work around a bit to insert the custom avatar
			$direct = get_option('avatar_default');
			$new_av_url = str_replace( 'emailhash', $email, $direct );
			// 'www' version for WP2.9 and older
			if ( strpos( $default, 'http://0.gravatar.com/avatar/') === 0 || strpos( $default, 'http://www.gravatar.com/avatar/') === 0 )
				$avatar = str_replace( $default, $new_av_url."&size={$size}x{$size}", $avatar );

			//otherwise, just swap the placeholder with the hash
			$avatar = str_replace( 'emailhash', $email, $avatar );
			
			//this is ugly, but has to be done
			//make sure we pass the correct size params to the generated avatar
			$avatar = str_replace( '%3F', "%3Fsize={$size}x{$size}%26", $avatar );
			
		}

		return $avatar;
	}	
	function scripts( $hook ) {
		//we use this js for the live preview when toggling avatar options 
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