@echo off
set file=%1
set subdir=%2
if '%subdir%'=='' goto nosubdir
set source=d:\projects\websites\drupal\d7\current\%subdir%\%file%
set target=D:\Projects\websites\scym.org\next\public_html\%subdir%
goto start
:nosubdir
set source=d:\projects\websites\drupal\d7\current\%file%
set target=D:\Projects\websites\scym.org\next\public_html
:start
echo Updating from %source%
echo to %target%
if not exist %source% goto error
xcopy %source% %target% /y
echo Updated %file%
goto end
:error
echo no source file
:end