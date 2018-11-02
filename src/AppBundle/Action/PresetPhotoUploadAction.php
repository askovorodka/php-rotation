<?php

namespace AppBundle\Action;

use AppBundle\Dto\Upload\UploadDto;
use AppBundle\Entity\Preset;
use AppBundle\Entity\PresetPhoto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Загрузка фотографии подборки
 *
 * @package AppBundle\Action
 */
class PresetPhotoUploadAction extends AbstractPhotoUploadAction
{
    /**
     * @Route(
     *     name="preset_photo_upload",
     *     path="/api/v3/presets/{id}/preset-photo/upload",
     *     requirements={
     *         "id"= "\d+"
     *     }
     * )
     *
     * @Method({"POST"})
     */
    public function __invoke(Request $request, $id)
    {
        $this->modifyRequest($request, PresetPhoto::class);

        $em = $this->registry->getManager();
        $presetRepository = $em->getRepository(Preset::class);

        /** @var Preset $preset */
        if (!($preset = $presetRepository->find($id))) {
            throw new NotFoundHttpException("resource `preset` with `id` = $id not found");
        }

        $uploadDto = $this->getUploadData($request);

        $photo = $this->uploadPhoto($uploadDto);
        $em->persist($photo);

        // В случае наличия сохраненного изображение делаем обновление изображения
        if (!($presetPhoto = $em->getRepository(PresetPhoto::class)->findOneBy(['preset' => $preset]))) {
            $presetPhoto = new PresetPhoto();
        }
        $presetPhoto->setPreset($preset);
        $presetPhoto->setPhoto($photo);
        $presetPhoto->setAlt($uploadDto->getAlt());
        $presetPhoto->setTitle($uploadDto->getTitle());
        $presetPhoto->setDescription($uploadDto->getDescription());
        $em->persist($presetPhoto);

        $em->flush();

        return $presetPhoto;
    }

    /**
     * @param Request $request
     *
     * @return UploadDto
     */
    protected function getUploadData(Request $request)
    {
        $alts = $request->get('alt');
        $titles = $request->get('title');
        $descriptions = $request->get('description');
        /** @var UploadedFile $uploadedFiles */
        $uploadedFiles = $request->files->get('file');

        $parameters = [
            'alt' => $alts,
            'title' => $titles,
            'description' => $descriptions,
            'file' => $uploadedFiles,
        ];

        $lastCount = null;
        foreach ($parameters as $key => $array) {
            if (!(is_array($array) && count($array))) {
                throw new \InvalidArgumentException("{$key}[] array must be set and not empty");
            }
            if (count($array) !== 1) {
                throw new \InvalidArgumentException('alt[], title[], description[], listOrder[] and file[] must by arrays with size 1');
            }
        }

        return new UploadDto($uploadedFiles[0], (string)$alts[0], (string)$titles[0], (string)$descriptions[0]);
    }
}
