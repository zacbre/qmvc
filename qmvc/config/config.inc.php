<?php
/*
    qmvc - A small but powerful MVC framework written in PHP.
    Copyright (C) 2016 ThrDev
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Database Configuration
define("DB_USERNAME", "thrdev");
define("DB_PASSWORD", "");
define("DB_HOST", "localhost");
define("DB_NAME", "qmvc");

// Required for Google's Recaptcha, Uncomment if needed
/*
define("RECAPTCHA_SITE_KEY", "");
define("RECAPTCHA_PRIVATE_KEY", "");
define("RECAPTCHA_URL", "https://www.google.com/recaptcha");
*/

// Set to the root URL of your website.
define("SITE_URL", "");

// Change to something random.
define("SESSION_NAME", "qmvc");

// Use memcached? Must have php-memcached and memcached installed.
define("USE_MEMCACHED", false);

/* Homepage Routes */
$router->Connect("/", array("controller" => "index"));
$router->Connect("/template", array('controller' => 'templating'));
$router->Connect("/404", array("controller" => "404", "error_page" => "404"));
$router->Connect('/submit', array('controller' => 'index', 'action' => 'submit'));

//GLOBAL MODULES