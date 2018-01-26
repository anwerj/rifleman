@extends('layouts.master')

@section('title', 'Session')

@section('navs')
    <li>Session: <small>{{$session->id}}</small></li>
@endsection

@section('content')
    <div class="connnection-list row">
@foreach($connections as $index => $connection)
    <div class="col-md-{{intval(12/count($connections))}} connection" id="{{$connection->id}}">
        <div class="top">
            <div class="">{{$connection->path}}</div>
            <small class="text-success">sha1: {{$connection->id}}</small>
        </div>
        <form id="form_list" action="{{$pre['v']->route('session', 'list')}}" onsubmit="core.submit(event, this, 'list_content')">
            <div class="list_actions row">
                    <input type="hidden" name="session_id" value="{{$session->id}}">
                    <div class="col-md-8">
                        <input class="form-control" name="path" placeholder="path goes here">
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

</script>
<script type="text/javascript">
    $(document).ready(function ()
    {

        $('#form_list').submit();
    });
</script>
@endsection


