@extends('layouts.app')

@section('content')
<style>
  .uper {
    margin-top: 40px;
  }
</style>

<div class="container">
   <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Users Overview </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('users.create') }}"> Create New User</a>
            </div>
        </div>
    </div>

  <div class="uper">
    @if(session()->get('success'))
      <div class="alert alert-success">
        {{ session()->get('success') }}  
      </div><br />
    @endif
    <table class="table table-striped">
      <thead>
          <tr>
            <td>ID</td>
            <td>Name</td>
            <td>Email</td>
            <td>Role</td>
            <td width="2%">Edit</td>
            <td width="2%">Delete</td>
          </tr>
      </thead>
      <tbody>
          @foreach($users as $user)
          <tr>
              <td>{{$user->id}}</td>
              <td>{{$user->name}}</td>
              <td>{{$user->email}}</td>
              <td>{{$user->role}}</td>
              <td><a href="{{ route('users.edit', $user->id)}}" class="btn btn-primary">Edit</a></td>
              <td>
                  <form action="{{ route('users.destroy', $user->id)}}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Delete</button>
                  </form>
              </td>
          </tr>
          @endforeach
      </tbody>
    </table>
  <div>
</div>

@endsection