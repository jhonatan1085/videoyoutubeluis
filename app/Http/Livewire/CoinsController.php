<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Denomination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads; //trait para subir imagenes de livewire
use Livewire\WithPagination; //paginacion de livewire

class CoinsController extends Component
{
    use WithFileUploads;
    use WithPagination;

    //propiedades publicas

    public $type, $value,$search, $image, $selected_id, $pageTitle, $ComponentName; //propiedades publicas
    private $pagination = 5;

    public function mount(){ //metodo que se ejecuta al inicio, sirve para inicializar 
        $this->PageTitle= 'Listado';
        $this->ComponentName= 'Denominaciones';
        $this->type = 'Elegir';
        $this->selected_id = 0;
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {

        if(strlen($this->search) > 0){
            $data = Denomination::where('type','like', '%' . $this->search . '%')->paginate($this->pagination);
           }else{
            $data = Denomination::orderBy('id','desc')->paginate($this->pagination);
           }
    
            return view('livewire.denominations.component', ['data' => $data])
            ->extends('layouts.theme.app')
            ->section('content');
    }



    public function Edit($id){
        // $record = Category::find($id);
        $record = Denomination::find($id, ['id','type','value', 'image']);
         $this->type = $record->type;
         $this->value = $record->value;
         $this->selected_id = $record->id;
         $this->image = null;
 
         $this->emit('modal-show', 'show modal!');
     }
 
     public function Store(){
         $rules= [
             'type' => 'required|not_in:Elegir',
             'value' => 'required|unique:denominations'
         ];
         $messages= [
             'type.required' => 'El tipo es requerido',
             'type.not_in' => 'Elige un valor para el tipo distinto a elegir',
             'value.required' => 'El valor es requerido',
             'value.unique' => 'Ya existe el valor'
         ];
 
         $this->validate($rules,$messages);
 
         $denomination = Denomination::create([
             'type' => $this->type,
             'value' => $this->value

         ]);
         

         if($this->image){
             $customFileName = uniqid() . '_.' . $this->image->extension();
             $this->image->storeAs('public/coins',$customFileName);
             $denomination->image = $customFileName;
             $denomination->save();
         }
 
         $this->resetUI();
 
         $this->emit('item-add','Denominacion Rergistrada');
     }
 
     public function Update(){
        $rules= [
            'type' => 'required|not_in:Elegir',
            'value' => "required|unique:denominations,value,{$this->selected_id}"
        ];
        $messages= [
            'type.required' => 'El tipo es requerido',
            'type.not_in' => 'Elige un valor para el tipo distinto a elegir',
            'value.required' => 'El valor es requerido',
            'value.unique' => 'Ya existe el valor'
        ];
 
         $this->validate($rules,$messages);
 
         $denomination = Denomination::find($this->selected_id);
 
         $denomination->update([
            'type' => $this->type,
            'value' => $this->value
         ]);
 
 
         if($this->image){
             $customFileName = uniqid() . '_.' . $this->image->extension();
             $this->image->storeAs('public/coins/',$customFileName);
             $imageName = $denomination->image; //guardamos la imagen actual
             $denomination->image = $customFileName;
             $denomination->save();
 
             if($imageName != null){
                 if(file_exists('storage/coins/' . $imageName)){
                     unlink('storage/coins/' . $imageName); // elimina la imagen
                 }
             }
 
         }
 
         $this->resetUI();
 
         $this->emit('item-updated','Denominacion Actualizada');
 
     }
 
     public function resetUI(){
       

         $this->type = '';
          $this->value = '';
          $this->search = '';
          $this->image = null;
          $this->selected_id = 0;
   
     }
 
     protected $listeners = [ //para escuchar los eventos
         'deleteRow' => 'Destroy'
     ];
 
     public function Destroy(Denomination $denomination ){
 
        // $category = Category::find($id);
        // dd($category); 
 
         $imageName =  $denomination->image;
 
         $denomination->delete();
 
         if($imageName != null){
             unlink('storage/coins/' . $imageName); 
         }
 
         $this->resetUI();
         $this->emit('item-deleted','Denominacion Eliminada');
 
 
     }
}
