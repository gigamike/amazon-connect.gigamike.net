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
                <h2>Edit Customer </h2>
            </div>
            <div class="pull-right">
                <a href="{{ route('customers.index') }}"> Customers Overview</a>
            </div>
        </div>
    </div>

  <div class="card uper">
    <div class="card-header">
      Edit Customer
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
        <form method="post" action="{{ route('customers.update', $customer->id ) }}">
            <div class="mb-3">
                @csrf
                @method('PATCH')
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" name="first_name" value="{{ $customer->first_name }}"/>
            </div>
            <div class="mb-3">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" name="last_name" value="{{ $customer->last_name }}"/>
            </div>
            <div class="mb-3">
                <label for="last_name">Email:</label>
                <input type="email" class="form-control" name="email" value="{{ $customer->email }}"/>
            </div>
            <div class="mb-3">
                <label for="mobile_no">Mobile No.:</label>
                <input type="text" class="form-control" name="mobile_no" value="{{ $customer->mobile_no }}"/>
            </div>
            <div class="mb-3">
                <label for="address">Address:</label>
                <textarea class="form-control" name="address">{{ $customer->address }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Data</button>
        </form>
    </div>
  </div>
</div>  

@endsection