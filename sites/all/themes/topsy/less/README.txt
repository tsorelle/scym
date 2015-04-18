Setup to customize from LESS files:
Terry SoRelle - 4/16/2015

1) Create folder [theme]\assets

1) Download bootstrap zip file.

2) Extract to [theme]\assets
    dist\fonts
    dist\js
    less

3) create folder  [theme]\assets\css

4) cut/paste less\bootstrap\bootstrap.less to [theme]\less
5) cut/paste less\bootstrap\variables.less to [theme]\less
6)  update bootstrap.less to reference source files in bootstrap\(file).less
    except variables.less

7)  to deploy after less compile, copy [theme]\less\style.css to [theme]\assets\css\style.css
    or minimize to [theme]\assets\style.css

8) changes to info file:

    stylesheets[all][] = assets/css/style.css

    ; Disable BootstrapCDN
    settings[bootstrap_cdn] = ''

    scripts[] = 'assets/js/bootstrap.min.js'
