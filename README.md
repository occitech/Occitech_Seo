# Occitech SEO

Magento module gathering good SEO practices.

## What does it do?

* Microdatas
    * Breadcrumb
    * Products (simple, configurable, grouped)
    * Basic knowledge graph
    * Reviews
    * Search
* Categories canonical URLs
    * Keep current category view page in the canonical URL. Ex: http://demo.magentocommerce.com/men/pants-denim.html?p=2 
    will have http://demo.magentocommerce.com/men/pants-denim.html?p=2 as canonical.
    * Page number is the only thing that's keep. Sort field or directions, or filters are removed from the canonical. Ex:
    http://demo.magentocommerce.com/men/pants-denim.html?dir=asc&order=name&p=2 will have
    http://demo.magentocommerce.com/men/pants-denim.html?p=2 as canonical.

## Installation

### Composer

Add this repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/occitech/Occitech_Seo.git"
        }
    ]
}
```

then `composer require occitech/seo`

### [Modman](https://github.com/colinmollenhour/modman)

Download an archive from this repository, place it in a `.modman` directory at the root of your Magento project, then `modman deploy` 

### Old fashioned

Download an archive from this repository, then move the folder to the correct destination in your Magento project.

## Post-installation

For now some template have to be modified.

### `page/1column.phtml`, `page/2columns-left.phtml`, `page/2columns-right.phtml`, `page/3columns.phtml`

The `<head>` tag should look like this:

```php
<head <?php echo Mage::helper('occitech_seo')->webSiteElementRoot(); ?>>
```

The `<body>` tag should look like this:

```php
<body […] <?php echo Mage::helper('occitech_seo')->webPageElementRoot() ?>>
```

The `<div>` or whatever tag surround `<?php echo $this->getChildHtml('content') ?>` should look like this:

```php
<div […] <?php echo Mage::helper('occitech_seo')->itemprop('mainContentOfPage') ?>>
```

### `catalog/product/list.phtml`

For both grid and list mode, insert this code after the closing list item `</li>` tag (it should be right before a `<?php endforeach ?>`:

```php
<?php 
    echo $this->getLayout()
        ->createBlock('occitech_seo/product', 'microdata', array('product' => $_product))
        ->toHtml();
?>
```

## Licence

[MIT Licence](http://opensource.org/licenses/MIT)
