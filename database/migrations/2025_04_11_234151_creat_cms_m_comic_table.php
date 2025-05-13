<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatCmsMComicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_m_comics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(false)->comment('漫画名');
            $table->string('summary',500)->nullable(false)->comment('摘要');
            $table->integer('hits')->default(0)->comment('点击数');
            $table->tinyInteger('status')->default(1)->comment('1|显示;2|隐藏');
            $table->timestamp('post_time')->nullable()->comment('发布时间');
            $table->tinyInteger('is_top')->nullable(false)->comment('是否置顶');
            $table->string('tags')->nullable()->comment('标签');
            $table->string('author')->nullable()->comment('作者');
            $table->string('cover')->nullable()->comment('封面地址');
            $table->string('seo_title')->nullable()->comment('SEO标题');
            $table->string('seo_description')->nullable()->comment('SEO描述');
            $table->string('seo_keywords')->nullable()->comment('SEO关键字');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
        DB::statement("ALTER TABLE `cms_m_comics` comment '漫画表'");

        Schema::create('cms_m_comic_chapters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('comic_id')->nullable(false)->comment('漫画ID');
            $table->string('title')->nullable(false)->comment('章节标题');
            $table->string('number')->nullable()->comment('章节编号');
            $table->integer('hits')->default(0)->comment('点击数');
            $table->string('file_path')->nullable(false)->comment('图片地址');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
        DB::statement("ALTER TABLE `cms_m_comic_chapters` comment '漫画章节表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cms_m_comics');
        Schema::drop('cms_m_comic_chapters');
    }
}
