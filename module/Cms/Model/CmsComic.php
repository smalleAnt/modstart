<?php


namespace Module\Cms\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsComic extends Model
{
    use SoftDeletes;

    protected $table = 'cms_m_comics';

    public static function getListWithTrashed($where, $perPage = 0, $page = 1)
    {
        $query = self::withTrashed()->where($where)->orderBy('updated_at')->orderBy('id');
        if ($perPage > 0) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            return $query->get();
        }
    }
}
