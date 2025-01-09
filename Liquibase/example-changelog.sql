--liquibase formatted sql

--changeset mattheo:1 labels:table-creation context:initial-setup
--comment: Creating infoutilisateur table
CREATE TABLE IF NOT EXISTS infoutilisateur (
                                               identifiant TEXT NOT NULL,
                                               motdepasse TEXT NOT NULL,
                                               role TEXT NOT NULL,
                                               PRIMARY KEY (identifiant)
    );
--rollback DROP TABLE infoutilisateur;


--changeset mattheo:2 labels:etudiants-creation context:etudiant-setup
--comment: Creating etudiants table
CREATE TABLE IF NOT EXISTS etudiants (
    civilite VARCHAR(4),
    nom VARCHAR(50),
    prenom VARCHAR(50),
    semestre INTEGER,
    nom_ressource VARCHAR(50)
    );
--rollback DROP TABLE etudiants;


--changeset mattheo:3 labels:alter-infoutilisateur update context:alter-table
--comment: create new table
alter table infoutilisateur add column changeMDP boolean;
--rollback alter table infoutilisateur drop column changeMDP;


--changeset your.name:4 labels:infoutilisateur-insert context:additional-data
--comment: Inserting initial user data
INSERT INTO infoutilisateur (identifiant, motdepasse, role, changeMDP) VALUES
    ('iut.info','iutinfo1.','administrateur', false);
--rollback DELETE FROM infoutilisateur WHERE identifiant = 'iut.info';


--changeset mattheo:5 labels:alter-infoutilisateur update context:alter-table
--comment: create new table
alter table infoutilisateur add column mail text;
--rollback alter table infoutilisateur drop column mail;

--changeset mattheo:6 labels:add-constraint-infoutilisateur update context:alter-table
--comment: create new table
alter table infoutilisateur add constraint unique_mail UNIQUE (mail);
--rollback alter table infoutilisateur drop constraint unique_mail mail;

--changeset matthéo:7 labels:new-table-code context:table-codeverif
--comment: create table
CREATE TABLE codeverif(
    email text not null ,
    codev integer primary key ,
    date timestamp not null ,
    foreign key (email) references infoutilisateur(mail)
);
--rollback: drop table codeverif

--changeset mattheo:8 labels:add-column-etudiants update context:alter-table
--comment: alter new table
alter table etudiants add column email text not null;
--rollback alter table etudiant drop column email;

--changeset mattheo:9 labels:add-column-expiration update context:alter-table-codeverif
--comment: alter table codeverif
alter table codeverif add column expiration timestamp not null;
--rollback alter table codeverif drop column expiration;

--changeset matthéo:10 labels:new-table context:table-report
--comment: create table
CREATE TABLE report(
    id serial primary key ,
    datereport timestamp not null ,
    raison text not null ,
    nom text not null ,
    prenom text not null
);
--rollback: drop table report

--changeset matthéo:11 labels:create-validationEDT context:table-report
--comment: create table
create table validationEDT
    id serial primary key,
    nom text not null,
    prenom text not null,
    valider boolean not null,
    dateValidation timestamp not null

--changeset matthéo:12 labels:create-versionValideEDT context:table-report
--comment: create table
create table versionValideEDT(
    id serial primary key,
    version int default 1,
    dateValidation timestamp
);
insert into versionValideEDT (dateValidation) values('2024-09-01');