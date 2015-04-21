Config notes
==============

sitesettings.yml 
----------------
contains server specific settings that should be updated at deployment. 
Especially, in sitesettings.yml, change environment: development to production or staging. 

appsettings.yml
---------------
Contains settings that are shared between all environments (development, staging, production'.

appsettings-(environment).yml
---------------
Settings specific to the deployment enviroment, such as database connection parameters
are contained in files following this naming convention.
E.g. appsettings-development.yml, appsettings-staging-yml, appsettings-production.yml


For security reasons, appsettings-production.yml is not pushed to the repository. See .gitignore file.

Any other yml files are no longer used.
