<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination; //paginacion de livewire
use DB;

class AsignarController extends Component
{
    use WithPagination;

    public $role,  $ComponentName, $permisosSelected = [], $oldPermissions = []; //propiedades publicas
    private $pagination = 10;


    public function mount()
    { //metodo que se ejecuta al inicio, sirve para inicializar 
        $this->role = 'Elegir';
        $this->ComponentName = 'Asignar Permisos';
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }


    public function render()
    {
        $permisos = Permission::select('name', 'id', DB::raw("0 as checked"))
            ->orderby('name', 'asc')
            ->paginate($this->pagination);

        if ($this->role != 'Elegir') {
            $list = Permission::join('role_has_permissions as rp', 'rp.permission_id', 'permissions.id')
                ->where('role_id', $this->role)->pluck('permissions.id')->toArray();

            $this->old_permissions = $list;
        }

        if ($this->role != 'Elegir') {
            foreach ($permisos as $permiso) {
                $role = Role::find($this->role);
                $tienePermiso = $role->hasPermissionTo($permiso->name);
                if ($tienePermiso) {
                    $permiso->checked = 1;
                }
            }
        }

        return view('livewire.asignar.component', [
            'roles' => Role::orderBy('name', 'asc')->get(),
            'permisos' => $permisos
        ])->extends('layouts.theme.app')
            ->section('content');
    }

    public $listeners = [
        'revokeall' => 'RemoveAll'
    ];

    public function RemoveAll()
    {

        if ($this->role == 'Elegir') {
            $this->emit('sync-error', 'Selecciona un rol Valido');
            return;
        }

        $role = Role::find($this->role);
        $role->syncPermissions([0]);

        $this->emit('removeall', "se revocaron todos lospermisos al role $role->name");
    }

    public function SyncAll()
    {

        if ($this->role == 'Elegir') {
            $this->emit('sync-error', 'Selecciona un rol Valido');
            return;
        }

        $role = Role::find($this->role);
        $permisos = Permission::pluck('id')->toArray();
        $role->syncPermissions($permisos);

        $this->emit('syncall', "se sincronizaron todos los permisos al role $role->name");
    }

    public function SyncPermiso($state, $permisoName)
    {

        if ($this->role != 'Elegir') {

            $roleName = Role::find($this->role);

            if ($state) {
                $roleName->givePermissionTo($permisoName);
                $this->emit('permi', 'Permiso asignado correctamente');
            } else {
                $roleName->revokePermissionTo($permisoName);
                $this->emit('permi', 'Permiso Eliminado correctamente');
            }
        }else{
            $this->emit('permi', 'Elige un Rol Valido');
        }
    }
}
