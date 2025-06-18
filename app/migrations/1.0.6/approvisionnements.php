<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class ApprovisionnementsMigration_106
 */
class ApprovisionnementsMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('approvisionnements', [
            'columns' => [
                new Column(
                    'ID_APPROVISIONNEMENT',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true
                    ]
                ),
                new Column(
                    'ID_PRODUIT',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'ID_APPROVISIONNEMENT'
                    ]
                ),
                new Column(
                    'ID_FOURNISSEUR',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'ID_PRODUIT'
                    ]
                ),
                new Column(
                    'QUANTITE_APPROVIONNEMENT',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'ID_FOURNISSEUR'
                    ]
                ),
                new Column(
                    'PRIX_UNITAIRE_ACHAT',
                    [
                        'type' => Column::TYPE_DECIMAL,
                        'notNull' => true,
                        'size' => 10,
                        'scale' => 2,
                        'after' => 'QUANTITE_APPROVIONNEMENT'
                    ]
                ),
                new Column(
                    'DATE_APPROVISIONNEMENT',
                    [
                        'type' => Column::TYPE_DATE,
                        'notNull' => true,
                        'after' => 'PRIX_UNITAIRE_ACHAT'
                    ]
                ),
            ],
            'indexes' => [
                new Index('PRIMARY', ['ID_APPROVISIONNEMENT'], 'PRIMARY'),
            ],
            'options' => [
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '1',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8mb4_general_ci',
            ],
        ]);
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void
    {
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
    }
}
