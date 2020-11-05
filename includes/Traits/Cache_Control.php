<?php

namespace Essential_Addons_Elementor\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

trait Cache_Control
{
    public function clear_3rd_party_cache()
    {
        global $wp_fastest_cache, $kinsta_cache;

        do_action('eael/before_clear_3rd_party_cache');

        // W3 Total Cache
        if (function_exists('w3tc_pgcache_flush')) {
            w3tc_pgcache_flush();
        }

        // WP Super Cache
        if (function_exists('wp_cache_clean_cache')) {
            global $file_prefix, $supercachedir;

            if (empty($supercachedir) && function_exists('get_supercache_dir')) {
                $supercachedir = get_supercache_dir();
            }

            wp_cache_clean_cache($file_prefix);
        }

        // Wp Fastest Cache
        if (method_exists('WpFastestCache', 'deleteCache') && !empty($wp_fastest_cache)) {
            $wp_fastest_cache->deleteCache(true);
        }

        // WP Optimize
        if (class_exists('WP_Optimize') && defined('WPO_PLUGIN_MAIN_PATH')) {
            if (!class_exists('WP_Optimize_Cache_Commands')) {
                include_once WPO_PLUGIN_MAIN_PATH . 'cache/class-cache-commands.php';
            }

            if (class_exists('WP_Optimize_Cache_Commands')) {
                $wp_optimize_cache_commands = new \WP_Optimize_Cache_Commands();
                $wp_optimize_cache_commands->purge_page_cache();
            }
        }

        // Autoptimize
        if (class_exists('autoptimizeCache')) {
            \autoptimizeCache::clearall();
        }

        // LiteSpeed
        if (class_exists('\LiteSpeed\Purge')) {
            \LiteSpeed\Purge::purge_all();
        }

        // WP Engine
        if (class_exists('\WpeCommon')) {
            if (method_exists('\WpeCommon', 'purge_memcached')) {
                \WpeCommon::purge_memcached();
            }

            if (method_exists('\WpeCommon', 'clear_maxcdn_cache')) {
                \WpeCommon::clear_maxcdn_cache();
            }

            if (method_exists('\WpeCommon', 'purge_varnish_cache')) {
                \WpeCommon::purge_varnish_cache();
            }
        }

        // Kinsta
        if (class_exists('\Kinsta\Cache') && !empty($kinsta_cache)) {
            $kinsta_cache->kinsta_cache_purge->purge_complete_caches();
        }

        // SiteGround
        if (function_exists('sg_cachepress_purge_cache')) {
            sg_cachepress_purge_cache();
        }

        do_action('eael/after_clear_3rd_party_cache');
    }
}
