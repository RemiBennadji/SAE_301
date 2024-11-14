--liquibase formatted sql

--changeset your.name:1 labels:table-creation context:initial-setup
--comment: Creating infoutilisateur table
CREATE TABLE IF NOT EXISTS infoutilisateur (
                                               id INT NOT NULL,
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
INSERT INTO infoutilisateur (id, identifiant, motdepasse, role) VALUES
    (2, 'iut.info','iutinfo1.','administrateur');

--rollback DELETE FROM infoutilisateur WHERE id = 2 AND identifiant = 'iut.info';

--changeset mattheo labels:infoutilisateur update context:update-table
--comment: create new table
create table infoutilisateur(
                                identifiant text primary key ,
                                motdepasse text not null ,
                                role text not null ,
                                changeMDP boolean not null
);

--rollback DROP TABLE infoutilisateur;
