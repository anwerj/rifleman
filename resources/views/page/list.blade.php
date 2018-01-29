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
            <small class="text-warning">sha1: {{$connection->id}}</small>
        </div>
        <div class="list_actions row" data-spy="affix" data-offset-top="60" data-offset-bottom="200">
            <div class="col-md-7">
                <form class="m-0 form_list" id="form_list_{{$connection->id}}" action="{{$pre['v']->route('session', 'list')}}" onsubmit="core.submit(event, this, 'list_content')">
                    <input type="hidden" name="session_id" value="{{$session->id}}">
                    <input class="form-control input_path" name="path" placeholder="path goes here" value="{{$connection->prefill}}">
                </form>
            </div>
            <div class="col-md-5 text-right">
                <div class="btn-group btn_emoji">
                    <button class="btn btn_sub_back" onclick="core.custom(event, '{{$connection->id}}:back', 'sub_click')">🔙</button>
                    <button class="btn btn_sub_up" onclick="core.custom(event, '{{$connection->id}}:up', 'sub_click')">🔝</button>
                    <button class="btn btn_sub_go" onclick="core.custom(event, '{{$connection->id}}:go', 'sub_click')">✔️</button>
                </div>
            </div>
        </div>
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
        <div class="entry_name" onclick="core.custom(event, this.parentNode, 'entry_click')">
            <span class="" rel="link">{%=entry.name %}</span>
            <i>{%=entry.base_path %}</i>
        </div>
    </div>
    {% } %}
</script>

<script id="template_line_detail" type="text/template">{%= entry %}<br></script>
<script id="template_line_info" type="text/template">{%-itemIndex %}<br></script>


<script src="/js/rdiff.js" type="text/javascript"></script>
<script type="text/javascript">
    core.fn.handleStatus = function (data, id)
    {
        if (data && (data.success || data.status == 'connected'))
        {
            $('#status_'+id).removeClass('disconnected').addClass('connected');
        }
        else
        {
            $('#status_'+id).removeClass('connected').addClass('disconnected');
        }
    };

    core.fn.handleSubmit = function (connection, path)
    {
        if (path !== undefined)
        {
            $('#form_list_'+connection+' .input_path').val(path);
        }
        $('#form_list_'+connection).submit();
    }

    core.onAjax.list_content = function (event, target)
    {
        var path;
        return {
            pre: function ()
            {
                console.log(target);
                $('.form_list').removeClass('active');
                $(target).addClass('active');

            }
        }
    }

    core.handles.list_content = function (data)
    {
        var id, content, connections = data.connections, session = data.session, type;
        core.fn.handleStatus(session, session.id);
        for(id in connections)
        {
            core.fn.handleStatus(connections[id], id);
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
    };

    core.onCustom.entry_click = function (event, target)
    {
        var entry = $(target);
        var basePath = entry.attr('data-basepath');
        var connectionId = entry.attr('data-connectionid')
        // Handle Command+Click later
        core.fn.handleSubmit(connectionId, basePath);
    };

    core.onCustom.sub_click = function (event, target)
    {
        var splitted = target.split(':');
        var connection = splitted[0];
        var path = ($('#'+connection+' .input_path').val() || '').split('/');

        switch (splitted[1])
        {
            case 'back':
                path = path.slice(0,-1);
                break;
            case 'up':
                path = path.slice(0,-2);
                break
        }
        core.fn.handleSubmit(connection, path.join('/'));
    }

    core.onInit.init = function ()
    {
        core.fn.handleSubmit('{{$connections[0]->id}}');
    }
</script>
@endsection


