<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Livewire\WithFileUploads; //trait para subir imagenes de livewire
use Livewire\WithPagination; //paginacion de livewire
use App\Models\User;
use DB;

class PermisosController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $permissionName, $search, $selected_id, $pageTitle, $ComponentName; //propiedades publicas
    private $pagination = 10;

    public function mount()
    { //metodo que se ejecuta al inicio, sirve para inicializar 
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Permisos';
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
    
    public function render()
    {

        if (strlen($this->search) > 0) {
            $permisos = Permission::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);
        } else {
            $permisos = Permission::orderBy('name', 'desc')->paginate($this->pagination);
        }

        return view('livewire.permisos.component', [
            'permisos' => $permisos
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }


    public function CreatePermission()
    {

        $rules = [
            'permissionName' => 'required|min:2|unique:permissions,name'
        ];
        $messages = [
            'permissionName.require' => 'El nombre del Permiso es requerido',
            'permissionName.unique' => 'El Permiso ya existe',
            'permissionName.min' => 'El nombre del Permiso debe tener al menos 2 caracteres'
        ];

        $this->validate($rules, $messages);

        Permission::create([
            'name' => $this->permissionName
        ]);

        $this->emit('permiso-added', 'Se Registro el Permiso con exito');
        $this->resetUI();
    }


    public function Edit(Permission $permiso)
    {

        //$role = Role::find($id);
        $this->selected_id = $permiso->id;
        $this->permissionName = $permiso->name;

        $this->emit('show-modal', 'Show modal');
    }

    public function UpdatePermission()
    {

        $rules = [
            'permissionName' => "required|min:2|unique:permissions,name,{ $this->selected_id }"
        ];
        $messages = [
            'permissionName.require' => 'El nombre del Rol es requerido',
            'permissionName.unique' => 'El rol ya existe',
            'permissionName.min' => 'El nombre del rol debe tener al menos 2 caracteres'
        ];

        $this->validate($rules, $messages);

        $permiso = Permission::find($this->selected_id);
        $permiso->name = $this->permissionName;
        $permiso->save();


        $this->emit('permiso-updated', 'Se Actualizo el Permiso con exito');
        $this->resetUI();
    }

    protected $listeners = [ //para escuchar los eventos
        'deleteRow' => 'Destroy'
    ];

    public function Destroy($id)
    {
        $permissionsCount = Permission::find($id)->getRoleNames()->count();

        if($permissionsCount > 0 ){
            $this->emit('permiso-deleted', 'No se puede eliminar el Permiso por que tiene roles asociados');
            return;
        }

        Permission::find($id)->delete();
        $this->emit('role-deleted', 'Se elimino el rol con exito');
    }

    public function resetUI(){
        $this->permissionName='';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
    }
}
