<?php

/**
 * An extension of the data extension class applied to images to allow uploading to gallery albums.
 */
class GalleryImageExtension extends DataExtension
{
    private static $has_one = array(
        'GalleryAlbum' => 'GalleryAlbum'
    );
}
