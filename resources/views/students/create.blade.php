@extends('layouts.app')
@section('main')

<div class="row">
        <h5><i class="bi bi-plus-square-fill"></i> Add New Student</h5>
        <hr />

        <nav class="my-3">
          <ol class="breadcrumb">
            <!--we use breadcrumb to show the current page in the context of the website-->
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <!--active class is used to indicate the current page-->
            <li class="breadcrumb-item active">Add New Student</li>
          </ol>
        </nav>
        <div class="col-md-6">
          <form action="/students/store" method="post" enctype="multipart/form-data">
            @csrf
            

            <div class="row mb-3">
              <label for="name" class="form-label">Student Name</label>

              <input
                type="text"
                name="name"
                id="name"
                class="form-control
                @if($errors->has('name')) {{'is-invalid'}} @endif"
                placeholder="Enter Your name" value="{{old('name')}}"
              />
              @if($errors->has('name'))
             <div class="invalid-feedback">{{$errors->first("name")}}</div>@endif
            </div>

            <!--photo upload-->
            <div class="row mb-3">
              <label for="image" class="form-label">Student image</label>

              <input type="file" name="image" id="image" class="form-control
              @if($errors->has('image')) {{'is-invalid'}} @endif
              " />
               @if($errors->has('image'))
             <div class="invalid-feedback">{{$errors->first("image")}}</div>@endif
            </div>

            <div class="row mb-3">
              <label for="email" class="form-label">Student Email</label>

              <!--old function to keep previous values in the form -->
              <input
                type="email"
                name="email"
                id="email"
                class="form-control
                @if($errors->has('email')) {{'is-invalid'}} @endif
                "placeholder="Enter Your email" value="{{old('email')}}" 
              />
               @if($errors->has('email'))
             <div class="invalid-feedback">{{$errors->first("email")}}</div>@endif
            </div>

            <div class="mb-3 mt-3">
              <button type="submit" class="btn btn-dark">Save Student</button>
              <button type="reset" class="btn btn-danger">Clear All</button>
            </div>
          </form>
        </div>
      </div>

    @endsection