# silverstripe-reviews

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/i-lateral/silverstripe-reviews/badges/quality-score.png?b=1)](https://scrutinizer-ci.com/g/i-lateral/silverstripe-reviews/?branch=1)

Add ability to review dataobjects by expanding on the core "comments" module

This module allows you to attach a "comments form" (and thread) to an object,
in much the same way as the `silverstripe-comments` module, but the form
adds a `Rating` field and you can customise the min and max values via
SilverStripe config.

## Installation

Install via composer:

    composer require i-lateral/silverstripe-reviews

## Usage

Adding reviews to an object is done in much the same way as the comments module.
First you must add the extension, then you can customise any of the `CommentsOptions`.

For example, if I have a `Product` class (in the global namespace) then I would add the
following to `config.yml` (**note**: you must also add the comments extension).

```yml
Product:
  extensions:
    - SilverStripe\Comments\Extensions\CommentsExtension # must be added first
    - ilateral\SilverStripe\Reviews\Extensions\ReviewsExtension
```

Once you have done this, you can customise options (such as min/max rating) as below:

```yml
Product:
  extensions:
    - SilverStripe\Comments\Extensions\CommentsExtension
    - ilateral\SilverStripe\Reviews\Extensions\ReviewsExtension
  # Customise comments options
  comments:
    show_ratings: true # Disables ratings
    min_rating: 1 # Minimum rating possible
    max_rating: 5 # Maximum rating possible 
    enable_url: false # Re-show the URL field (hidden by default)
```

**NOTE** This module extends on the functionality of the comments module, so you still need to add the template variable to your Page/Controller template:

    $CommentsForm
