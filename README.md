<p align="center">
    <a href="https://deltapath.com" target="_blank">
        <img src="http://www.deltapath.com/wp-content/uploads/Deltapath-logo1.svg" height="100px">
    </a>
    <h1 align="center">Anecita Gabisan</h1>
    <br>
</p>

TECHNOLOGIES USED
-------------------
      PHP
      Yii2
      Javascript
      JQuery
      HTML
      Bootstrap
      CSS
      
LIBRARIES USED
-------------------
      Sweet Alert
      Flipclock JS

The project is based on the [Yii2 Basic Project Template](http://www.yiiframework.com/) application. Yii is an open source, object-oriented, component-based MVC PHP web application framework. 

YII2 DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------
      Web Server (Nginx or Apache)
      PHP <= 5.4.0
      GIT
      MySQL
      Yii2
      Javascript
      JQuery
      HTML
      Bootstrap
      CSS

DEVELOPMENT ENVIRONMENT SET-UP
------------
### Get Source Code
Go to your web root directory set in your Web Server.
Clone source code by executing command:
~~~
git clone https://github.com/amgabisan/word-mastermind.git
~~~

Change permission of runtime folder and web/assets folder by executing command:
~~~
chmod 777 -R runtime/ web/assets
~~~

### Add libraries via Composer
Go to your project folder. 
If you have not installed composer in your machine, please check **Composer Installation**.

#### Composer Installation
~~~
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
~~~

You can then install the project dependencies using the following command: 
~~~
composer install
~~~

## Database
### Create Database
Go to your database by executing command :
~~~
mysql -u root -p
~~~

In your database command line, execute command:
~~~
CREATE DATABASE deltapath;
~~~

### Import Database
The path for the database sql is in `database/database.sql`. 
Go to your db folder and import the database in your server by executing command:
~~~
mysql -u root -p deltapath < database.sql
~~~

### Configure Database
Edit the file `config/db.php` with real data, for example:

```php
$databases = [
    'db'  => 'deltapath',
];

$dbConfig = [
    'class' => 'yii\db\Connection',
    'dsn' => '',
    'username' => 'root',
    'password' => 'dimensions',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
    'attributes' => [
        PDO::MYSQL_ATTR_LOCAL_INFILE => true
    ]
];
```

## Nginx Configuration
### Nginx
Go to your nginx configuration folder.
Update nginx config:

~~~
server {
   set $host_path "/var/www/html";
   listen       80;
   server_name localhost;

   root    $host_path/word-mastermind/web/;

   index   index.php;
   client_max_body_size 500M;
   proxy_connect_timeout 600;
   proxy_send_timeout 600;
   proxy_read_timeout 600;
   send_timeout 600;
    if ($request_uri ~* "^(.*/)index\.php$") {
        return 301 $1;
    }

    location / {
        index  index.html index.php;
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ ^/(protected|framework|themes/\w+/views) {
        deny  all;
    }

    #avoid processing of calls to unexisting static files by yii
    location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
        try_files $uri =404;
    }

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php {
        fastcgi_split_path_info  ^(.+\.php)(.*)$;

        #let yii catch the calls to unexising PHP files
        set $fsn /index.php;
        if (-f $document_root$fastcgi_script_name){
            set $fsn $fastcgi_script_name;
        }

        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        include fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fsn;

        #PATH_INFO and PATH_TRANSLATED can be omitted, but RFC 3875 specifies them for CGI
        fastcgi_param  PATH_INFO        $fastcgi_path_info;
        fastcgi_param  PATH_TRANSLATED  $document_root$fsn;
    }

    # prevent nginx from serving dotfiles (.htaccess, .svn, .git, etc.)
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
}
~~~

**You are now up and running. Enjoy !**
