@echo off
set projectroot=\projects\websites\scym.org\next\public_html\
set yuicompressor=\projects\websites\common\yuicompressor-2.4.8.jar
set themeroot=%projectroot%sites\all\themes\scymtheme\
echo Compressing CSS file
java -jar %yuicompressor% %themeroot%less\style.css  > %themeroot%assets\css\scym-theme.min.css
echo done.