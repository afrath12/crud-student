<!-- we are extending the app layout from layouts/app.blade.php -->
@extends('layouts.app')
@section('main')

 <div class="row">
        <h5>
          <i class="bi bi-person"></i>
          Student Details
        </h5>
        <nav class="my-3">
          <ol class="breadcrumb">
            <!--we use breadcrumb to show the current page in the context of the website-->
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <!--active class is used to indicate the current page-->
            <li class="breadcrumb-item active">View Student</li>
          </ol>
        </nav>


        <div class="card-body">
            <h5 class="card-title">{{$student->name}}</h5>
          </div>
        <div class="card">
          <!--image selection-->
          <img
            src="/students/{{$student->image}}"
            class="card-img-top"
            alt="Student Image"
          />
          <div class="card-body">
            <h5 class="card-title">{{$student->email}}</h5>
          </div>
        </div>
      </div>

@endsection