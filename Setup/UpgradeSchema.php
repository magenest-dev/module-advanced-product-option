<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 09/07/2016
 * Time: 01:47
 */

namespace Magenest\AdvancedProductOption\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

//        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $table = $setup->getConnection()->newTable($setup->getTable('magenest_apo_quantity_mapping'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product ID'
                )->addColumn(
                    'value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [],
                    'Value'
                )->setComment(
                    'Quantity Mapping'
                );
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.1.0') < 0) {

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_template'),
                'excludeMode',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Exclude Mode is supposed to have value 1 or 0'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_template'),
                'excluded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Excluded contains the exclude product id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.3.0') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_option'),
                'tooltip',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                    'nullable' => true,
                    'comment' => 'Option tooltip'
                ]);
        }

        if (version_compare($context->getVersion(), '1.4.0') < 0) {
            $setup->getConnection()->modifyColumn(
                $setup->getTable('magenest_apo_row'),
                'price_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment'=>'Price Type'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_row'),
                'calculate_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                    'nullable' => true,
                    'comment' => 'Option calculate'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_row'),
                'special_date_from',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Special Date From'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_row'),
                'special_date_to',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Special Date To'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_tier_price'),
                'tier_price_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                    'nullable' => true,
                    'comment' => 'Tier price type'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_tier_price'),
                'calculate_tier_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                    'nullable' => true,
                    'comment' => 'Option calculate tier'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.5.0') < 0) {

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_apo_quantity_mapping'),
                'quantity',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                    'nullable' => true,
                    'comment' => 'Quantity'
                ]
            );
        }

        $setup->endSetup();
    }
}
