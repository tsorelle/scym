<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2015
 * Time: 7:02 AM
 */

namespace Drupal\tops\content;


use Tops\sys\IContentManager;

class NodeManager implements IContentManager
{
    public function getText($pathAlias) {
        $path = \drupal_lookup_path('source',$pathAlias);
        $parts = explode('/',$path);
        $result ='';
        if (sizeof($parts) == 2 && $parts[0] == 'node') {
            $nodeId = $parts[1];
            $node = node_load($nodeId);
            if (isset($node->body)) {
                if (is_array($node->body) && array_key_exists('und',$node->body)) {
                    $result = $node->body["und"][0]["value"];
                }
            }
        }
        return $result;
    }
}