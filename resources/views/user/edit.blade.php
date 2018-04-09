@extends('layouts.app')


@section('content')


{!! Form::model($data, ['action' => 'UsersController@update']) !!}

        <user-form :routename="{{ json_encode(\Request::route()->getName()) }}" :user="{{ json_encode($data['user']) }}" :message="{{ json_encode($data['message']) }}" :messagetype="{{ json_encode($data['messagetype']) }}" :roles="{{ json_encode($data['roles']) }}"></user-form>

{!! Form::close() !!}

@endsection
    </div>

    
