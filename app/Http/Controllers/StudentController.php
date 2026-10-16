<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        // fetch all the students from the database and paginate them 5 per page
        $students=Student::latest()->paginate(5);
        //display all the students in the index page
        return view('students.index', ['students' => $students]);
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request){
       // dd($request);
       // validate all the fields of the form
       $request->validate([
        'name' => 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000', // max size 10MB
        'email' => 'required|email',
       ]);

         // store the image in the public folder
         $imageName = time().'.'.$request->image->extension();// get the image extension
         $request->image->move(public_path('students'), $imageName); // move the image to the public folder

         // create a new student object and save the data to the database
         $student = new Student;
         $student ->image = $imageName;
         $student ->name = $request->name;
         $student ->email = $request->email;
         $student->save();
         return back() ->withSuccess('student added successfully'); // redirect back to the create page
    }

    public function show($id)
    {
        // Fetch student matching ID from the database
        $student = Student::where('id', $id)->first();
        // Load the 'students.show'
        return view('students.show', ['student' => $student]);
    }

    public function edit($id)
    {
        // Fetch student matching ID from the database
        $student = Student::where('id', $id)->first();
        // Load the 'students.edit'
        return view('students.edit', ['student' => $student]);
    }

    public function update(Request $request, $id)
    {
         $request->validate([
        'name' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10000', // max size 10MB
        'email' => 'required|email',
       
       ]);
       
        // Fetch student matching ID from the database
        $student = Student::where('id', $id)->first();

        // Check if a new image is uploaded
       if(isset($request->image)){
        
        // store the image in the public folder
        $imageName = time().'.'.$request->image->extension();// get the image extension
        $request->image->move(public_path('students'), $imageName); // move the image to the public folder
        $student->image=$imageName;
       }
        // Fetch student matching ID from the database
        // Update the student's information
        $student->name = $request->name;
        $student->email = $request->email;
        $student->save();
        // Redirect back to the index page with a success message
        return back()->withSuccess('Student updated successfully');
    }

    public function destroy($id)
    {
        // Fetch student matching ID from the database
        $student = Student::where('id', $id)->first();
        // Delete the student from the database
        $student->delete();
        // Redirect back to the index page with a success message
        return back()->withSuccess('Student deleted successfully');
    }
}