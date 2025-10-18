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

    public function upload(UploadedFile $file, string $dir, string | null $filename = null) : string
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

    public function remove(string|null $filepath): void
    {
        if ($filepath == null) {
            return;
        }

        [$dir, $filename] = array_slice(explode("/",$filepath), -2, 2);
        $absolute_filepath = join("/",[$this->baseTargetDir,$dir,$filename]);

        if (file_exists($absolute_filepath)) {
            try {
                unlink($absolute_filepath);
            } catch (\Exception $e) {
                throw new \RuntimeException("Erreur lors de la suppression du fichier: " . $e->getMessage());
            }
        } else {
            throw new \RuntimeException("Le fichier $absolute_filepath n'existe pas.");
        }
    }
}