lilURL 0.1.2  

http://github.com/thunderrabbit/lilurl

lilURL is a simple PHP/MySQL app that works basically like tinyurl.com,
allowing you to create shortcuts on your own server.

Copied from original lilURL 0.1.1 http://lilurl.sourceforge.net

Thanks to http://github.com/gbremer for importing into github  http://github.com/gbremer/lilurl

-----------------------------------------------------------------------

To install:
0. $ git clone https://github.com/thunderrabbit/lilurl.git [site_root_dir_name]
   For me, that is $ git clone https://github.com/thunderrabbit/lilurl.git ~/art.robnugen.com

1. Create a MySQL database and user for lilURL. 

2. Import the lilurl.sql file:

      (( like so:
      
         mysql -u [lilurl_user] -p [lilurl_db] < lilurl.sql
      
      ))

3. in the includes/ directory: $  cp conf.php-sample conf.php; cp mysql.php-sample mysql.php
   Edit the configuration files mysql.php and conf.php to suit your needs.

4. Set up mod_rewrite, if necessary

      (( a .htaccess file with the lines:
   
         RewriteEngine On
         RewriteRule (.*) index.php
   
        should suffice ))
