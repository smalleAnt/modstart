<?php


namespace Module\Cms\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;
use Module\Cms\Model\CmsComic;
use Module\Cms\Util\CmsContentUtil;

class ComicController extends ModuleBaseController
{
    public function index($tag)
    {
        if (empty($tag)) {
            return Response::redirect(modstart_web_url(''));
        }
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize('pageSize');
        $option = [];
        $option['search'][] = ['tags' => ['like' => "%:$tag:%"]];
        $paginateData = CmsContentUtil::paginate($page, $pageSize, $option);
        $viewData = [];
        $viewData['tag'] = $tag;
        $viewData['records'] = $paginateData['records'];
        $viewData['page'] = $page;
        $viewData['pageSize'] = $pageSize;
        $viewData['total'] = $paginateData['total'];
        $viewData['pageTemplate'] = '?page={page}';
        $viewData['pageHtml'] = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, '?page={page}');
        $viewData['pageTitle'] = $tag . ' | ' . modstart_config('siteName');
        $viewData['pageKeywords'] = $tag;
        $viewData['pageDescription'] = $tag;
        return $this->view('cms.tag.index', $viewData);
    }

    public function show($comicId)
    {
        $viewData = [];
        $data = CmsComic::getOneWithoutTrashed(['id'=>$comicId]);
        $viewData['record'] = $data->toArray();
        return $this->view('cms.comic.detail', $viewData);
    }
}
