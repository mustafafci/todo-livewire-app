<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Todo as TodoModel;
use Livewire\WithPagination;

class Todo extends Component
{
    use WithPagination;

    #[Validate('required|string|min:3|max:100')]
    public $name = '';

    public $search = '';

    public $editingTodoID;

    #[Validate('required|string|min:3|max:100')]
    public $editingTodoName;

    public function create()
    {
        $this->validateOnly('name');

        TodoModel::create(['name' => $this->name]);

        $this->reset(['name']);
       // session()->flash('success', 'Created Successfully');
       flash()->success('Created Successfully!');


        $this->resetPage();
    }

    public function destroy(TodoModel $todo)
    {
        $todo->delete();
        flash()->success('Deleted Successfully');
    }

    public function toggle($id)
    {
        $todo = TodoModel::findOrFail($id);
        //dd($todo);
        $todo->completed = !$todo->completed;
        $todo->save();

        flash()->success('Status updated successfully');
    }

    public function edit(TodoModel $todo)
    {

        $this->editingTodoID = $todo->id;
        $this->editingTodoName = $todo->name;
    }

    public function cancel()
    {
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function update()
    {
        //dd($this->editingTodoID, $this->editingTodoName);

        $todo = TodoModel::findOrFail($this->editingTodoID);

        $todo->name = $this->editingTodoName;

        $todo->save();

        $this->cancel();
        flash()->success('updated successfully');
    }


    public function render()
    {
        return view('livewire.todo', [
            'todos' => TodoModel::latest()->where('name', 'like', "%$this->search%")->paginate(5)
        ]);
    }
}
