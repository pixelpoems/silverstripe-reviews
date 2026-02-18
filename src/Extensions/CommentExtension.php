<?php

declare(strict_types=1);

namespace ilateral\SilverStripe\Reviews\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Comments\Model\Comment;
use ilateral\SilverStripe\Reviews\Helpers\ReviewHelper;
use SilverStripe\Forms\FieldList;

class CommentExtension extends Extension
{
    private static array $db = [
        'Rating' => 'Int'
    ];

    private static array $casting = [
        'MaxRating' => 'Int',
        'RatingStars' => 'HTMLText',
        'ExcessStars' => 'HTMLText'
    ];

    private static array $summary_fields = [
        'Rating'
    ];

    public function getMaxRating()
    {
        return $this->getOwner()->Parent()->getCommentsOption('max_rating');
    }

    /**
     * Get the rating as HTML Star characters
     * (one star per increment of rating).
     */
    public function getRatingStars(): string
    {
        return ReviewHelper::getStarsFromValues(
            $this->getOwner()->Parent()->getCommentsOption('min_rating'),
            round($this->getOwner()->Rating)
        );
    }

    /**
     * Get the excess rating as HTML Star characters
     * (one star per increment of rating).
     */
    public function getExcessStars(): string
    {
        $max = $this->getOwner()->Parent()->getCommentsOption("max_rating");
        $rating = $this->getOwner()->Rating;
        $excess = $max - round($rating);

        return ReviewHelper::getStarsFromValues(
            $this->getOwner()->Parent()->getCommentsOption("min_rating"),
            $excess,
            $html = "&#9734;"
        );
    }

    public function updateCMSFields(FieldList $fields): void
    {
        /** @var Comment */
        $owner = $this->getOwner();

        $fields->insertBefore(
            'Name',
            $owner->dbObject('Rating')->scaffoldFormField($owner->fieldLabel('Rating'))
        );
    }
}
