<?php

namespace App\Http\Util;
use Illuminate\Support\Facades\Storage;

class ImageSaverUtil
{
  public static function save($folder_name, $file): string
  {
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
    $path = $file->storeAs($folder_name, $filename, 'public');
    return $path;
  }
  public static function delete($path): void
  {
    if ($path && Storage::disk('public')->exists($path)) {
      Storage::disk('public')->delete($path);
    }
  }
  public static function update($path, $folder_name, $file): string
  {
    ImageSaverUtil::delete($path);
    return ImageSaverUtil::save($folder_name, $file);
  }
}
