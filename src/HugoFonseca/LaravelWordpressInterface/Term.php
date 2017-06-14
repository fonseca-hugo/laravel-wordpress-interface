<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;


class Term extends WpModel
{

    protected $table = 'wp_terms';
    protected $primaryKey = 'term_id';

}