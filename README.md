Pixel Call to Action Widget
============================

* Contributors: [@wpmark](http://twitter.com/wpmark)
* Plugin Name: pixel-cta-widget
* Plugin URI: [https://github.com/pixeljunction/pixel-cta-widget](https://github.com/pixeljunction/pixel-cta-widget)
* Description: A Call to Action Widget that allows you to pull in WordPress content (e.g. posts, pages) and show as a call to action image with title and link to the content.
* Author: [Mark Wilkinson](http://markwilkinson.me)
* License: GNU General Public License v2.0
* License URI: [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

## Installation

Installation is simple. Navigate to the Plugins menu in the WordPress dashboard and click the add new link, then click on upload. Select the .zip file of this plugin that you will have downloaded and then once uploaded activate the plugin. To define your own classes which can then be selected, see below.

## Usage

The plugin adds a metabox to all public post types including Posts and Pages. The metabox allows you to add a Call to Action image using the normal image uploader. This is stored as a custom field. A widget is provided for adding Call to Actions into your widgetized areas. The widget, by default, outputs the content title and the CTA image.

## Hooks

The plugin contains a number of hooks to allow developers to output code in various places when the plugin runs. These hooks, along with some examples on implementation are outlined below:

### pxlcta_before_title

This hooks runs before the title of the CTA widget is outputted and is passed the ID of the post being displayed. This is a good hook to use if you want to add content before the widget content. An example is below:

```php
function my_pxlcta_before_slider() {

	echo '<p>This output content before the CTA widget title.</p>';

}

add_action( 'pxlcta_before_title', 'my_pxlcta_before_slider' );
```

### pxlcta_before_featured_image

This hooks runs before the CTA image is outputted and is passed the ID of the post being displayed. This is a good hook to use if you want to add content between the title and the image.

### pxlcta_after_featured_image

This hooks runs after the featured image of the CTA widget is outputted and is passed the ID of the post being displayed. This is a good hook to use if you want to add content after the entire widget.

## Filters

The following filters are used in the plugin to allow developers to filter the output in various places.

### pxlcta_title

This allows you to change how the title is displayed. Please note this includes the $before_title and $after_title widget variables and therefore if you do use this filter you should include these variables in your filterable function.

### pxlcta_cta_image_size

This allows you to use a custom image size for the CTA image. By default the plugin uses an image size defined named pxlcta_image which is 300px wide. However you can change this. The example below uses the WordPress thumbnail instead:

```php
function my_pxlcta_cta_image_size( $content ) {
	
	$content = 'thumbnail';
	
	return $content;

}

add_action( 'pxlcta_cta_image_size', 'my_pxlcta_cta_image_size' );
```

### pxlcta_post_types

This filter allows you to control the post types that are included in the plugin, controlling where the metabox should appear and which posts to show in the dropdown list of content in the widget itself. It needs an array of post types.