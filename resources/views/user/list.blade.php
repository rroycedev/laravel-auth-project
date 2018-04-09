@extends('layouts.app')
   
@section('content')

    {!! Form::model($users, ['action' => 'UsersController@create']) !!}

   
        <user-list :routename="{{ json_encode(\Request::route()->getName()) }}" :users="{{ json_encode($users) }}"></user-list>

    {!! Form::close() !!}
    
    <modal-box id="deleteUserModal" title="Delete User">
        </modal-box>


@endsection

@section('js-content')


@endsection
