<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'age'          => ['type' => 'INT'],
            'skills'       => ['type' => 'TEXT'],
            'address'      => ['type' => 'TEXT'],
            'designation'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'createdAt'    => ['type' => 'DATETIME', 'null' => true],
            'updatedAt'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('employees');
    }

    public function down()
    {
        $this->forge->dropTable('employees');
    }
}
