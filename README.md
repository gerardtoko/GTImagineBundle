GTImagineBundle
===============

Symfony2 Bundle to assist in imagine manipulation using the imagine library (Fork of LiipImagineBundle)


This bundle is a fork of LiipImagineBundle which provides easy image
manipulation support for Symfony2. The goal of the fork is to make the
code more performance processing.


For example with this bundle the following is possible:

``` jinja
<img src="{{ '/relative/path/to/image.jpg' | imagine_filter('thumbnail') }}" />
````

This will perform the transformation called `thumbnail`, which you can define
to do a number of different things, such as resizing, cropping, drawing,
masking, etc.

This bundle integrates the standalone PHP "[Imagine library](/avalanche123/Imagine)".


##  Installation

### Download GTImagineBundle using composer

Add GTImagineBundle in your composer.json:

```js
{
    "require": {
        "gerardtoko/imagine-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

```bash
$ php composer.phar update gerardtoko/imagine-bundle
```

Composer will install the bundle to your project's `vendor/gerardtoko/imagine-bundle` directory.


### Register the bundle

You must register the bundle in your kernel:
```php
    <?php
    
    // app/AppKernel.php    
    public function registerBundles()
    {
        $bundles = array(    
            // ...    
             new GT\ImagineBundle\GTImagineBundle(),
        );    
        // ...
    }
```

## Configuration

Example of configuration yml file:
```yml

parameters:
    web_root:   %kernel.root_dir%/../web
    web_media:  %kernel.root_dir%/../web/medias

gt_imagine:
    driver:     gd
    mkdir_mode: 0777
    formats:    [jpg, png]
    web_root:   %web_root%
    data_root:  %web_media%/thumbnails
    filter_sets:
        media_image:
            directory:  %web_media%/images
            quality:    75
            filters:
                thumbnail:  { size: [140, 200], mode: outbound }
                crop:       { start: [0, 0], size: [140, 200] }
        profile_image:
            directory:  %web_media%/images
            quality:    75
            filters:
                thumbnail:  { size: [50, 50], mode: outbound }
                crop:       { start: [0, 0], size: [50, 50] }

```


## Basic Usage

This bundle works by configuring a set of filters and then applying those
filters to images inside a template So, start by creating some sort of filter
that you need to apply somewhere in your application. For example, suppose
you want to thumbnail an image to a size of 120x90 pixels:

``` yaml
# app/config/config.yml
    
gt_imagine:
    filter_sets:
        my_thumb:
            directory:  %web_media%/images
            filters:
                thumbnail:  { size: [120, 90], mode: outbound }
```

You've now defined a filter set called `my_thumb` that performs a thumbnail transformation.
We'll learn more about available transformations later, but for now, this
new filter can be used immediately in a template:

``` jinja
<img src="{{ '/relative/path/to/image.jpg' | imagine_filter('my_thumb') }}" />
```

Or if you're using PHP templates:

``` php
<img src="<?php $this['imagine']->filter('/relative/path/to/image.jpg', 'my_thumb') ?>" />
```

Behind the scenes, the bundles applies the filter(s) to the image on the first
request and then caches the image to a similar path. On the next request,
the cached image would be served directly from the file system.

In this example, the final rendered path would be something like
`/media/my_thumb/relative/path/to/image.jpg`. This is where Imagine
would save the filtered image file.

In order to get an absolute path to the image add another parameter with the value true:


## Filters

The LiipImagineBundle provides a set of built-in filters.
You may easily roll your own filter, see [the filters chapter in the documentation](Resources/doc/filters.md).


There are several configuration options available:

 - `data_root` - can be set to the absolute path to your original image's
    directory. This option allows you to store the original image in a 
    different location from the web root. Under this root the images will 
    be looked for in the same relative path specified in the apply_filter
    template filter.

    default: `%kernel.root_dir%/../web/thumbnails`

 - `web_root` - must be the absolute path to you application's web root. This
    is used to determine where to put generated image files, so that apache
    will pick them up before handing the request to Symfony2 next time they
    are requested.

    default: `%kernel.root_dir%/../web`


 - `driver` - one of the three drivers: `gd`, `imagick`, `gmagick`

    default: `gd`

 - `filters` - specify the filters that you want to define and use

Each filter that you specify have the following options:

 - `options` - options that should be passed to the specific filter type

## Built-in Filters

Currently, this bundles comes with just one built-in filter: `thumbnail`.

### Thumbnail

The `thumbnail` filter, as the name implies, performs a thumbnail transformation
on your image. The configuration looks like this:

``` yaml
filters:
    my_thumb:
        thumbnail: { size: [120, 90], mode: outbound }
```

The `mode` can be either `outbound` or `inset`.

### Resize

The `resize` filter may be used to simply change the width and height of an
image irrespective of its proportions.

Consider the following configuration example, which defines two filters to alter
an image to an exact screen resolution:

``` yaml
avalanche_imagine:
    filters:
        cga:
            resize: { size: [320, 200] }
        wuxga:
            resize: { size: [1920, 1200] }
```

### RelativeResize

The `relative_resize` filter may be used to `heighten`, `widen`, `increase` or
`scale` an image with respect to its existing dimensions. These options directly
correspond to methods on Imagine's `BoxInterface`.

Given an input image sized 50x40 (width, height), consider the following
annotated configuration examples:

``` yaml
avalanche_imagine:
    filters:
        heighten:
            relative_resize: { heighten: 60 } # Transforms 50x40 to 75x60
        widen:
            relative_resize: { widen: 32 }    # Transforms 50x40 to 40x32
        increase:
            relative_resize: { increase: 10 } # Transforms 50x40 to 60x50
        scale:
            relative_resize: { scale: 2.5 }   # Transforms 50x40 to 125x100
```

If you prefer using Imagine without a filter configuration, the `RelativeResize`
class may be used directly.



## Using the controller as a service

If you need to use the filters in a controller, you can just load `ImagineController.php` controller as a service and handle the response:

``` php
class MyController extends Controller
{
    public function indexAction()
    {
        $imagine = $container->get('gt_imagine');
        $srcPath = $imagine->filter("image.jpg", "my_thumb");
        
        //filter All
        $imagine->filterAll("my_thumb");
        
        // ..
    }
}
```
