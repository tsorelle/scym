<?php

use Drupal\tops\Mvvm\TViewModel;
use Tops\sys\TTracer;
use Tops\sys\TUser;


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
    // \Tops\sys\TTracer::Trace("Comment uid = $comment->uid");
    if (!isset($comment->uid)) {
        $authorName = 'Some user';
    }
    else {
        $authorId = $comment->uid;
        $authorObject = \Tops\sys\TUser::getById($authorId);
        \Tops\sys\TTracer::ShowArray($authorObject);
        $authorName = $authorObject->getUserShortName();
        $userName = $authorObject->getUserName();

        \Tops\sys\TTracer::Trace("Author user name is '$userName' or '$authorName'");

        if (empty($authorName) || $authorName == $userName) {
            $authorName = "User ".$userName;
        }
        // $authorObject->setProfileValue('username',$userName);
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

function _scymtheme_swapSubmittedName($submitted,$uid) {
    if (empty($submitted)) {
        return $submitted;
    }
    $authorObject = \Tops\sys\TUser::getById($uid);
    $fullName = $authorObject->getUserShortName();
    $userName = $authorObject->getUserName();
    if ($userName == 'admin' && empty($fullName)) {
        $fullName = 'The administrator';
    }
    if ($userName == $fullName) {
        return $submitted;
    }

    $endTag = (strstr($submitted,'<span')) ? '</span' : '</a';
    return str_replace($userName.$endTag,$fullName.$endTag,$submitted);
}

function scymtheme_preprocess_node(&$variables) {
    if (isset($variables['submitted']) && isset($variables['node'])) {
        $authorId = $variables['node']->uid;
        $variables['submitted'] = _scymtheme_swapSubmittedName($variables['submitted'],$variables['node']->uid);
    }

}


/**
 * Implements hook_preprocess_page().
 *
 * @see page.tpl.php
 */
function scymtheme_preprocess_page(&$variables) {

    $variables['siteshortname'] = 'SCYM';

    // Add information about the number of sidebars.
    if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
        $variables['content_column_class'] = ' class="col-sm-6"';
    }
    elseif (!empty($variables['page']['sidebar_first']) || !empty($variables['page']['sidebar_second'])) {
        $variables['content_column_class'] = ' class="col-sm-9"';
    }
    else {
        $variables['content_column_class'] = ' class="col-sm-12"';
    }

    // Primary nav.
    $variables['primary_nav'] = FALSE;
    if ($variables['main_menu']) {
        // Build links.
        $variables['primary_nav'] = menu_tree(variable_get('menu_main_links_source', 'main-menu'));
        // Provide default theme wrapper function.
        $variables['primary_nav']['#theme_wrappers'] = array('menu_tree__primary');
    }

    // Secondary nav.
    $variables['secondary_nav'] = FALSE;
    if ($variables['secondary_menu']) {
        // Build links.
        $variables['secondary_nav'] = menu_tree(variable_get('menu_secondary_links_source', 'user-menu'));
        // Provide default theme wrapper function.
        $variables['secondary_nav']['#theme_wrappers'] = array('menu_tree__secondary');
    }

    $variables['navbar_classes_array'] = array('navbar');

    if (theme_get_setting('bootstrap_navbar_position') !== '') {
        $variables['navbar_classes_array'][] = 'navbar-' . theme_get_setting('bootstrap_navbar_position');
    }
    else {
        $variables['navbar_classes_array'][] = 'container';
    }
    if (theme_get_setting('bootstrap_navbar_inverse')) {
        $variables['navbar_classes_array'][] = 'navbar-inverse';
    }
    else {
        $variables['navbar_classes_array'][] = 'navbar-default';
    }
}

/**
 * Register custom login form
 * see scymtheme/user-login-form.tpl.php
 *
 * @return array of template redirects
 */
function scymtheme_theme() {

    return array(
        'user_login_block' => array(
            'template' => 'templates/user-login-form',
            'render element' => 'form'
        )
    );
}

/**
 * Identify dropdown menu blocks as implemented in scymtheme/block.tplphp
 *
 * @param $data
 * @param $block
 */
function scymtheme_preprocess_block(&$data, $block) {
    if ($data['block_html_id'] == 'block-user-login') {
        $data['dropdown_id'] = 'login-div';
    }
}

/**
 * Setup displayname property in user variable
 *
 * @param $variables
 * @param $hook
 */
function scymtheme_preprocess(&$variables, $hook)
{
    if (array_key_exists('user',$variables)) {
        $user = $variables['user'];
        if (!isset($user->displayname)) {
            if ($user->uid) {
                $currentUser = TUser::getCurrent();
                $name = $currentUser->getUserShortName();
            }
            else {
                $name = 'Guest';
            }
            $variables['user']->displayname = $name;
        }
    }
}

/**
 * Style user login block, remove subject
 *
 * @param $data
 * @param $block
 */
function scymtheme_block_view_user_login_alter(&$data, $block) {
    $data['subject'] = null;
    $content['#attributes']['class'][] = 'dropdown-menu';
}

function _scymtheme_getArrayValue($a,$key)
{
    if (is_array($a) && array_key_exists($key,$a)) {
        return $a[$key];
    }
    return null;
}

/**
 * Style login button and convert labels to placeholders on user login form
 *
 * @param $form
 * @param $form_state
 * @param $form_id
 */
function scymtheme_form_user_login_block_alter(&$form, &$form_state, $form_id) {
    $form['actions']['submit']['#value'] = 'Sign in';
    $form['actions']['submit']['#attributes']['class'][] = 'btn-success';
    $fieldNames = array_keys($form);
    foreach( $fieldNames as $fieldName) {
        if (substr($fieldName, 0, 1) != '#') {
            $element = $form[$fieldName];
            $type = _scymtheme_getArrayValue($element,'#type');
            if ($type == 'textfield' || $type == 'password')
            {
                $title = _scymtheme_getArrayValue($element,'#title');
                if (!empty($title)) {
                    $form[$fieldName]['#title'] = null;
                    $form[$fieldName]['#attributes']['placeholder'] = $title;
                }
            }
        }
    }
}

