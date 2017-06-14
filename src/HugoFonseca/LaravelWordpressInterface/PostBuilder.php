<?php
/**
 * Created by PhpStorm.
 * User: hugofonseca
 * Date: 14/04/15
 * Time: 10:59
 */

namespace HugoFonseca\LaravelWordpressInterface;


use Illuminate\Database\Eloquent\Builder;

class PostBuilder extends Builder
{
    /**
     * Get only posts with a custom status
     *
     * @param string $postStatus
     * @return PostBuilder
     */
    public function status($postStatus) {
        return $this->where('post_status', $postStatus);
    }

    /**
     * Get only published posts
     *
     * @return PostBuilder
     */
    public function published() {
        return $this->status('publish');
    }

    /**
     * Get only posts from a custom post type
     *
     * @param string $type
     * @return PostBuilder
     */
    public function type($type) {
        return $this->where('post_type', $type);
    }

    public function taxonomy($taxonomy, $term) {
        return $this->whereHas('taxonomies', function ($query) use ($taxonomy, $term) {
            $query->where('taxonomy', $taxonomy)->whereHas('term', function ($query) use ($term) {
                $query->where('slug', $term);
            });
        });
    }

    /**
     * Get only posts with a specific slug
     *
     * @param string slug
     * @return PostBuilder
     */
    public function slug($slug) {
        return $this->where('post_name', $slug);
    }

    /**
     * Overrides the paginate() method to a custom and simple way.
     *
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int $currentPage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 10, $columns = array(), $pageName = 'page', $currentPage = 1) {
        $skip = $currentPage * $perPage - $perPage;
        return $this->skip($skip)->take($perPage)->get();
    }
}