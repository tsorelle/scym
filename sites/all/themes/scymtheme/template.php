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

/**
 * Process variables for comment.tpl.php.
 *
 * @see comment.tpl.php
 */
function scymtheme_preprocess_comment(&$variables) {
    $comment = $variables['elements']['#comment'];
    $node = $variables['elements']['#node'];
    $variables['comment']   = $comment;
    $variables['node']      = $node;
    $variables['author']    = theme('username', array('account' => $comment));

    $variables['created']   = format_date($comment->created);


    // Avoid calling format_date() twice on the same timestamp.
    if ($comment->changed == $comment->created) {
        $variables['changed'] = $variables['created'];
    }
    else {
        $variables['changed'] = format_date($comment->changed);
    }

    $variables['new']       = !empty($comment->new) ? t('new') : '';
    $variables['picture']   = theme_get_setting('toggle_comment_user_picture') ? theme('user_picture', array('account' => $comment)) : '';
    $variables['signature'] = $comment->signature;

    $uri = entity_uri('comment', $comment);
    $uri['options'] += array('attributes' => array('class' => array('permalink'), 'rel' => 'bookmark'));

    // $variables['title']     = l($comment->subject, $uri['path'], $uri['options']);

    if (!isset($comment->uid)) {
        $authorName = 'Some user';
    }
    else {
        $authorId = $comment->uid;
        $authorObject = \Tops\sys\TUser::getById($authorId);
        $authorName = $authorObject->getUserShortName();
        $userName = $authorObject->getUserName();

        if (empty($authorName) || $authorName == $userName) {
            $authorName = "User ".$userName;
        }
    }

    $variables['author-name'] = $authorName;

    $titleDate = date("F j",$comment->created);

    $comment->subject ='On '.$titleDate.' '.$authorName.' wrote ...';
    $variables['title']     = l($comment->subject, $uri['path'], $uri['options']);
    $variables['permalink'] = l(t('Permalink'), $uri['path'], $uri['options']);
    $variables['submitted'] = t('Submitted by user !username on !datetime', array('!username' => $variables['author'], '!datetime' => $variables['created']));

    // Preprocess fields.
    field_attach_preprocess('comment', $comment, $variables['elements'], $variables);

    // Helpful $content variable for templates.
    foreach (element_children($variables['elements']) as $key) {
        $variables['content'][$key] = $variables['elements'][$key];
    }

    // Set status to a string representation of comment->status.
    if (isset($comment->in_preview)) {
        $variables['status'] = 'comment-preview';
    }
    else {
        $variables['status'] = ($comment->status == COMMENT_NOT_PUBLISHED) ? 'comment-unpublished' : 'comment-published';
    }

    // Gather comment classes.
    // 'comment-published' class is not needed, it is either 'comment-preview' or
    // 'comment-unpublished'.
    if ($variables['status'] != 'comment-published') {
        $variables['classes_array'][] = $variables['status'];
    }
    if ($variables['new']) {
        $variables['classes_array'][] = 'comment-new';
    }
    if (!$comment->uid) {
        $variables['classes_array'][] = 'comment-by-anonymous';
    }
    else {
        if ($comment->uid == $variables['node']->uid) {
            $variables['classes_array'][] = 'comment-by-node-author';
        }
        if ($comment->uid == $variables['user']->uid) {
            $variables['classes_array'][] = 'comment-by-viewer';
        }
    }
}



