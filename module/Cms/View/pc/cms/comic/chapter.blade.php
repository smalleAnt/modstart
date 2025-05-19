@extends($_viewFrame)
@section('pageTitleMain'){{$record['comic']['name']}} {{$record['number']}} {{$record['title']}}@endsection
@section('pageKeywords'){{$record['comic']['name']}} {{$record['number']}} {{$record['title']}}@endsection
@section('pageDescription'){{$record['comic']['name']}} {{$record['number']}} {{$record['title']}}@endsection
@section('bodyContent')

    <div class="ub-container margin-bottom">
        <div class="tw-bg-white">
            <div class="ub-article">
                <div class="row">
                    @if ($record['pre_chapter_id'] != null)
                        <a href="/comic/{{$record['comic_id']}}/chapter/{{$record['pre_chapter_id']}}"  class="offset-md-2 col-md-1 btn btn-primary" style="">上一章</a>
                    @endif
                    <h1 class="ub-text-center col-md-6">{{$record['number']}} {{$record['title']}}</h1>
                    @if ($record['next_chapter_id'] != null)
                        <a href="/comic/{{$record['comic_id']}}/chapter/{{$record['next_chapter_id']}}"  class="col-md-1 btn btn-primary" style="">下一章</a>
                    @endif
                </div>
                <div class="tw-p-10" style="text-align: center;">
                    @foreach($record['imgs'] as $img)
                    <img style="width:90%;" src="{{\ModStart\Core\Assets\AssetsUtil::fix($img['file_path'])}}" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection





