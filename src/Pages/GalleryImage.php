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

use SilverWare\Extensions\Model\DetailFieldsExtension;
use Page;

/**
 * An extension of the page class for a gallery image.
 *
 * @package SilverWare\Gallery\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-gallery
 */
class GalleryImage extends Page
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Gallery Image';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Gallery Images';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An individual image within a gallery album';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/gallery: admin/client/dist/images/icons/GalleryImage.png';
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowInMenus' => 0
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'AlbumLink' => 'HTMLFragment'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        DetailFieldsExtension::class
    ];
    
    /**
     * Defines the meta field configuration for this object.
     *
     * @var array
     * @config
     */
    private static $meta_fields = [
        'Image' => 'Root.Main',
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
            'name' => 'Album',
            'icon' => 'folder-o',
            'text' => '$AlbumLink'
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
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Remove Field Objects:
        
        $fields->removeFieldFromTab('Root.Main', 'Content');
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the name of the asset folder used for uploading images.
     *
     * @return string
     */
    public function getMetaImageFolder()
    {
        if ($album = $this->getAlbum()) {
            return $album->getMetaImageFolder();
        }
    }
    
    /**
     * Answers the summary for the object.
     *
     * @return DBHTMLText
     */
    public function getMetaSummary()
    {
        return $this->dbObject('ImageMetaCaption');
    }
    
    /**
     * Answers the parent album of the receiver.
     *
     * @return GalleryAlbum
     */
    public function getAlbum()
    {
        return $this->getParent();
    }
    
    /**
     * Answers a string of HTML containing a link to the parent album.
     *
     * @return string
     */
    public function getAlbumLink()
    {
        return sprintf(
            '<a href="%s">%s</a>',
            $this->getAlbum()->Link(),
            $this->getAlbum()->Title
        );
    }
    
    /**
     * Answers a link for the previous image in the album.
     *
     * @return string
     */
    public function getPrevLink()
    {
        return $this->getAlbum()->getPrevLink($this);
    }
    
    /**
     * Answers a link for the next image in the album.
     *
     * @return string
     */
    public function getNextLink()
    {
        return $this->getAlbum()->getNextLink($this);
    }
    
    /**
     * Answers the text for the previous image button.
     *
     * @return string
     */
    public function getPrevText()
    {
        return _t(__CLASS__ . '.PREVIOUS', 'Previous');
    }
    
    /**
     * Answers the text for the next image button.
     *
     * @return string
     */
    public function getNextText()
    {
        return _t(__CLASS__ . '.NEXT', 'Next');
    }
    
    /**
     * Answers true if the date is to be shown.
     *
     * @return boolean
     */
    public function getDateShown()
    {
        return !$this->getAlbum()->HideImageDate;
    }
    
    /**
     * Answers true if the footer is to be shown.
     *
     * @return boolean
     */
    public function getFooterShown()
    {
        return ($this->getAlbum()->getImageCount() > 1);
    }
}
