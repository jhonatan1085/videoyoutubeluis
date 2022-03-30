<div class="row sales layout-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <p>{{ $ComponentName }} </p>
                </h4>

            </div>


            <div class="widget-content">

                <div class="form-inline">
                    <div class="form-group mr-5">
                        <select wire:model="role" class="form-control">
                            <option value="Elegir" selected>== Selecciona el Role ==</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button wire:click.prevent="SyncAll()" type="button" class="btn btn-dark mbmobile inblock mr-5">
                        Sincronizar Todos
                    </button>

                    <button onClick="Revocar()" type="button" class="btn btn-dark mbmobile inblock mr-5">
                        Revocar Todos
                    </button>
                </div>


                <div class="row mt-3">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-1">
                                <thead class="text-white" style="background:#3B3F5C">
                                    <tr>
                                        <th class="table-th text-white text-center">ID</th>
                                        <th class="table-th text-white text-center">PERMISO</th>
                                        <th class="table-th text-white text-center">ROLRES CON EL PERMISO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permisos as $permiso)
                                        <tr>
                                            <td>
                                                <h6 class="text-center">{{ $permiso->id }}</h6>
                                            </td>
                                            <td class="text-center">
                                                <div class="n-check">
                                                    <label class="new-control new-checkbox checkbox-primary">
                                                        <input type="checkbox"
                                                            wire:change="SyncPermiso($('#p'+ {{ $permiso->id }}).is(':checked'),'{{ $permiso->name }}')"
                                                            id="p{{ $permiso->id }}" value="{{ $permiso->id }}"
                                                            class="new-control-input"
                                                            {{ $permiso->checked == 1 ? 'checked' : '' }}>
                                                        <span class="new-control-indicator"></span>
                                                        <h6>{{ $permiso->name }}</h6>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <h6>{{ \App\Models\User::permission($permiso->name)->count() }}</h6>
                                                <!-- solo es una forma de hacerlo pero no recomendada-->
                                            </td>


                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $permisos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    include form
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('sync-error', msg => {
            noty(msg)
        })

        window.livewire.on('permi', msg => {
            noty(msg)
        })

        window.livewire.on('syncall', msg => {
            noty(msg)
        })

        window.livewire.on('removeall', msg => {
            noty(msg)
        })
    });

    function Revocar() {

        swal({
            title: 'CONFIRMAR',
            text: '¿CONFIRMAS REVOCAR TODOS LOS PERMISOS?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'Aceptar',
        }).then(function(result) {
            if (result.value) {
                window.livewire.emit('revokeall')
                swal.close()
            }
        })
    }
</script>
