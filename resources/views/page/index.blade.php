@extends('layouts.master')

@section('title', 'Home Page')

@section('navs')
    <li>Generate Session</li>
@endsection

@section('content')
    <div class="container-fluid mt-5">
        <form action="{{$v->route('session', 'connect')}}" method="post" class="alert alert-secondary" onsubmit="core.submit(event, this, 'connections')">
            <h3>Add Connections</h3>
            <small>sha1: {{$session['id']}}</small>
            <input type="hidden" name="session[id]" value="{{$session['id']}}">
            @foreach($session['connections'] as $index => $connection)
                <div class="form-group mt-3" id="{{$connection['id']}}">
                    <label >Connection {{$index+1}}</label>
                    <input class="form-control" name="con[{{$connection['id']}}][path]">
                    <div class="response"></div>
                </div>
            @endforeach
            <div class=" text-right">
                <button class="btn btn-success col-3">Connect</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script id="template_connections" type="text/template">
    {% if(success === false){ %}
        {% if(action === 'add_connector'){ %}

    <div class="alert alert-warning">Add file to server at path</div>
    <textarea class="file">{%- content %}</textarea>
        {% } else if(action === 'add_valid_path'){ %}

    <div class="alert text-sm-center alert-danger">Add validate path</div>

        {% } %}
    {% } else { %}

    <div class="alert alert-success">Success</div>

    {% } %}
</script>

<script type="text/javascript">
    $(document).ready(function ()
    {
        core.handles['connections'] = function (compiled, data, id)
        {
            console.log('#'+id+' .response', compiled);
            $('#'+id+' .response').html(compiled);
        };
    })
</script>
@endsection
