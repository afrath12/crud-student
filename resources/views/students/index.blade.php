<!-- we are extending the app layout from layouts/app.blade.php -->
@extends('layouts.app')
@section('main')

<div class="row">
        <!--justification and alignment-->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <!--icon for person-->
          <h5><i class="bi bi-person"></i> Student Details</h5>
          <a href="{{ url('students/create') }}" class="btn btn-primary">New Student</a>
        </div>

        <!--success message after create / update / delete-->
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
          <p class="mb-0">{{ $message }}</p>
        </div>
        @endif

        <div class="col-md-12 table-responsive mt-3">
          <!--table responsive-->
          <table class="table table-bordered">
            <!--table-bordered-->
            <thead>
              <tr>
                <th>No</th>
                <th>Student Name</th>
                <th>Student Image</th>
                <th>Student Email</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>

            @forelse ($data as $student)
              <tr>
                <td>{{ ++$i }}</td>
                <td><a href="{{ url('students/'.$student->id.'/show') }}">{{$student->name}}</a></td>
                <td><img src="{{ asset('students/'.$student->image) }}" style="width:100px; height:100px; object-fit:contain"/></td>
                <td>{{$student->email}}</td>

                <td>
                  <!--edit button pencil,small,dark-->
                  <!--link to edit page-->
                  <a href="{{ url('students/'.$student->id.'/edit') }}" class="btn btn-dark btn-sm">
                      <i class="bi bi-pencil-square"></i>
                  </a>

                  <!--delete button trash,small,red-->
                  <a href="{{ url('students/'.$student->id.'/delete') }}" 
                     onclick="return confirm('Are you sure you want to delete this student?');" 
                     class="btn btn-danger btn-sm">
                      <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">No students found</td>
              </tr>
            @endforelse
            </tbody>
          </table>
          <!--pagination links-->
          {{ $data->links() }}
        </div>
      </div>
@endsection