<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;

use Illuminate\Database\Eloquent\Builder;

class CommentBuilder extends Builder
{
    /**
     * Where clause for only approved comments
     *
     * @return CommentBuilder
     */
    public function approved() {
        return $this->where('comment_approved', 1);
    }
}