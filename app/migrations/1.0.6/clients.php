<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class ClientsMigration_106
 */
class ClientsMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('clients', [
            'columns' => [
                new Column(
                    'ID_CLIENT',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true
                    ]
                ),
                new Column(
                    'NOM_CLIENT',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'ID_CLIENT'
                    ]
                ),
                new Column(
                    'PRENOM_CLIENT',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'NOM_CLIENT'
                    ]
                ),
                new Column(
                    'ADRESSE_CLIENT',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'PRENOM_CLIENT'
                    ]
                ),
                new Column(
                    'TELEPHONE_CLIENT',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 50,
                        'after' => 'ADRESSE_CLIENT'
                    ]
                ),
            ],
            'indexes' => [
                new Index('PRIMARY', ['ID_CLIENT'], 'PRIMARY'),
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
