@extends('layouts.master')

@section('page_title')
{{ $vars['threads'][0]['label'] }}
@endsection

@section('content')

<div class="container">
    @foreach($vars['threads'] as $item)
    <div class="row">
        <div class="col-sm-1">
            <img class="avatar" src="{{ $item['avatar'] }}" alt="{{ $item['username'] }}" />
        </div>
        <div class="col-sm-10 item">
            <div class="col-sm-12">
                <h4><a href="{{ route('thread', ['channel' => $item['label'], 'title' => $item['slug']]) }}">{{ $item['clean_title'] }}</a></h4>
                <div class="alert-dismissible">
                    <div class="close">{{ $item['comments'] }}</div>
                </div>
                <p><a href="{{ route('channel', ['channel' => $item['label']]) }}">{{ $item['label'] }}</a> &middot; By <a href="">{{ $item['username'] }}</a> &middot; {{ $item['created_at'] }}</p>
                <p>{{ $item['body'] }}</p>
            </div>

        </div>
    </div>
    @endforeach
    @if(!count($vars['threads']))
    <div class="row">
        <div class="col-sm-10 offset-sm-1 item">
            <div class="col-sm-12">
                <p>No threads yet, create a new discussion</p>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
