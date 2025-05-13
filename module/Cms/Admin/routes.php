<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'cms/config/setting', 'ConfigController@setting');

$router->match(['get', 'post'], 'cms/model', 'ModelController@index');
$router->match(['get', 'post'], 'cms/model/edit', 'ModelController@edit');
$router->match(['post'], 'cms/model/delete', 'ModelController@delete');
$router->match(['get', 'post'], 'cms/model/field/{modelId}', 'ModelController@field');
$router->match(['get', 'post'], 'cms/model/field/{modelId}/edit', 'ModelController@fieldEdit');
$router->match(['post'], 'cms/model/field/{modelId}/delete', 'ModelController@fieldDelete');
$router->match(['post'], 'cms/model/field/{modelId}/sort', 'ModelController@fieldSort');

$router->match(['get', 'post'], 'cms/template', 'TemplateController@index');

$router->match(['get', 'post'], 'cms/cat', 'CatController@index');
$router->match(['get', 'post'], 'cms/cat/add', 'CatController@add');
$router->match(['get', 'post'], 'cms/cat/edit', 'CatController@edit');
$router->match(['post'], 'cms/cat/delete', 'CatController@delete');
$router->match(['get'], 'cms/cat/show', 'CatController@show');
$router->match(['post'], 'cms/cat/sort', 'CatController@sort');

$router->match(['get', 'post'], 'cms/content/{modelId}', 'ContentController@index');
$router->match(['get', 'post'], 'cms/content/edit/{modelId}', 'ContentController@edit');
$router->match(['post'], 'cms/content/delete/{modelId}', 'ContentController@delete');
$router->match(['get', 'post'], 'cms/content/batch_move/{modelId}', 'ContentController@batchMove');

$router->match(['get', 'post'], 'cms/backup', 'BackupController@index');
$router->match(['get', 'post'], 'cms/restore', 'RestoreController@index');
$router->match(['post'], 'cms/restore/delete', 'RestoreController@delete');
$router->match(['post'], 'cms/restore/submit', 'RestoreController@submit');

$router->match(['get', 'post'], 'cms/comic_content/{modelId}', 'ComicController@index');
$router->match(['get', 'post'], 'cms/comic_content/edit/{modelId}', 'ComicController@edit');
$router->match(['post'], 'cms/comic_content/delete/{modelId}', 'ComicController@delete');
$router->get('cms/comic_content/{comicId}/chapter', 'ComicController@chapterList');

$router->group(['prefix' => 'cms/comic_content'],function ()use ($router){
    $router->match(['get', 'post'], '/{modelId}', 'ComicController@index');
    $router->match(['get', 'post'], '/edit/{modelId}', 'ComicController@edit');
    $router->match(['post'], '/delete/{modelId}', 'ComicController@delete');
    $router->get('/{comicId}/chapter', 'ComicController@chapterList');
});


// 重构路由层级结构，形成清晰的父子关系
$router->group(['prefix' => 'cms/comic_content'], function () use ($router) {
    // 列表页路由（父级）
    $router->match(['get', 'post'], '/{modelId}', 'ComicController@index')->name('comic.list');

    // 详情页分组（子层级）
    $router->group(['prefix' => '/{modelId}'], function () use ($router) {
        $router->get('/chapter/{comicId}', 'ComicController@chapterList')->name('comic.chapter');
        $router->match(['get', 'post'], '/edit', 'ComicController@edit');
        $router->post('/delete', 'ComicController@delete');
    });
});

