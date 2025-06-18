<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class ConcernerMigration_106
 */
class ConcernerMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('concerner', [
            'columns' => [
                new Column(
                    'ID_PRODUIT',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'first' => true
                    ]
                ),
                new Column(
                    'ID_VENTE',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'ID_PRODUIT'
                    ]
                ),
                new Column(
                    'QUANTITE_VENDUE',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'ID_VENTE'
                    ]
                ),
                new Column(
                    'PRIX_UNITAIRE_VENDUE',
                    [
                        'type' => Column::TYPE_DECIMAL,
                        'notNull' => true,
                        'size' => 10,
                        'scale' => 2,
                        'after' => 'QUANTITE_VENDUE'
                    ]
                ),
            ],
            'indexes' => [
                new Index('PRIMARY', ['ID_PRODUIT', 'ID_VENTE'], 'PRIMARY'),
            ],
            'options' => [
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '',
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
