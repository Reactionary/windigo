<?php
/*
Plugin Name: Add Link to Facebook
Plugin URI: http://wordpress.org/extend/plugins/add-link-to-facebook/
Description: Automatically add links to published posts to your Facebook wall or pages
Version: 1.109
Author: Marcel Bokhorst
Author URI: http://blog.bokhorst.biz/about/
*/

/*
	Copyright (c) 2011 Marcel Bokhorst

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

#error_reporting(E_ALL);

// Check PHP version
if (version_compare(PHP_VERSION, '5.0.0', '<'))
	die('Add Link to Facebook requires at least PHP 5, installed version is ' . PHP_VERSION);

// Debug console
if (get_option('al2fb_debug')) {
	require_once('PhpConsole.php');
	PhpConsole::start();
}

// Include support class
require_once('add-link-to-facebook-class.php');

// Check pre-requisites
WPAL2Facebook::Check_prerequisites();

// Start plugin
global $wp_al2fb;
if (empty($wp_al2fb)) {
	$wp_al2fb = new WPAL2Facebook();
	register_activation_hook(__FILE__, array(&$wp_al2fb, 'Activate'));
}

// Template tag for likers
if (!function_exists('al2fb_likers')) {
	function al2fb_likers($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_likers($post);
	}
}

// Template tag for like count
if (!function_exists('al2fb_like_count')) {
	function al2fb_like_count($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_like_count($post);
	}
}

// Template tag for Facebook like button
if (!function_exists('al2fb_like_button')) {
	function al2fb_like_button($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_like_button($post, false);
	}
}

// Template tag for Facebook like box
if (!function_exists('al2fb_like_box')) {
	function al2fb_like_box($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_like_button($post, true);
	}
}

// Template tag for Facebook send button
if (!function_exists('al2fb_send_button')) {
	function al2fb_send_button($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_send_button($post);
	}
}

// Template tag for Facebook comments plugins
if (!function_exists('al2fb_comments_plugin')) {
	function al2fb_comments_plugin($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_comments_plugin($post);
	}
}

// Template tag for Facebook face pile
if (!function_exists('al2fb_face_pile')) {
	function al2fb_face_pile($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_face_pile($post);
	}
}

// Template tag for profile link
if (!function_exists('al2fb_profile_link')) {
	function al2fb_profile_link($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_profile_link($post);
	}
}

// Template tag for Facebook registration
if (!function_exists('al2fb_registration')) {
	function al2fb_registration($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_registration($post);
	}
}

// Template tag for Facebook login
if (!function_exists('al2fb_login')) {
	function al2fb_login($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_login($post);
	}
}

// Template tag for Facebook activity feed
if (!function_exists('al2fb_activity_feed')) {
	function al2fb_activity_feed($post_ID = null) {
		global $wp_al2fb;
		if (empty($post_ID))
			global $post;
		else
			$post = get_post($post_ID);
		if (isset($post))
			echo $wp_al2fb->Get_activity_feed($post);
	}
}

// That's it!

if (!function_exists('al2fb_comment_example')) {
	function al2fb_comment_example($message, $comment, $post) {
		// Author
		$message = $comment->comment_author . ' ' .  __('commented on', c_al2fb_text_domain) . ' ';
		// Blog title
		$message .= html_entity_decode(get_bloginfo('title'), ENT_QUOTES, get_bloginfo('charset'));
		// In reply to
		if (!empty($comment->comment_parent)) {
			$parent = get_comment($comment->comment_parent);
			if (!empty($parent))
				$message .= ' (in reply to ' . $parent->comment_author . ')';
		}
		// New lines
		$message .= ":\n\n";
		// Comment text
		$message .= $comment->comment_content;

		return $message;
	}
	//add_filter('al2fb_comment', 'al2fb_comment_example', 10, 3);
}

?>
