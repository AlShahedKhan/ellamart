<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

trait ImageUploadTrait
{
    /**
     * Handle image upload and return the image URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $fieldName
     * @param  mixed  $imageableModel
     * @param  string  $folder  The folder to store the image (e.g., 'profile_images' or 'category_images')
     * @return string|null
     */
    public function uploadImage($request, $fieldName, $imageableModel, $folder)
    {
        if ($request->hasFile($fieldName)) {
            // Store in the specified folder
            $path = $request->file($fieldName)->store($folder, 'public'); // Store the image
            $image = Image::create([
                'url' => Storage::url($path),
                'imageable_id' => $imageableModel->id,
                'imageable_type' => get_class($imageableModel), // Setting up polymorphic relation
            ]);
            return $image->url; // Return the URL of the uploaded image
        }
        return null; // No image uploaded
    }
}
