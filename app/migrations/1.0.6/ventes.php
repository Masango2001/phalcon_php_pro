<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class VentesMigration_106
 */
class VentesMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('ventes', [
            'columns' => [
                new Column(
                    'ID_VENTE',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true
                    ]
                ),
                new Column(
                    'ID_UTILISATEUR',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'ID_VENTE'
                    ]
                ),
                new Column(
                    'ID_CLIENT',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'ID_UTILISATEUR'
                    ]
                ),
                new Column(
                    'DATE_VENTE',
                    [
                        'type' => Column::TYPE_DATE,
                        'notNull' => true,
                        'after' => 'ID_CLIENT'
                    ]
                ),
            ],
            'indexes' => [
                new Index('PRIMARY', ['ID_VENTE'], 'PRIMARY'),
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
