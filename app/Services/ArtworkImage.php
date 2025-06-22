<?php

namespace App\Services;

use Core\Constants\Constants;
use Core\Database\ActiveRecord\Model;
use Error;
use PhpParser\Node\Stmt\Return_;
use App\Models\Artwork;

class ArtworkImage
{
    /** @var array<string, mixed> */
    private array $image;

    /** @param array<string, mixed> $validations */
    public function __construct(
        private Artwork $model,
        private array $validations = []
    ) {}

    // RETORNA A PASTA AONDE ESTA A IMAGEM, SE NAO TIVER RETORNA A IMG DEFAULT
    public function path(): string
    {
        if ($this->model->image_url) {
            $hash = md5_file($this->getAbsoluteSavedFilePath());
            return $this->baseDir() . $this->model->image_url . '?' . $hash;
        }

        return "/assets/images/defaults/artwork.png";
    }
    /** @param array<string, mixed> $image */
    public function update(array $image): bool
    {
        $this->image = $image;

        if (!$this->isValidImage()) {
            return false;
        }

        if ($this->updateFile()) {
            $this->model->update([
                'image_url' => $this->getFileName(),
            ]);

            return true;
        }

        return false;
    }

    protected function updateFile(): bool
    {
        if (empty($this->getTmpFilePath())) {
            return false;
        }

        $this->removeOldImage();

        $resp = move_uploaded_file(
            $this->getTmpFilePath(),
            $this->getAbsoluteDestinationPath()
        );

        if (!$resp) {
            $error = error_get_last();
            throw new \RuntimeException(
                'Failed to move uploaded file:' . ($error['message'] ?? 'Unknown error')
            );
        }

        return true;
    }

    private function getFileName(): string
    {
        $file_name_splitted = explode('.', $this->image['name']);
        $file_extension = end($file_name_splitted);

        return 'artwork.' . $file_extension;
    }

    private function baseDir(): string
    {
        return "/assets/uploads/artworks/{$this->model->id}/";
    }

    private function getTmpFilePath(): string
    {
        return $this->image['tmp_name'];
    }

    public function removeOldImage(): void
    {
        if ($this->model->image_url) {
            unlink($this->getAbsoluteSavedFilePath());
        }
    }

    private function getAbsoluteSavedFilePath(): string
    {
        return Constants::rootPath()->join('public' . $this->baseDir())->join($this->model->image_url);
    }

    private function isValidImage(): bool
    {
        if (isset($this->validations['extension'])) {
            $this->validateImageExtension();
        }

        if (isset($this->validations['size'])) {
            $this->validateImageSize();
        }

        return $this->model->errors('image_url') === null;
    }

    private function validateImageSize(): void
    {
        if ($this->image['size'] > $this->validations['size']) {
            $this->model->addError('image_url', 'Tamanho do arquivo invalido');
        }
    }

    private function validateImageExtension(): void
    {
        $file_name_splitted = explode('.', $this->image['name']);
        $file_extension = end($file_name_splitted);

        if (!in_array($file_extension, $this->validations['extension'])) {
            $this->model->addError('image_url', 'Extensão de arquivo invalida');
        }
    }

    private function storeDir(): string
    {
        $path = Constants::rootPath()->join('public' . $this->baseDir());

        if (!is_dir($path)) {
            mkdir(directory: $path, recursive: true);
        }

        return $path;
    }

    private function getAbsoluteDestinationPath(): string
    {
        return $this->storeDir() . $this->getFileName();
    }
}
