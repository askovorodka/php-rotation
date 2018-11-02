<?php

namespace AppBundle\Dto\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * DTO, предоставляющий данные для загрузки иконки
 *
 * @package AppBundle\Dto\Upload
 */
class UploadIconDto
{
    /** @var UploadedFile */
    protected $uploadedFile;


    /**
     * UploadDto constructor.
     *
     * @param UploadedFile $uploadedFile
     */
    public function __construct(
        UploadedFile $uploadedFile
    ) {
        $this->uploadedFile = $uploadedFile;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }
}
