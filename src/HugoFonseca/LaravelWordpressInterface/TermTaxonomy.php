<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;


class TermTaxonomy extends WpModel
{

    // Set the table including database prefix used in WordPress
    protected $table = 'wp_term_taxonomy';
    protected $primaryKey = 'term_taxonomy_id';
    protected $with = array('term');

    public function term() {
        return $this->belongsTo('HugoFonseca\LaravelWordpressInterface\Term', 'term_id');
    }

    public function parentTerm() {
        return $this->belongsTo('HugoFonseca\LaravelWordpressInterface\TermTaxonomy', 'parent');
    }

    public function posts() {
        return $this->belongsToMany('HugoFonseca\LaravelWordpressInterface\Post', 'wp_term_relationships', 'term_taxonomy_id', 'object_id');
    }

    /**
     * Overriding newQuery() to the custom TermTaxonomyBuilder with some interesting methods
     *
     * @param bool $excludeDeleted
     * @return TermTaxonomyBuilder
     */
    public function newQuery($excludeDeleted = true) {
        $builder = new TermTaxonomyBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);

        if (!empty($this->taxonomy)) {
            $builder->where('taxonomy', $this->taxonomy);
        }

        return $builder;
    }

    /**
     * Magic method to return the meta data like the post original fields
     *
     * @param string $key
     * @return string
     */
    public function __get($key) {
        if (!isset($this->$key)) {
            if (isset($this->term->$key)) {
                return $this->term->$key;
            }
        }

        return parent::__get($key);
    }

}