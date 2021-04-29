<?php

/**
 * An extension of the page class for a gallery.
 */
class Gallery extends Page implements ListSource
{
    private static $singular_name = "Gallery";
    private static $plural_name   = "Galleries";
    
    private static $description = "Holds a series of albums and images";
    
    private static $icon = "silverware-gallery/images/icons/Gallery.png";
    
    private static $default_child = "GalleryAlbum";
    
    private static $asset_folder = "Gallery";
    
    private static $db = array(
        'AlbumsPerPage' => 'Int',
        'PaginateAlbums' => 'Boolean',
        'ShowAlbumTitles' => 'Boolean'
    );
    
    private static $defaults = array(
        'AlbumsPerPage' => 12,
        'PaginateAlbums' => 1,
        'ShowAlbumTitles' => 1,
        'ImageDefaultWidth' => 1200,
        'ImageDefaultHeight' => 900,
        'ImageDefaultResize' => 'fit-max',
        'ThumbnailDefaultWidth' => 400,
        'ThumbnailDefaultHeight' => 400,
        'ThumbnailDefaultResize' => 'fill-priority'
    );
    
    private static $allowed_children = array(
        'GalleryAlbum'
    );
    
    private static $extensions = array(
        'ImageDefaultsExtension'
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
        
        // Create Options Tab:
        
        $fields->findOrMakeTab('Root.Options', _t('Gallery.OPTIONS', 'Options'));
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            ToggleCompositeField::create(
                'GalleryOptions',
                $this->i18n_singular_name(),
                array(
                    CheckboxField::create(
                        'PaginateAlbums',
                        _t('Gallery.PAGINATEALBUMS', 'Paginate albums')
                    ),
                    NumericField::create(
                        'AlbumsPerPage',
                        _t('Gallery.IMAGESPERPAGE', 'Albums per page')
                    )->displayIf('PaginateAlbums')->isChecked()->end(),
                    CheckboxField::create(
                        'ShowAlbumTitles',
                        _t('Gallery.SHOWALBUMTITLES', 'Show album titles')
                    )
                )
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a list of albums within the receiver.
     *
     * @return DataList
     */
    public function getAlbums()
    {
        return GalleryAlbum::get()->filter(
            array(
                'ParentID' => $this->ID
            )
        );
    }
    
    /**
     * Answers a list of images within the receiver.
     *
     * @return DataList
     */
    public function getImages()
    {
        return GalleryImage::get()->filter(
            array(
                'ParentID' => $this->Children()->column('ID')
            )
        );
    }
    
    /**
     * Answers a list of images within the gallery.
     *
     * @return SS_List
     */
    public function getListItems()
    {
        return $this->getImages();
    }
    
    /**
     * Answers the asset folder path for the receiver.
     *
     * @return string
     */
    public function getFolderPath()
    {
        return $this->config()->asset_folder . '/' . $this->getFolderName();
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
     * Answers a list component for the albums within the receiver.
     *
     * @return ListComponent
     */
    public function AlbumList()
    {
        $list = ListComponent::create();
        
        $list->addExtraClass('gallery');
        
        $list->setStyleIDFrom($this);
        
        $list->HideTitle  = 1;
        $list->HeadingTag = "h3";
        
        $list->ImageLinksTo = "Item";
        $list->TitleLinksTo = "Item";
        
        $list->ShowTitles    = $this->ShowAlbumTitles;
        $list->ItemsPerPage  = $this->AlbumsPerPage;
        $list->PaginateItems = $this->PaginateAlbums;
        
        $list->ImageWidth  = $this->getDefaultThumbnailWidth();
        $list->ImageHeight = $this->getDefaultThumbnailHeight();
        $list->ImageResize = $this->getDefaultThumbnailResize();
        
        $list->setSource($this->getAlbums());
        
        return $list;
    }
}

/**
 * An extension of the page controller class for a gallery.
 */
class Gallery_Controller extends Page_Controller
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
