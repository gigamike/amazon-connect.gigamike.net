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
                <h2>Add User </h2>
            </div>
            <div class="pull-right">
                <a href="{{ route('users.index') }}"> User Overview</a>
            </div>
        </div>
    </div>

  <div class="card uper">
    <div class="card-header">
      Add User
    </div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
          </ul>
        </div><br />
      @endif
        <form method="post" action="{{ route('users.store') }}">
            <div class="mb-3">
                @csrf
                <label for="country_name">Name:</label>
                <input type="text" class="form-control" name="name"/>
            </div>
            <div class="mb-3">
                <label for="cases">Email :</label>
                <input type="email" class="form-control" name="email"/>
            </div>
            <div class="mb-3">
                <label for="cases">Pasword :</label>
                <input type="password" class="form-control" name="password"/>
            </div>
              <button type="submit" class="btn btn-primary">Add User</button>
            
        </form>
    </div>
  </div>
</div>

@endsection