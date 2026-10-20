<!-- we are extending the app layout from layouts/app.blade.php -->
@extends('layouts.app')
@section('main')

<div class="row">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5><i class="bi bi-person"></i> Student Details</h5>

    <!--opens the Add modal-->
    <button type="button" class="btn btn-primary" id="createNewStudent">New Student</button>
</div>
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
            <!--edit button :-to open the edit modal-->
            <button type="button" class="btn btn-dark btn-sm editStudent" data-id="{{ $student->id }}">
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


<!-- "fade" = smooth animation, "tabindex=-1" = lets the Esc key close it -->
<div class="modal fade" id="ajaxModel" tabindex="-1" aria-hidden="true">
  <!-- The popup box. "modal-dialog-centered" puts it in the middle of the screen -->
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">


    <!-- The white content area inside the popup -->
      <div class="modal-header">
        <!-- Title is empty here. JavaScript fills it with "Add New Student" or "Edit Student" -->
        <h5 class="modal-title" id="modelHeading"></h5>
        <!-- The X button at the top right. data-bs-dismiss="modal" closes the popup -->
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!--MIDDLE PART OF THE POPUP (the form)-->
      <div class="modal-body">
        <!-- The form. enctype="multipart/form-data" is required to upload files (the image) -->
        <form id="studentForm" name="studentForm" enctype="multipart/form-data">
          <!-- hidden field ,if empty create student ,has number then edit student-->
          <input type="hidden" name="student_id" id="student_id">
          @csrf

          <!-- validation errors are listed here by JavaScript -->
          <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
          </div>

          <!-- form fields for name, image, and email -->
          <div class="mb-3">
            <label for="name" class="form-label">Student Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Your name">
          </div>

          <div class="mb-3">
            <label for="image" class="form-label">Student Image</label>
            <!-- current image, shown only when editing -->
            <div id="currentImageBox" class="mb-2" style="display:none">
              <img id="currentImage" class="img-thumbnail" style="max-width:150px;">
              <div class="form-text">Current image. Choose a new file only to replace it.</div>
            </div>
            <input type="file" class="form-control" id="image" name="image">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Student Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Your email">
          </div>

          <!-- The Save and Cancel buttons at the bottom of the form -->
          <button type="submit" class="btn btn-dark" id="saveBtn" value="create">Save Student</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Load the jQuery library -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $(function () {

    // Get the Bootstrap modal (popup) object for the element with id="ajaxModel"
    // getOrCreateInstance = reuse it if it exists, otherwise make a new one
    // We keep it in "modal" so we can call modal.show() and modal.hide() later
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('ajaxModel'));

    //Click to Create Button
    
    // When the "Add New Student" button is clicked
    $('#createNewStudent').click(function () {  // mark the save button as "create mode"
      $('#saveBtn').val("create-student");      // empty hidden id field = this is a NEW student
      $('#student_id').val('');                 // empty id = create
      $('#studentForm').trigger("reset");       // clear all the form inputs
      $('.print-error-msg').hide();             // hide any old validation error box
      $('#currentImageBox').hide();             // hide the "current image" preview (no image yet)
      $('#modelHeading').html("<i class='bi bi-plus-square-fill'></i> Add New Student"); // set the popup title
      modal.show();                             // open the popup
    });

   
    //Click to Edit Button

    // Listen for clicks on any ".editStudent" button
    // 'body' is used because table rows may be added later (event delegation)
    $('body').on('click', '.editStudent', function () {
      var student_id = $(this).data('id');       // read the student's id from the button's data-id attribute
     
     // Send a GET request to /students/{id}/edit to fetch that student's data
      $.get("{{ url('students') }}" + '/' + student_id + '/edit', function (data) {
          $('#studentForm').trigger("reset");     // clear the form first
          $('.print-error-msg').hide();           // hide old error messages
          $('#modelHeading').html("<i class='bi bi-pencil-square'></i> Edit Student"); // set popup title to Edit
          $('#saveBtn').val("edit-student");     // mark the save button as "edit mode"
          $('#student_id').val(data.id);        // put the id in the hidden field = this is an UPDATE
          $('#name').val(data.name);            // fill the name input with the student's name
          $('#email').val(data.email);         // fill the name input with the student's email

          // If the student has an image, show it in the "current image" box; otherwise hide that box
          if (data.image) {
              $('#currentImage').attr('src', "{{ asset('students') }}/" + data.image);
              $('#currentImageBox').show();
          } else {
              $('#currentImageBox').hide();
          }
          modal.show();
      });
    });

    //Create / Update Code
    // When the form is submitted (Save button clicked)
    $('#studentForm').submit(function (e) {
      e.preventDefault();         // stop the normal page reload form submit
      $('#saveBtn').html('Sending..'); // change button text so the user knows it is working


      // Send the form data to the server using AJAX (no page reload)
      $.ajax({
        data: new FormData(this),               // collect all form fields, including the image file
        processData: false,                     // don't convert FormData to a string (required for files)
        contentType: false,                     //let the browser set the correct content type (required for files)
        url: "{{ url('students/store') }}",     // the Laravel route that handles create AND update
        type: "POST",                           // use the POST method
        dataType: 'json',                       // expect JSON back from the server

      // If the server responds with success, close the modal
      //  and reload the page to show the new/updated student
        success: function (data) {
          modal.hide();
          window.location.reload();             
        },

        // If the server replies with an error
        error: function (xhr) {
          $('#saveBtn').html('Save Student');  // put the button text back to normal
          if (xhr.status === 422) {            // Laravel validation error -422
            printErrorMsg(xhr.responseJSON.errors); // show the validation errors in the red box
          } else {
            alert('Something went wrong. Please try again.');
          }
        }
      });
    });

    // "errors" looks like: { name: ["Name is required"], email: ["Email is invalid"] }
    function printErrorMsg(errors) {
      $('.print-error-msg').find('ul').html('');     // clear old error list items
      $('.print-error-msg').css('display', 'block'); // make the red error box visible
      $.each(errors, function (key, value) {

      // value[0] = the first error message for that field; add it as a list item
        $('.print-error-msg').find('ul').append('<li>' + value[0] + '</li>');
      });
    }

  });
</script>
@endsection