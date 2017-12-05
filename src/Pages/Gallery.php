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
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Lists\ListSource;
use SilverWare\Masonry\Components\MasonryComponent;
use Page;

/**
 * An extension of the page class for a gallery.
 *
 * @package SilverWare\Gallery\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-gallery
 */
class Gallery extends Page implements ListSource
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Gallery';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Galleries';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Holds a series of gallery images organised into albums';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/gallery: admin/client/dist/images/icons/Gallery.png';
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = GalleryAlbum::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ShowImageCounts' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ImageDefaultResizeWidth' => 1200,
        'ImageDefaultResizeHeight' => 900,
        'ImageDefaultResizeMethod' => 'fit-max',
        'ImageDefaultLinked' => 1,
        'ShowImageCounts' => 0
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        GalleryAlbum::class
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListViewExtension::class,
        ImageDefaultsExtension::class
    ];
    
    /**
     * Defines the list component class to use.
     *
     * @var string
     * @config
     */
    private static $list_component_class = MasonryComponent::class;
    
    /**
     * Defines the default values for the list view component.
     *
     * @var array
     * @config
     */
    private static $list_view_defaults = [
        'ShowImage' => 'all',
        'ShowHeader' => 'all',
        'ShowDetails' => 'none',
        'ShowSummary' => 'none',
        'ShowContent' => 'none',
        'ShowFooter' => 'none',
        'ImageLinksTo' => 'item',
        'ImageItems' => 1,
        'LinkTitles' => 1,
        'LinkImages' => 1,
        'OverlayImages' => 1,
        'Gutter' => 10,
        'ColumnUnit' => 'percent',
        'PercentWidth' => [
            'Tiny' => '100',
            'Small' => '50',
            'Medium' => '33.33333333',
            'Large' => '33.33333333',
            'Huge' => '33.33333333'
        ],
        'HorizontalOrder' => 1,
        'PaginateItems' => 1,
        'ItemsPerPage' => 12,
        'ImageResizeWidth' => 600,
        'ImageResizeHeight' => 400,
        'ImageResizeMethod' => 'fill-priority'
    ];
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Gallery';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'GalleryOptions',
                    $this->fieldLabel('Gallery'),
                    [
                        CheckboxField::create(
                            'ShowImageCounts',
                            $this->fieldLabel('ShowImageCounts')
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
        
        $labels['Gallery'] = _t(__CLASS__ . '.GALLERY', 'Gallery');
        $labels['ShowImageCounts'] = _t(__CLASS__ . '.SHOWIMAGECOUNTS', 'Show image counts');
        
        // Answer Field Labels:
        
        return $labels;
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
                $this->config()->asset_folder,
                $this->getFolderName()
            );
            
        }
        
        return $this->config()->asset_folder;
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
     * Answers a list of albums within the gallery.
     *
     * @return DataList
     */
    public function getAlbums()
    {
        return GalleryAlbum::get()->filter('ParentID', $this->ID);
    }
    
    /**
     * Answers a list of images within the gallery.
     *
     * @return DataList
     */
    public function getImages()
    {
        return GalleryImage::get()->filter('ParentID', $this->AllChildren()->column('ID') ?: null);
    }
    
    /**
     * Answers a list of images within the receiver.
     *
     * @return DataList
     */
    public function getListItems()
    {
        return $this->getAlbums();
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
