<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ZipFileService
{
    public function extractImages($zipFilePath, $destinationPath)
    {
        $zip = new ZipArchive;
        $imagePaths = [];

        if ($zip->open($zipFilePath) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
                    $fileContent = $zip->getFromIndex($i);
                    $tempFilePath = $destinationPath . '/' . basename($filename);
                    Storage::disk('local')->put($tempFilePath, $fileContent);
                    $imagePaths[] = $tempFilePath;
                }
            }
            $zip->close();
        } else {
            throw new \Exception('Cannot open ZIP file.');
        }

        if (empty($imagePaths)) {
            throw new \Exception('No image files found in the ZIP archive.');
        }

        return $imagePaths;
    }
}
