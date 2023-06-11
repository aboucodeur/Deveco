-- Base de donnee de l'application;
CREATE table if NOT EXISTS categories (
    cat_id serial primary key,
    cat_nom varchar(200) not null,
    createdAt timestamp default now(),
    constraint categories_unique1 unique (cat_nom)
);
CREATE table if NOT EXISTS comptes (
    co_id serial primary key,
    solde int default 0 not null,
    createdAt timestamp default now()
);
CREATE table if NOT EXISTS transactions (
    tra_id serial primary key,
    tra_mte int default 0 not null,
    tra_type char(1) not null,
    createdAt timestamp default now(),
    co_id int,
    cat_id int default null,
    constraint transactions_fkey1 foreign key (co_id) references comptes (co_id) on update cascade on delete cascade,
    constraint transactions_fkey2 foreign key (cat_id) references categories (cat_id) on update cascade on delete cascade,
    constraint transactions_check1 check (tra_type in ('d', 'r'))
);
-- Inserer quelques categories
INSERT into categories (cat_nom)
values('Education'),
('Transport'),
('Alimentation'),
('Depenses'),
('Habillement'),
('Accessoires Informatique');
-- Creation du comptes
insert INTO comptes (solde) values (0);
