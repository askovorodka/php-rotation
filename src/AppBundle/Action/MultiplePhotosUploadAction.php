<?php

namespace AppBundle\Action;

use AppBundle\Dto\Upload\UploadDto;
use AppBundle\Entity\Hotel;
use AppBundle\Entity\HotelPhoto;
use AppBundle\Entity\PresetPhoto;
use AppBundle\Entity\Room;
use AppBundle\Entity\RoomPhoto;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Загрузка фотографии для hotel и room
 *
 * @package AppBundle\Action
 */
class MultiplePhotosUploadAction extends AbstractPhotoUploadAction
{
    /**
     * Ресурсы, для которых доступна загрузка фотографий
     */
    const RESOURCES_MAP = [
        'hotels' => [
            'entityClass' => Hotel::class,
            'subResources' => [
                'hotel-photos' => [
                    'entityClass' => HotelPhoto::class,
                ]
            ],
        ],
        'rooms' => [
            'entityClass' => Room::class,
            'subResources' => [
                'room-photos' => [
                    'entityClass' => RoomPhoto::class,
                ]
            ],
        ]
    ];

    /**
     * @Route(
     *     name="multiple_photos_upload",
     *     path="/api/v3/{resourceName}/{id}/{subResourceName}/upload",
     *     requirements={
     *         "id"= "\d+",
     *         "resourceName"="hotels|rooms",
     *         "subResourceName"="hotel-photos|room-photos",
     *     }
     * )
     *
     * @Method({"POST"})
     */
    public function __invoke(Request $request, $id, $resourceName, $subResourceName)
    {
        if (!(
            array_key_exists($resourceName, self::RESOURCES_MAP)
            && isset(self::RESOURCES_MAP[$resourceName]['subResources'][$subResourceName])
        )) {
            throw new NotFoundHttpException("files upload for `$resourceName`/`$subResourceName` is not supported");
        }

        $resourceDefinition = self::RESOURCES_MAP[$resourceName];
        $resourceEntityClass = $resourceDefinition['entityClass'];
        $subResourceEntityClass = $resourceDefinition['subResources'][$subResourceName]['entityClass'];

        $this->modifyRequest($request, $subResourceEntityClass);

        $resourceEntityName = explode('\\', $resourceEntityClass);
        $resourceEntityName = ucfirst(end($resourceEntityName));
        $setResourceEntityMethodName = sprintf('set%s', $resourceEntityName);

        $em = $this->registry->getManager();
        $entityRepository = $em->getRepository($resourceEntityClass);

        /** @var Hotel $resourceEntity */
        if (!($resourceEntity = $entityRepository->find($id))) {
            throw new NotFoundHttpException("resource `$resourceName` with `id` = $id not found");
        }

        $result = new ArrayCollection();
        /** @var UploadDto $uploadDto */
        foreach ($this->getUploadData($request) as $key => $uploadDto) {
            $photo = $this->uploadPhoto($uploadDto);
            $em->persist($photo);

            /** @var HotelPhoto|RoomPhoto|PresetPhoto $subResourceEntity */
            $subResourceEntity = new $subResourceEntityClass();
            $subResourceEntity->setPhoto($photo);
            $subResourceEntity->setAreaWidth($uploadDto->getAreaWidth());
            $subResourceEntity->setAreaHeight($uploadDto->getAreaHeight());
            $subResourceEntity->setOffsetTop($uploadDto->getTop());
            $subResourceEntity->setOffsetLeft($uploadDto->getLeft());
            $subResourceEntity->$setResourceEntityMethodName($resourceEntity);
            $subResourceEntity->setAlt($uploadDto->getAlt());
            $subResourceEntity->setTitle($uploadDto->getTitle());
            $subResourceEntity->setDescription($uploadDto->getDescription());
            $subResourceEntity->setListOrder($uploadDto->getListOrder());

            $em->persist($subResourceEntity);

            $result->add($subResourceEntity);
        }

        $em->flush();

        return $result;
    }

    /**
     * Получение данных для загрузки изображений
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getUploadData(Request $request)
    {
        $alts = $request->get('alt');
        $titles = $request->get('title');
        $descriptions = $request->get('description');
        $listOrders = $request->get('listOrder');
        $width = $request->get('width');
        $height = $request->get('height');
        $areaWidth = $request->get('areaWidth');
        $areaHeight = $request->get('areaHeight');
        $top = $request->get('top');
        $left = $request->get('left');
        /** @var UploadedFile $uploadedFiles */
        $uploadedFiles = $request->files->get('file');

        $parameters = [
            'alt' => $alts,
            'title' => $titles,
            'description' => $descriptions,
            'listOrder' => $listOrders,
            'file' => $uploadedFiles,
            'width' => $width,
            'height' => $height,
            'areaWidth' => $areaWidth,
            'areaHeight' => $areaHeight,
            'top' => $top,
            'left' => $left,
        ];

        $lastCount = null;
        foreach ($parameters as $key => $array) {
            if (!(is_array($array) && count($array))) {
                throw new \InvalidArgumentException("{$key}[] array must be set and not empty");
            }
            if ($lastCount !== null && count($array) !== $lastCount) {
                throw new \InvalidArgumentException('alt[], title[], description[], listOrder[], width[], height[], areaWidth[], areaHeight[], top[], left[] and file[] arrays must have same dimensions');
            }
            $lastCount = count($array);
        }

        $data = [];
        foreach ($uploadedFiles as $key => $uploadedFile) {
            $data[] = new UploadDto($uploadedFile, (string)$alts[$key], (string)$titles[$key],
                (string)$descriptions[$key], (int)$listOrders[$key], (int)$width[$key], (int)$height[$key],
                (int)$areaWidth[$key], (int)$areaHeight[$key], (int)$top[$key], (int)$left[$key]);
        }
        return $data;
    }
}
