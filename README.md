# MangoFP
Manage Contact Form 7 messages directly in WordPress like leads in CRM system.

Plugin subscribes to Contact Form 7 hooks to store messages information in its own database. The plugin provides messages management UI in Wordpress Admin Area. There is separate settings view for plugin configuration and for messages management process.

## Prepare development environment
### Clone plugin components
Plugin code is divided to front end components and common back-end comopent. Front end components are made in Vue. Initial source-code of comopents is in separate GitHub repositories. Clone then to seprate folders in one commonn projects folders (.e.g. in ~/project). In your projects folder: 
* **mangofp** - plugin main back-end code, plugin building tools:
```
	git clone ***
```
* **mangofp-front** - ui in Admin Area for message management:
```
	git clone ***
```
* **mangofp-settings-ts** - ui in Admin Area for settings and configuration:
```
	git clone ***
```

### Prepare plugin for local Wordpress server
Prepare local webserver and install Wordpress into it. We assume, that Wordpress website for local development is accessible in  ~/var/www/html/wpdev

Add needed plugins to your local wordpress site:
* **Contact Form 7** provdes forms messages. Create some forms, add to some testpages on your testsite
* **Loco Translate** use to translate text resources of your plugin

Create now symlink from your projects directory to plugin directory of your local Wordpress site.
In **/var/www/html/wpdev/wp-content/plugins** execute:
```
ln -s ~/projects/mangofp mangofp
```
Here the path is to the actual project folder

### Configure plugin to use front end assets from local development servers
In development environemt you can use dynamically generated local assets serverd by Vue applications servers. To enable this add to your Wordpress wp-config.php file following:
```
	define( 'MANGO_FP_DEBUG', true );
```

### Test that plugin works in local developent environment
Start development servers for ui components. For that cd to mangofp-front project folder and issue command:
```
	npm run serve
```
Leave the process running in terminal and open another. It that terminal window cd to mangofp-settings-ts rpoject folder and issue command:
```
	npm run serve
```
Leave this terminal running, too and check with browser if MangoFp plugin works in Admin Area.

## Make new version of plugin code

### Generating language resources:
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

### Configuration for local development
To ensures that local front end projects (mangofp-front and mangofp-settings) are served from local development servers, add into local Wordpress configuration file wp-config.php:

	define( 'MANGO_FP_DEBUG', true );

### Make new plugin installation package
* Make complete plugin code for releasing - in mangofp-front project execute make script for building the plugin full code:
```
	./make_stage.sh
```

* To refresh only backend php of the pugin code in staging - execute in mangofp project folder execute:
```
	.generate_autoload.sh && ./stage_backend.sh
```

## Additional options 

### Database and option removal
By default database and options will not be removed during unistall of the plugin. To remove all plugin data during unistall add to the wp-config.php:

	define( 'MANGO_FP_REMOVE_TABLES_ON_UNINSTALL', true );

