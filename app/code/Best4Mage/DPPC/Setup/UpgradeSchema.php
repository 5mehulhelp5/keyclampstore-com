<?php

namespace Best4Mage\DPPC\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Best4Mage\DPPC\Model\ShapeFactory;
use Best4Mage\DPPC\Model\SideFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class UpgradeSchema implements UpgradeSchemaInterface
{

    protected $_shapeFactory;

    protected $_sideFactory;

    protected $_date;

    public function __construct(ShapeFactory $shapeFactory, SideFactory $sideFactory, DateTime $date)
    {
        $this->_shapeFactory = $shapeFactory;
        $this->_sideFactory  = $sideFactory;
        $this->_date = $date;
    }
    
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $dppcShapeTable = $setup->getTable('dppc_shape');
            if ($setup->getConnection()->isTableExists($dppcShapeTable) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $dppcShapeTable,
                    'formula',
                    ['type' => Table::TYPE_TEXT, 'nullable' => false, 'default' =>  null, 'comment' => 'Shape Formula']
                );
            }
            $dppcSideTable = $setup->getTable('dppc_side');
            if ($setup->getConnection()->isTableExists($dppcSideTable) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $dppcSideTable,
                    'sort_order',
                    ['type' => Table::TYPE_SMALLINT, 'nullable' => true, 'comment' => 'Sort Order']
                );
            }
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $sidesData = [
                ['title' => 'A','code' => 'side_a','sort_order' => 1],
                ['title' => 'B','code' => 'side_b','sort_order' => 2],
                ['title' => 'C','code' => 'side_c','sort_order' => 3],
                ['title' => 'D','code' => 'side_d','sort_order' => 4],
            ];

            foreach ($sidesData as $sides) {
                $this->_sideFactory->create()->setData($sides)->save();
            }

            $shapesData = [
                [
                    'title' => 'Rectangle',
                    'side_id' => 'side_a,side_b',
                    'image' => 'rectangle.png',
                    'formula' => 'side_a * side_b',
                    'min_max_value' => '500:2500,500:2500',
                    'sort_order' => 1,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Square',
                    'side_id' => 'side_a',
                    'image' => 'square.png',
                    'formula' => 'side_a * side_a',
                    'min_max_value' => '500:2500',
                    'sort_order' => 2,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Circle (Actual Formula)',
                    'side_id' => 'side_a',
                    'image' => 'circle.png',
                    'formula' => 'PI * (side_a / 2) * (side_a / 2)',
                    'min_max_value' => '500:2500',
                    'sort_order' => 3,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Length',
                    'side_id' => 'side_a',
                    'image' => 'length.png',
                    'formula' => 'side_a',
                    'min_max_value' => '500:2500',
                    'sort_order' => 4,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Ellipse (Actual Formula)',
                    'side_id' => 'side_a,side_b',
                    'image' => 'ellipse.png',
                    'formula' => 'PI * (side_a / 2) * (side_a / 2)',
                    'min_max_value' => '500:2500,500:2500',
                    'sort_order' => 5,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Polygon (Actual Formula)',
                    'side_id' => 'side_a,side_b,side_c,side_d',
                    'image' => 'polygon.png',
                    'formula' => '(side_a * side_b) + (side_d * (side_c - side_a))',
                    'min_max_value' => '500:2500,500:2500,500:2500,500:2500',
                    'sort_order' => 6,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Triangle (Actual Formula)',
                    'side_id' => 'side_a,side_b,side_c',
                    'image' => 'triangle.png',
                    'formula' => '(side_b * (((sqrt(((side_a + side_b + side_c) / 2) * (((side_a + side_b + side_c) / 2) - side_a) * (((side_a + side_b + side_c) / 2) - side_b) * (((side_a + side_b + side_c) / 2) - side_c))) * 2) / side_a)) / 2',
                    'min_max_value' => '500:2500,500:2500,500:2500',
                    'sort_order' => 7,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Quadrilateral (Actual Formula)',
                    'side_id' => 'side_a,side_b,side_c,side_d',
                    'image' => 'quadrilateral.png',
                    'formula' => '(0.5 * side_a * side_d * 0.9848) + (0.5 * side_b * side_c * 0.9396)',
                    'min_max_value' => '500:2500,500:2500,500:2500,500:2500',
                    'sort_order' => 8,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Circle (Square/Rectangle Formula)',
                    'side_id' => 'side_a',
                    'image' => 'circle_srf.png',
                    'formula' => 'side_a * side_a',
                    'min_max_value' => '500:2500',
                    'sort_order' => 9,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Ellipse (Square/Rectangle Formula)',
                    'side_id' => 'side_a,side_b',
                    'image' => 'ellipse_srf.png',
                    'formula' => 'side_a * side_b',
                    'min_max_value' => '500:2500,500:2500',
                    'sort_order' => 10,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Polygon (Square/Rectangle Formula)',
                    'side_id' => 'side_b,side_c',
                    'image' => 'polygon_srf.png',
                    'formula' => 'side_b * side_c',
                    'min_max_value' => '500:2500,500:2500',
                    'sort_order' => 11,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Triangle (Square/Rectangle Formula)',
                    'side_id' => 'side_a,side_b,side_c',
                    'image' => 'triangle_srf.png',
                    'formula' => 'side_b * (((sqrt(((side_a + side_b + side_c) / 2) * (((side_a + side_b + side_c) / 2) - side_a) * (((side_a + side_b + side_c) / 2) - side_b) * (((side_a + side_b + side_c) / 2) - side_c))) * 2) / side_a)',
                    'min_max_value' => '500:2500,500:2500,500:2500',
                    'sort_order' => 12,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
                [
                    'title' => 'Quadrilateral (Square/Rectangle Formula)',
                    'side_id' => 'side_a,side_b',
                    'image' => 'quadrilateral_srf.png',
                    'formula' => 'side_a * side_b',
                    'min_max_value' => '500:2500,500:2500',
                    'sort_order' => 13,
                    'calculation_type' => 1,
                    'status' => 1,
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate()
                ],
            ];

            foreach ($shapesData as $shapes) {
                $this->_shapeFactory->create()->setData($shapes)->save();
            }
        }
        $setup->endSetup();
    }
}
