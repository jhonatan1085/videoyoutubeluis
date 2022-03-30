<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads; //trait para subir imagenes de livewire
use Livewire\WithPagination; //paginacion de livewire

class ProductsController extends Component
{

    use WithFileUploads;
    use WithPagination;

    //propiedades publicas

    public $name, $barcode, $cost, $price, $stock, $alerts, $categoryid, $image, $search, $selected_id, $pageTitle, $ComponentName; //propiedades publicas
    private $pagination = 5;

    public function mount()
    { //metodo que se ejecuta al inicio, sirve para inicializar 
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Productos';
        $this->categoryid = 'Elegir';
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        if (strlen($this->search) > 0) {
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                ->select('products.*', 'c.name as category')
                ->where('products.name', 'like', '%' . $this->search . '%')
                ->orWhere('products.barcode', 'like', '%' . $this->search . '%')
                ->orWhere('c.name', 'like', '%' . $this->search . '%')
                ->orderBy('products.name', 'asc')->paginate($this->pagination);
        } else {
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                ->select('products.*', 'c.name as category')
                ->orderBy('products.name', 'asc')->paginate($this->pagination);
        }

        return view('livewire.products.component', [
            'data' => $products,
            'categories' => Category::orderBy('name', 'asc')->get()
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function Store()
    {
        $rules = [
            'name' => 'required|unique:products|min:3',
            'cost' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'categoryid' => 'required|not_in:Elegir' //no permite que selecciones elegir
        ];
        $messages = [
            'name.required' => 'Nombre de Producto es requerido',
            'name.unique' => 'Nombre de Producto ya Existe',
            'name.min' => 'El nombre de Producto debe tener almenos 3 caracteres',
            'cost.required' => 'Costo de Producto es requerido',
            'price.required' => 'Precio de Producto es requerido',
            'stock.required' => 'Stock de Producto es requerido',
            'alerts.required' => 'Alerts de Producto es requerido',
            'categoryid.not_in' => 'Elige un nombre de Categoria diferente a Elegir'
        ];

        $this->validate($rules, $messages);

        $product = Product::create([
            'name' => $this->name,
            'barcode' => $this->barcode,
            'cost'  => $this->cost,
            'price' => $this->price,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->categoryid
        ]);

        if ($this->image) {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/products', $customFileName);
            $product->image = $customFileName;
            $product->save();
        }

        $this->resetUI();

        $this->emit('product-add', 'Producto Rergistrado');
    }

    public function Edit(Product $product)
    {
        $this->selected_id = $product->id;
        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->cost = $product->cost;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->alerts = $product->alerts;
        $this->categoryid = $product->category_id;
        $this->image = null;

        $this->emit('modal-show', 'Show modal');
    }


    public function Update()
    {
        $rules = [
            'name' => "required|min:3|unique:products,name,{$this->selected_id}",
            'cost' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'categoryid' => 'required|not_in:Elegir' //no permite que selecciones elegir
        ];
        $messages = [
            'name.required' => 'Nombre de Producto es requerido',
            'name.unique' => 'Nombre de Producto ya Existe',
            'name.min' => 'El nombre de Producto debe tener almenos 3 caracteres',
            'cost.required' => 'Costo de Producto es requerido',
            'price.required' => 'Precio de Producto es requerido',
            'stock.required' => 'Stock de Producto es requerido',
            'alerts.required' => 'Alerts de Producto es requerido',
            'categoryid.not_in' => 'Elige un nombre de Categoria diferente a Elegir'
        ];

        $this->validate($rules, $messages);

        $product  = Product::find($this->selected_id);

        $product->update([
            'name' => $this->name,
            'barcode' => $this->barcode,
            'cost'  => $this->cost,
            'price' => $this->price,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->categoryid
        ]);

        if ($this->image) {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/products', $customFileName);
            $imageTemp = $product->image; //imagen temporal
            $product->image = $customFileName;
            $product->save();

            if ($imageTemp != null) {
                if (file_exists('storage/products/' . $imageTemp)) {
                    unlink('storage/products/' . $imageTemp); // elimina la imagen
                }
            }
        }

        $this->resetUI();

        $this->emit('product-updated', 'Producto Actualizado');
    }


    public function resetUI()
    {
        $this->name = '';
        $this->barcode = '';
        $this->cost = '';
        $this->price = '';
        $this->stock = '';
        $this->alerts = '';
        $this->categoryid = 'Elegir';
        $this->image = null;
        $this->search = '';
        $this->selected_id = 0;
    }

    protected $listeners = [ //para escuchar los eventos
        'deleteRow' => 'Destroy'
    ];

    public function Destroy(Product $product){
        $imageTemp =  $product->image;

        $product->delete();

        if($imageTemp != null){
            if (file_exists('storage/products/' . $imageTemp)) {
                unlink('storage/products/' . $imageTemp); // elimina la imagen
            }
        }

        $this->resetUI();
        $this->emit('product-deleted','Producto Eliminado');
    }
}
