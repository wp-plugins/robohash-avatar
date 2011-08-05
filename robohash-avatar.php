<?php
/*
Plugin Name: RoboHash Avatar
Plugin URI: http://trepmal.com/plugins/robohash-avatar/
Description: Robohash characters as default avatars 
Author: Kailey Lampert
Version: 0.1
Author URI: http://kaileylampert.com/
*/
/*
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
	}
	function addavatar ( $avatar_defaults ) {
		$avatar_defaults[ $this->url . '?set=set1' ] = 'Robohash (Robots)';
		$avatar_defaults[ $this->url . '?set=set2' ] = 'Robohash (Monsters)';
		$avatar_defaults[ $this->url . '?set=set3' ] = 'Robohash (Robot Heads)';
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
}
new robohashavatar( );