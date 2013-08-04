#Bitfalls Utils

This is a suite of classes I use in almost every project, especially if I use the [Phalcon Framework](http://phalconphp.com/).
It contains ready-to-use components that can be plugged into any existing project to enhance its functionality.

## Usage

Just download into your library and point your autoloader at the folder, or install via composer by requiring bitfalls_utils.
Then, to use a component like Asset Manager, do the following:

Somewhere in your dependency manager, register the AssetManager like so:
```
    $am = new \Bitfalls\Utilities\AssetManager();
    if (!file_exists({{MY_MINIFY_FOLDER}})) {
        mkdir({{MY_MINIFY_FOLDER}}, 0777, true);
    }

    $am->setJsMinifyFolder({{MY_MINIFY_FOLDER}});
    $am->setCssMinifyFolder({{MY_MINIFY_FOLDER}});

    $am->setJsPathPrefix('www.mydomain.com/cdn/minify');
    $am->setCssPathPrefix('www.mydomain.com/cdn/minify');
    $am->addJsFilter(new \Bitfalls\Phalcon\JsMin());
    $am->addCssFilter(new \Bitfalls\Phalcon\CssMin());
    return $am;
```

Naturally, replace {{MY_MINIFY_FOLDER}} and www.mydomain.com with your own values.
Note that in order to use the filters used in the above example, you need Phalcon installed, because they
extend those of Phalcon.

Then, in your code (view, layout, controller, whatever), add files as follows:

```
$am->addJs({{PATH_TO_JS_FILE_1}});
$am->addJs({{PATH_TO_JS_FILE_2}});
$am->outputJs();
```

That's it. The script tag will be echoed and will src the minified file. Note that a new minified file is generated ONLY if one of the JS/CSS files you're adding has been changed since the last time it was generated. This reduces overhead.

## Contributing

You can send pull requests or examples of usage.

## License

General Apache license, see LICENSE.

### Origin

Bitfalls is my [blog](http://www.bitfalls.com).