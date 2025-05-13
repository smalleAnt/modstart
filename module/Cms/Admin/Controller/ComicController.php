<?php


namespace Module\Cms\Admin\Controller;


use Carbon\Carbon;
use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\TagUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Tags;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Layout\LayoutGrid;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Support\Manager\FieldManager;
use ModStart\Widget\TextLink;
use Module\Cms\Core\CmsRecommendBiz;
use Module\Cms\Field\CmsField;
use Module\Cms\Model\CmsComic;
use Module\Cms\Type\CmsContentVerifyStatus;
use Module\Cms\Type\CmsMode;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsModelUtil;
use Module\Cms\Util\CmsTemplateUtil;
use Module\Member\Util\MemberFieldUtil;

class ComicController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    private $model;
    private $modelId;
    private $modelTable;
    private $modelDataTable;
    private $comicId;

    private function init($modelId)
    {
        AdminPermission::permitCheck('CmsContentManage' . $modelId);
        $this->modelId = $modelId;
        $this->model = CmsModelUtil::get($modelId);
        $this->modelTable = 'cms_content';
        $this->modelDataTable = "cms_m_" . $this->model['name'];
    }

    /**
     * 动漫类别页
     * @param AdminPage $page
     * @param $modelId
     * @return array|AdminPage|string
     */
    public function index(AdminPage $page, $modelId)
    {
        MemberFieldUtil::register();
        $this->init($modelId);
        $grid = Grid::make(new CmsComic());
        $grid->text('name', '名称');
        $grid->text('author', '作者');
        $grid->text('summary', '摘要');
        $grid->number('hits', '点击数');
        $grid->type('status', '状态')->type(CmsModelContentStatus::class, [
            CmsModelContentStatus::SHOW => 'success',
            CmsModelContentStatus::HIDE => 'muted',
        ]);
        $grid->display('post_time', '发布时间');
        $filterFields = array_filter($this->model['_customFields'], function ($o) {
            return $o['isSearch'];
        });
        $tableName = $this->modelDataTable;
        $grid->gridFilter(function (GridFilter $filter) use ($filterFields, $tableName) {
//            $filter->eq('id', 'ID');
            $filter->like('name', '名称');
        });
        $grid->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@edit', ['modelId' => $this->modelId]));
        $grid->canEdit(true)->urlEdit(action('\\' . __CLASS__ . '@edit', ['modelId' => $this->modelId]));
        $grid->canDelete(true)->urlDelete(action('\\' . __CLASS__ . '@delete', ['modelId' => $this->modelId]));


        $grid->hookItemOperateRendering(function (ItemOperate $itemOperate) {
            $item = $itemOperate->item();
            $url = modstart_admin_url("cms/comic_content/{$item->id}/chapter");

            $title = addslashes($item->title);
            $itemOperate->push('<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="aa()">章节</a>');
        });
        if (Request::isPost()) {
            return $grid->request();
        }
        $page->append(<<<JS
<script>
function aa(){
    $('#adminTabPage').append(`<iframe src="" class="hidden" frameborder="0" data-tab-page="5555"></iframe>`);
    $('#adminTabMenu').append(`<a href="javascript:;" data-tab-menu="5555" draggable="false">aaaaa<i class="close iconfont icon-close"></i></a>`);
}
</script>
JS
        );
        return $page->pageTitle($this->model['title'])->append($grid);
    }

    /**
     * 编辑页面
     * @param AdminPage $page
     * @param $modelId
     * @return array|AdminPage|string
     */
    public function edit(AdminDialogPage $page, $modelId)
    {
        $this->init($modelId);
        $id = CRUDUtil::id();
        $record = [];
        $model = new CmsComic();
        if ($id) {
            $this->comicId = $id;
            $record = ModelUtil::get($model, $id);
            BizException::throwsIfEmpty('记录不存在', $record);
            if (!empty($record)) {
                foreach ($record as $k => $v) {
                    if (in_array($k, ['id', 'created_at', 'updated_at'])) {
                        continue;
                    }
                    $record[$k] = $v;
                }
            }
        }
        $form = Form::make(null);
        $form->layoutGrid(function (LayoutGrid $layout) {
            $layout->layoutColumn(8,function (Form $form){
                $form->layoutPanel('基本信息', function (Form $form) {
                    $form->text('name', '名称')->required();
                    if (in_array($this->model['mode'], [CmsMode::LIST_DETAIL, CmsMode::PAGE])) {
                        $form->textarea('summary', '摘要');
                        $form->datetime('post_time', '发布时间')->required()->help('可以是未来时间，在未来发布')->defaultValue(Carbon::now());
                        $form->radio('status', '状态')->optionType(CmsModelContentStatus::class)->required()->defaultValue(CmsModelContentStatus::SHOW);

                        $form->switch('isTop', '置顶');
                        $form->tags('tags', '标签')->serializeType(Tags::SERIALIZE_TYPE_COLON_SEPARATED);
                        $form->text('author', '作者');
                        $form->text('source', '来源');
                        $form->image('cover', '封面');
                    }
                });
                $form->layoutPanel('章节列表', function (Form $form){
                    $html = '';
                    $chapters = ModelUtil::all('cms_m_comic_chapters',['comic_id'=>$this->comicId]);
                    // 如果没有章节，显示提示
                    if (empty($chapters)) {
                        $html .= '<p>暂无章节！</p>';
                    }else{
                        foreach ($chapters as $chapter) {
                            $html .= "<p>{$chapter['title']}</p>";
                        }
                    }
                    $form->html("chapters",$html);
                });
            });

            $layout->layoutColumn(4, function ($form) {
                $form->layoutPanel('SEO信息', function (Form $form) {
                    $form->text('seoTitle', 'SEO标题');
                    $form->text('seoDescription', 'SEO描述');
                    $form->textarea('seoKeywords', 'SEO关键词');
                });
            });
        });
        $form->item($record)->fillFields();
        $form->showReset(false)->showSubmit(false);
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->formRequest(function (Form $form) use ($record,$model) {
                $data = $form->dataForming();
                $recordValue = ArrayUtil::keepKeys($data, [
                    'name', 'summary', 'status','author','post_time','is_top','tags','cover','seo_title','seo_description',
                    'seo_keywords'
                ]);
                ModelUtil::transactionBegin();
                if (!empty($record['id'])) {
                    $recordValue['updated_at'] = Carbon::now();
                    ModelUtil::update($model, $record['id'], $recordValue);
                } else {
                    $recordValue = ModelUtil::insert($model, $recordValue);
                    $recordDataValue['id'] = $recordValue['id'];
                    ModelUtil::insert($model, $recordDataValue);
                }
                ModelUtil::transactionCommit();
                return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
            });
        }

        return $page->pageTitle($this->model['title'] . '编辑')->body($form);
    }

    /**
     * 删除
     * @param AdminPage $page
     * @param $modelId
     * @return array|AdminPage|string
     */
    public function delete($modelId)
    {
        AdminPermission::demoCheck();
        $this->init($modelId);
        $ids = CRUDUtil::ids();
        $model = new CmsComic();
        foreach ($ids as $id) {
            $record = ModelUtil::get($model, $id);
            BizException::throwsIfEmpty($id.'记录不存在', $record);
            ModelUtil::transactionBegin();
            ModelUtil::delete($model, $id);
            ModelUtil::transactionCommit();
        }
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }

    public function chapterList(AdminPage $page,$comicId)
    {
        $data = ['chapters'=>[]];
        return view('module::cms.view.admin.comic.chapter', $data);
    }
}
