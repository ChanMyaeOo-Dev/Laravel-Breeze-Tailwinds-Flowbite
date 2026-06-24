<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk to store uploaded images on. Defaults to the
    | FILESYSTEM_DISK environment variable or "s3".
    |
    */

    'disk' => env('IMAGE_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Max Width
    |--------------------------------------------------------------------------
    |
    | Maximum width in pixels for optimized images. Images wider than this
    | will be scaled down proportionally.
    |
    */

    'max_width' => (int) env('IMAGE_MAX_WIDTH', 1200),

    /*
    |--------------------------------------------------------------------------
    | Quality
    |--------------------------------------------------------------------------
    |
    | Compression quality for optimized images (1-100). Lower values produce
    | smaller files but reduce visual quality.
    |
    */

    'quality' => (int) env('IMAGE_QUALITY', 80),

    /*
    |--------------------------------------------------------------------------
    | Max File Size
    |--------------------------------------------------------------------------
    |
    | Maximum allowed file size in megabytes for uploaded images.
    |
    */

    'max_file_size' => (int) env('IMAGE_MAX_FILE_SIZE', 5),

];
