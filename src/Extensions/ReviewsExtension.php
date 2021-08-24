<?php

namespace ilateral\SilverStripe\Reviews\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Controller;
use ilateral\SilverStripe\Reviews\Control\ReviewsController;
use ilateral\SilverStripe\Reviews\Helpers\ReviewHelper;

class ReviewsExtension extends DataExtension
{
    /**
     * Extra configuration values (to be combined with those provided by comments)
     *
     * min_rating: The minimum value used for the ratings field
     * max_rating: The maximum value used for the ratings field
     * disable_url:Hide the "URL" field 
     *
     * @var array
     *
     * @config
     */
    private static $comments = [
        'show_ratings' => true,
        'min_rating'   => 1,
        'max_rating'   => 5,
        'enable_url'   => false
    ];

    private static $casting = [
        "AverageRating" => "Decimal",
        "AverageRatingStars" => "HTMLText",
        "ExcessRatingStars" => "HTMLText",
        "AverageRatingPercent" => "HTMLText"
    ];


    /**
     * Get the MEAN average rating for the objects
     * reviews
     * 
     * @return float
     */
    public function getAverageRating()
    {
        $comments = $this
            ->getOwner()
            ->Comments()
            ->filter("Rating:not", null);
        
        $total_rating = 0;
        $total_comments = $comments->count();

        foreach ($comments as $comment) {
            $total_rating = $total_rating + $comment->Rating;
        }

        if ($total_rating > 0) {
            return $total_rating / $total_comments;
        }

        return 0;
    }

    /**
     * Get the average rating as HTML Star characters
     * (one star per increment of rating).
     * 
     * @return string
     */
    public function getAverageRatingStars()
    {
        return ReviewHelper::getStarsFromValues(
            $min = $this->getOwner()->getCommentsOption("min_rating"),
            round($this->getOwner()->AverageRating)
        );
    }

    /**
     * Get the stars remaining (total minus the average)
     * 
     * @return string
     */
    public function getExcessRatingStars()
    {
        $max = $this->getOwner()->getCommentsOption("max_rating");
        $rating = $this->getOwner()->AverageRating;
        $excess = $max - round($rating);

        return ReviewHelper::getStarsFromValues(
            $this->getOwner()->getCommentsOption("min_rating"),
            $excess,
            $html = "&#9734;"
        );
    }

    /**
     * Custom comments form that overwrites the default comments extension
     * 
     * @return string
     */
    public function CommentsForm()
    {
        $owner = $this->getOwner();
        // Check if enabled
        $enabled = $owner->getCommentsEnabled();
        if ($enabled && $owner->getCommentsOption('include_js')) {
            Requirements::javascript('silverstripe/comments:client/dist/js/jquery.min.js');
            Requirements::javascript('silverstripe/comments:client/dist/js/jquery-validation/jquery.validate.min.js');
            Requirements::javascript('silverstripe/admin:client/dist/js/i18n.js');
            Requirements::add_i18n_javascript('silverstripe/comments:client/lang');
            Requirements::javascript('silverstripe/comments:client/dist/js/CommentsInterface.js');
        }

        $controller = ReviewsController::create();
        $controller->setOwnerRecord($owner);
        $controller->setParentClass($owner->getClassName());
        $controller->setOwnerController(Controller::curr());

        $session = Controller::curr()->getRequest()->getSession();
        $moderatedSubmitted = $session->get('CommentsModerated');
        $session->clear('CommentsModerated');

        $form = ($enabled) ? $controller->CommentsForm() : false;

        // a little bit all over the show but to ensure a slightly easier upgrade for users
        // return back the same variables as previously done in comments
        return $this
            ->owner
            ->customise(array(
                'AddCommentForm' => $form,
                'ModeratedSubmitted' => $moderatedSubmitted,
            ))
            ->renderWith('ReviewsInterface');
    }   
}