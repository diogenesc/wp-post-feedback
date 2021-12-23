<?php
/**
 * Plugin Name:       WP Post Feedback
 * Plugin URI:        https://diogenesc.com
 * Description:       Create endpoints to count user like/unlike as post metadata
 * Version:           1.0.0
 * Author:            Diógenes Castro
 * Author URI:        https://diogenesc.com
 * Text Domain:       wp-post-feedback
 * Domain Path:       /langs
 * License:           MIT
 */

if (!defined('ABSPATH')) {
    die('You can not access this file!');
}

class WP_Post_Feedback
{
    static $instance = false;

    private function __construct()
    {
        add_action('init', array($this, 'loadTextdomain'));
        add_action('rest_api_init', array($this, 'registerRestRoute'));
        add_filter('manage_post_posts_columns', array($this, 'addFeedbackColumnsToPostList'));
        add_action('manage_post_posts_custom_column', array($this, 'fillFeedbackColumns'), 10, 2);
        add_filter('manage_edit-post_sortable_columns', array($this, 'makeFeedbackColumnsSortable'));
    }

    public function loadTextdomain()
    {
        load_plugin_textdomain('wp-post-feedback', false, dirname(plugin_basename( __FILE__ )) . '/languages');
    }

    public function registerRestRoute()
    {
        register_rest_route('wp-post-feedback/v1', '/post/(?P<id>\d+)/like', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'likeCountIncrement'),
            'permission_callback' => function() {
                return current_user_can('edit_others_posts');
            },
        ));
        register_rest_route('wp-post-feedback/v1', '/post/(?P<id>\d+)/unlike', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'unlikeCountIncrement'),
            'permission_callback' => function() {
                return current_user_can('edit_others_posts');
            },
        ));
    }

    /**
     * Callbacks
     */
    public function likeCountIncrement($data) {
        $metaKey = 'like_count';

        $likesCount = get_post_meta($data['id'], $metaKey, true);

        if (!$likesCount) {
            $likesCount = 0;
        }

        update_post_meta($data['id'], $metaKey, intval($likesCount) + 1);

        return [
            'article_id' => $data['id'],
            'like_count' => get_post_meta($data['id'], $metaKey, true),
        ];
    }

    public function unlikeCountIncrement($data) {
        $metaKey = 'unlike_count';

        $unlikesCount = get_post_meta($data['id'], $metaKey, true);

        if (!$unlikesCount) {
            $unlikesCount = 0;
        }

        update_post_meta($data['id'], $metaKey, intval($unlikesCount) + 1);

        return [
            'article_id' => $data['id'],
            'unlike_count' => get_post_meta($data['id'], $metaKey, true),
        ];
    }

    /**
     * Add columns to posts list
     */
    public function addFeedbackColumnsToPostList($columns) {
        return array_merge($columns, ['like_count' => __('Likes count', 'wp-post-feedback')], ['unlike_count' => __('Unlikes count', 'wp-post-feedback')]);
    }
    
    public function fillFeedbackColumns($column_key, $post_id) {
        if ($column_key == 'like_count') {
            echo get_post_meta($post_id, 'like_count', true) ?: '-';
        }
        if ($column_key == 'unlike_count') {
            echo get_post_meta($post_id, 'unlike_count', true) ?: '-';
        }
    }

    /**
     * Make columns sortable
     */
    public function makeFeedbackColumnsSortable($columns) {
        $columns['like_count'] = 'like_count';
        $columns['unlike_count'] = 'unlike_count';

        return $columns;
    }

    public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new self;
        }

		return self::$instance;
	}
}

$WP_Post_Feedback = WP_Post_Feedback::getInstance();