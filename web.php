<?php
use App\Images;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 
Route::get('/', function () {
    return view('welcome');
});
 
//or you can change to post method Route::post
 
Route::post('imagepost', function(Request $request)
{
    $file = $request->image;
    //save image
    $path = $request->image->store('/public');
    //gen filename
    $filename=$request->image->hashName();
    $abs_path='storage/'.$filename;
 
    $img = Image::make($abs_path);
 
   
    $weight=$img->width();
    $height=$img->height();
    $rows=2;
    $cols=2;
    $fragmentY= $height/$rows;
    $fragmentX=$weight/$cols;
 
    try{
        for($i=0;$i<$rows;$i++)
        {
            $top=$fragmentY*$i;
            for($c=0;$c<$cols;$c++)
            {
                $left=$fragmentX*$c;
                $img = Image::make($abs_path);
                $img->crop($fragmentX ,$fragmentY,round($left),round($top));
                $img->save('storage/'.$i.$c.'_'.$filename);
 
                $model = new Images;
                $model->name =$i.$c.'_'.$filename;
                $model->path = 'storage/'.$i.$c.'_'.$filename;
                $model->save();
                
            }
        } 
        return response()->json('ok');
    }
    catch (Exception $e) {
    return response()->json('error');
    }
   
    
 
});
 
// usage inside a laravel route
Route::get('image', function()
{
  return view('image');
   
});
 
Route::get('image/{id}', function ($id) {
    $model = App\Images::find($id);
    return response()->json($model);
});
 
Route::get('images', function () {
    $model = App\Images::get();
    return response()->json($model);
});
 
Route::get('deleteimages', function () {
     App\Images::truncate();
});
