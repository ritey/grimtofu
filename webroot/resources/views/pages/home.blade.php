@extends('layouts.master')

@section('page_title')
Grimtofu
@endsection

@section('content')

<div class="container threads">

    <div class="row">

        <div class="col-sm-12 item">

            <h2>Grimtofu the latest free forum software</h2>

            <p class="lead">Use the menu to browse the forums or create a new discussion.</p>

            <p><strong>Forum version:</strong> Alpha</p>

            <p>This project development is ad funded, by following ads you're helping support the development of this project through a simple click.</p>

            <p>We appreciate feedback in the <a href="{{ route('channel',['channel' => 'feedback forum']) }}">feedback forum</a>.</p>

        </div>

    </div>

</div>

@endsection
