<?php


namespace Module\Cms\Admin\Controller;


use Carbon\Carbon;
use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Layout\LayoutGrid;
use Module\Cms\Model\CmsComic;
use Module\Cms\Model\CmsComicChapter;
use Module\Cms\Util\CmsModelUtil;
use Module\Member\Util\MemberFieldUtil;

class ComicChapterController extends Controller
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
        $this->init(7);
        $grid = Grid::make(new CmsComicChapter());
        $grid->number('id', 'ID');
        $grid->text('comic.name', '所属漫画');
        $grid->text('number', '章节编号');
        $grid->text('title', '章节名称');
        $grid->number('sort', '排序');

        $grid->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@edit', ['modelId' => 7]));
        $grid->canEdit(true)->urlEdit(action('\\' . __CLASS__ . '@edit', ['modelId' => 7]));
        $grid->canDelete(true)->urlDelete(action('\\' . __CLASS__ . '@delete', ['modelId' => 7]));

        if (Request::isPost()) {
            return $grid->request();
        }
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
        $model = new CmsComicChapter();
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
                    $selectOptions = CmsComic::getOptions();
                    $form->select('comic.id', '所属漫画')->options($selectOptions)->required();
                    $form->text('number', '章节编号')->required();
                    $form->text('title', '章节标题');
                    $form->number('sort', '排序')->required();
                    $form->images('file_path', '图片');

                });
            });
        });
        $form->item($record)->fillFields();
        $form->showReset(false)->showSubmit(false);
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->formRequest(function (Form $form) use ($record,$model) {
                $data = $form->dataSubmitted();
                $recordValue = ArrayUtil::keepKeys($data, [
                    'comic_id', 'number', 'title','sort','file_path'
                ]);
                $domain = config('app.url');
                $domain = strlen($domain) == 0 ? 'http://ruants.com' : $domain;
                $recordValue['comic_id'] = $data['comic']['id'];
                $recordValue['file_path'] = str_replace($domain,'',$recordValue['file_path']);
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
        $model = new CmsComicChapter();
        foreach ($ids as $id) {
            $record = ModelUtil::get($model, $id);
            BizException::throwsIfEmpty($id.'记录不存在', $record);
            ModelUtil::transactionBegin();
            ModelUtil::delete($model, $id);
            ModelUtil::transactionCommit();
        }
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }
}
