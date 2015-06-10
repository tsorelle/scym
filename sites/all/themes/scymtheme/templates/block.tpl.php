<?php
/**
 * @file
 * Default theme implementation to display a block.
 *
 * Copied from Bootstrap theme.
 * Added logic to reformat blocks displayed in Bootstrap navbar as drop down.
 * See template.php function scymtheme_theme()
 *
 * Available variables:
 * - $dropdown_id: indicates a dropdown blocks and provides id for enclosing Div.
 * - $block->subject: Block title.
 * - $content: Block content.
 * - $block->module: Module that generated the block.
 * - $block->delta: An ID for the block, unique within each module.
 * - $block->region: The block region embedding the current block.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - block: The current template type, i.e., "theming hook".
 *   - block-[module]: The module generating the block. For example, the user
 *     module is responsible for handling the default user navigation block. In
 *     that case the class would be 'block-user'.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Helper variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $block_zebra: Outputs 'odd' and 'even' dependent on each block region.
 * - $zebra: Same output as $block_zebra but independent of any block region.
 * - $block_id: Counter dependent on each block region.
 * - $id: Same output as $block_id but independent of any block region.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 * - $block_html_id: A valid HTML ID and guaranteed unique.
 *
 * @see bootstrap_preprocess_block()
 * @see template_preprocess()
 * @see template_preprocess_block()
 * @see bootstrap_process_block()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>

<?php if (empty($dropdown_id)): ?>
    <section id="<?php print $block_html_id; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
<?php else: ?>
    <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
            <?php if ($dropdown_id == 'login-div'): ?>
                <!-- block for login form -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Sign in <span class="caret"></span></a>
            <?php elseif ($dropdown_id == 'search-div'): ?>
                <!-- block for search form -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Search the site."><span class="glyphicon glyphicon-search"></span></a>
            <?php endif;?>

            <div class="dropdown-menu" id="<?php print $dropdown_id;?>">
<?php endif;?>

  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
    <h2<?php print $title_attributes; ?>><?php print $title; ?></h2>
  <?php endif;?>
  <?php print render($title_suffix); ?>

  <?php print $content ?>

<?php print empty($dropdown_id) ? '</section>' : '</div></li></ul>'; ?> <!-- /.block -->
