<?php

namespace Essential_Addons_Elementor\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

trait Cache_Control
{
    /**
     * Clear self cache files
     *
     * @since 3.0.0
     */
    public function clear_cache()
    {
        check_ajax_referer('essential-addons-elementor', 'security');

        do_action('eael/before_clear_cache');

        // clear self cache
        $this->empty_dir(EAEL_ASSET_PATH);

        // clear 3rd party cache
        $this->clear_3rd_party_cache();

        do_action('eael/after_clear_cache');

        wp_send_json(true);
    }

    /**
     * Clear 3rd party cache files
     *
     * @since 4.3.6
     */
    public function clear_3rd_party_cache()
    {
        global $file_prefix, $wp_fastest_cache, $kinsta_cache;

        do_action('eael/before_clear_3rd_party_cache');

        // W3 Total Cache
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }

        // WP Super Cache
        if (function_exists('wp_cache_clean_cache') && !empty($file_prefix)) {
            wp_cache_clean_cache($file_prefix, true);
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

        // WP Rocket
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
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
