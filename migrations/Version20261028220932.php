<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028220932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE remuneration ADD note_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE remuneration ADD evaluation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE remuneration ADD CONSTRAINT FK_1969358B26ED0855 FOREIGN KEY (note_id) REFERENCES note (id)');
        $this->addSql('ALTER TABLE remuneration ADD CONSTRAINT FK_1969358B456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1969358B26ED0855 ON remuneration (note_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1969358B456C5646 ON remuneration (evaluation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE remuneration DROP CONSTRAINT FK_1969358B26ED0855');
        $this->addSql('ALTER TABLE remuneration DROP CONSTRAINT FK_1969358B456C5646');
        $this->addSql('DROP INDEX UNIQ_1969358B26ED0855');
        $this->addSql('DROP INDEX UNIQ_1969358B456C5646');
        $this->addSql('ALTER TABLE remuneration DROP note_id');
        $this->addSql('ALTER TABLE remuneration DROP evaluation_id');
    }
}
