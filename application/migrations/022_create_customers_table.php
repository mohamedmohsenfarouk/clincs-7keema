<?php defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Create_customers_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ),
            'first_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'last_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'password' => array(
                'type' => 'TEXT',
                'null' => false,
            ),
            'verification_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'phone_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => null,
            ),
            'token' => array(
                'type' => 'TEXT',
                'constraint' => '200',
                'default' => null,
            ),
            'address' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => null,
            ),
            'city' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => null,
            ),
            'state' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => null,
            ),
            'zip_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => null,
            ),
            'notes' => array(
                'type' => 'TEXT',
            ),
            'timezone' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'UTC',
            ),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('customers');
    }

    public function down()
    {
        $this->dbforge->drop_table('customers');
    }
}
