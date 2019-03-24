<?php

namespace App\Http\Controllers;

use App\Girl;
use App\Photo;
use App\Target;
use App\User;
use App\Privatephoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use File;
use Carbon\Carbon;

use App\ImageResize;


class AnketController extends Controller
{
    //
    function createGirl()
    {
        $user = Auth::user();
        $targets = Target::select(['id', 'name'])->get();


        return view('createAnket')
            ->with(
                ['username' => $user->name, 'tagrets' => $targets]);
    }

    function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required',
            'sex' => 'required',
            'age' => 'required|numeric|min:18',
            'met' => 'required',
            'description' => 'required',
            'private' => 'required',
            // 'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $user = Auth::user();

        $girl = Girl::select('id', 'name', 'user_id')->where('user_id', $user->id)->first();
        if ($girl != null) {
            return redirect('/edit');
        }

        $girl = new Girl();
        $girl->name = $request->name;
        $girl->sex = $request->sex;
        $girl->meet = $request->met;
        $girl->age = $request->age;
        $girl->weight = $request->weight;
        $girl->height = $request->height;
        $girl->description = $request->description;
        $girl->private = $request->private;
        $girl->user_id = $user->id;
        $girl->save();


        if (Input::hasFile('file')) {
            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path().'/public/images/upload/'.strtolower($image_new_name.'.'.$image_extension);// кладем файл с новыс именем
            $request->file('file')
                ->move(base_path().'/public/images/upload/',
                    strtolower($image_new_name.'.'.$image_extension));

            $girl['main_image'] = $image_new_name.'.'.$image_extension;
            //сохраняем уменьшенную копию
            $small = base_path().'/public/images/small/'.strtolower($image_new_name.'.'.$image_extension);
            copy($temp_file, $small);

            $image = new ImageResize($small);
            $image->resizeToHeight(150);
            $image->save($small);
        }
        $girl->save();

        if (Auth::guest()) {
            return redirect('/login');
        }

        if (Input::hasFile('images')) {
            $count = 0;
            foreach ($request->images as $key) {
                $image_extension = $request->file('file')->getClientOriginalExtension();
                $image_new_name = md5(microtime(true));
                $key->move(public_path().'/images/upload/', strtolower($image_new_name.'.'.$image_extension));
                $id = $girl['id'];
                $photo = new Photo();
                $photo['photo_name'] = $image_new_name.'.'.$image_extension;
                $photo['girl_id'] = $id;
                $photo->save();
            }
        }

        foreach ($request->privateimages as $key) {
            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $key->move(public_path().'/images/upload/', strtolower($image_new_name.'.'.$image_extension));
            $id = $girl['id'];
            $photo = new Privatephoto();
            $photo['photo_name'] = $image_new_name.'.'.$image_extension;
            $photo['girl_id'] = $id;
            $photo->save();
        }

        //цели
        foreach ($request->target as $item) {
            $target = Target::select(['id', 'name'])->where('id', $item)->first();
            if ($target != null) {
                $girl->target()->attach($target);
            }
        }


        return redirect('/anket');
    }

    public function girlsEditAuchAnket()
    {
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        if ($user == null) {
            return redirect('/login');
        }
        $girl = Girl::select([
            'name',
            'age',
            'id',
            'phone',
            'description',
            'main_image',
            'sex',
            'meet',
            'weight',
            'height',
            'country_id',
            'region_id',
            'city_id',
            'private',
        ])->where('user_id', $user->id)->first();
        if ($girl == null) {
            return $this->index();
        }
        $phone = $user->phone;

        $targets = Target::select(['id', 'name'])->get();
        $allTarget = [];
        foreach ($targets as $tag) {
            array_push($allTarget, $tag->name);
        }
        dump($allTarget);
        $targets = $girl->target()->get();
        $anketTarget = [];
        foreach ($targets as $tag) {
            array_push($anketTarget, $tag->name);
        }

        //   $countries = collect(DB::select('select * from countries'));
        //$countries = collect(DB::select('select * from countries'));

        return view('editGirl')->with([
            'username' => $user->name,
            'girl' => $girl,
            'phone' => $phone,
            'targets' => $targets,
            'allTarget' => $allTarget,
            'anketTarget' => $anketTarget,
        ]);
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'sex' => 'required',
            'age' => 'required|numeric|min:18',
            'met' => 'required',
            'private' => 'required',
            'description' => 'required',
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        if ($user == null) {
            return redirect('/login');
        }


        $girl = Girl::select([
            'name',
            'id',
            'phone',
            'description',
            'private',
            'main_image',
            'sex',
            'meet',
            'weight',
            'height',
        ])->where('user_id', $user->id)->first();
        if ($girl == null) {
            return redirect('/index');
        }
        $girl->age = $request->age;
        $girl->description = $request->description;
        $girl->private = $request->private;

        if ($request->has('famele')) {
            $sex = 'famele';
        }
        if ($request->has('male')) {
            $sex = 'male';
        }
        DB::table('girls')->where('id', $girl->id)->update(['age' => $request['age']]);
        DB::table('girls')->where('id', $girl->id)->update(['sex' => $request['sex']]);
        DB::table('girls')->where('id', $girl->id)->update(['meet' => $request['met']]);
        DB::table('girls')->where('id', $girl->id)->update(['description' => $request['description']]);
        if (Input::hasFile('file')) {
            $old_image_name = $girl['main_image'];
            $path = base_path().'/public/images/upload/'.$old_image_name;
            File::Delete($path);
            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path().'/public/images/upload/'.strtolower($image_new_name.'.'.$image_extension);// кладем файл с новыс именем
            $new_name = $image_new_name.'.'.$image_extension;
            $request->file('file')
                ->move(base_path().'/public/images/upload/', strtolower($image_new_name.'.'.$image_extension));
            DB::table('girls')->where('id', $girl->id)->update(['main_image' => $new_name]);
            $origin_size = getimagesize($temp_file);
        }
        //тут местоположее
        $girl->save();

        //переделываем цели
        $girl->target()->detach();
        $target_requwest = $request->input('tags');
        $targets = Target::select('id',
            'name',
            'created_at',
            'updated_at')->whereIn('name', $target_requwest)->get();

        foreach ($targets as $target) {
            $girl->target()->attach($target);
        }

        //    return $this->girlsEditAuchAnket();
        return redirect('/anket');
    }

    public function updateMainImage(Request $request)
    {

        $validatedData = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $updateMainImagePrice = DB::table('prices')->where('price_name', '=', 'update_main_image')->first();
        $user = Auth::user();
        dump($updateMainImagePrice);
        $price = $updateMainImagePrice->price;

        $temp = $user->money - intval($price);

        if (Input::hasFile('file') and $temp >= 0) {

            //сохраняем новый файл

            $girl = Girl::select(['main_image'])->where('user_id', $user->get_id())->first();

            $image_extension = $request->file('file')->getClientOriginalExtension();
            //$image_new_name = md5(microtime(true));
            $image_new_name = $girl->main_image;
            $temp_file = base_path().'/public/images/upload/'.strtolower($image_new_name);// кладем файл с новыс именем
            $request->file('file')
                ->move(base_path().'/public/images/upload/',
                    strtolower($image_new_name));
            $origin_size = getimagesize($temp_file);
            $girl['main_image'] = $image_new_name;
            $small = base_path().'/public/images/small/'.strtolower($image_new_name);
            copy($temp_file, $small);
            $image = new ImageResize($small);
            $image->resizeToHeight(150);


            $user->money = $user->money - $price;
            $user->save();
        }
        $girl->save();

        //тут списываем деньгт


        return response()->json(['ok']);
    }

    //получаем главное изображение
    public function getmainImage()
    {
        $auth = Auth::user();
        $girl = Girl::select(['main_image'])->where('user_id', $auth->id)->first();

        return response()->json($girl->main_image);
    }

    public function getImages(Request $request)
    {
        $user = Auth::user();
        $girl = Girl::select(['id'])->where('user_id', $user->get_id())->first();
        $images = $girl->photos()->get();

        return response()->json($images);
    }

    public function updateGalerayImage(Request $request)
    {
        $user = Auth::user();
        if (Input::hasFile('file')) {

            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path().'/public/images/upload/'.strtolower($image_new_name.'.'.$image_extension);// кладем файл с новыс именем
            $request->file('file')
                ->move(base_path().'/public/images/upload/', strtolower($image_new_name.'.'.$image_extension));
            $photo = new Photo();
            $girl = Girl::select(['id', 'user_id'])->where('user_id', $user->get_id())->first();
            $photo['photo_name'] = $image_new_name.'.'.$image_extension;
            $photo = new Photo();
            $photo['photo_name'] = $image_new_name.'.'.$image_extension;
            $photo['girl_id'] = $girl->id;
            $photo->save();
        }

        return response()->json(['ok']);
    }

    public function deleteImage(Request $request)
    {

        $imagename = $request->imagename;
        $user = Auth::user();
        try {
            $temp_file = base_path().'/public/images/upload/'.$imagename;
            File::Delete($temp_file);
            // тут будем удалять из таблицы
            $photo = Photo::select('id')->where('photo_name', $imagename)->get();
            $photo->delete();
        } catch (\Exception $e) {
            echo "delete errod";
        }
        $image = Photo::select(['id', 'photo_name'])->where('photo_name', $imagename)->first();
        try {
            File::delete($imagename);
        } catch (IOException $e) {
        }
        $image->delete();

        return response()->json(['ok']);
    }

    public function getPrivateImages(Request $request)
    {
        $user = Auth::user();
        $girl = Girl::select(['id'])->where('user_id', $user->get_id())->first();
        $images = $girl->privatephotos()->get();

        return response()->json($images);
    }

    public function updatePrivateGalerayImage(Request $request)
    {
        $user = Auth::user();
        if (Input::hasFile('file')) {
            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path().'/public/images/upload/'.strtolower($image_new_name.'.'.$image_extension);// кладем файл с новыс именем
            $request->file('file')
                ->move(base_path().'/public/images/upload/', strtolower($image_new_name.'.'.$image_extension));
            $photo = new Privatephoto();
            $girl = Girl::select(['id', 'user_id'])->where('user_id', $user->get_id())->first();
            $photo['photo_name'] = $image_new_name.'.'.$image_extension;
            $photo = new Privatephoto();
            $photo['photo_name'] = $image_new_name.'.'.$image_extension;
            $photo['girl_id'] = $girl->id;
            $photo->save();
        }

        return response()->json(['ok']);
    }

    public function deletePrivateImage(Request $request)
    {

        $imagename = $request->imagename;
        $user = Auth::user();
        try {
            $temp_file = base_path().'/public/images/upload/'.$imagename;
            File::Delete($temp_file);
            // тут будем удалять из таблицы
            $photo = Privatephoto::select('id')->where('photo_name', $imagename)->get();
            $photo->delete();
        } catch (\Exception $e) {
            echo "delete errod";
        }
        $image = Privatephoto::select(['id', 'photo_name'])->where('photo_name', $imagename)->first();
        try {
            File::delete($imagename);
        } catch (IOException $e) {
        }
        $image->delete();

        return response()->json(['ok']);
    }

    function image_resize(
        $source_path,
        $destination_path,
        $newwidth,
        $newheight = false,
        $quality = false // качество для формата jpeg
    )
    {

        ini_set("gd.jpeg_ignore_warning", 1); // иначе на некотоых jpeg-файлах не работает

        list($oldwidth, $oldheight, $type) = getimagesize($source_path);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $typestr = 'jpeg';
                break;
            case IMAGETYPE_GIF:
                $typestr = 'gif';
                break;
            case IMAGETYPE_PNG:
                $typestr = 'png';
                break;
        }
        $function = "imagecreatefrom$typestr";
        $src_resource = $function($source_path);

        if (!$newheight) {
            $newheight = round($newwidth * $oldheight / $oldwidth);
        } elseif (!$newwidth) {
            $newwidth = round($newheight * $oldwidth / $oldheight);
        }
        $destination_resource = imagecreatetruecolor($newwidth, $newheight);

        imagecopyresampled($destination_resource, $src_resource, 0, 0, 0, 0, $newwidth, $newheight, $oldwidth,
            $oldheight);

        if ($type = 2) { # jpeg
            imageinterlace($destination_resource, 1); // чересстрочное формирование изображение
            imagejpeg($destination_resource, $destination_path, $quality);
        } else { # gif, png
            $function = "image$typestr";
            $function($destination_resource, $destination_path);
        }

        imagedestroy($destination_resource);
        imagedestroy($src_resource);
    }

    function resize_image($file, $w, $h, $crop = false)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }

    //получаем обьекты для карусели
    public function getdataforcarousel()
    {
        $current_date = Carbon::now();
        $users = User::select([                 //получаем пользователей
            'id',
            'name',
            'beginvip',
            'endvip',
        ])
            //  ->where('vip','=','1')
            ->where('beginvip', '<', $current_date)
            ->where('endvip', '>', $current_date)
            ->orderBy('created_at', 'DESC')->get();
        $ankets = [];
        foreach ($users as $user) {
            $girl = Girl::select(['id', 'name', 'main_image', 'age'])->where('user_id', $user->id)->first();
            array_push($ankets, $girl);
        }

        return response()->json(['ankets' => $ankets]);
    }

    public function getDataForChangeMainImage(Request $request)
    {
        $user = Auth::user();
        $updateMainImagePrice = DB::table('prices')->where('price_name', '=', 'update_main_image')->get();

        return response()->json(['update_main_image' => $updateMainImagePrice, 'user_money' => $user->money]);
    }
}
