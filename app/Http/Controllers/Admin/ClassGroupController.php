<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassGroup::with('mentor')
            ->withCount('participants');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $classes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        // Get mentors who don't have a class assigned yet
        $availableMentors = User::where('role', 'mentor')
            ->whereDoesntHave('mentorOfClass')
            ->get();
            
        return view('admin.classes.create', compact('availableMentors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:class_groups'],
            'description' => ['nullable', 'string'],
            'mentor_id' => ['nullable', 'exists:users,id'],
        ]);

        $classGroup = ClassGroup::create([
            'name' => $request->name,
            'code' => Str::upper($request->code),
            'description' => $request->description,
            'mentor_id' => $request->mentor_id,
        ]);

        ActivityLog::log('Created class group: ' . $classGroup->name, auth()->id(), [
            'class_group_id' => $classGroup->id,
            'mentor_id' => $request->mentor_id,
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class group created successfully.');
    }

    public function show(ClassGroup $class)
    {
        $class->load(['mentor', 'participants']);
        
        // Get available participants (not assigned to any class)
        $availableParticipants = User::where('role', 'participant')
            ->whereNull('class_group_id')
            ->get();

        // Get available mentors (not assigned to any class)
        $availableMentors = User::where('role', 'mentor')
            ->whereDoesntHave('mentorOfClass')
            ->get();

        return view('admin.classes.show', compact('class', 'availableParticipants', 'availableMentors'));
    }

    public function edit(ClassGroup $class)
    {
        // Get available mentors (not assigned OR assigned to this class)
        $availableMentors = User::where('role', 'mentor')
            ->where(function($q) use ($class) {
                $q->whereDoesntHave('mentorOfClass')
                  ->orWhere('id', $class->mentor_id);
            })
            ->get();
            
        return view('admin.classes.edit', compact('class', 'availableMentors'));
    }

    public function update(Request $request, ClassGroup $class)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:class_groups,code,' . $class->id],
            'description' => ['nullable', 'string'],
            'mentor_id' => ['nullable', 'exists:users,id'],
        ]);

        $class->update([
            'name' => $request->name,
            'code' => Str::upper($request->code),
            'description' => $request->description,
            'mentor_id' => $request->mentor_id,
        ]);

        ActivityLog::log('Updated class group: ' . $class->name, auth()->id(), [
            'class_group_id' => $class->id,
            'mentor_id' => $request->mentor_id,
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class group updated successfully.');
    }

    public function destroy(ClassGroup $class)
    {
        // Remove class assignment from participants
        User::where('class_group_id', $class->id)->update(['class_group_id' => null]);
        
        $name = $class->name;
        $class->delete();

        ActivityLog::log('Deleted class group: ' . $name, auth()->id());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class group deleted successfully.');
    }

    /**
     * Add participants to this class (supports multiple)
     */
    public function addMember(Request $request, ClassGroup $class)
    {
        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $addedCount = 0;
        $errors = [];

        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);

            if (!$user) {
                continue;
            }

            // Only participants can be added as members
            if ($user->role !== 'participant') {
                $errors[] = "{$user->name} is not a participant.";
                continue;
            }

            // Check if already assigned to this class
            if ($user->class_group_id === $class->id) {
                $errors[] = "{$user->name} is already a member.";
                continue;
            }

            // Update participant's class
            $user->update(['class_group_id' => $class->id]);
            $addedCount++;

            ActivityLog::log('Added participant to class: ' . $user->name . ' -> ' . $class->name, auth()->id(), [
                'class_group_id' => $class->id,
                'user_id' => $user->id,
            ]);
        }

        if ($addedCount > 0) {
            $message = "{$addedCount} participant(s) added successfully.";
            if (count($errors) > 0) {
                $message .= ' Some errors: ' . implode(', ', $errors);
            }
            return back()->with('success', $message);
        }

        return back()->with('error', 'No participants were added. ' . implode(', ', $errors));
    }

    /**
     * Remove a participant from this class
     */
    public function removeMember(ClassGroup $class, User $user)
    {
        if ($user->class_group_id !== $class->id) {
            return back()->with('error', 'User is not a member of this class.');
        }

        $user->update(['class_group_id' => null]);

        ActivityLog::log('Removed participant from class: ' . $user->name . ' <- ' . $class->name, auth()->id(), [
            'class_group_id' => $class->id,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Participant removed successfully.');
    }

    /**
     * Assign a mentor to this class
     */
    public function assignMentor(Request $request, ClassGroup $class)
    {
        $request->validate([
            'mentor_id' => ['required', 'exists:users,id'],
        ]);

        $mentor = User::findOrFail($request->mentor_id);

        if ($mentor->role !== 'mentor') {
            return back()->with('error', 'Selected user is not a mentor.');
        }

        // Check if mentor is already assigned to another class
        if ($mentor->mentorOfClass && $mentor->mentorOfClass->id !== $class->id) {
            return back()->with('error', 'This mentor is already assigned to another class.');
        }

        $class->update(['mentor_id' => $mentor->id]);

        ActivityLog::log('Assigned mentor to class: ' . $mentor->name . ' -> ' . $class->name, auth()->id(), [
            'class_group_id' => $class->id,
            'mentor_id' => $mentor->id,
        ]);

        return back()->with('success', 'Mentor assigned successfully.');
    }
}
