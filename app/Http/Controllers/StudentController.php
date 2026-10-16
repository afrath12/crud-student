<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000', // max size 10MB
            'email' => 'required|email',
        ]);

        $input = $request->all();

        // store the image in the public folder
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('students'), $imageName);
        $input['image'] = $imageName;

        Student::create($input);

        return redirect()->route('students.index')
                        ->with('success', 'Student created successfully');
    }

    public function show($id): View
    {
        $student = Student::find($id);
        return view('students.show', compact('student'));
    }

    public function edit($id): View
    {
        $student = Student::find($id);
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10000', // max size 10MB
            'email' => 'required|email',
        ]);

        $input = $request->all();

        if ($request->hasFile('image')) {
            // a new image was uploaded, store it in the public folder
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('students'), $imageName);
            $input['image'] = $imageName;
        } else {
            // no new image, keep the old one
            $input = Arr::except($input, ['image']);
        }

        $student = Student::find($id);
        $student->update($input);

        return redirect()->route('students.index')
                        ->with('success', 'Student updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Student::find($id)->delete();
        return redirect()->route('students.index')
                        ->with('success', 'Student deleted successfully');
    }
}