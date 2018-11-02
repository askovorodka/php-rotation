<?php

namespace AppBundle\Dto\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * DTO, предоставляющий данные для загрузки фотографий
 *
 * @package AppBundle\Dto\Upload
 */
class UploadDto
{
    /** @var UploadedFile */
    protected $uploadedFile;
    /** @var string|null */
    protected $alt;
    /** @var string|null */
    protected $title;
    /** @var string|null */
    protected $description;
    /** @var int|null */
    protected $listOrder;
    /** @var int|null */
    protected $width;
    /** @var int|null */
    protected $height;
    /** @var int|null */
    protected $areaWidth;
    /** @var int|null */
    protected $areaHeight;
    /** @var int|null */
    protected $top;
    /** @var int|null */
    protected $left;

    /**
     * UploadDto constructor.
     *
     * @param UploadedFile $uploadedFile
     * @param string|null $alt
     * @param string|null $title
     * @param string|null $description
     * @param int|null $listOrder
     * @param int|null $width
     * @param int|null $height
     * @param int|null $areaWidth
     * @param int|null $areaHeight
     * @param int|null $top
     * @param int|null $left
     */
    public function __construct(
        UploadedFile $uploadedFile,
        ?string $alt,
        ?string $title,
        ?string $description,
        ?int $listOrder,
        ?int $width,
        ?int $height,
        ?int $areaWidth,
        ?int $areaHeight,
        ?int $top,
        ?int $left
    ) {
        $this->uploadedFile = $uploadedFile;
        $this->alt = $alt;
        $this->title = $title;
        $this->description = $description;
        $this->listOrder = $listOrder;
        $this->width = $width;
        $this->height = $height;
        $this->areaWidth = $areaWidth;
        $this->areaHeight = $areaHeight;
        $this->top = $top;
        $this->left = $left;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    /**
     * @return null|string
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getListOrder(): ?int
    {
        return $this->listOrder;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @return int|null
     */
    public function getAreaWidth(): ?int
    {
        return $this->areaWidth;
    }

    /**
     * @return int|null
     */
    public function getAreaHeight(): ?int
    {
        return $this->areaHeight;
    }

    /**
     * @return int|null
     */
    public function getTop(): ?int
    {
        return $this->top;
    }

    /**
     * @return int|null
     */
    public function getLeft(): ?int
    {
        return $this->left;
    }
}
