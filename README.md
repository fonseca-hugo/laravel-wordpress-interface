Laravel Wordpress Interface
================

*Laravel Wordpress Interface a work in progress*

--

Laravel Wordpress Interface is an interface created to get data from Wordpress database using the Eloquent ORM developed for the Laravel Framework.

Using this approach, Wordpress can be used only as a CMS, to create posts, custom types, etc, leaving the rest of the back-end and front-end to a Framework of your choice (in this case, Laravel).

## Installation

To install Laravel Wordpress Interface just create/edit a `composer.json` file and add:

    "require": {
        ...
        "fonseca-hugo/laravel-wordpress-interface": "dev-master"
    },
    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/fonseca-hugo/laravel-wordpress-interface"
      }
    ]

After that run `composer install` and wait.

## Usage

First, create your own models, which extend the models included in this package (this allows for more flexibility and also any override you require).

Example:
    
    Post.php
    <?php namespace App;
    
    use HugoFonseca\LaravelWordpressInterface\Post as WpPost;
    
    
    class Post extends WpPost {
    
    }

To get published posts, you can do the following:

    $posts = Post::published()->get();


### Posts

    // All published posts
    $posts = Post::published()->get();
    $posts = Post::status('publish')->get();

    // A specific post
    $post = Post::find(31);
    echo $post->post_title;

You can also retrieve related data from posts:

    // Get a custom meta value (like 'link' or whatever) from a post
    $post = Post::find(31);
    echo $post->meta->link; // OR
    echo $post->fields->link;
    echo $post->link; // OR

Updating post custom fields:

    $post = Post::find(1);
    $post->meta->username = 'hugofonseca';
    $post->meta->url = 'http://hugofonseca.co.uk';
    $post->save();

Inserting custom fields:

    $post = new Post;
    $post->save();

    $post->meta->username = 'hugofonseca';
    $post->meta->url = 'http://hugofonseca.co.uk';
    $post->save();

### Custom Post Type

Retrieving custom post types: you can use the `type(string)` method or create your own class.

    // using type() method
    $videos = Post::type('video')->status('publish')->get();

    // using your own class
    use HugoFonseca\LaravelWordpressInterface/Post as WpPost;
    class Video extends WpPost
    {
        protected $postType = 'video';
    }
    $videos = Video::status('publish')->get();

Custom post types and meta data:

    // Get 3 posts with custom post type (store) and show its title
    $stores = Post::type('store')->status('publish')->take(3)->get();
    foreach ($stores as $store) {
        $storeAddress = $store->address; // option 1
        $storeAddress = $store->meta->address; // option 2
        $storeAddress = $store->fields->address; // option 3
    }

### Taxonomies

Retrieving taxonomies for a specific post:

    $post = Post::find(1);
    $taxonomy = $post->taxonomies()->first();
    echo $taxonomy->taxonomy;

Or you can search for posts using its taxonomies:

    $post = Post::taxonomy('category', 'php')->first();

### Pages

Pages are like custom post types. You can use `Post::type('page')` or the `Page` class.

    // Find a page by slug
    $page = Page::slug('about')->first(); // OR
    $page = Post::type('page')->slug('about')->first();
    echo $page->post_title;

### Categories & Taxonomies

Get a category or taxonomy or load posts from a certain category:

    // all categories
    $cat = Taxonomy::category()->slug('uncategorized')->posts()->first();
    echo "<pre>"; print_r($cat->name); echo "</pre>";

    // only all categories and posts connected with it
    $cat = Taxonomy::where('taxonomy', 'category')->with('posts')->get();
    $cat->each(function($category) {
        echo $category->name;
    });

    // clean and simple all posts from a category
    $cat = Category::slug('uncategorized')->posts()->first();
    $cat->posts->each(function($post) {
        echo $post->post_title;
    });


### Attachments and Revisions

Getting the attachments and/or revisions from a `Post` or `Page`:

    $page = Page::slug('about')->with('attachment')->first();
    // get feature image from page or post
    print_r($page->attachment);

    $post = Post::slug('test')->with('revision')->first();
    // get all revisions from a post or page
    print_r($post->revision);


## Licence

Laravel Wordpress Interface is licensed under the MIT license.