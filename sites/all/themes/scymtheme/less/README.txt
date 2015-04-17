Setup to customize from LESS files:
Terry SoRelle - 4/16/2015

1) Create folder [theme]\assets

2) Download bootstrap zip file.

3) Extract to [theme]\assets
    dist\fonts
    dist\js
    less

4) create folder  [theme]\assets\css

5) cut/paste less\bootstrap\bootstrap.less to [theme]\less
6) cut/paste less\bootstrap\variables.less to [theme]\less
7) update bootstrap.less to reference source files in bootstrap\(file).less
   except variables.less

8) to deploy after less compile, copy or minimize [theme]\less\style.css to \assets\css\scym-theme.min.css
    ('scym-theme.min.css' is for example)

9) changes to info file:

    stylesheets[all][] = assets/css/style.css

    ; Disable BootstrapCDN
    settings[bootstrap_cdn] = ''

    scripts[] = 'assets/js/scym-theme.min.js'


To update to a new bootstrap version repeat these all steps except the last (.info update), overwriting rather than
creating the folders.

The less folder is not required for deployment.