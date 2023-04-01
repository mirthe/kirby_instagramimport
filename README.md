# Kirby Plugin: Instagram import

This plugin allows you to show import posts from Instagram to Kirby pages.

## Git submodule

```
git submodule add https://github.com/mirthe/kirby_instagramimport site/plugins/instagram-import
```

## Usage

There are 2 routines available, one to import your entire profile from an export. A second to import only the latste N posts. To run either routine, you need to be logged into the panel as an admin user to protect your site from some abuse.

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

The folders are created in a folder called 'temp' in this plugin. You can move them to you 'content' folder. I've added an extra level by year in my site, but you can ofcourse to do something else entirely with your newly created pages. Optionally, after both routines, I'll use the panel to manually correct the posts once they're in their final place, move the tags to a seperate field and add a title. 

## Example 

Check out the display on my site at
https://mirthe.org/fotofeed

## Blueprint

### Photopost.yml

    title: Photopost
    num: date
    icon: dashboard

    columns:
        left:
            width: 2/3
            fields:
            photo:
                type: files
                layout: cards

        right:
            width: 1/3
            fields:
            date:
                type: date
                time: true
            intro:
                label: Intro
                type: textarea
                size: small
            tags:
                type: tags
                options: query
                query: site.index.filterBy("template", "in", ["photopost"]).pluck("tags", ",", true)
            sourcelink:
                type: url

## Todo

- Offer as an official Kirby plugin
- Add sample Kirby templates to this readme
- Add more sample Kirby Blueprint to this readme
- Cleanup code
- Lots..
