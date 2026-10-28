create database gestao_cafeteria;
use gestao_cafeteria;

create table usuarios (
    id int auto_increment primary key,
    nome varchar(100) not null,
    usuario varchar(50) not null unique,
    senha varchar(255) not null
);

create table cardapio (
    id int auto_increment primary key,
    nome_item varchar(100) not null,
    categoria varchar(50) not null,
    descricao text,
    preco decimal(10, 2) not null,
    disponivel boolean default true
);

insert into usuarios (nome, usuario, senha) values ('Gerente', 'admin', 'admin123');
