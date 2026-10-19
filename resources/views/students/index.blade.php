<!-- we are extending the app layout from layouts/app.blade.php -->
@extends('layouts.app')
@section('main')

<div class="row">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5><i class="bi bi-person"></i> Student Details</h5>

    <!--opens the Add modal-->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
      New Student
    </button>
  </div>

  <!--success message after create / update / delete-->
  @if ($message = Session::get('success'))
  <div class="alert alert-success">
    <p class="mb-0">{{ $message }}</p>
  </div>
  @endif


   <!-- table wrapper: table-responsive makes it scroll sideways on small screens -->
  <div class="col-md-12 table-responsive mt-3">
    <table class="table table-bordered">
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

      <!--loop through the student data to display them in the table-->
        @forelse ($data as $student)
        <tr>
          <td>{{ ++$i }}</td> <!-- row number, $i comes from the controller so numbering continues across pages -->
          <td>{{ $student->name }}</td>
          <td><img src="{{ asset('students/'.$student->image) }}" style="width:100px; height:100px; object-fit:contain"/></td>
          <td>{{ $student->email }}</td>
          <td>
            <!--edit button opens that student's own modal-->
            <button type="button" class="btn btn-dark btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#editStudentModal{{ $student->id }}">
              <i class="bi bi-pencil-square"></i>
            </button>

            <!--delete button with confirmation-->
            <a href="{{ url('students/'.$student->id.'/delete') }}"
               onclick="return confirm('Are you sure you want to delete this student?');"
               class="btn btn-danger btn-sm">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>

        <!-- shown when there are no students in the database -->
        @empty
        <tr>
          <td colspan="5" class="text-center">No students found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
    <!-- pagination links  for the table -->
    {{ $data->links() }}
  </div>
</div>


<!-- ADD MODAL -->
 <!-- true only when the last submitted form was the Add form (so we show its errors/old values here) -->
@php $addFailed = old('_form') === 'add'; @endphp
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- enctype="multipart/form-data" is required to upload the image file -->
      <form action="/students/store" method="post" enctype="multipart/form-data">
        <!-- CSRF token for security -->
        @csrf
        <input type="hidden" name="_form" value="add">
        <!-- tells us which form was submitted, used when validation fails -->

        <div class="modal-header">
          <h5 class="modal-title">Add New Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Student Name</label>
            <input type="text" name="name" placeholder="Enter Your name"
                   class="form-control @if($addFailed && $errors->has('name')) is-invalid @endif"
                   value="{{ $addFailed ? old('name') : '' }}">
            @if($addFailed && $errors->has('name'))
              <div class="invalid-feedback">{{ $errors->first('name') }}</div>
            @endif
          </div>

          <!-- image field (file inputs cannot be refilled, so no old value here) -->
          <div class="mb-3">
            <label class="form-label">Student image</label>
            <input type="file" name="image"
                   class="form-control @if($addFailed && $errors->has('image')) is-invalid @endif">
            @if($addFailed && $errors->has('image'))
              <div class="invalid-feedback">{{ $errors->first('image') }}</div>
            @endif
          </div>

          <div class="mb-3">
            <label class="form-label">Student Email</label>
            <input type="email" name="email" placeholder="Enter Your email"
                   class="form-control @if($addFailed && $errors->has('email')) is-invalid @endif"
                   value="{{ $addFailed ? old('email') : '' }}">
            @if($addFailed && $errors->has('email'))
              <div class="invalid-feedback">{{ $errors->first('email') }}</div>
            @endif
          </div>
        </div>

        <!-- modal footer: save and cancel buttons -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-dark">Save Student</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>


<!-- EDIT MODALS (one per student, already pre-filled) -->
@foreach ($data as $student)
@php $editFailed = old('_form') === 'edit' && old('_student_id') == $student->id; @endphp
<div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

    <!-- form posts to this student's update route -->
      <form action="/students/{{ $student->id }}/update" method="post" enctype="multipart/form-data">
        @csrf
        <!-- HTML forms only support GET/POST, so we spoof PUT for the update route -->
        @method('PUT')
        <!-- identifies this as the edit form and which student, used when validation fails -->
        <input type="hidden" name="_form" value="edit">
        <input type="hidden" name="_student_id" value="{{ $student->id }}">

         <!-- modal body: pre-filled form fields -->
        <div class="modal-header">
          <h5 class="modal-title">Edit Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <!-- name field: shows old input if validation failed, otherwise the saved name -->
            <label class="form-label">Student Name</label>
            <input type="text" name="name"
                   class="form-control @if($editFailed && $errors->has('name')) is-invalid @endif"
                   value="{{ $editFailed ? old('name') : $student->name }}">
            @if($editFailed && $errors->has('name'))
              <div class="invalid-feedback">{{ $errors->first('name') }}</div>
            @endif
          </div>

          <div class="mb-3">
            <!-- show the current image if the student has one -->
            <label class="form-label">Student Image</label>
            @if($student->image)
              <div class="mb-2">
                <img src="{{ asset('students/'.$student->image) }}" class="img-thumbnail" style="max-width:150px;">
                <div class="form-text">Current image. Choose a new file only to replace it.</div>
              </div>
            @endif
            <input type="file" name="image"
                   class="form-control @if($editFailed && $errors->has('image')) is-invalid @endif">
            @if($editFailed && $errors->has('image'))
              <div class="invalid-feedback">{{ $errors->first('image') }}</div>
            @endif
          </div>

          <div class="mb-3">
            <label class="form-label">Student Email</label>
            <input type="email" name="email"
                   class="form-control @if($editFailed && $errors->has('email')) is-invalid @endif"
                   value="{{ $editFailed ? old('email') : $student->email }}">
            @if($editFailed && $errors->has('email'))
              <div class="invalid-feedback">{{ $errors->first('email') }}</div>
            @endif
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-dark">Update Student</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>
@endforeach


<!-- Reopen the correct modal when validation fails -->
 <!-- after a failed validation the page reloads and modals are closed, so we reopen the right one -->
@if ($errors->any())
<script>
  // wait until the page is fully loaded so the modal elements exist
  document.addEventListener('DOMContentLoaded', function () {
    // values sent back by the hidden inputs (_form and _student_id)
    const formType  = @json(old('_form'));
    const studentId = @json(old('_student_id'));

     // pick the edit modal of that student, otherwise fall back to the add modal
    const modalId = (formType === 'edit' && studentId)
        ? 'editStudentModal' + studentId
        : 'addStudentModal';

    // create the Bootstrap modal and show it
    new bootstrap.Modal(document.getElementById(modalId)).show();
  });
</script>
@endif

@endsection