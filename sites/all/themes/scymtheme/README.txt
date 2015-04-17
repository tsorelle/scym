This theme is based on the Bootstrap theme starter kit:
http://drupal.org/node/1978010

Modifications to support the TOPS module were made to template.php and page.tpl.php
The changes for Tops are
1) In template.php,
     the topsy_status_messages function inserts a KnockoutJS component element for ViewModel pages that is required
     for display of service response messages.

2) In templates\page.tpl.php,
    Added one line at the end of the file to display trace messages.

All other modifications are specific to scym.org.

The style sheet, assets/css/scym-theme.min.css, is generated from the LESS files in the less directory.
LESS compilation and minimization is done in the development environment. LESS compiler generates less\style.css
as source for scym-theme.min.css. Then use lib/tools/build.php to perform the mimimization and update.
A Java installation is required (http://www.java.com)

If the version of bootstrap needs to be updated, see setup instructions in less\README.txt.

The stylesheet, assets/css/scym-more.css, is intended for temporary style changes as needed. These change should
eventually be moved to less/content.less for recompilation into the minified css file.



