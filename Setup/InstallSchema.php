<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/05/2016
 * Time: 16:50
 */
namespace Magenest\AdvancedProductOption\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Review\Block\Adminhtml\Product\Edit\Tab;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /*
            * Create table 'magenest_apo_template'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_apo_template')
        )->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'Template Id'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'default'  => '0',
            ],
            'Is Active'
        )->setComment(
            'Product advanced option template'
        );
        $installer->getConnection()->createTable($table);
        /*
            * Create table 'magenest_apo_option'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_apo_option')
        )->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'Option Id'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Type'
        )->addColumn(
            'is_required',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Is Required'
        )->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            [],
            [],
            'Template Id'
        )->addColumn(
            'parent_option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            [],
            [],
            'Parent Option Id'
        )->setComment(
            'Product advanced option'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_apo_row')
        )->addColumn(
            'row_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'Row Id'
        )->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            [],
            [],
            'Option Id'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Description'
        )->addColumn(
            'tooltip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'tooltip'
        )->addColumn(
            'enableTooltip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'enableTooltip'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'SKU'
        )->addColumn(
            'qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Quantity'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Image'
        )->addColumn(
            'swatch',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Swatch'
        )->addColumn(
            'productImg',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'productImg'
        )->addColumn(
            'children',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Children'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Price'
        )->addColumn(
            'price_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Price type,fixed or percentage'
        )->addColumn(
            'special_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Special Price'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'default'  => '0',
            ],
            'Is Active'
        )->addColumn(
            'price_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Price Type'
        )->addColumn(
            'calculate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Option calculate'
        )->setComment(
            'Product advanced option'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_apo_tier_price')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'Row Id'
        )->addColumn(
            'row_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'Row Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'default'  => 0,
            ],
            'Website ID'
        )->addColumn(
            'customer_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Customer group'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Price'
        )->addColumn(
            'min_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Min qty'
        )->addColumn(
            'max_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'max qty'
        )->addColumn(
            'calculate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Tier price type'
        )->addColumn(
            'calculate_tier_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            [
                'nullable' => true,
                'default'  => null,
            ],
            'Option calculate tier'
        )->setComment(
            'Product advanced option tier price'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_apo_product')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'The Id'
        )->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Template Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Product Id'
        )->addColumn(
            'tooltip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Option tooltip'
        )->setComment(
            'Product advanced option assigned product'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }//end install()
}//end class
