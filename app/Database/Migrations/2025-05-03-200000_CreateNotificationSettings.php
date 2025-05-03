<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_2025_05_03_200000_CreateNotificationSettings extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'user_id' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => true,
      ],
      'browser_enabled' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 1,
      ],
      'email_enabled' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 1,
      ],
      'polling_interval' => [
        'type' => 'INT',
        'constraint' => 11,
        'default' => 30000,
      ],
      'email_lead_time' => [
        'type' => 'INT',
        'constraint' => 11,
        'default' => 60,
      ],
      'last_email_sent' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
    ]);
    
    $this->forge->addKey('user_id', true);
    $this->forge->createTable('notification_settings');
  }
  
  public function down()
  {
    $this->forge->dropTable('notification_settings');
  }
}
