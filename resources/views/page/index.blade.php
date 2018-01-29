@extends('layouts.master')

@section('title', 'Home Page')

@section('navs')
    <li>Generate Session</li>
@endsection

@section('content')
    <div class="container mt-5">
        <form action="{{$pre['v']->route('session', 'connect')}}" method="post" class="alert alert-secondary" onsubmit="core.submit(event, this, 'connector')">
            <h3>Add Connections</h3>
            <small>sha1: {{$session->id}}</small>
            <input type="hidden" name="session[id]" value="{{$session->id}}">
            @foreach($connections as $index => $connection)
                <div class="form-group mt-3" id="{{$connection->id}}">
                    <label >Connection {{$index+1}}</label>
                    <input class="form-control" name="con[{{$connection->id}}][path]" value="{{$connection->path}}">
                    <small class="">
                        Refresh Secret
                        <input class="" name="con[{{$connection->id}}][refresh_secret]" type="checkbox">
                    </small>
                    <div class="parsed_content"></div>
                </div>
            @endforeach

            <div id="action_sessions" class="text-right" ></div>
        </form>
    </div>

    <div class="container mt-5">
        <pre>

        </pre>
    </div>
@endsection

@section('js')
<script id="template_connection" type="text/template">
    {% if(connection.status === 'connected'){ %}
    <div class="alert alert-success">Success</div>
    {% } else { %}
    <div class="alert alert-warning">Error in connecting : <span class="code">{%=connection.error %}</span></div>
    <pre class="file file_line prettyprint">$dns = "{%=btoa(connection.id+':'+connection.secret+':'+'..')%}";</pre>
    {% } %}
</script>

<script id="template_action_sessions" type="text/template">
    {% if(status === 'connected'){ %}
    <a class="btn btn-success col-3" href="{{ $pre['v']->route('page', 'list')}}&session_id={%=id %}">Continue</a>
    {% } else { %}
    <button class="btn btn-info col-3">Check</button>
    {% } %}
</script>

<script type="text/javascript">
    $(document).ready(function ()
    {
        $('#action_sessions').html(core.ejs('action_sessions')({status:'idle'}));
        core.handles['connector'] = function (data, id)
        {
            var id, connections = data.connections, session = data.session;
            for(var i in connections)
            {
                console.log('#'+connections[i].id+' .parsed_content');
                $('#'+connections[i].id+' .parsed_content').html(core.ejs('connection')({connection : connections[i], session: session}));
            }
            PR.prettyPrint();
            $('#action_sessions').html(core.ejs('action_sessions')(session));
        };
        core.onAjax['connector'] = function ()
        {
            return {
                pre : function ()
                {
                    $('#action_sessions .btn').text('Checking...');
                }
            }
        }
    })
</script>
@endsection
