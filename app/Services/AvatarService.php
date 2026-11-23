<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class AvatarService {
 public function store(UploadedFile $file): string {
  try {
   $manager = new ImageManager(new Driver(), decodeAnimation: false);
   $bytes = (string) $manager->read($file->getRealPath())->cover(256,256)->toJpeg(85);
  } catch (\Throwable $e) {
   report($e);
   throw ValidationException::withMessages(['avatar'=>'The image could not be prepared. Try another JPEG or PNG; the server also needs GD enabled.']);
  }
  $path = 'avatars/'.Str::uuid().'.jpg';
  try {
   if (!Storage::disk('public')->put($path, $bytes)) { throw new \RuntimeException('Avatar write failed.'); }
  } catch (\Throwable $e) {
   report($e);
   throw ValidationException::withMessages(['avatar'=>'The image could not be saved. Please try again later.']);
  }
  return $path;
 }
}
