<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;


class Comment extends WpModel
{

    protected $table = 'wp_comments';
    protected $primaryKey = 'comment_ID';

    /**
     * Post relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post() {
        return $this->belongsTo('HugoFonseca\LaravelWordpressInterface\Post', 'comment_post_ID');
    }

    /**
     * Original relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original() {
        return $this->belongsTo('HugoFonseca\LaravelWordpressInterface\Comment', 'comment_parent');
    }

    /**
     * Replies relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies() {
        return $this->hasMany('HugoFonseca\LaravelWordpressInterface\Comment', 'comment_parent');
    }

    /**
     * Verify if the current comment is approved
     *
     * @return bool
     */
    public function isApproved() {
        return $this->attributes['comment_approved'] == 1;
    }

    /**
     * Verify if the current comment is a reply from another comment
     *
     * @return bool
     */
    public function isReply() {
        return $this->attributes['comment_parent'] > 0;
    }

    /**
     * Verify if the current comment has replies
     *
     * @return bool
     */
    public function hasReplies() {
        return count($this->replies) > 0;
    }

    /**
     * Find a comment by post ID
     *
     * @param int $postId
     * @return Comment
     */
    public static function findByPostId($postId) {
        $instance = new static;
        return $instance->where('comment_post_ID', $postId)->get();
    }

    /**
     * Override the parent newQuery() to the custom CommentBuilder class
     *
     * @param bool $excludeDeleted
     * @return CommentBuilder
     */
    public function newQuery($excludeDeleted = true) {
        $builder = new CommentBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);

        if ($excludeDeleted && $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
        }

        return $builder;
    }
}