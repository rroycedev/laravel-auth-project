@extends('welcome')


@section('content')
    <!--  create users -->
 

    {!! Form::model($data, ['action' => 'UsersController@store']) !!}

        <user-form :username="{{ json_encode("") }}" :message="{{ json_encode($data['message']) }}" :messagetype="{{ json_encode($data['messagetype']) }}" :roles="{{ json_encode($data['roles']) }}"></user-form>

    {!! Form::close() !!}

@endsection
