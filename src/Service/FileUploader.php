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

    public function upload(UploadedFile $file, string $dir, string $filename = null) : string
    {
        if ($filename == null){
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        $safeFilename = $this->slugger->slug($filename);
        $filename = $safeFilename . "-" . uniqid() . "." . $file->guessExtension();

        try {
            $file->move($this->baseTargetDir . "/" . $dir, $filename);
        } catch (FileException $e) {
            return "";
        }

        return "uploads/" . $dir . "/" . $filename;
    }

    public function remove(string $dir, string $filename): void
    {
        if (file_exists($filename)) {
            try {
                unlink($filename);
            } catch (\Exception $e) {
                throw new \RuntimeException("Erreur lors de la suppression du fichier: " . $e->getMessage());
            }
        } else {
            throw new \RuntimeException("Le fichier $filename n'existe pas.");
        }
    }
}