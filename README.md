# Kirby Plugin: Instagram import

This plugin allows you to show import posts from Instagram to Kirby pages.

## Git submodule

```
git submodule add https://github.com/mirthe/kirby_instagramimport site/plugins/instagramimport
```

## Usage

### Bulk import history

First, request and download an export of your posts via
https://www.instagram.com/download/request/

In the zip, you'll find a folder 'posts' and a 'posts_1.json'.
Place these in the plugin directory and run the routine.

    https://yoursite.com/insta/bulkimport

I've had some issues with smileys and international characters, so spent some time changing the source json file. I ran the bulkimport locally a few times until satisfied with the results. 

### Get latest posts

Based on the code by Eder Ribeiro at
https://github.com/ribeiroeder/php-curl-instagram-graph

To add new posts, you'll need a token for Instagram. To get a token, follow the steps outlined at
https://www.mageplaza.com/kb/how-to-get-instagram-feed-access-token.html

Note: this routine won't work with a private Instagram profile, as you can only get a token for public profiles.

Add the following to your Kirby config where XX is your token:
    
    'instagram.token' => 'XXX',

Run the routine on

    https://yoursite.com/insta/getlatest

## Tweaks

The folders are created in a folder called 'temp' in this plugin. You can move it to you 'content' folder. I've added an extra level by year, but you can ofcourse to do something else entirely with your newly created pages. Optionally, after both routines, I'll use the panel to manually correct the posts once they're in their final place, move the tags to a seperate field and add a title. 

## Example 

Check out the display on my site at
https://mirthe.org/fotofeed

## Todo

- Offer as an official Kirby plugin
- Add sample Kirby templates to this readme
- Add sample Kirby Blueprint to this readme
- Cleanup code
- Lots..
