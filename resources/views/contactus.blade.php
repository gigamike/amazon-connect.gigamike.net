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
                <h2>Contact Us </h2>
            </div>
        </div>
    </div>

  <div class="card uper">
    <div class="card-body">
      @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
          </ul>
        </div><br />
      @endif
        <form method="post" action="{{ route('contactus.store') }}">
            <div class="mb-3">
                @csrf
                <label for="country_name">Phone Number:</label>
                <input type="text" class="form-control" name="phone"/>
            </div>
              <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
  </div>
</div>

@endsection