@extends('layouts.master')

@section('page_title')
All threads
@endsection

@section('content')

<div class="container">
    @foreach($vars['threads'] as $item)
    <div class="row">
        <div class="col-sm-10 offset-sm-1 item">

            <div class="col-sm-12">
                <h4><a href="{{ route('thread', ['channel' => strtolower($item['labels'][0]['name']), 'title' => strtolower(str_replace(' ','-',$item['title'])) . '::'.$item['number']]) }}">{{ str_replace('[question_mark]','?',$item['title']) }}</a></h4>
                <div class="alert-dismissible">
                    <div class="close">{{ $item['comments'] }}</div>
                </div>
                <p><a href="{{ route('channel', ['channel' => strtolower($item['labels'][0]['name'])]) }}">{{ $item['labels'][0]['name'] }}</a> &middot; <a href="">{{ $item['user']['login'] }}</a> &middot; {{ $item['created_at'] }}</p>
                <p>{{ $item['body'] }}</p>
            </div>

        </div>
    </div>
    @endforeach

    <div class="row">

        <div class="col-sm-12">

            <div class="justify-content-center">
                {!! $vars['threads']->links() !!}
            </div>

        </div>

    </div>
</div>

@endsection
