<?php


namespace Module\Cms\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Model\CmsComic;
use Module\Cms\Model\CmsComicChapter;
use Module\Cms\Util\CmsContentUtil;

class ComicController extends ModuleBaseController
{

    public function show($comicId)
    {
        $viewData = [];
        $data = CmsComic::getOneWithoutTrashed(['id'=>$comicId]);
        $data = $data->toArray();
        if (empty($data)) {
            $data = [];
        }else{
            $data['status_des'] = CmsComic::$statusMap[$data['status']];
        }
        $viewData['record'] = $data;
        return $this->view('cms.comic.detail', $viewData);
    }

    public function chapter($comicId,$chapterId)
    {
        $viewData = [];
        $data = CmsComicChapter::getOneWithoutTrashed(['id'=>$chapterId]);
        $data = $data->toArray();
        $sort = $data['sort'];
        $comicId = (int)$comicId;
        //获取下一章
        $nextChapter = CmsComicChapter::getNext($comicId,$sort);
        $data['next_chapter_id'] = is_null($nextChapter)  ? null : $nextChapter->id;
        $preChapter = CmsComicChapter::getPre($comicId,$sort);
        $data['pre_chapter_id'] = is_null($preChapter)  ? null : $preChapter->id;

        $viewData['record'] = $data;
        return $this->view('cms.comic.chapter', $viewData);
    }
}
