<?php

/**
 * Loader order: Tops and scym modules must be loaded first.
 * See scym.module notes.
 */


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


function _scymtheme_typeToLi($type)
{
    $first = substr($type->type,0,1);
    $article = ($first == 'a' || $first == 'i' || $first == 'o' || $first == 'u') ? 'an ' : 'a ';

    $href = '/node/add/'.str_replace('_','-',$type->type);
    $title = strip_tags($type->description);
    $text = 'Create '.$article. $type->name;

    return _scymtheme_menuLi($href,$title,$text);
}

function _scymtheme_menuLi($href,$title,$text) {
    return sprintf('<li role="presentation"><a role="menuitem" tabindex="-1" href="%s" title="%s" >%s</a></li> ',
        $href,$title,$text);
}

function _scymtheme_buildContentTypeMenu(\Tops\sys\IUser $currentUser) {

    $userContentTypes = $currentUser->getContentTypes();
    if (empty($userContentTypes)) {
        return false;
    }

    $typeList = '';
    if (array_key_exists('page',$userContentTypes)) {
        $type = $userContentTypes['page'];
        $typeList .= _scymtheme_typeToLi($type);
    }
    if (array_key_exists('article',$userContentTypes)) {
        $type = $userContentTypes['article'];
        $typeList .= _scymtheme_typeToLi($type);
    }
    foreach ($userContentTypes as $key => $type) {
        if ($key != 'page' && $key != 'article') {
            $typeList .= _scymtheme_typeToLi($type);
        }
    }

    $typeList .= _scymtheme_menuLi('/admin/content','Manage all content','Manage Content');

    return
        array(
            '#prefix' => '<li class="dropdown" > '.
                '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Create Content"> '.
                '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a> '.
                '<ul class="dropdown-menu" role="menu" aria-labelledby="editMenu1">',
            '#markup' => $typeList,
            '#suffix' => '</ul></li>',
    );
}

function _scymtheme_buildAdminToolsMenu(\Tops\sys\IUser $currentUser) {
    $items = '';
    $mailboxManager = $currentUser->isAuthorized('manage mailboxes');
    $isRegistrar = $currentUser->isAuthorized('administer registrations');
    $manageTasks = $currentUser->isAuthorized('manage web site tasks');
    $registerDocuments = $currentUser->isAuthorized('register documents');
    $manageDirectory = $currentUser->isAuthorized('administer directory');
    if ($manageTasks) {
        $items .= _scymtheme_menuLi('\tasks','Manage Web Site','Task List');
    }
    if ($mailboxManager) {
        $items .= _scymtheme_menuLi('\Mailboxes','Manage Mailboxes','Mailboxes');
    }
    if ($registerDocuments) {
        $items .= _scymtheme_menuLi('\RegisterDocument','Register Uploaded Document','Register Document');
    }
    if ($manageDirectory) {
        $items .= _scymtheme_menuLi('\management\EMailings','Manage mailing lists','Manage mailing lists');
        $items .= _scymtheme_menuLi('\members\download','Directory downloads','Download directory lists');
    }
    if ($isRegistrar) {
        $items .= _scymtheme_menuLi('\RegistrationAdmin','Manage Yearly Meeting Registrations','Manage Registrations');
    }


    return (empty($items)) ? false :
        array(
            '#prefix' => '<li class="dropdown" > '.
                '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Tools"> '.
                '<span class="glyphicon  glyphicon-cog" aria-hidden="true"></span></a> '.
                '<ul class="dropdown-menu" role="menu" aria-labelledby="editMenu1">',
            '#markup' => $items,
            '#suffix' => '</ul></li>',
        );
}

/**
 * Implements hook_preprocess_page().
 *
 * @see page.tpl.php
 */
function scymtheme_preprocess_page(&$variables) {

    $variables['siteshortname'] = 'SCYM';

    $variables['userContentMenu'] = false;
    $variables['adminToolsMenu'] = false;

    if ($variables['logged_in']) {
        $currentUser = TUser::getCurrent();
        $variables['userContentMenu'] = _scymtheme_buildContentTypeMenu($currentUser);
        $variables['adminToolsMenu']  = _scymtheme_buildAdminToolsMenu($currentUser);

    }

    // Add information about the number of sidebars.
    if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
        $variables['content_column_class'] = ' class="col-sm-6"';
    }
    elseif (!empty($variables['page']['sidebar_first']) || !empty($variables['page']['sidebar_second'])) {
        $variables['content_column_class'] =
            $variables['is_front'] ? ' class="col-sm-8"' : ' class="col-sm-9"';
    }
    else {
        $variables['content_column_class'] = ' class="col-sm-12"';
    }

    // Primary nav.
    $variables['primary_nav'] = FALSE;
    if ($variables['main_menu']) {
        // Build links.
        if ($variables['logged_in']) {
            $variables['primary_nav'] = menu_tree(variable_get('menu_main_links_source', 'main-menu'));
        }
        else {
            $variables['primary_nav'] = menu_tree('menu-main-menu-anonymous');
        }
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
    else if ($data['block_html_id'] == 'block-search-form') {
       $data['dropdown_id'] = 'search-div';
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

function scymtheme_bootstrap_search_form_wrapper($variables) {
    $output = '<div class="input-group">';
    $output .= $variables['element']['#children'];
    $output .= '<span class="input-group-btn">';
    $output .= '<button type="submit" class="btn btn-default">';
    // We can be sure that the font icons exist in CDN.
    if (theme_get_setting('bootstrap_cdn')) {
        $output .= _bootstrap_icon('search');
    }
    else {
        // use locally installed icons
        $output .= '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>';
    }

    $output .= '</button>';
    $output .= '</span>';
    $output .= '</div>';
    return $output;
}
