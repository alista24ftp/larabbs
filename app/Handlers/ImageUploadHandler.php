<?php
namespace App\Handlers;

use Image;

class ImageUploadHandler
{
    // only allow image files with the following extensions
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    public function save($file, $folder, $file_prefix, $max_width=false)
    {
        // create saved directory pattern, such as: uploads/images/avatars/201709/21/
        // directory name separation allows for faster searching
        $folder_name = "uploads/images/$folder/" . date("Ym/d", time());

        // absolute upload path, `public_path()` gets `public` directory's absolute path
        // eg. /home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21/
        $upload_path = public_path() . '/' . $folder_name;

        // file extension (since images copy-paste from clipboard might remove extension)
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // concat filename (adding prefix to improve readability, prefix can correspond to model ID)
        // eg. 1_1493521050_7BVc9v9ujP.png
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        // Abort if uploaded file is not an image (extension not a part of allowed_ext)
        if(!in_array($extension, $this->allowed_ext)){
            return false;
        }

        // Move image to target location path
        $file->move($upload_path, $filename);

        // If there are image dimension restrictions in place, crop image
        if($max_width && $extension != 'gif'){
            // class function for cropping images
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name/$filename"
        ];
    }

    public function reduceSize($file_path, $max_width)
    {
        // Instantiate image, param is file's absolute path on disk
        $image = Image::make($file_path);

        // Resize image
        $image->resize($max_width, null, function($constraint){
            // set width to be $max_width, and aspect ratio
            $constraint->aspectRatio();

            // prevent increase in image dimensions after cropping
            $constraint->upsize();
        });

        // save image after editing
        $image->save();
    }
}
