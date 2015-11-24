# GPIOHelperPHP
A PHP helper library for the Onion Omega

This library uses direct file operations to change the pin states, rather than calling shell commands.  Should be a bit faster than [other samples](https://community.onion.io/topic/39/simple-php-web-gpio-example-switching-leds) I've seen.  This method was cribbed in part from [a Python version](https://community.onion.io/topic/40/simple-python-wrapper-and-demo) on the community site.

## Instructions

Following similar instructions for a different project in the community forums:

1. First you have to install PHP and PHP-CGI from the console:

    ```bash
    opkg update  
    opkg install php5 php5-cgi  
    ```

2. Edit the /etc/config/uhhtpd file, and add this line to the end of the 'main' section:

    ```bash
    list interpreter ".php=/usr/bin/php-cgi"  
    ```

3. Restart the web server:

    ```bash
    /etc/init.d/uhttpd restart  
    ```

4. Create the directory /www/php
5. Add the files gpiohelper.php and test.php to the /www/php directory.
6. Navigate to http://192.168.1.100/php/test.php where 192.168.1.100 is the IP address or domain of your Omega.
