@extends('layouts.master')

@section('page_title')
{{ $vars['thread'][0]['labels'][0]['name'] }} | {{ $vars['thread'][0]['title'] }}
@endsection

@section('content')

<div>
    @foreach($vars['thread'] as $item)
    <div>
       <h2>{{ $item['title'] }}</h2>
       <p><a href="">{{ $item['user']['login'] }}</a> &middot; {{ $item['created_at'] }}</p>
       <p>{{ $item['body'] }}</p>
    </div>
    @endforeach
    @foreach($vars['comments'] as $item)
    <div>
       <p><a href="">{{ $item['user']['login'] }}</a> &middot; {{ $item['created_at'] }}</p>
       <p>{{ $item['body'] }}</p>
    </div>
    @endforeach

    <div>
        <form method="POST" action="{{ route('save.comment') }}" role="form" class="form-horizontal">
            <input type="hidden" name="clear" />
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            @if($vars['token'])
            <div class="form-group row">
                <label for="message" class="col-sm-2 control-label sr-only">Message</label>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <textarea autocomplete="false" id="message" name="message" class="form-control" rows="6">{{ old('message') }}</textarea>
                            @if ($errors->has('message'))
                                <span class="form-text error">
                                    <strong>{{ $errors->first('message') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-12 text-xs-right text-sm-right">
                    <button type="submit" class="btn btn-primary">Reply</button>
                </div>
            </div>
            @endif
        </form>
    </div>


    <div class="row">

        <div class="col-sm-12">

            <div class="justify-content-center">
                {!! $vars['comments']->links() !!}
            </div>

        </div>

    </div>


</div>

@endsection
