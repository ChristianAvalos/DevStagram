<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(Request $request,User $user){
        return view('perfil.index');
    }

    public function store(Request $request){
        //Modificar el request
        $request->request->add(['username' => Str::slug($request->username)]);

        //paso el email de esta forma para la verificacion del usuario
        $request->request->add(['email' => (auth()->user()->email)]);
        
        $this->validate($request,[
            'username' => ['required', 'unique:users,username,'.auth()->user()->id,'min:3', 'max:20'],
        ]);
        if($request->imagen){
        //Obtengo datos del archivo
        $imagen = $request->file('imagen');

        //le doy nombres unicos a cada imagen 
        $nombreImagen = Str::uuid() . "." . $imagen->extension();

        //paso los datos para InventionImage
        $imageServidor = Image::make($imagen);

        //Corto la imagen 
        $imageServidor->fit(1000,1000);

        $imagenPath = public_path('perfiles') . '/' . $nombreImagen;

        $imageServidor->save($imagenPath);
        }

        //Guardar cambios
        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        

        if($request->password <> null){
            if(!auth()->attempt($request->only('email','password'))){
                return back()->with('mensaje','Password actual incorrecto');
            }else{
                $this->validate($request,[
                    'newpassword' => 'required|min:6',
                ]);
                $usuario->password = Hash::make( $request->newpassword);
            }
        }

        $usuario->save();

        //redirigir 
        return redirect()->route('posts.index',$usuario->username);


    }
}
