<?php 
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadService {

  /*
    DEBUT PSEUDO-CODE 
      Récupérer le fichier
      Renommer le fichier
      Déplacer le fichier
      Retourner le nom du fichier
      Supprimer le fichier
    FIN PSEUDO-CODE
  */



  public function __construct(private readonly ParameterBagInterface $param)
  {
  }

  public function uploadFile(UploadedFile $file): string
  {
      try {
          // $orignalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
          $fileName = uniqid('file-') . '.' . $file->guessExtension();
          $file->move(
            $this->param->get('uploads_directory'),
            $fileName);

          return $fileName;
      } catch (\Exception $e) {
          throw new \Exception('An error occured while uploading the image: ' . $e->getMessage());
      }
  }

  public function deleteImage(string $fileName): void
    {
        if ($fileName !== 'default_image') {return;}
        try {
            $filePath = $this->param->get('uploads_images_directory') . '/' . $fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while deleting the image: ' . $e->getMessage());
        }
    }
}