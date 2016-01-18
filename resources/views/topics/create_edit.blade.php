@extends('layouts.default')

@section('title')
    {{ lang('Create New Topic') }} - @parent
@stop

@section('content')

    <div class="topic_create">

        <div class="col-md-8 main-col">

            <div class="reply-box form box-block">

                <div class="alert alert-warning">
                    {!! lang('be_nice') !!}
                </div>

                @include('layouts.partials.errors')

                @if (isset($topic))
                    {!! Form::model($topic, ['route' => ['topics.update', $topic->id], 'id' => 'topic-create-form', 'method' => 'patch']) !!}
                @else
                    {!! Form::open(['route' => 'topics.store','id' => 'topic-create-form', 'method' => 'post']) !!}
                @endif

                <div class="form-group">
                    <select class="selectpicker form-control" name="node_id">

                        <option value=""
                                disabled {{ app('App\Topic')->present()->haveDefaultNode($node, null) ?: 'selected' }}>{{ lang('Pick a node') }}</option>

                        @foreach ($nodes['top'] as $top_node)
                            <optgroup label="{{{ $top_node->name }}}">
                                @foreach ($nodes['second'][$top_node->id] as $snode)
                                    <option value="{{ $snode->id }}" {{ app('App\Topic')->present()->haveDefaultNode($node, $snode) ? 'selected' : '' }} >{{ $snode->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'topic-title', 'placeholder' => lang('Please write down a topic')]) !!}
                </div>

                @include('topics.partials.composing_help_block')

                <div class="form-group">
                    {!! Form::textarea('body', null, ['class' => 'form-control',
                                                      'rows' => 20,
                                                      'style' => "overflow:hidden",
                                                      'id' => 'reply_content',
                                                      'placeholder' => lang('Please using markdown.')]) !!}
                </div>

                <div class="form-group status-post-submit">
                    {!! Form::submit(lang('Publish'), ['class' => 'btn btn-primary', 'id' => 'topic-create-submit']) !!}
                </div>

                <div class="box preview markdown-body" id="preview-box"
                     style="display:none;"></div>

                {!! Form::close() !!}

            </div>
        </div>

        <div class="col-md-4 side-bar">

            @if ( $node )

                <div class="panel panel-default corner-radius help-box">
                    <div class="panel-heading text-center">
                        <h3 class="panel-title">{!! lang('Current Node') !!} : {{ $node->name }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ $node->description }}
                    </div>
                </div>

            @endif

            <div class="panel panel-default corner-radius help-box">
                <div class="panel-heading text-center">
                    <h3 class="panel-title">{{ lang('This kind of topic is not allowed.') }}</h3>
                </div>
                <div class="panel-body">
                    <ul class="list">
                        <li>list 1</li>
                        <li>list 2</li>
                </div>
            </div>

            <div class="panel panel-default corner-radius help-box">
                <div class="panel-heading text-center">
                    <h3 class="panel-title">{{ lang('We can benefit from it.') }}</h3>
                </div>
                <div class="panel-body">
                    <ul class="list">
                        <li>list 1</li>
                        <li>list 2</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

@stop
