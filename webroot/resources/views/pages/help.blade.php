@extends('layouts.master')

@section('page_title')
Grimtofu
@endsection

@section('content')

<div class="container threads">

    <div class="row">

        <div class="col-sm-12 item">

        	{!! $vars['content'] !!}

        </div>

    </div>

</div>

@endsection
