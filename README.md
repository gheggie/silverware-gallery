# SilverWare Gallery Module

[![Latest Stable Version](https://poser.pugx.org/silverware/gallery/v/stable)](https://packagist.org/packages/silverware/gallery)
[![Latest Unstable Version](https://poser.pugx.org/silverware/gallery/v/unstable)](https://packagist.org/packages/silverware/gallery)
[![License](https://poser.pugx.org/silverware/gallery/license)](https://packagist.org/packages/silverware/gallery)

Provides an image gallery for [SilverWare][silverware] apps, divided into a series of albums.

![Gallery](https://i.imgur.com/wNMV6ao.png)

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Issues](#issues)
- [Contribution](#contribution)
- [Maintainers](#maintainers)
- [License](#license)

## Requirements

- [SilverWare][silverware]
- [SilverWare Lightbox Module][silverware-lightbox]
- [SilverWare Masonry Module][silverware-masonry]

## Installation

Installation is via [Composer][composer]:

```
$ composer require silverware/gallery
```

## Usage

The module provides three pages ready for use within the CMS:

- `Gallery`
- `GalleryAlbum`
- `GalleryImage`

Create a `Gallery` page as the top-level of your image gallery. Under the `Gallery` you
may add `GalleryAlbum` pages as children to divide the gallery into a series
of albums. Then, as children of `GalleryAlbum`, add your `GalleryImage` pages.

A `GalleryImage` consists of a title, image and caption. `Gallery` and `GalleryAlbum` pages
are also implementors of `ListSource`, and can be used with components to show a series of albums or images
as list items.

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## Contribution

Your contributions are gladly welcomed to help make this project better.
Please see [contributing](CONTRIBUTING.md) for more information.

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](http://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](http://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive

[silverware]: https://github.com/praxisnetau/silverware
[silverware-lightbox]: https://github.com/praxisnetau/silverware-lightbox
[silverware-masonry]: https://github.com/praxisnetau/silverware-masonry
[composer]: https://getcomposer.org
[issues]: https://github.com/praxisnetau/silverware-gallery/issues
