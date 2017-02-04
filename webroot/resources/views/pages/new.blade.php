@extends('layouts.master')

@section('content')

<div class="container">

    <div class="row">
        <div class="col-sm-10 offset-sm-1 item">

            <div class="col-sm-12">

                <form method="POST" action="{{ route('new') }}" role="form" class="form-horizontal">
                    <input type="hidden" name="clear" />
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    @if ($vars['error_message'])
                        <span class="block">{!! $vars['error_message'] !!}</span>
                        <p class="col-md-12"></p>
                    @endif

                    <div class="form-group row">
                        <label for="title" class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" id="title" autocomplete="false" name="title" class="form-control" value="{{ old('title') }}" />
                            @if ($errors->has('title'))
                                <span class="form-text pad2 error">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="category" class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-8">
                                    <select name="category" id="category" class="form-control">
                                        <option value="all">All</option>
                                        @foreach($vars['categories'] as $item)
                                            @if ($vars['request']->old('category') == $item['name'] || $vars['channel'] == strtolower($item['name']))
                                                <option value="{{ $item['name'] }}" selected>{{ $item['name'] }}</option>
                                            @else
                                                <option value="{{ $item['name'] }}">{{ $item['name'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="message" class="col-sm-2 control-label">Message</label>
                        <div class="col-sm-10">
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
                            <button type="submit" class="btn btn-primary">Publish</button>
                        </div>
                    </div>

                </form>

            </div>

        </div>
    </div>

</div>

@endsection
