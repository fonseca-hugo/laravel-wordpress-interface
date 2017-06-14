<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HugoFonseca\LaravelWordpressInterface\PostMeta;

class Post extends WpModel
{

    // Set the table including database prefix used in WordPress
    protected $table = 'wp_posts';
    protected $with = array('meta');
    protected $postType = 'post';
    protected $sortField = 'post_date';
    protected $sortDir = 'desc';

    /**
     * Meta data relationship
     *
     * @return PostMetaCollection
     */
    public function meta() {
        return $this->hasMany('HugoFonseca\LaravelWordpressInterface\PostMeta', 'post_id');
    }

    public function fields() {
        return $this->meta();
    }

    /**
     *
     * Taxonomy relationship
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function taxonomies() {
        return $this->belongsToMany('HugoFonseca\LaravelWordpressInterface\TermTaxonomy', 'wp_term_relationships', 'object_id', 'term_taxonomy_id');
    }

    /**
     * Comments relationship
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function comments() {
        return $this->hasMany('HugoFonseca\LaravelWordpressInterface\Comment', 'comment_post_ID');
    }

    /**
     * Get attachment
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function attachment() {
        return $this->hasMany('HugoFonseca\LaravelWordpressInterface\Post', 'post_parent')->where('post_type', 'attachment');
    }

    /**
     * Get revisions from post
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function revision() {
        return $this->hasMany('HugoFonseca\LaravelWordpressInterface\Post', 'post_parent')->where('post_type', 'revision');
    }

    /**
     * Overriding newQuery() to the custom PostBuilder with some interesting methods
     *
     * @param bool $excludeDeleted
     * @return PostBuilder
     */
    public function newQuery($excludeDeleted = true) {
        $builder = new PostBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        $builder->orderBy($this->sortField, $this->sortDir);
        if (!empty($this->postType)) {
            $builder->type($this->postType);
        }
        if ($excludeDeleted && $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
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
            if (isset($this->meta()->get()->$key)) {
                return $this->meta()->get()->$key;
            }
        }
        return parent::__get($key);
    }

    public function save(array $options = array()) {
        if (isset($this->attributes[$this->primaryKey])) {
            $this->meta->save($this->attributes[$this->primaryKey]);
        }
        return parent::save($options);
    }

    public function hasMany($related, $foreignKey = null, $localKey = null) {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $instance = new $related;
        $localKey = $localKey ?: $this->getKeyName();
        return new HasMany($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey);
    }

    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null) {
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $instance = new $related;
        $otherKey = $otherKey ?: $instance->getForeignKey();
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }
        $query = $instance->newQuery();
        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

    /**
     * Adds the featured image from post meta to the posts
     *
     * @param $posts
     * @param string $path
     * @param string $thumbKey
     * @param string $key
     */
    static function addFeaturedImageToPosts(&$posts, $path = '', $thumbKey = '_thumbnail_id', $key = 'featured_image') {
        foreach($posts as &$post) {
            self::addFeaturedImageToPost($post, $path, $thumbKey, $key);
        }
    }

    /**
     * Adds the featured image from post meta to the post
     *
     * @param $posts
     * @param string $path
     * @param string $thumbKey
     * @param string $key
     */
    static function addFeaturedImageToPost(&$post, $path = '', $thumbKey = '_thumbnail_id', $key = 'featured_image') {
        $post[$key] = '';
        foreach ($post['meta'] as $meta) {
            if ($meta['meta_key'] == "_wp_attached_file") {
                $post[$key] = $path . $meta['meta_value'];
                break;
            }
            if ($meta['meta_key'] == $thumbKey) {
                $postMeta = PostMeta::where(['post_id' => $meta['meta_value'], 'meta_key' => '_wp_attached_file'])->get()->toArray();
                if (!empty($postMeta)) {
                    $post[$key] = $path . $postMeta[0]['meta_value'];
                }
                break;
            }
        }
    }

    /**
     * Extracts a MetaData field from the post
     *
     * @param $post
     * @param $key
     * @return string
     */
    static function getMetaField($post, $key) {
        $result = '';

        foreach ($post['meta'] as $meta) {
            if ($meta['meta_key'] == $key) {
                $result = $meta['meta_value'];
                break;
            }
        }

        return $result;
    }

}