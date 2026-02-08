<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'null' => false,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'TEXT',
            ]
        ]);

        $this->forge->addPrimaryKey('id');

        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
