<?php

namespace AppBundle\Action;

use AppBundle\Dto\Upload\UploadDto;
use AppBundle\Dto\Upload\UploadIconDto;
use AppBundle\Entity\Amenity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;

/**
 * Загрузка иконки аменитиса
 *
 * @package AppBundle\Action
 */
class AmenitiesIconUploadAction extends AbstractPhotoUploadAction
{
    /**
     * @Route(
     *     name="amenities_icon_upload",
     *     path="/api/v3/amenities/{id}/icon/upload",
     *     requirements={
     *         "id"= "\d+"
     *     }
     * )
     *
     * @Method({"POST"})
     * @param Request $request
     * @param $id
     * @return Amenity|null|object
     */
    public function __invoke(Request $request, $id)
    {
        $this->modifyRequest($request, Amenity::class);

        $em = $this->registry->getManager();
        $amenityRepository = $em->getRepository(Amenity::class);

        /** @var Amenity $preset */
        if (!($amenity = $amenityRepository->find($id))) {
            throw new NotFoundHttpException("resource `amenity` with `id` = $id not found");
        }

        /** initialize Gedmo/Slug generate sysname from title */
        if (!$amenity->getSysname()) {
            $amenity->setSysname(null);
            $em->persist($amenity);
            $em->flush();
        }

        $amenity->setIcon(
            $this->uploadIcon(
                $this->getUploadData($request)->getUploadedFile(),
                $amenity->getSysname()
            )
        );

        $em->persist($amenity);
        $em->flush();

        return $amenity;
    }

    /**
     * @param Request $request
     *
     * @return UploadIconDto
     */
    protected function getUploadData(Request $request)
    {
        /** @var UploadedFile $uploadedFiles */
        $uploadedFiles = $request->files->get('file');

        $parameters = [
            'file' => $uploadedFiles,
        ];

        foreach ($parameters as $key => $array) {
            if (!(is_array($array) && count($array))) {
                throw new \InvalidArgumentException("{$key}[] array must be set and not empty");
            }
        }

        return new UploadIconDto($uploadedFiles[0]);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $sysname
     * @return string
     */
    protected function uploadIcon(UploadedFile $uploadedFile, $sysname)
    {
        $constraints = $this->validator->validate($uploadedFile, null, ['upload']);
        if (0 !== count($constraints)) {
            throw new ValidationException($constraints);
        }

        $uploadsDirectory = $this->container->getParameter('uploads_fonts_directory');

        $savedFileName = "{$sysname}.{$uploadedFile->guessExtension()}";
        $uploadedFile->move($uploadsDirectory, $savedFileName);

        return $savedFileName;
    }
}
