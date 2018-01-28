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
        <form class="m-0" id="form_list_{{$connection->id}}" action="{{$pre['v']->route('session', 'list')}}" onsubmit="core.submit(event, this, 'list_content')">
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

        <div class="parsed_content"></div>
    </div>
@endforeach
    </div>
@endsection

@section('js')

<script id="template_list_content" type="text/template">
    {% if(entry._missing){ %}
    <div class="entry missing">
        <div class="entry_name">
            <span class="" rel="link">{%=entry.name %}</span>
        </div>
    </div>
    {% } else { %}
    <div class="entry {%=entry.is_dir ?'directory':'file' %}"
         data-basepath="{%=entry.base_path %}"
         data-connectionid="{%=entryIndex %}"
         title="{%=entry.real_path %}">
        <div class="entry_name" onclick="core.handles.on_entry_click(this.parentNode)">
            <span class="" rel="link">{%=entry.name %}</span>
            <i>{%=entry.base_path %}</i>
        </div>
    </div>
    {% } %}
</script>

<script id="template_line_detail" type="text/template">
    <div>{%= entry %}</div>
</script>
<script id="template_line_info" type="text/template">
    <div>{%-itemIndex %}</div>
</script>


<script src="/js/rdiff.js" type="text/javascript"></script>
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
            var id, content, connections = data.connections, session = data.session, type;
            handleStatus(session, session.id);
            for(id in connections)
            {
                handleStatus(connections[id], id);
                content = connections[id].content;
                type = type || connections[id].type;
                if (type !== connections[id].type)
                {
                    // Handle diff types
                }
            }
            if (type === 'file')
            {
                if (connections.length > 2)
                {
                    // Can't generate diff for than two files
                }
                rdiff({
                    preferred : '{{$connections[0]->id}}',
                    compilers : {ld:core.ejs('line_detail'), li:core.ejs('line_info')}
                }).handleFile(connections);
                PR.prettyPrint();
            }
            else
            {
                rdiff({
                    preferred : '{{$connections[0]->id}}',
                    compilers : {list:core.ejs('list_content')}
                }).handleList(connections);
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


