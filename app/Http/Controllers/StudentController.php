<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $data = Student::latest()->paginate(5);
        return view('students.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('students.create');
    }

    public function store(Request $request): JsonResponse
{
    // student_id is empty when creating, filled when editing
    $isEdit = $request->filled('student_id');

    $this->validate($request, [
        'name'  => 'required',
        'email' => 'required|email',
        // image is required when creating, optional when editing
        'image' => ($isEdit ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif|max:10000',
    ]);

    $input = $request->only(['name', 'email']);

    // only touch the image when a new file was chosen
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('students'), $imageName);
        $input['image'] = $imageName;
    }

    // id found -> update that student, otherwise -> create a new one
    Student::updateOrCreate(['id' => $request->student_id], $input);

    session()->flash('success', $isEdit ? 'Student updated successfully' : 'Student created successfully');
    return response()->json(['success' => true]);
}

    public function show($id): View
    {
        $student = Student::find($id);
        return view('students.show', compact('student'));
    }

    public function edit($id): JsonResponse
    {
    return response()->json(Student::findOrFail($id));
    }

    public function destroy($id): RedirectResponse
    {
        Student::find($id)->delete();
        return redirect()->route('students.index')
                        ->with('success', 'Student deleted successfully');
    }
}