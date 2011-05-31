npAssetsOptimizerPlugin
=======================

This plugin provides a task to optimize web assets used in your project, typically for better frontend performances:

 * CSS files will be combined and compressed using  [cssmin](http://code.google.com/p/cssmin/) or the [Minify CSS Compressor](http://minify.googlecode.com/svn/trunk/min/lib/Minify/CSS/Compressor.php) library
 * Javascript files will be combined and compressed using [JSMin](http://github.com/rgrove/jsmin-php/), [JSminPlus](http://crisp.tweakblogs.net/blog/cat/716) or the [Google Closure Compiler API](http://code.google.com/intl/en_US/closure/compiler/docs/api-ref.html)
 * PNG web images will be optimized with [pngout](http://www.jonof.id.au/pngout), [advpng](http://advancemame.sourceforge.net/comp-readme.html) or [pngcrush](http://pmt.sourceforge.net/pngcrush/) if installed and available on the system
 * JPEG web images will be optimized with [jpegtran](http://jpegclub.org/jpegtran/).
 * Of course you can create your own optimization drivers

Combined javascript and css files will then replace original ones in the response, optionnaly with a timestamp appended as a GET parameter to force browsers to redownload them in case they're served with an `Expires` header and/or `304` HTTP status code, and if they've changed in the meanwhile.

Of course, the optimizations made and their use is configurable by environments, so you can enable the call for optimized assets only in `prod` for example. Each optimization process can also be disabled. See the **Basic configuration** section of this document to find out how to do so.

Everything is done by configuring some YAML by default, but the plugin architecture allows to easily extend the service and optimizers as well. See the **Advanced configuration** section of this document to find out how.

Prerequisites
-------------

### Required

 * PHP 5.2.4
 * Symfony 1.2, 1.3 or 1.4

### Optional

 * PHP CURL extension enabled and available using the php command line for Google Closure Compiler support
 * Optionally, these PNG image optimization programs:
   - `pngout` ([get it](http://www.jonof.id.au/pngout)) 
   - `advpng` ([get it](http://advancemame.sourceforge.net/comp-readme.html)) 
   - `pngcrush` ([get it](http://pmt.sourceforge.net/pngcrush/)) 
 * Optionally, this JPEG image optimization programs:
   - `jpegtran` ([get it](http://jpegclub.org/jpegtran/)) 

Installation
------------

### Installation using PEAR

    $ php symfony plugin:install -s beta npAssetsOptimizerPlugin

### Installation using SVN

    $ cd /path/to/your/symfony/project
    $ svn co http://svn.symfony-project.com/plugins/npAssetsOptimizerPlugin/trunk plugin/npAssetsOptimizerPlugin

No need to say that if you're using SVN for your project, use the `svn:externals` property instead.

### Enable the plugin

Edit your `config/ProjectConfiguration.class.php` file to enable the plugin:

    <?php
    class ProjectConfiguration extends sfProjectConfiguration
    {
      public function setup()
      {
        $this->enablePlugins(array(
          // ... other plugin(s)
          'npAssetsOptimizerPlugin',
        ));
      }
    }
   
You're done with the plugin installation, now you have to configure it.

Basic configuration
-------------------

Basic configuration of assets optimization is done in the `np_assets_optimizer_plugin` section of a standard `app.yml` configuration file. The plugin ships with a commented default one you'll find in the `config` folder of the plugin directory:

    all:
      np_assets_optimizer_plugin:
        enabled: true                          # status of the plugin
        class: npAssetsOptimizerService        # the plugin service class to use
        configuration:                         # optimization service configuration
          javascript:                          # Javascript optimizer configuration
            enabled: false                     # status of optimization
            class: npOptimizerJavascript       # the javascript optimizer class to use
            params:                            # optimizer class configuration
              driver: JSMin                    # javascript optimization driver name
              destination: /js/optimized.js    # destination path for optimized .js file
              timestamp: true                  # adds a timestamp to the combined file url
              files:                           # list of js assets to optimize and combine
                - jquery.js
                - jquery-ui.js
                - application.js
          stylesheet:                          # stylesheets configuration section
            enabled: false                     # status of optimization
            class: npOptimizerStylesheet       # the stylesheet optimizer class to use
            params:                            # optimizer class configuration
              driver: Cssmin                   # stylesheet optimization driver name
              destination: /css/optimized.css  # destination path for optimized .css file
              timestamp: true                  # adds a timestamp to the combined file url
              files:                           # list of css assets to optimize and combine
                - /facebox/facebox.css
                - main
                - skins/foo.css
          png_image:                           # PNG images configuration section
            enabled: false                     # status of optimization
            class: npOptimizerPngImage         # the PNG image optimizer class to use
            params:                            # optimizer class configuration
              driver: Pngout                   # PNG image optimization driver name
              folders:                         # folders to scan for PNG files to optimize
                - %SF_WEB_DIR%/images          # by default, contains the web/images folder
          jpeg_image:                          # JPEG images configuration section
            enabled: false                     # status of optimization
            class: npOptimizerJpegImage        # the JPEG image optimizer class to use
            params:                            # optimizer class configuration
              driver: Jpegtran                 # driver name
              folders:                         # folders to scan for JPEG files to optimize (.jpg & .jpeg)
                - %SF_WEB_DIR%/images          # by default, contains the web/images folder

Just create your own `np_assets_optimizer_plugin` in your application `app.yml` file to override these default settings.

**Important note:** for the `files` section of both `javascript` and `stylesheet` optimizer configuration sections, the ordering of files is extremely important and should reflect the order used in your own `view.yml` files. Also, **the very same syntax should be used**: don't use `main.css` in the `app.yml` file where it's `main` or `/css/main.css` in the `view.yml` one.

### Available drivers

For javascript: `JSMin`, `JSMinPlus` or `GoogleClosureCompilerAPI`.

For stylesheets: `Cssmin` or `MinifyCssCompressor`.

For PNG images: `Pngout`, `PngCrush` or `AdvPNG`.

For JPEG images: `Jpegtran`.


Usage
-----

When you're done with configuration, you can launch the assets optimization task, which is available under the `optimize` namespace:

    $ php symfony list optimize

The `application` argument is mandatory, whereas the `type` option allows to set the type of assets to optimize:

    $ php symfony optimize:assets frontend --type=stylesheet
    $ php symfony optimize:assets frontend --type=javascript
    $ php symfony optimize:assets frontend --type=png_image
    $ php symfony optimize:assets frontend --type=jpeg_image

To optimize all assets in one call:

    $ php symfony optimize:assets --type=all

To get full help on how to use this task, just launch:

    $ php symfony help optimize:assets

These tasks **must be executed manually** every time you make one of these changes:

 * adding or modifying a CSS file handled by the plugin
 * adding or modifying a Javascript file handled by the plugin
 * adding or modifying a PNG image handled by the plugin
 * adding or modifying a JPEG image handled by the plugin

When optimized javascripts and css assets are generated, they aim to be used instead of the old ones. So you have to replace the calls to `include_javascripts()` and `include_stylesheets()` helpers in your layouts respectivelly by the `include_optimized_javascripts()` and `include_optimized_stylesheets()` ones, as shown in the example below:

    <html>
      <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <title>Foo</title>
        <?php include_optimized_stylesheets() ?>
      </head>
      <body>
        <?php echo $sf_content; ?>
        <?php include_optimized_javascripts() ?>
      </body>
    </html>

**Note1:** Obviously, the optimized assets **must** have been generated before with the task for this to work.

**Note2:** Don't worry adding the `npOptimizer` helper to the `default_helpers` section of your `setting.yml` file, it will be automatically added at plugin configuration time.

Advanced Configuration
----------------------

If you want to manage asset optimization by yourself, just extend the corresponding optimizer class; for instance, if you want to compress and combine javascript files your way instead of JSMin, JSminPlus or Google Closure Compiler ones, you can write your own driver by extending the `npDriverBase` class and implementing its abstract `doProcessFile($file, $replace = false)` method:

    class npDriverMyDriver extends npDriverBase
    {
      public function doProcessFile($file, $replace = false)
      {
        // optimize javascript contents here...
    
        if ($replace)
        {
          // replace file here
          
          return $file;
        }
        else
        {
          // fetch contents from $file, optimize it, and return it
          
          return $optimizedContents;
        }
      }
    }

Don't forget to declare the new driver to use for javascript assets in your `app.yml` file.

By default the task will use the `prod` environment, because asset optimizations are traditionnaly used in a production context.

But in the example below, we'll configure the optimizer only for the `dev` environment:

    dev:
      np_assets_optimizer_plugin:
        enabled: true
        class: npAssetsOptimizerService
        configuration:
          javascript:
            enabled: true
            class: npOptimizerJavascript
            params:
              driver: myDriver
              destination: /js/optimized.js
              timestamp: false
              files:
                - jquery.js
                - application.js
    all:
      np_assets_optimizer_plugin:
        enabled: false

Last, generate the new optimized javascript file with the `optimize:assets` task for the `dev` environment:

    $ php symfony optimize:assets frontend --type=javascript --env=dev

Changelog
---------

### v0.8.2 - 2009-12-XX

 * **BC BREAK:** the `optimize:assets` task now uses the `prod` environment by default
 * added an exception throw when the GoogleClosureCompilerAPI driver retrieves an error from a call to the service

### v0.8.1 - 2009-12-29

 * **BC BREAK:** added a mandatory `application` argument to the `assets:optimize` task, to avoid processing assets optimization from the wrong application configuration
 * fixed a bug which prevent to understand the `enabled` configuration setting for optimizers
 * fixed a typo in the file name of the `npDriverAdvPNG` class

Many thanks to [Pascal Borreli](http://borreli.com/) for the bug reports.

### v0.8.0 - 2009-12-25 (yes, x-mas release!)

 * **BC BREAK:** The accepted `type` options of the `optimize:assets` tasks have been renamed and are now `all`, `javascript`, `stylesheet` and `png_image`
 * **BC BREAK:** Refactored drivers management, they now have their own classes
 * Added `JSMinPlus` javascript optimization driver, based on [JSminPlus](http://crisp.tweakblogs.net/blog/cat/716)
 * **BC BREAK:** Driver names now reflects the driver class name: 
   - `JSMin` driver will use the `npDriverJSMin` class
   - `JSMinPlus` driver will use the `npDriverJSMinPlus` class
   - `GoogleClosureCompilerAPI` driver will use the `npDriverGoogleClosureCompilerAPI` class
 * Added `MinifyCssCompressor` stylesheet optimization driver, based on [Minify](http://minify.googlecode.com/svn/trunk/min/lib/Minify/CSS/Compressor.php)
 * Added `AdvPNG` and `PngCrush` PNG optimization drivers
 * Added more unit tests

### v0.6.0 - 2009-12-21

 * Initial release

Credits
-------

This plugin is maintened by [Nicolas Perriault](http://prendreuncafe.com/). Patches and feedback are welcome, send them to `nperriault` at `gmail` dot com.

Some parts of the code it contains have been heavily inspired by Ryan Weaver's [ioCombinerPlugin](http://www.symfony-project.org/plugins/ioCombinerPlugin), many thanks to him for the great inspiration and discussions.
