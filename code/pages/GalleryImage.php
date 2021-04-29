<?php

/**
 * An extension of the page class for a gallery image.
 */
class GalleryImage extends Page
{
    private static $singular_name = "Gallery Image";
    private static $plural_name   = "Gallery Images";
    
    private static $description = "An image within a gallery album";
    
    private static $icon = "silverware-gallery/images/icons/GalleryImage.png";
    
    private static $allowed_children = "none";
    
    private static $can_be_root = false;
    
    private static $db = array(
        
    );
    
    private static $defaults = array(
        'ShowInMenus' => 0
    );
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Remove Field Objects:
        
        $fields->removeFieldFromTab('Root.Main', 'Content');
        $fields->removeFieldFromTab('Root.Main', 'MetaSummaryToggle');
        $fields->removeFieldFromTab('Root.Main', 'Metadata');
        
        // Modify Image Toggle:
        
        $fields->fieldByName('Root.Main.MetaImageToggle')->setStartClosed(false);
        
        // Modify Meta Image Field:
        
        if ($Album = $this->Album()) {
            $this->setMetaImageFolder($fields, $Album->getFolderPath());
        }
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the parent album of the receiver.
     *
     * @return GalleryAlbum
     */
    public function Album()
    {
        if ($this->Parent() instanceof GalleryAlbum) {
            return $this->Parent();
        }
    }
    
    /**
     * Answers a summary for the receiver.
     *
     * @return string
     */
    public function MetaSummary()
    {
        return $this->MetaImageCaption;
    }
}

/**
 * An extension of the page controller class for a gallery image.
 */
class GalleryImage_Controller extends Page_Controller
{
    /**
     * Defines the URLs handled by this controller.
     */
    private static $url_handlers = array(
        
    );
    
    /**
     * Defines the allowed actions for this controller.
     */
    private static $allowed_actions = array(
        
    );
    
    /**
     * Performs initialisation before any action is called on the receiver.
     */
    public function init()
    {
        // Initialise Parent:
        
        parent::init();
        
        // Load Requirements:
        
        Requirements::themedCSS('silverware-gallery', SILVERWARE_GALLERY_DIR);
    }
}
