<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads; //trait para subir imagenes de livewire
use Livewire\WithPagination; //paginacion de livewire
use App\Models\User;
use DB;


class RolesController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $roleName, $search, $selected_id, $pageTitle, $ComponentName; //propiedades publicas
    private $pagination = 5;

    public function mount()
    { //metodo que se ejecuta al inicio, sirve para inicializar 
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Roles';
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
    public function render()
    {

        if (strlen($this->search) > 0) {
            $roles = Role::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);
        } else {
            $roles = Role::orderBy('name', 'desc')->paginate($this->pagination);
        }

        return view('livewire.roles.component', [
            'roles' => $roles
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }


    public function CreateRole()
    {

        $rules = [
            'roleName' => 'required|min:2|unique:roles,name'
        ];
        $messages = [
            'roleName.require' => 'El nombre del Rol es requerido',
            'roleName.unique' => 'El rol ya existe',
            'roleName.min' => 'El nombre del rol debe tener al menos 2 caracteres'
        ];

        $this->validate($rules, $messages);

        Role::create([
            'name' => $this->roleName
        ]);

        $this->emit('role-added', 'Se Registro el Rol con exito');
        $this->resetUI();
    }


    public function Edit(Role $role)
    {

        //$role = Role::find($id);
        $this->selected_id = $role->id;
        $this->roleName = $role->name;

        $this->emit('show-modal', 'Show modal');
    }

    public function UpdateRole()
    {

        $rules = [
            'roleName' => "required|min:2|unique:roles,name,{ $this->selected_id }"
        ];
        $messages = [
            'roleName.require' => 'El nombre del Rol es requerido',
            'roleName.unique' => 'El rol ya existe',
            'roleName.min' => 'El nombre del rol debe tener al menos 2 caracteres'
        ];

        $this->validate($rules, $messages);

        $role = Role::find($this->selected_id);
        $role->name = $this->roleName;
        $role->save();


        $this->emit('role-updated', 'Se Actualizo el Rol con exito');
        $this->resetUI();
    }

    protected $listeners = [ //para escuchar los eventos
        'deleteRow' => 'Destroy'
    ];

    public function Destroy($id)
    {
        $permissionsCount = Role::find($id)->permissions->count();

        if($permissionsCount > 0 ){
            $this->emit('role-error', 'No se puede eliminar el role por que tiene permisos asociados');
            return;
        }

        Role::find($id)->delete();
        $this->emit('role-deleted', 'Se elimino el rol con exito');
    }

    public function resetUI(){
        $this->nameRole='';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
    }
}
