<?php

namespace Flare\Application {

    /**
     * 
     * @author anthony
     * 
     */
    interface Window
    {
        /**
         * 
         * @return string|array
         */
        public function assets();
    }

}

namespace {

    if (!function_exists('asset')) {

        /**
         * 
         * @param string $location
         * @param string $cacheBuster
         * @return string
         */
        function asset($location, $cacheBuster = null)
        {
            return ltrim($location, '/').'.js'.($cacheBuster ? '?v='.$cacheBuster : '');
        }
    }

    if (!function_exists('assets')) {

        /**
         * 
         * @param array $scripts
         * @param string $cacheBuster
         * @return array
         */
        function assets(array $scripts, $cacheBuster = null)
        {
            $assets = array();
            foreach ($scripts as $script) {
                $assets[] = asset($script, $cacheBuster);
            }
            return $assets;
        }
    }

}