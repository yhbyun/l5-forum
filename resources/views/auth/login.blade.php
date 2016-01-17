@extends('layouts.default')

@section('title')
    {{ lang('Login') }} @parent
@stop

@section('content')

    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a href="/auth/github" class="btn btn-success btn-lg">
                    {{ lang('Signin with Gitbuh') }}<i class="glyphicon glyphicon-pencil"> </i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-default corner-radius">

            <div class="panel-heading text-center">
                <h3 class="panel-title"></h3>
            </div>

            <div class="panel-body text-center">
            </div>
        </div>

@stop
