<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class UtilisateursMigration_106
 */
class UtilisateursMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('utilisateurs', [
            'columns' => [
                new Column(
                    'ID_UTILISATEUR',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true
                    ]
                ),
                new Column(
                    'USERNAME',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'ID_UTILISATEUR'
                    ]
                ),
                new Column(
                    'EMAIL',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'USERNAME'
                    ]
                ),
                new Column(
                    'PASSWORD',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'EMAIL'
                    ]
                ),
                new Column(
                    'ROLE',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 100,
                        'after' => 'PASSWORD'
                    ]
                ),
            ],
            'indexes' => [
                new Index('PRIMARY', ['ID_UTILISATEUR'], 'PRIMARY'),
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
