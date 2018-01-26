@extends('layouts.master')

@section('title', 'Home Page')

@section('navs')
    <li>Generate Session</li>
@endsection

@section('content')
    <div class="container-fluid mt-5">
        <form action="{{$v->route('session', 'connect')}}" method="post" class="alert alert-secondary" onsubmit="core.submit(event, this, 'connector')">
            <h3>Add Connections</h3>
            <small>sha1: {{$session['id']}}</small>
            <input type="hidden" name="session[id]" value="{{$session['id']}}">
            @foreach($session['connections'] as $index => $connection)
                <div class="form-group mt-3" id="{{$connection['id']}}">
                    <label >Connection {{$index+1}}</label>
                    <input class="form-control" name="con[{{$connection['id']}}][path]">
                    <small class="">
                        Refresh Secret
                        <input class="" name="con[{{$connection['id']}}][refresh_secret]" type="checkbox">
                    </small>
                    <div class="response"></div>
                </div>
            @endforeach

            <div class=" text-right">
                <button class="btn btn-success col-3">Connect</button>
            </div>
        </form>
    </div>

    <div class="container-fluid mt-5">
        <pre>

        </pre>
    </div>
@endsection

@section('js')
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>

<script id="template_connection" type="text/template">
    {% if(connection.status === 'connected'){ %}
    <div class="alert alert-success">Success</div>
    {% } else { %}
    <div class="alert alert-warning">Add file to server at path : <code class="">_rifle.config.php</code></div>
<pre class="file prettyprint lang-php">
&lt;?php
$session = [
    'id'         => '{%=session.id %}}',
    'secret'     => '{%=session.secret %}}',
    'connection' => [
        'id'     => '{%=connection.id %}}',
        'secret' => '{%=connection.secret %}',
    ]
];
</pre>
    {% } %}
</script>

<script type="text/javascript">
    $(document).ready(function ()
    {
        core.handles['connector'] = function (data, id)
        {
            var id, connections = data.connections, session = data.session;
            for(var i in connections)
            {
                console.log('#'+connections[i].id+' .response');
                $('#'+connections[i].id+' .response').html(core.ejs('connection')({connection : connections[i], session: session}));
            }
            PR.prettyPrint();
        };
    })
</script>
@endsection
