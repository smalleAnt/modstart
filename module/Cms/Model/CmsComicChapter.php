<?php


namespace Module\Cms\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsComicChapter extends Model
{
    use SoftDeletes;

    protected $table = 'cms_m_comic_chapter';


}
