# Guzzle Plugin
This is an example of how to use the Tabs Api with the guzzle library.

The HmacPlugin is attached as a service provider (i.e. plugin) to the guzzle 
client and requests are then correctly hashed before sending to the api.

## Installing via composer
1. Clone this repo into a directory of your choosing and navigate to it.
2. Download composer and install the repo:

```
curl -sS https://getcomposer.org/installer | php
./composer.phar install
```
3. Copy the config.sample.php and rename to config.php.
4. Enter your connection details into the constant definitions.
5. http://localhost/api-documentation/examples/guzzle/property.php should work now.

Note: Please be aware, this plugin has not been extensively tested and should
only be treated as an example which you may use for the basis of your project.