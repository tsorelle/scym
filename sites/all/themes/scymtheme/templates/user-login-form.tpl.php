<?php
/**
 * Custom log in form for dropdown in bootstrap nav bar.
 * See comments in template.php
 */
?>
    <?php print '<!-- login form here -->' ?>
    <div class="form-group">
        <input id="edit-name" type="text" placeholder="User name" name="name" class="form-control">
        <!-- ?php print drupal_render($form['name']);? -->
    </div>
    <div class="form-group">
        <input id="edit-pass" type="password" name="pass" placeholder="Password" class="form-control">
        <!-- ?php print drupal_render($form['pass']);? -->
    </div>
    <div id="signin-button">
        <!-- a class="btn btn-success" href="scym-front-signed-in.html" role="button">Sign in</a -->
        <?php print drupal_render_children($form,array('form_id','form_build_id','actions')); ?>
    </div>
    <div>
        <?php print drupal_render($form['links']); ?>
    </div>

