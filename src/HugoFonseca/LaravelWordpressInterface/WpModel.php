<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;

use Illuminate\Database\Eloquent\Model;


class WpModel extends Model
{

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    // WordPress uses uppercase "ID" for the primary key
    protected $primaryKey = 'ID';
}