# MangoFP
User defined contacts processing.
Initially for Contact Form 7.

## Development environment
Deployment and utility scripts assume, that projects are installed in following repositories:
* Local webserver is serving Wordpress with developed plugin installed in following path:
```
/var/www/html/wpdev/wp-content/plugins/mangofp
```
* Wordpress plugin development project can be symlinked from your projects directory.
In **/var/www/html/wpdev/wp-content/plugins** execute:
```
ln -s ~/projects/mangofp mangofp
```
Here the path is to the actual project folder

## Generating language resources:
We are using gettext through grunt. So take a look at Gruntfile.js and package.json and then install npm and in project folder execute:

```
npm install
```
Then you can generate the pot file

```
npm run gettext
```

Then open Loco translate in local dev environment and translate the resources. All following function calls are considered translatable resources:
```
gettext, __, esc_html__, esc_html_e
```

## Configuration for local development
To ensures that local front end projects (mangofp-front and mangofp-settings) are served from local development servers, add into local Wordpress configuration file wp-config.php:

	define( 'MANGO_FP_DEBUG', true );

## Database and option removal
By default database and options will not be removed during unistall of the plugin. To remove all plugin data during unistall add to the wp-config.php:

	define( 'MANGO_FP_REMOVE_TABLES_ON_UNINSTALL', true );

## Prepare for staging
Script **stage_backend.sh** prepares all stageables (code, language resources from translation plugin). Try not to use it directly. It should be used through **../mangofp-front/make_stage.sh**
