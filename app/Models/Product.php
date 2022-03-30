<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = ['name','barcode','cost','price','stock','alerts','image','category_id'];

    public function Category(){
        return $this->belongsTo(Category::class);
    }

    public function getImagenAttribute(){

        if($this->image != null)
            return (file_exists('storage/products/' . $this->image) ? $this->image : 'image_icon.png');
        else
            return 'image_icon.png';

/*
        if(file_exists('storage/products/' . $this->image))
            return $this->image;
        else
            return 'image_icon.png';
            */
    }

}
