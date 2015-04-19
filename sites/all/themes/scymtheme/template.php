<?php

use Drupal\tops\Mvvm\TViewModel;


/**
 * Overrides theme_status_messages
 *
 * Inserts a KnockoutJS component element for ViewModel pages
 *
 * @param $variables
 * @return string|void
 */
function scymtheme_status_messages($variables) {
    $vmPath = TViewModel::getVmPath();
    $result = bootstrap_status_messages($variables);
    if (!empty($vmPath)) {
        $result = "$result\n<messages-component></messages-component>";
    }
    return $result;
}



