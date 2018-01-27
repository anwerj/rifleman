@extends('layouts.master')

@section('title', 'Session')

@section('navs')
    <li><i id="status_{{$session->id}}" class="status"></i> Session: <small>{{$session->id}}</small></li>
@endsection

@section('content')
    <div class="connnection-list row">
@foreach($connections as $index => $connection)
    <div class="col-md-{{intval(12/count($connections))}} connection" id="{{$connection->id}}">
        <div class="top">
            <div class=""> <i id="status_{{$connection->id}}" class="status"></i> {{$connection->path}}</div>
            <small class="text-success">sha1: {{$connection->id}}</small>
        </div>
        <form id="form_list_{{$connection->id}}" action="{{$pre['v']->route('session', 'list')}}" onsubmit="core.submit(event, this, 'list_content')">
            <div class="list_actions row">
                    <input type="hidden" name="session_id" value="{{$session->id}}">
                    <div class="col-md-8">
                        <input class="form-control" name="path" placeholder="path goes here" value="{{$connection->prefill}}">
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-success">Go</button>
                    </div>
            </div>
        </form>

        <div class="list_content">

        </div>
    </div>
@endforeach
    </div>
@endsection

@section('js')

<script id="template_list_content" type="text/template">
    {% for(var i in content){ entry=content[i]; %}
        <div class="entry {%=entry.is_dir ?'directory':'file' %}"
             data-basepath="{%=entry.base_path %}"
             data-connectionid="{%=connectionId %}"
             title="{%=entry.real_path %}">
            <div class="entry_name" onclick="core.handles.on_entry_click(this.parentNode)">
                <span class="" rel="link">{%=entry.name %}</span>
                <i>{%=entry.base_path %}</i>
            </div>
        </div>
    {% } %}
</script>

<script id="template_file_content" type="text/template">
    <pre class="prettyprint" contenteditable="true">{%-content.body.replace(/</g,'&lt;') %}</pre>
</script>

<script type="text/javascript">
    $(document).ready(function ()
    {
        var handleStatus = function (data, id)
        {
            if (data && (data.success || data.status == 'connected'))
            {
                $('#status_'+id).removeClass('disconnected').addClass('connected');
            }
            else
            {
                $('#status_'+id).removeClass('connected').addClass('disconnected');
            }
        }
        var handleList = function (connection, id)
        {
            $('#'+id+' .list_content').html(core.ejs('list_content')({
                content: connection.content,
                entry: null,
                connectionId: id,
            }));
        }
        var handleFile = function (connection, id)
        {
            $('#'+id+' .list_content').html(core.ejs('file_content')({
                content: connection.content,
                entry: null,
                connectionId: id,
            }));
            PR.prettyPrint();
        }
        core.onAjax.list_content = function ()
        {
            //
        }
        core.handles.list_content = function (data)
        {
            var id, content, connections = data.connections, session = data.session;
            handleStatus(session, session.id);
            for(id in connections)
            {
                handleStatus(connections[id], id);
                content = connections[id]['content'];
                if (connections[id].type === 'file')

                {
                    handleFile(connections[id], id);
                }
                else
                {
                    handleList(connections[id], id);
                }
            }
        }
        core.handles.on_entry_click = function (target)
        {
            var entry = $(target);
            var basePath = entry.attr('data-basepath');
            var connectionId = entry.attr('data-connectionid')
            // Handle Command+Click later
            $('#form_list_'+connectionId+' [name="path"]').val(basePath);
            $('#form_list_'+connectionId).submit();
        }
        $('#form_list_{{$connections[0]->id}}').submit();
    });
</script>
@endsection


