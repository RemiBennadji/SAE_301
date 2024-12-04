--liquibase formatted sql

--changeset your.name:1 labels:table-creation context:initial-setup
--comment: Creating infoutilisateur table
CREATE TABLE IF NOT EXISTS infoutilisateur (
                                               identifiant TEXT NOT NULL,
                                               motdepasse TEXT NOT NULL,
                                               role TEXT NOT NULL,
                                               PRIMARY KEY (id, identifiant)
    );

--rollback DROP TABLE infoutilisateur;

--changeset your.name:2 labels:etudiants-creation context:student-setup
--comment: Creating etudiants table
CREATE TABLE IF NOT EXISTS etudiants (
                                         id SERIAL PRIMARY KEY,
                                         civilite VARCHAR(4),
    nom VARCHAR(100),
    prenom VARCHAR(100),
    semestre INTEGER,
    nom_ressource VARCHAR(50),
    email VARCHAR(255)
    );

--rollback DROP TABLE etudiants;

--changeset your.name:3 labels:etudiants-insert context:additional-data
--comment: Inserting additional student records
INSERT INTO etudiants (civilite, nom, prenom, semestre, nom_ressource, email) VALUES
                                                                                  ('M.', 'VIGNOLLE', 'Victor', 1, 'A1', 'victor.vignolle@uphf.fr'),
                                                                                  ('M.', 'VIGNOLLE', 'Victor', 1, 'A1', 'victor.vigaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaanolle@uphf.fr');

--rollback DELETE FROM etudiants WHERE email = 'victor.vignolle@uphf.fr' OR email = 'victor.vigaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaanolle@uphf.fr';

--changeset your.name:4 labels:infoutilisateur-insert context:additional-data
--comment: Inserting initial user data
INSERT INTO infoutilisateur (identifiant, motdepasse, role) VALUES
    ('iut.info','iutinfo1.','administrateur');

--rollback DELETE FROM infoutilisateur WHERE identifiant = 'iut.info';

--changeset mattheo:5 labels:create-infoutilisateur update context:update-table
--comment: create new table
create table infoutilisateur(
                                identifiant text primary key ,
                                motdepasse text not null ,
                                role text not null ,
                                changeMDP boolean not null
);

--rollback DROP TABLE infoutilisateur;

--changeset mattheo:6 labels:insert-infoutilisateur context:insert-table
--comment: insert iut.info
insert into infoutilisateur(identifiant, motdepasse, role, changeMDP)
values ('iut.info', 'iutinfo1.', 'administrateur', false);
--rollback delete from infoutilisateur where identifiant = 'iut.info';

--changeset mattheo:7 labels:create-table-asso context:table-mailidentifant
--comment: create table mail-identifiant
create table MailIdentifiant(
    mail text ,
    identifiant text ,
    primary key (mail, identifiant),
    foreign key (mail) references etudiants(email),
    foreign key (identifiant) references infoutilisateur(identifiant)
);
--rollback DROP TABLE MailIdentifant;

--changeset mattheo:8 labels:start-function context:atomate-table-MailIdentifiant
--comment: start function of trigger
CREATE OR REPLACE FUNCTION insert_MailIdentifiant_trigger()
RETURNS TRIGGER AS $$
DECLARE
       etudiant_email TEXT;
BEGIN
SELECT email
INTO etudiant_email
FROM etudiants
WHERE email LIKE NEW.identifiant || '%';
IF FOUND THEN INSERT INTO MailIdentifiant (mail, identifiant) VALUES (etudiant_email, NEW.identifiant);
END IF;
RETURN NEW;
END;
$$ LANGUAGE plpgsql;
--rollback: Drop function if exist insert_MailIdentifiant_trigger;

--changeset mattheo:9 labels:create-trigger context:trigger-mailidentifiant
--comment: create trigger
CREATE TRIGGER insert_trigger_on_Mailidentifiant
    AFTER INSERT ON infoutilisateur
    FOR EACH ROW
    EXECUTE FUNCTION insert_MailIdentifiant_trigger();
--rollback: DROP TRIGGER insert_MailIdentifiant ON infoutilisateur;