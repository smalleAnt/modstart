<?php


namespace Module\Cms\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsComic extends Model
{
    use SoftDeletes;

    protected $table = 'cms_m_comics';

    const SERIALIZING = 1;
    const FINISH = 2;

    public static function getStatusList()
    {
        return [
            self::SERIALIZING => '连载中',
            self::FINISH => '已完结',
        ];
    }

    public static $statusMap = [
        self::SERIALIZING => '连载中',
        self::FINISH => '已完结',
    ];

    public function chapter()
    {
        return $this->hasMany(CmsComicChapter::class,'comic_id','id')->orderBy('sort','desc');
    }

    public static function getListWithTrashed($where, $perPage = 0, $page = 1)
    {
        $query = self::withTrashed()->where($where)->orderBy('updated_at')->orderBy('id');
        if ($perPage > 0) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            return $query->get();
        }
    }

    public static function getOneWithoutTrashed($where)
    {
        $query = self::with('chapter')->where($where);
        return $query->first();
    }

    public static function getOptions()
    {
        return self::get()->pluck('name', 'id');
    }
}
