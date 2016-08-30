# qmvc
A small but powerful MVC framework written in PHP.

# Documentation

To start using qmvc - you'll need to edit the config.inc.php file located in /config/. Next, setup your database by importing qmvc.sql with phpMyAdmin or mysql -u username -p db_name < qmvc.sql

Lastly, you'll need to setup your webserver's configuration like the following:
(I'm using nginx but I'm sure the rules roughly look the same for an htaccess file with apache)
```
root /public/;
location / {
  try_files $uri /index.php?uri=$uri&$args;
}
```
(You'll want to set the root directory to the public folder within the MVC to load in index.php)

I've started documenting different functions within qmvc. If you take a look at app/controller.inc.php you should be able to see some of the function declarations. Make sure you only use the ones that are below "User Callable Functions".

I've included some sample files with the MVC to document how this works and some simple ways to use it.
