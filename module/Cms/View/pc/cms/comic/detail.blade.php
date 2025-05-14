@extends($_viewFrame)

@section('pageTitleMain'){{$record['name']}}@endsection
@section('pageKeywords'){{isset($record['seoKeywords'])?$record['seoKeywords']:$record['name']}}@endsection
@section('pageDescription'){{isset($record['seoDescription'])?$record['seoDescription']:$record['name']}}@endsection

@section('bodyContent')
    <style>
        .ub-pair { padding: .5em 0 .5em 5em;line-height: 1em; }
    </style>

    <div class="ub-container">
        <div class="ub-article-a margin-bottom margin-top">
            <div class="row">
                <div class="col-md-4">
                    <div class="">
                        <div class="cover contain ub-cover-1-1"
                             style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}});">
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="content">
                        <h1>{{$record['name']}}</h1>
                        <div class="info">
                            <div class="ub-pair" >
                                <div class="name">发布时间：</div>
                                <div class="value">{{empty($record['post_time'])?'暂无':$record['post_time']}}</div>
                            </div>
                            <div class="ub-pair" >
                                <div class="name">状态：</div>
                                <div class="value">{{empty($record['status_des'])?'':$record['status_des']}}</div>
                            </div>
                            <div class="ub-pair">
                                <div class="name">作者：</div>
                                <div class="value">{{empty($record['author'])?'暂无':$record['author']}}</div>
                            </div>
                            <div class="ub-pair">
                                <div class="name">标签：</div>
                                <div class="value">{{empty($record['tags'])?'暂无':$record['tags']}}</div>
                            </div>
                            <div class="ub-pair">
                                <div class="name">简介：</div>
                                <div class="value">{{empty($record['summary'])?'暂无':$record['summary']}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="ub-panel">
                    <div class="title">
                        <div class="head">
                            <i class="iconfont icon-magic-wand"></i>
                            章节列表
                        </div>
                    </div>
                    <div class="body">
                        @if(count($record['chapter'])>0)
                            <div class="ub-list-items" style="height: 300px; overflow: auto;">
                                <div class="row">
                                    @foreach($record['chapter'] as $chapter)
                                        <div class="col-md-3 col-6">
                                            <div class="item-p" style="border: #eee 1px solid;margin-bottom: 0.3em;padding: 0;box-shadow:none;">
                                                <a class="title" href="/comic/{{$chapter['comic_id']}}/chapter/{{$chapter['id']}}/">{{$chapter['number']}} {{$chapter['title']}}</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12" style="text-align: center">
                                    <h2>暂无章节！</h2>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection





