<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113163055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_product DROP FOREIGN KEY FK_C890CED44584665A');
        $this->addSql('ALTER TABLE purchase_product DROP FOREIGN KEY FK_C890CED4558FBEB9');
        $this->addSql('DROP TABLE purchase_product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_product (purchase_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_C890CED4558FBEB9 (purchase_id), INDEX IDX_C890CED44584665A (product_id), PRIMARY KEY(purchase_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT FK_C890CED44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT FK_C890CED4558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
