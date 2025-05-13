@extends($_viewFrame)


@section('bodyContent')
    <style>
        .ub-pair { padding: .5em 0 .5em 5em;line-height: 1em; }
    </style>

    <div class="ub-container">
        <div class="ub-article-a margin-bottom">
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
                                <div class="name">状态：</div>
                                <div class="value">{{empty($record['post_time'])?'暂无':$record['post_time']}}</div>
                            </div>
                            <div class="ub-pair" >
                                <div class="name">发布时间：</div>
                                <div class="value">{{empty($record['post_time'])?'暂无':$record['post_time']}}</div>
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
    </div>

@endsection





