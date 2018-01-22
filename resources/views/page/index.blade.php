@extends('layouts.master')

@section('title', 'Home Page')

@section('navs')
    <li>Generate Session</li>
@endsection

@section('content')
    <div class="container-fluid mt-5">
        <form action="{{$v->route('session')}}" method="post" class="alert alert-secondary">
            <h3>Add Connections</h3>
            <div class="form-group mt-3">
                <label >Connection 1</label>
                <input class="form-control" name="con[][path]">
            </div>
            <div class="form-group mt-3">
                <label >Connection 2</label>
                <input class="form-control" name="con[][path]">
            </div>
            <div class=" text-right">
                <button class="btn btn-success col-3">Connect</button>
            </div>
        </form>
    </div>
@endsection

