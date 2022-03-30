<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads; //trait para subir imagenes de livewire
use Livewire\WithPagination; //paginacion de livewire

class CategoriesController extends Component
{

    use WithFileUploads;
    use WithPagination;

    //propiedades publicas

    public $name, $search, $image, $selected_id, $pageTitle, $ComponentName; //propiedades publicas
    private $pagination = 5;

    public function mount(){ //metodo que se ejecuta al inicio, sirve para inicializar 
        $this->PageTitle= 'Listado';
        $this->ComponentName= 'Categorias';
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
       // $data = Category::all();

       if(strlen($this->search) > 0){
        $data = Category::where('name','like', '%' . $this->search . '%')->paginate($this->pagination);
       }else{
        $data = Category::orderBy('id','desc')->paginate($this->pagination);
       }

        return view('livewire.category.categories', ['categories' => $data])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Edit($id){
       // $record = Category::find($id);
       $record = Category::find($id, ['id','name','image']);

        $this->name = $record->name;
        $this->selected_id = $record->id;
        $this->image = null;

        $this->emit('show-modal', 'show modal!');
    }

    public function Store(){
        $rules= [
            'name' => 'required|unique:categories|min:3'
        ];
        $messages= [
            'name.required' => 'Nombre de Categoria es requerido',
            'name.unique' => 'Nombre de categoria ya Existe',
            'name.min' => 'El nombre de la categoria debe tener almenos 3 caracteres',
        ];

        $this->validate($rules,$messages);

        $category = Category::create([
            'name' => $this->name
        ]);
        
        $customFileName;

        if($this->image){
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/categorias',$customFileName);
            $category->image = $customFileName;
            $category->save();
        }

        $this->resetUI();

        $this->emit('category-add','Categoria Rergistrada');
    }

    public function Update(){
        $rules= [
            'name' => "required|min:3|unique:categories,name,{$this->selected_id}"
        ];
        $messages= [
            'name.required' => 'Nombre de Categoria es requerido',
            'name.unique' => 'Nombre de categoria ya Existe',
            'name.min' => 'El nombre de la categoria debe tener almenos 3 caracteres',
        ];

        $this->validate($rules,$messages);

        $category = Category::find($this->selected_id);

        $category->update([
            'name' =>$this->name
        ]);


        if($this->image){
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/categorias/',$customFileName);
            $imageName = $category->image; //guardamos la imagen actual
            $category->image = $customFileName;
            $category->save();

            if($imageName != null){
                if(file_exists('storage/categorias/' . $imageName)){
                    unlink('storage/categorias/' . $imageName); // elimina la imagen
                }
            }

        }

        $this->resetUI();

        $this->emit('category-updated','Categoria Actualizada');

    }

    public function resetUI(){
        $this->name='';
        $this->image= null;
        $this->search = '';
        $this->selected_id = 0;
    }

    protected $listeners = [ //para escuchar los eventos
        'deleteRow' => 'Destroy'
    ];

    public function Destroy(Category $category ){

       // $category = Category::find($id);
       // dd($category); 

        $imageName =  $category->image;

        $category->delete();

        if($imageName != null){
            unlink('storage/categorias/' . $imageName); 
        }

        $this->resetUI();
        $this->emit('category-deleted','Categoria Eliminada');


    }
}
