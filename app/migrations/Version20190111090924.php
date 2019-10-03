<?php declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111090924 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable("app_blog_post_content");
    }
}
