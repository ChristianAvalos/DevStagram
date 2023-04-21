<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;



class ImagenController extends Controller
{
    public function store(Request $request){

        //Obtengo datos del archivo
        $imagen = $request->file('file');

        //le doy nombres unicos a cada imagen 
        $nombreImagen = Str::uuid() . "." . $imagen->extension();

        //paso los datos para InventionImage
        $imageServidor = Image::make($imagen);

        //Corto la imagen 
        $imageServidor->fit(1000,1000);

        $imagenPath = public_path('uploads') . '/' . $nombreImagen;

        $imageServidor->save($imagenPath);
        
        return response()->json(['imagen'=> $nombreImagen]);
    }
}
