<?php


namespace Module\Cms\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsComicChapter extends Model
{
    use SoftDeletes;

    protected $table = 'cms_m_comic_chapters';

    public function comic()
    {
        return $this->belongsTo(CmsComic::class,'comic_id','id');
    }

    public function imgs()
    {
        return $this->hasMany(CmsComicChapterImg::class,'chapter_id','id')->orderBy('sort','asc');
    }

    public static function getOneWithoutTrashed($where)
    {
        $query = self::with(['comic','imgs'])->where($where);
        return $query->first();
    }

    //获取下一章
    public static function getNext($comicId,$sort)
    {
         return self::where('comic_id',$comicId)->where('sort','>',$sort)->orderBy('sort','desc')->first();
    }

    //获取下一章
    public static function getPre($comicId,$sort)
    {
        return self::where('comic_id',$comicId)->where('sort','<',$sort)->orderBy('sort','desc')->first();
    }
}
