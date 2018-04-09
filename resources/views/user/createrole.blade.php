@extends('welcome')


@section('content')
    <!--  create users -->
 

    {!! Form::model($data, ['action' => 'UsersController@store']) !!}

        <user-role-form :role="{{ json_encode($data['role']) }}" :message="{{ json_encode($data['message']) }}" :messagetype="{{ json_encode($data['messagetype']) }}"></user-role-form>

    {!! Form::close() !!}

@endsection
