<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;

use Illuminate\Database\Eloquent\Builder;

class TermTaxonomyBuilder extends Builder
{
    private $categorySlug;

    public function posts() {
        return $this->with('posts');
    }

    public function category() {
        return $this->where('taxonomy', 'category');
    }

    /**
     * Get only posts with a specific slug
     *
     * @param string slug
     * @return PostBuilder
     */
    public function slug($categorySlug = null) {
        if (!is_null($categorySlug) && !empty($categorySlug)) {
            // set this category_slug to be used in with callback
            $this->categorySlug = $categorySlug;

            // exception to filter on slug from category
            $exception = function ($query) {
                $query->where('slug', '=', $this->categorySlug);
            };

            // load term to filter
            return $this->whereHas('term', $exception);
        }

        return $this;
    }
}