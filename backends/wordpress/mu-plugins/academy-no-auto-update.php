<?php
/*
 * Academy lab: keep WordPress at 5.3.2. With outbound internet, WordPress 5.3.2
 * background-updates itself to the latest 5.3.x within a minute of the first page
 * view (seen in the local test: 5.3.2 -> 5.3.27), which changes the old behaviour
 * the WAF exercises rely on. Added at container start by entry.sh.
 */
add_filter('automatic_updater_disabled', '__return_true');
add_filter('auto_update_core', '__return_false');
