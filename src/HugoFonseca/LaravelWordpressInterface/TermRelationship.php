<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;


class TermRelationship extends WpModel
{

    protected $table = 'wp_term_relationships';
    protected $primaryKey = array('object_id', 'term_taxonomy_id');

    public function post() {
        return $this->belongsTo('HugoFonseca\LaravelWordpressInterface\Post', 'object_id');
    }

    public function taxonomy() {
        return $this->belongsTo('HugoFonseca\LaravelWordpressInterface\TermTaxonomy', 'term_taxonomy_id');
    }

}