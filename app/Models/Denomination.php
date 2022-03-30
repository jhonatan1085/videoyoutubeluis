<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    use HasFactory;

    protected $fillable = ['type','value','image'];


    public function getImagenAttribute(){
        if($this->image != null)
            return (file_exists('storage/coins/' . $this->image) ? 'coins/' . $this->image : 'image_icon.png');
        else
            return 'image_icon.png';

    }
}
