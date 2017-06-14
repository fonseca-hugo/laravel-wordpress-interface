<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;


class PostMeta extends WpModel
{

    protected $table = 'wp_postmeta';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;
    protected $fillable = array('meta_key', 'meta_value', 'post_id');

    /**
     * Post relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post() {
        return $this->belongsTo('HugoFonseca\LaravelWordpressInterface\Post');
    }

    /**
     * Override newCollection() to return a custom collection
     *
     * @param array $models
     * @return PostMetaCollection
     */
    public function newCollection(array $models = array()) {
        return new PostMetaCollection($models);
    }

}