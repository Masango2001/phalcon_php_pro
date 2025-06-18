<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class FournisseursMigration_106
 */
class FournisseursMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('fournisseurs', [
            'columns' => [
                new Column(
                    'ID_FOURNISSEUR',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true
                    ]
                ),
                new Column(
                    'NOM_COMPLET_FOURNISSEUR',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'ID_FOURNISSEUR'
                    ]
                ),
                new Column(
                    'ADRESSE_FOURNISSEUR',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'NOM_COMPLET_FOURNISSEUR'
                    ]
                ),
                new Column(
                    'EMAIL_FOURNISSEUR',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'ADRESSE_FOURNISSEUR'
                    ]
                ),
                new Column(
                    'TELEPHONE_FOURNISSEUR',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 50,
                        'after' => 'EMAIL_FOURNISSEUR'
                    ]
                ),
            ],
            'indexes' => [
                new Index('PRIMARY', ['ID_FOURNISSEUR'], 'PRIMARY'),
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
