<?php
/**
 * Custom log in form for dropdown in bootstrap nav bar.
 * See comments in template.php
 */
?>
    <?php
        print '<!-- login form here -->';
        hide($form['links']);
    ?>
    <div class="form-group">
        <!-- input id="edit-name" type="text" placeholder="User name" name="name" class="form-control" -->
        <?php
            $form['name']['#required'] = false;
            print drupal_render($form['name']);
        ?>
    </div>
    <div class="form-group">
        <!-- input id="edit-pass" type="password" name="pass" placeholder="Password" class="form-control" -->
        <?php
        $form['pass']['#required'] = false;
        print drupal_render($form['pass']);
        ?>
    </div>

    <div id="login-form-links">
        <a href="/user/password" title="Request new password via e-mail.">Request new password . . .</a><br/>
        <p><a href="/user/register" title="Request a new user account.">Request an account . . .</a></p>
    </div>

    <?php print drupal_render_children($form); ?>

