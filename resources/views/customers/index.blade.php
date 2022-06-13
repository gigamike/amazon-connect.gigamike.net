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
                <h2>Customer Overview </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('customers.create') }}"> Create New Customer</a>
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
            <td>First Name</td>
            <td>Last Name</td>
            <td>Email</td>
            <td>Mobile No.</td>
            <td width="2%">Edit</td>
            <td width="2%">Delete</td>
          </tr>
      </thead>
      <tbody>
          @foreach($users as $user)
          <tr>
              <td>{{$user->id}}</td>
              <td>{{$user->first_name}}</td>
              <td>{{$user->last_name}}</td>
              <td>{{$user->email}}</td>
              <td>{{$user->mobile_no}}</td>
              <td><a href="{{ route('customers.edit', $user->id)}}" class="btn btn-primary">Edit</a></td>
              <td>
                  <form action="{{ route('customers.destroy', $user->id)}}" method="post">
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