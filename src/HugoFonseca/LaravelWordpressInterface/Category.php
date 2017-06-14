<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;


class Category extends TermTaxonomy
{

    /**
     * Used to set the post's type
     */
    protected $taxonomy = 'category';

}