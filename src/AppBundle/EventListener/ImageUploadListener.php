<?php
namespace AppBundle\EventListener;


use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Observation;
use AppBundle\Services\FileUploader;
use Symfony\Component\HttpFoundation\File\File;

class ImageUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Observation) {
            return;
        }
        if ($fileName = $entity->getImage()) {
            $entity->setImage(new File($this->uploader->getTargetDir().'/'.$fileName));
        }
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Observation) {
            return;
        }
        $file = $entity->getImage();
        // only upload new files
        if (!$file instanceof UploadedFile) {
            return;
        }
        $fileName = $this->uploader->upload($file);
        $entity->setImage($fileName);
    }
}