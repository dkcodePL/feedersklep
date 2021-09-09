<?php

namespace ForMage\WholesaleImport\Model\Import\Product;

use Magento\Framework\Exception\AlreadyExistsException;

class CategoryProcessor extends \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor
{

    /**
     * Returns IDs of categories by string path creating nonexistent ones.
     *
     * @param string $categoriesString
     * @param string $categoriesSeparator
     * @return array
     */
    public function upsertCategories($categoriesString, $categoriesSeparator)
    {
        $categoriesIds = [];
        $categories    = explode($categoriesSeparator, $categoriesString);
        foreach ($categories as $category) {
            try {
                /**
                 *
                 * @note Validate if category is a number and exists as a category ID
                 * @note To use this feature, the category name does not have to match with categories' IDs
                 *
                 */
                if (is_numeric($category) && $this->getCategoryById($category)) {
                    $categoriesIds[] = $category;
                    $catModel = $this->getCategoryById($category);
                    if ($catModel) {
                        foreach ($catModel->getParentIds() as $parentId) {
                            //$categoriesIds[] = $parentId;
                        }
                    }
                }
                else {
                    $categoriesIds[] = $this->upsertCategory($category);
                }
            }
            catch (AlreadyExistsException $e) {
                $this->addFailedCategory($category, $e);
            }
        }
        return $categoriesIds;
    }

    /**
     * Add failed category
     *
     * @param string $category
     * @param \Magento\Framework\Exception\AlreadyExistsException $exception
     *
     * @return $this
     */
    private function addFailedCategory($category, $exception)
    {
        $this->failedCategories[] =
            [
                'category'  => $category,
                'exception' => $exception,
            ];
        return $this;
    }
}