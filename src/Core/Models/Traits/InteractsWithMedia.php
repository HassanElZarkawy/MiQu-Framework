<?php /** @noinspection PhpUnused */

namespace Miqu\Core\Models\Traits;

use Exception;
use iamdual\Uploader;
use Illuminate\Database\Eloquent\Model;
use Miqu\Core\Models\Image;

trait InteractsWithMedia
{
    /**
     * @var Model
     */
    private $profile_image;

    /**
     * @var array
     */
    private $gallery_images;

    private $media_allowed_extensions = [
        'png', 'jpeg', 'jpg', 'gif'
    ];

    /**
     * @param bool $force_refresh
     * @return Model|null
     * @throws Exception
     */
    public function profileImage(bool $force_refresh = false) : ?Image
    {
        if ( $this->profile_image && ! $force_refresh )
            return $this->profile_image;

        return $this->profile_image = $this->baseQuery('profile')->first();
    }

    /**
     * @param bool $force_refresh
     * @return string|null
     * @throws Exception
     */
    public function profileImageUrl(bool $force_refresh = false) : ?string
    {
        $image = $this->profileImage($force_refresh);
        if ( $image )
            return url( $image->path );
        return null;
    }

    /**
     * @param bool $force_refresh
     * @return array|null
     * @throws Exception
     */
    public function gallery(bool $force_refresh = false) : ?array
    {
        if ( $this->gallery_images && ! $force_refresh )
            return $this->gallery_images;

        return $this->gallery_images = $this->baseQuery('gallery')->get();
    }

    /**
     * Checks if the current object has a profile image
     * @param bool $force_refresh
     * @return bool
     * @throws Exception
     */
    public function hasProfileImage(bool $force_refresh = false) : bool
    {
        if ( $this->profile_image )
            return true;

        return $this->profileImage($force_refresh) !== null;
    }

    /**
     * checks if the current object has gallery items
     * @param bool $force_refresh
     * @return bool
     * @throws Exception
     */
    public function hasGallery(bool $force_refresh = false) : bool
    {
        if ( $this->gallery_images && ! $force_refresh )
            return true;

        return $this->gallery() !== null;
    }

    /**
     * Upload a file/files to the object library
     * @param string $type
     * @param string $field
     * @return bool
     * @throws Exception
     */
    public function upload(string $type, string $field) : bool
    {
        $uploaded_files = $this->startUploading($field);
        collect($uploaded_files)
            ->take( $type === 'profile' ? 1 : PHP_INT_MAX )
            ->each(
                function($path) use ($type) {
                    Image::create([
                        'object_type' => get_class($this),
                        'object_id' => $this->{$this->primaryKey},
                        'type' => $type,
                        'path' => $path
                    ]);
                }
            );
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function clearGallery() : bool
    {
        return $this->baseQuery('gallery')->delete();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function removeProfileImage() : bool
    {
        return $this->baseQuery('profile')->delete();
    }

    /**
     * @param string $type
     * @return Model
     * @throws Exception
     */
    private function baseQuery( string $type ) : Model
    {
        return Image::where('object_id', $this->{$this->primaryKey})
            ->where('object_type', get_class($this))
            ->where('type', $type);
    }

    /**
     * @param string $name
     * @return array|null
     */
    private function startUploading(string $name): ?array
    {
        if ( ! isset( $_FILES[ $name ] ) )
            return null;

        $data = $this->getFormattedUploadData($name);

        return collect($data)->map(function($item) {
            return $this->uploadFile($item);
        })->reject(function($item) {
            return $item === null;
        })->map(function($item) {
            return str_replace( BASE_DIRECTORY, '', $item );
        })->all();
    }

    /**
     * @param string $name
     * @return array
     */
    private function getFormattedUploadData(string $name) : array
    {
        $upload_data = $_FILES[ $name ];

        if ( isset( $upload_data[ 'tmp_name' ] ) )
            $upload_data = [ $upload_data ];

        return $upload_data;
    }

    /**
     * @param array $file_upload
     * @return string|null
     */
    private function uploadFile(array $file_upload ) : ?string
    {
        $uploader = $this->getUploader( $file_upload );
        if ( ! $uploader->upload() )
            return null;

        return $uploader->get_path();
    }

    /**
     * @param array $file
     * @return Uploader
     */
    private function getUploader(array $file): Uploader
    {
        $path = (string)string(BASE_DIRECTORY)->trimRight('/')->append(DIRECTORY_SEPARATOR)
            ->append(\Miqu\Helpers\env('storage.folder'))->replace('/', DIRECTORY_SEPARATOR)
            ->trimRight(DIRECTORY_SEPARATOR);

        if ( ! storage()->isDirectory( $path ) )
            mkdir( $path, 0777, true );

        return (new Uploader( $file ))->allowed_extensions( $this->media_allowed_extensions )
            ->max_size(100)
            ->path( $path );
    }
}