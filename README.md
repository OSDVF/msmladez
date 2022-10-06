# Web moravskoslezské evangelické mládeže

Upravené oficiální (jak jen mohou být) webové stránky mládeže Českobratrské církve
evangelické tak, aby seděly na naši mládež.

## Přidané schopnosti

- Zrcadlení příspěvků z facebooku

## How to run the website locally

The following instructions should work on any Unix-like system (MacOS, Linux).
For Windows, some steps will have to be worked around.

**Prerequisites:**

*   Local web server with PHP support and MySQL-like database. For more details
    check the requirements of
    [WordPress](https://wordpress.org/support/article/requirements/) and [Nette
    2.4](https://doc.nette.org/en/2.4/requirements).
*   PHP CLI (to run PHP scripts from command line)
*   node.js
*   yarn
*   Composer

Clone the Git repository:

```
$ git clone https://github.com/mladez-cce/web.git
```

Install PHP & JavaScript dependencies:

```
$ composer install
$ yarn install
```

Download WordPress into the `public` directory. You can use the WordPress CLI
which was automatically installed by composer:

```
$ vendor/bin/wp core download --path=public
```

As an alternative, you can manually [download](https://wordpress.org/download/)
WordPress and unzip it into the `public` directory.

Now create a local config file:

```
$ bin/create-local-config.sh
```

If the bash script doesn't work, you can manually copy
`config/config.local.sample.neon` to `config/config.local.neon` and populate the
auth keys (just insert some random values).

Add your database information to the newly created local config file,
`config/config.local.neon`. The relevant section should look like this:

```
database:
	host: 127.0.0.1
	database: mladez
	username: mladez
	password: YourSecretDatabasePassword
```

Make the `log` and `temp` directories writable:

```
$ chmod 777 log
$ chmod 777 temp
```

(Ideally, make it writable only by the web server.)

Install WordPress. You can either go through the standard wizard by accessing
the page URL or use the WP CLI for express installation:

```
$ vendor/bin/wp core install \
    --url=http://localhost \
    --title="Evangelická mládež" \
    --admin_user=admin \
    --admin_email=your@email.com \
    --path=public
```

The administrator password will be printed on the screen.

Install required WordPress plugins. You can do it
[manually](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation)
or use the WP CLI again:

```
$ vendor/bin/wp plugin install meta-box --path=public
$ vendor/bin/wp plugin activate meta-box --path=public
```

Optionally, install the Czech language pack and enable it in the settings (this
affects not only the admin interface but also the date format):

```
$ vendor/bin/wp language core install cs_CZ --path=public
```

Clear cache to avoid potential permission problems:

```
$ rm -r temp/*
```

Build CSS files:

```
$ yarn build-css
$ yarn build-gutenberg-css
```

Done. The website should be now up and running. You can access the
administration on `/wp-admin`.

## Import default site content

You can import the default site content (sample pages, posts, events, menus etc)
from [`wp-site-content-export.xml`](wp-site-content-export.xml).

1.  Install & activate [Wordpress Importer](https://cs.wordpress.org/plugins/wordpress-importer/)
2.  Log in into WordPress admin
3.  Go to `Tools → Import` and run `WordPress Importer`
4.  Upload [`wp-site-content-export.xml`](wp-site-content-export.xml)

For best experience (e.g. to get also the default media imported), first open
the XML file and replace all instances of the base site url (e.g.
`http://31.31.74.63/`) with your WordPress URL.

Note that the export is not intended as backup of the actual website. Its
purpose is merely to simplify setting up the local dev environment.

## Development intro (how to make changes)

_If you want to make changes, the following section is intended to help you._

### Stack

The website is built using the combination of
[WordPress](https://wordpress.org/) and [Nette](https://nette.org/), as bundled
by [MangoPress](https://github.com/manGoweb/MangoPress/).

To make changes, you should have rough understanding of both tools (refer to the
appropriate documentations). However, the Nette integration is light, and we're
mainly using it for [Latte](https://latte.nette.org/), a fancy templating
language, so that's mostly all you need.

PHP deps (including Nette & Latte) are managed by
[composer](https://getcomposer.org/).

WordPress deps (plugins) are not managed in any way and need to be handled
manually. However, there is not that many must-have plugins required for the
page to run, just make sure to follow & keep up-to-date the installation
instructions (above).

JavaScript & styling deps are managed in `package.json` and installed using
[npm](https://www.npmjs.com/) or [yarn](https://yarnpkg.com/).

Currently, we're using raw, vanilla JavaScript for the little functionality that
we need. Styles are written in [Sass](https://sass-lang.com/) and transpiled to
CSS. Use the following command to build the styles and watch for changes:

```
$ yarn watch-css
```

### Code structure

For core configuration (like database login) go to [`config`](config). Avoid
making changes to the WordPress config file.

The whole website is built as a **custom WordPress theme**, which contains both
the presentational logic (how the website looks) and the functional logic (e.g.
custom [post
types](https://wordpress.org/support/article/post-types/#custom-post-types) for
events).

Most of the relevant files are in the top-level [`theme`](theme) directory,
which is where you're most likely going to make your changes. Check
[`theme/README.md`](theme/README.md) (and additional _READMEs_ in subfolders)
for more info.

The rest is in [`public/assets`](public/assets) (static assets like images and
JavaScripts, which are not compiled).

[`public`](public) is where WordPress is installed and it should be the document
root for your web server.

### Making changes

Propose your change in a form of a GitHub [pull
request](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request).
Make sure you first test it locally.

## Additional site setup for production

Some functionality is not part of this repo and needs to be set up manually.
However, that functionality is mostly only relevant for the actual site instance
in production.

### Generated XML sitemap

The XML sitemap for improved search engine crawling can be generated using e.g.
the [Google XML
Sitemaps](https://wordpress.org/plugins/google-sitemap-generator/) plugin.

```
$ vendor/bin/wp plugin install google-sitemap-generator --path=public
$ vendor/bin/wp plugin activate google-sitemap-generator --path=public
```

Once it is installed, go to `Settings → XML-Sitemap` and make sure to include
also the custom post types in `Sitemap Content`.

### Visitor stats

As we wanted to avoid relying on external services (which are often problematic
from GDPR perspective, including Google Analytics), we chose the
[WP-Statistics plugin](https://wp-statistics.com/) to track our visitors.

```
vendor/bin/wp plugin install wp-statistics --path=public
vendor/bin/wp plugin activate wp-statistics --path=public
```

Once installed, consider enabling `Anonymize IP Addresses` in `Statistics →
Settings → Privacy`.
