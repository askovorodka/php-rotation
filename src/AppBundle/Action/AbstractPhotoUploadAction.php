<?php

namespace AppBundle\Action;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use AppBundle\Dto\Upload\UploadDto;
use AppBundle\Entity\Photo;
use AppBundle\Entity\PhotoCategory;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Общая логика загрузки фотографий
 *
 * @package AppBundle\Action
 */
abstract class AbstractPhotoUploadAction
{
    /** @var ValidatorInterface */
    protected $validator;
    /** @var RegistryInterface */
    protected $registry;
    /** @var ContainerInterface */
    protected $container;

    /**
     * UploadAction constructor.
     * @param RegistryInterface $registry
     * @param ValidatorInterface $validator
     * @param ContainerInterface $container
     */
    public function __construct(
        RegistryInterface $registry,
        ValidatorInterface $validator,
        ContainerInterface $container
    ) {
        $this->registry = $registry;
        $this->validator = $validator;
        $this->container = $container;
    }

    /**
     * Настройка Request для правильной работы api-platorm
     *
     * @param Request $request
     * @param $resourceEntityClass
     */
    protected function modifyRequest(Request $request, $resourceEntityClass)
    {
        $request->setRequestFormat('jsonhal');
        $request->setFormat('jsonhal', 'application/hal+json');
        // Отсутствие этих параметров в $_REQUEST приводит к нерабоспособности соответствующих api-методов
        $request->attributes->set('_api_collection_operation_name', 'photo_upload');
        $request->attributes->set('_api_resource_class', $resourceEntityClass);
    }

    /**
     * @param UploadDto $uploadDto
     * @return Photo
     */
    protected function uploadPhoto(UploadDto $uploadDto)
    {
        $constraints = $this->validator->validate($uploadDto->getUploadedFile(), null, ['upload']);
        if (0 !== count($constraints)) {
            throw new ValidationException($constraints);
        }

        $uploadsDirectory = $this->container->getParameter('uploads_directory');
        /** @var PhotoCategory $photoCategory */
        $photoCategory = $this->registry->getManager()->getRepository(PhotoCategory::class)->find(1);

        $savedFileName = md5(uniqid()) . '.' . $uploadDto->getUploadedFile()->guessExtension();
        $uploadDto->getUploadedFile()->move($uploadsDirectory, $savedFileName);

        $photo = new Photo();
        $photo->setWidth($uploadDto->getWidth());
        $photo->setHeight($uploadDto->getHeight());
        $photo->setPhoto($savedFileName);
        $photo->setTitle($uploadDto->getUploadedFile()->getClientOriginalName());
        $photo->setPhotoCategory($photoCategory);

        return $photo;
    }
}
