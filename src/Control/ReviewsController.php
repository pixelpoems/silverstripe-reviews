<?php

namespace ilateral\SilverStripe\Reviews\Control;

use SilverStripe\Core\Extension;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Comments\Forms\CommentForm;
use SilverStripe\Comments\Controllers\CommentingController;

class ReviewsController extends CommentingController
{

    private static $allowed_actions = [
        'delete',
        'spam',
        'ham',
        'approve',
        'rss',
        'CommentsForm',
        'reply',
        'doPostComment',
        'doPreviewComment'
    ];

    /**
     * Workaround for generating the link to this controller
     *
     * @param  string $action
     * @param  int    $id
     * @param  string $other
     * @return string
     */
    public function Link($action = '', $id = '', $other = '')
    {
        return Controller::join_links(
            Director::baseURL(),
            'ssreviews',
            $action,
            $id,
            $other
        );
    }

    public function CommentsForm()
    {
        $form = Injector::inst()->create(CommentForm::class, __FUNCTION__, $this);
        $fields = $form->Fields();

        $enable_url = $this->getOption('enable_url');
        $min = $this->getOption('min_rating');
        $max = $this->getOption('max_rating');
        $id = $this->getRequest()->postVar('ParentID');
        $class = $this->getRequest()->postVar('ParentClassName');
        
        // If we dont have exact values, look to see if we are using a post
        if ((empty($min) || empty($max)) && $id && $class) {
            if ($object = $class::get()->byID($id)) {
                $min = $object->getCommentsOption('min_rating');
                $max = $object->getCommentsOption('max_rating');
            }
        }

        // Add reviews field
        $required_text = _t(
            self::class . '.Rating_Required',
            'Please enter a Rating'
        );

        // Setup possible ratings
        $ratings = [];

        for ($i = $min; $i <= $max; $i++) {
            $ratings[$i] = $i;
        }

        // Add rating field to comment form
        $fields->insertBefore(
            'Comment',
            OptionsetField::create(
                'Rating',
                _t(
                    self::class . '.Rating',
                    'Rating'
                ),
                $ratings
            )->setCustomValidationMessage($required_text)
            ->setAttribute('data-msg-required', $required_text)
        );

        // Website URL is possibly overkill for a review, disable unless we overwrite this
        if ($enable_url !== true) {
            $fields->removeByName("URL");
        }

        $fields->setForm($form);
        $form->setFields($fields);

        // hook to allow further extensions to alter the comments form
        $this->extend('alterCommentForm', $form);

        return $form;
    }
}