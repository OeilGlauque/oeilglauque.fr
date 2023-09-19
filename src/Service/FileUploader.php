<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader 
{
    public function __construct(
        private string $baseTargetDir,
        private SluggerInterface $slugger
    ){}

    public function upload(UploadedFile $file, string $dir) : string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $filename = $safeFilename . "-" . uniqid() . "." . $file->guessExtension();

        try {
            $file->move($this->baseTargetDir . "/" . $dir, $filename);
        } catch (FileException $e) {
            dd($e);
            return "";
        }

        return "uploads/" . $dir . "/" . $filename;
    }
}