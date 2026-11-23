<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class ProductImageService {
 public function store(UploadedFile $file): string {
  try {
   $manager = new ImageManager(new Driver(), decodeAnimation: false);
   $bytes = (string) $manager->read($file->getRealPath())->cover(800,800)->toJpeg(85);
  } catch (\Throwable $e) {
   report($e);
   throw ValidationException::withMessages(['image'=>'The image could not be prepared. Try another JPEG or PNG; the server also needs GD enabled.']);
  }
  $path = 'products/'.Str::uuid().'.jpg';
  try {
   if (!Storage::disk('public')->put($path, $bytes)) { throw new \RuntimeException('Product image write failed.'); }
  } catch (\Throwable $e) {
   report($e);
   throw ValidationException::withMessages(['image'=>'The image could not be saved. Please try again later.']);
  }
  return $path;
 }
}
