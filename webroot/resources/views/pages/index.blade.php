@extends('layouts.master')

@section('page_title')
All threads
@endsection

@section('content')

<div class="container threads">
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
                <p><a href="{{ route('channel', ['channel' => $item['label']]) }}">{{ $item['label'] }}</a> &middot; by <a href="{{ route('user', ['username' => $item['username'] ]) }}">{{ $item['username'] }}</a> &middot; {{ $item['updated_at'] }}</p>
                {!! $item['body_intro'] !!}
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
