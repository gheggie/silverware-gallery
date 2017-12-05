<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Gallery\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-gallery
 */

namespace SilverWare\Gallery\Pages;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverWare\Components\BaseListComponent;
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Extensions\Model\DetailFieldsExtension;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Lists\ListSource;
use SilverWare\Masonry\Components\MasonryComponent;
use Page;

/**
 * An extension of the page class for a gallery album.
 *
 * @package SilverWare\Gallery\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-gallery
 */
class GalleryAlbum extends Page implements ListSource
{
    /**
     * Define cover constants.
     */
    const COVER_AUTO   = 'auto';
    const COVER_FIRST  = 'first';
    const COVER_RANDOM = 'random';
    const COVER_LATEST = 'latest';
    
    /**
     * Define sort constants.
     */
    const SORT_ORDER  = 'order';
    const SORT_ALPHA  = 'alpha';
    const SORT_LATEST = 'latest';
    const SORT_RANDOM = 'random';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Gallery Album';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Gallery Albums';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An album within a gallery which holds a series of images';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/gallery: admin/client/dist/images/icons/GalleryAlbum.png';
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = GalleryImage::class;
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'CoverMode' => 'Varchar(8)',
        'SortImagesBy' => 'Varchar(8)',
        'ImageLinksTo' => 'Varchar(8)',
        'HideAlbumDate' => 'Boolean',
        'HideImageDate' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideAlbumDate' => 0,
        'HideImageDate' => 0,
        'ListInherit' => 1,
        'HideFromMainMenu' => 1,
        'CoverMode' => self::COVER_AUTO,
        'SortImagesBy' => self::SORT_ORDER
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        GalleryImage::class
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListViewExtension::class,
        DetailFieldsExtension::class,
        ImageDefaultsExtension::class
    ];
    
    /**
     * Defines the meta field configuration for the object.
     *
     * @var array
     * @config
     */
    private static $meta_fields = [
        'Image' => [
            'params' => [
                'mode' => 'simple'
            ]
        ],
        'Summary' => false
    ];
    
    /**
     * Defines the detail fields to show for the object.
     *
     * @var array
     * @config
     */
    private static $detail_fields = [
        'date' => [
            'name' => 'Date',
            'icon' => 'calendar',
            'text' => '$MetaDateFormatted',
            'show' => 'DateShown'
        ],
        'album' => [
            'name' => 'Images',
            'icon' => 'picture-o ',
            'text' => '$NumberOfImages'
        ]
    ];
    
    /**
     * Defines the format for the meta date field.
     *
     * @var string
     * @config
     */
    private static $meta_date_format = 'd MMMM Y';
    
    /**
     * Defines the setting for showing the detail fields inline.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_inline = true;
    
    /**
     * Defines the setting for hiding the detail fields header.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_hide_header = true;
    
    /**
     * Defines the setting for hiding the detail field names.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_hide_names = true;
    
    /**
     * Defines the list component class to use.
     *
     * @var string
     * @config
     */
    private static $list_component_class = MasonryComponent::class;
    
    /**
     * Defines the mode for image links within the album.
     *
     * @var string
     * @config
     */
    private static $image_links_to = BaseListComponent::IMAGE_LINK_FILE;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Remove Unnecessary Fields:
        
        $fields->removeByName('ListObject[ImageLinksTo]');
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'GalleryAlbumOptions',
                    $this->fieldLabel('GalleryAlbum'),
                    [
                        DropdownField::create(
                            'CoverMode',
                            $this->fieldLabel('CoverMode'),
                            $this->getCoverModeOptions()
                        ),
                        DropdownField::create(
                            'SortImagesBy',
                            $this->fieldLabel('SortImagesBy'),
                            $this->getSortImagesByOptions()
                        ),
                        DropdownField::create(
                            'ImageLinksTo',
                            $this->fieldLabel('ImageLinksTo'),
                            BaseListComponent::singleton()->getImageLinksToOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        CheckboxField::create(
                            'HideAlbumDate',
                            $this->fieldLabel('HideAlbumDate')
                        ),
                        CheckboxField::create(
                            'HideImageDate',
                            $this->fieldLabel('HideImageDate')
                        )
                    ]
                )
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['CoverMode'] = _t(__CLASS__ . '.COVERMODE', 'Cover mode');
        $labels['GalleryAlbum'] = _t(__CLASS__ . '.GALLERYALBUM', 'Gallery Album');
        $labels['SortImagesBy'] = _t(__CLASS__ . '.SORTIMAGESBY', 'Sort images by');
        $labels['HideAlbumDate'] = _t(__CLASS__ . '.HIDEALBUMDATE', 'Hide album date');
        $labels['HideImageDate'] = _t(__CLASS__ . '.HIDEIMAGEDATES', 'Hide image dates');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->ImageLinksTo = $this->config()->image_links_to;
    }
    
    /**
     * Answers the meta title for the object.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        if ($this->getGallery()->ShowImageCounts) {
            return sprintf('%s (%d)', $this->Title, $this->getImageCount());
        }
        
        return $this->Title;
    }
    
    /**
     * Answers the meta image for the object.
     *
     * @return Image
     */
    public function getMetaImage()
    {
        // Initialise:
        
        $image = false;
        
        // Determine Cover Mode:
        
        switch ($this->CoverMode) {
            
            case self::COVER_FIRST:
                
                // First Image Mode:
                
                $image = $this->getFirstImage();
                
                break;
                
            case self::COVER_LATEST:
                
                // Latest Image Mode:
                
                $image = $this->getLatestImage();
                
                break;
                
            case self::COVER_RANDOM:
                
                // Random Image Mode:
                
                $image = $this->getRandomImage();
                
                break;
                
            default:
                
                // Auto Image Mode (album meta image or first child image):
                
                if (($file = parent::getMetaImage()) && $file->exists()) {
                    return $file;
                }
                
                $image = $this->getFirstImage();
                
        }
        
        // Answer Image File:
        
        if ($image) {
            return $image->getMetaImage();
        }
    }
    
    /**
     * Answers the name of the asset folder used for uploading images.
     *
     * @return string
     */
    public function getMetaImageFolder()
    {
        if ($this->URLSegment != $this->getDefaultURLSegment()) {
            
            return sprintf(
                '%s/%s',
                $this->getGallery()->getMetaImageFolder(),
                $this->getFolderName()
            );
            
        }
        
        return $this->getGallery()->getMetaImageFolder();
    }
    
    /**
     * Answers the asset folder name for the receiver.
     *
     * @return string
     */
    public function getFolderName()
    {
        return $this->URLSegment;
    }
    
    /**
     * Answers the parent gallery of the receiver.
     *
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->getParent();
    }
    
    /**
     * Answers a link for the image before the given image.
     *
     * @param GalleryImage $image
     *
     * @return string
     */
    public function getPrevLink(GalleryImage $image)
    {
        $ids = $this->getListItems()->column('ID');
        
        $key = array_search($image->ID, $ids);
        
        $pid = ($key === 0) ? $ids[count($ids) - 1] : $ids[$key - 1];
        
        return $this->getListItems()->byID($pid)->Link();
    }
    
    /**
     * Answers a link for the image after the given image.
     *
     * @param GalleryImage $image
     *
     * @return string
     */
    public function getNextLink(GalleryImage $image)
    {
        $ids = $this->getListItems()->column('ID');
        
        $key = array_search($image->ID, $ids);
        
        $nid = ($key === (count($ids) - 1)) ? $ids[0] : $ids[$key + 1];
        
        return $this->getListItems()->byID($nid)->Link();
    }
    
    /**
     * Answers a list of images within the gallery album.
     *
     * @return DataList
     */
    public function getImages()
    {
        return GalleryImage::get()->filter('ParentID', $this->ID);
    }
    
    /**
     * Answers a sorted list of images within the gallery album.
     *
     * @return DataList
     */
    public function getSortedImages()
    {
        // Obtain Images:
        
        $images = $this->getImages();
        
        // Answer Sorted Images:
        
        switch ($this->SortImagesBy) {
            
            case self::SORT_ALPHA:
                return $images->sort('Title', 'ASC');
                
            case self::SORT_LATEST:
                return $images->sort('Created', 'DESC');
                
            case self::SORT_RANDOM:
                return $images->sort('RAND()');
            
            default:
                return $images;
            
        }
    }
    
    /**
     * Answers the first image added to the album.
     *
     * @return GalleryImage
     */
    public function getFirstImage()
    {
        return $this->getImages()->first();
    }
    
    /**
     * Answers the latest image added to the album.
     *
     * @return GalleryImage
     */
    public function getLatestImage()
    {
        return $this->getImages()->sort('Created', 'DESC')->first();
    }
    
    /**
     * Answers an image at random from the album.
     *
     * @return GalleryImage
     */
    public function getRandomImage()
    {
        return $this->getImages()->sort('RAND()')->first();
    }
    
    /**
     * Answers the number of images within the receiver.
     *
     * @return integer
     */
    public function getImageCount()
    {
        return $this->getImages()->count();
    }
    
    /**
     * Answers a string describing the number of images within the receiver.
     *
     * @return string
     */
    public function getNumberOfImages()
    {
        $count = $this->getImageCount();
        
        $noun = $count == 1 ? _t(__CLASS__ . '.IMAGE', 'image') : _t(__CLASS__ . '.IMAGES', 'images');
        
        return sprintf('%d %s', $count, $noun);
    }
    
    /**
     * Answers a list of images within the receiver.
     *
     * @return DataList
     */
    public function getListItems()
    {
        return $this->getSortedImages();
    }
    
    /**
     * Answers the list component for the template.
     *
     * @return BaseListComponent
     */
    public function getListComponent()
    {
        $list = parent::getListComponent();
        
        if ($this->ImageLinksTo) {
            $list->ImageLinksTo = $this->ImageLinksTo;
        }
        
        if ($this->SortImagesBy == self::SORT_RANDOM) {
            $list->PaginateItems = false;
        }
        
        return $list;
    }
    
    /**
     * Answers true if the date is to be shown.
     *
     * @return boolean
     */
    public function getDateShown()
    {
        return !$this->HideAlbumDate;
    }
    
    /**
     * Answers an array of options for the cover mode field.
     *
     * @return array
     */
    public function getCoverModeOptions()
    {
        return [
            self::COVER_AUTO   => _t(__CLASS__ . '.AUTO', 'Auto'),
            self::COVER_FIRST  => _t(__CLASS__ . '.FIRST', 'First'),
            self::COVER_LATEST => _t(__CLASS__ . '.LATEST', 'Latest'),
            self::COVER_RANDOM => _t(__CLASS__ . '.RANDOM', 'Random')
        ];
    }
    
    /**
     * Answers an array of options for the sort images by field.
     *
     * @return array
     */
    public function getSortImagesByOptions()
    {
        return [
            self::SORT_ORDER  => _t(__CLASS__ . '.ORDER', 'Order'),
            self::SORT_ALPHA  => _t(__CLASS__ . '.ALPHA', 'Alpha'),
            self::SORT_LATEST => _t(__CLASS__ . '.LATEST', 'Latest'),
            self::SORT_RANDOM => _t(__CLASS__ . '.RANDOM', 'Random')
        ];
    }
    
    /**
     * Answers the default URL segment for the receiver.
     *
     * @return string
     */
    public function getDefaultURLSegment()
    {
        return $this->generateURLSegment(
            _t(
                'SilverStripe\CMS\Controllers\CMSMain.NEWPAGE',
                'New {pagetype}',
                [
                    'pagetype' => $this->i18n_singular_name()
                ]
            )
        );
    }
}
