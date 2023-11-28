<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:25')]
    public $name;

    public $search;
    public $completed = false;

    public $editingID;

    #[Validate('required|min:3|max:25')]
    public $editingName;

    public function save()
    {
        //validate name
        $validated = $this->validateOnly('name');

        // save to database
        Todo::create($validated);

        $this->reset('name');

        session()->flash('add', 'Added successfuly');
        $this->resetPage();
        // $this->redirect('/');
    }

    public function delete($todoID)
    {
        Todo::find($todoID)->delete();

        session()->flash('delete', 'deleted successfuly');
    }

    public function toggle($todoID)
    {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID)
    {
        $this->editingID = $todoID;
        $this->editingName = Todo::find($todoID)->name;
    }

    public function update()
    {
        $this->validateOnly('editingName');
        Todo::find($this->editingID)->update([
            'name' => $this->editingName,
        ]);
        $this->cancel();
    }
    public function cancel()
    {
        $this->reset('editingID', 'editingName');
    }
    public function closeToast()
    {
        if (session()->has('add')) {
            session()->forget('add');
        } elseif (session()->has('delete')) {
            session()->forget('delete');
        }
    }
    public function render()
    {
        $todos = Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5);
        return view('livewire.todo-list', compact('todos'));
    }
}
