<?php

/**
 * @file
 * template.php
 */

use Drupal\tops\Mvvm\TViewModel;

/**
 * Overrides theme_status-Messages
 *
 * Inserts a KnockoutJS component element for ViewModel pages
 *
 * @param $variables
 * @return string|void
 */
function topsy_status_messages($variables) {
    $vmPath = TViewModel::getVmPath();
    if (empty($vmPath)) {
        return;
    }

    $display = $variables['display'];
    $output = '<messages-component></messages-component>';
/*
 * // would we ever need to display both drupal messages and Peanut service messags?
 *
    $messages = drupal_get_messages($display);
    if (sizeof($messages) > 0) {
        $existingContent = $variables['messages'];
    }
*/
    return $output;
}


