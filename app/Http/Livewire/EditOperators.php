<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EditOperators extends Component
{
    public $route = '';
    public $search = '';
    public $state = '';
    public $who;

    public $operatorClearance;
    public $operatorName = '';
    public $operatorEmail = '';
    public $operatorPassword = '';

    public function mount() {
        $this->route = url()->previous();
    }

    public function render()
    {
        $data = DB::table('operators')
        ->where('id', $this->who)
        ->first();
        $roles = DB::table('clearance')->select('id', 'role')->pluck('id', 'role');

        return view('livewire.edit-operators', [
            'operators' => DB::table('operators')
            ->join('clearance', 'operators.clearance_id', '=', 'clearance.id')
            ->select('operators.id', 'operators.name', 'operators.email', 'clearance.role')
            ->where('role', 'like', '%'.$this->search.'%')
            ->orWhere('name', 'like', '%'.$this->search.'%')
            ->get(),
            'roles' => $roles,
            'data' => $data,
            'state' => $this->state,
        ]);
    }

    public function edit($id) {
        $this->state = 'edit';
        $this->who = $id;
        $data = DB::table('operators')
        ->where('id', $this->who)
        ->first();
        $this->operatorName = $data->name;
        $this->operatorEmail = $data->email;
        $this->operatorPassword = '';
        $this->operatorClearance = '';
    }

    public function add() {
        $this->state = 'add';
    }

    public function update($id) {
        $currPassword = DB::table('operators')
        ->select('password')
        ->where('id', $id)
        ->first();
        if(Hash::check($this->operatorPassword, $currPassword->password)) {
            $validatedData = $this->validate([
                'operatorName' => 'required|regex:/^[a-zA-Z0-9\s]+$/|between:3,60',
                'operatorEmail' => 'required|email:rfc,dns',
                'operatorClearance' => 'required'
            ]);
            $affected = DB::table('operators')
            ->where('id', $id)
            ->update([
                'name' => $validatedData['operatorName'],
                'email' => $validatedData['operatorEmail'],
                'clearance_id' => $validatedData['operatorClearance']
            ]);
            $this->state = '';
        }
        $this->operatorPassword = '';
        $this->addError('operatorPassword', 'The provided credentials do not match our records.');
    }
}
